<?php
require_once __DIR__ . '/../models/Conversation.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/csrf.php';

class ChatController {
    private $conversationModel;
    private $messageModel;
    private $notificationModel;
    
    public function __construct() {
        $this->conversationModel = new Conversation();
        $this->messageModel = new Message();
        $this->notificationModel = new Notification();
    }
    
    public function index() {
        $user = getCurrentUser();
        if (!$user) {
            redirect('/login');
        }
        
        $conversations = $this->conversationModel->getByUserId($user['id'], $user['type']);
        $totalUnread = $this->conversationModel->getTotalUnreadCount($user['id']);
        
        $meta = generateMetaTags('Messages', 'Your conversations');
        require __DIR__ . '/../views/chat/index.php';
    }
    
    public function conversation($id) {
        $user = getCurrentUser();
        if (!$user) {
            redirect('/login');
        }
        
        if (!$this->conversationModel->canAccess($id, $user['id'])) {
            setFlash('error', 'You do not have access to this conversation');
            redirect('/chat');
        }
        
        $conversation = $this->conversationModel->findById($id);
        $messages = $this->messageModel->getByConversationId($id);
        
        if ($user['type'] === 'jobseeker' && empty($messages)) {
            setFlash('error', 'This conversation is not available yet. Please wait for the employer to contact you.');
            redirect('/chat');
        }
        
        $conversations = $this->conversationModel->getByUserId($user['id'], $user['type']);
        
        $this->messageModel->markAsRead($id, $user['id']);
        
        $otherPartyName = $user['type'] === 'employer' ? $conversation['candidate_name'] : $conversation['employer_name'];
        $currentUser = $user;
        
        $meta = generateMetaTags('Chat with ' . $otherPartyName, 'Conversation about ' . $conversation['job_title']);
        require __DIR__ . '/../views/chat/conversation.php';
    }
    
    public function sendMessage($conversationId) {
        header('Content-Type: application/json');
        
        $user = getCurrentUser();
        if (!$user) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrfToken)) {
            echo json_encode(['error' => 'Invalid security token. Please refresh the page.']);
            return;
        }
        
        if (!$this->conversationModel->canAccess($conversationId, $user['id'])) {
            echo json_encode(['error' => 'Access denied']);
            return;
        }
        
        $message = trim($_POST['message'] ?? '');
        $hasAttachment = !empty($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK;
        
        if (empty($message) && !$hasAttachment) {
            echo json_encode(['error' => 'Please enter a message or attach a file']);
            return;
        }
        
        $existingMessages = $this->messageModel->getByConversationId($conversationId, 1);
        if (empty($existingMessages) && $user['type'] !== 'employer') {
            echo json_encode(['error' => 'Only employers can send the first message. Please wait for the employer to contact you.']);
            return;
        }
        
        $attachmentData = [];
        if ($hasAttachment) {
            $uploadResult = $this->handleAttachmentUpload($_FILES['attachment']);
            if ($uploadResult['error']) {
                echo json_encode(['error' => $uploadResult['error']]);
                return;
            }
            $attachmentData = $uploadResult;
        }
        
        $messageData = [
            'conversation_id' => $conversationId,
            'sender_id' => $user['id'],
            'message' => $message
        ];
        
        if (!empty($attachmentData)) {
            $messageData['attachment_path'] = $attachmentData['path'];
            $messageData['attachment_name'] = $attachmentData['name'];
            $messageData['attachment_type'] = $attachmentData['type'];
        }
        
        $messageId = $this->messageModel->create($messageData);
        
        if ($messageId) {
            $conversation = $this->conversationModel->findById($conversationId);
            $recipientId = $user['type'] === 'employer' ? $conversation['candidate_id'] : $conversation['employer_id'];
            
            $this->notificationModel->notifyNewChatMessage($recipientId, $user['id'], $conversationId, $user['name']);
            
            $newMessage = $this->messageModel->findById($messageId);
            echo json_encode([
                'success' => true,
                'message' => $newMessage
            ]);
        } else {
            echo json_encode(['error' => 'Failed to send message']);
        }
    }
    
    private function handleAttachmentUpload($file) {
        $allowedTypes = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/gif' => ['gif'],
            'image/webp' => ['webp'],
            'application/pdf' => ['pdf'],
            'application/msword' => ['doc'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
            'application/vnd.ms-excel' => ['xls'],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
            'text/plain' => ['txt'],
            'application/zip' => ['zip']
        ];
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'zip'];
        
        $maxSize = 10 * 1024 * 1024;
        
        if ($file['size'] > $maxSize) {
            return ['error' => 'File size exceeds 10MB limit'];
        }
        
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $allowedExtensions)) {
            return ['error' => 'File extension not allowed. Allowed: JPG, PNG, GIF, PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP'];
        }
        
        $mimeType = mime_content_type($file['tmp_name']);
        if (!isset($allowedTypes[$mimeType])) {
            return ['error' => 'File type not allowed. Allowed: images, PDF, DOC, DOCX, XLS, XLSX, TXT, ZIP'];
        }
        
        if (!in_array($fileExt, $allowedTypes[$mimeType])) {
            return ['error' => 'File extension does not match file content'];
        }
        
        $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
        $safeOriginalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
        $newFileName = $safeOriginalName . '_' . uniqid() . '.' . $fileExt;
        
        $uploadDir = __DIR__ . '/../../public/uploads/chat_attachments/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $targetPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return [
                'path' => '/uploads/chat_attachments/' . $newFileName,
                'name' => $file['name'],
                'type' => $mimeType,
                'error' => null
            ];
        }
        
        return ['error' => 'Failed to upload file'];
    }
    
    public function getMessages($conversationId) {
        header('Content-Type: application/json');
        
        $user = getCurrentUser();
        if (!$user) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        
        if (!$this->conversationModel->canAccess($conversationId, $user['id'])) {
            echo json_encode(['error' => 'Access denied']);
            return;
        }
        
        $messages = $this->messageModel->getByConversationId($conversationId);
        
        if ($user['type'] === 'jobseeker' && empty($messages)) {
            echo json_encode([
                'success' => true,
                'messages' => []
            ]);
            return;
        }
        
        $lastMessageId = $_GET['last_id'] ?? 0;
        
        if ($lastMessageId > 0) {
            $messages = $this->messageModel->getNewMessages($conversationId, $lastMessageId);
        }
        
        $this->messageModel->markAsRead($conversationId, $user['id']);
        
        echo json_encode([
            'success' => true,
            'messages' => $messages
        ]);
    }
    
    public function getUnreadCount() {
        header('Content-Type: application/json');
        
        $user = getCurrentUser();
        if (!$user) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        
        $count = $this->conversationModel->getTotalUnreadCount($user['id']);
        echo json_encode(['count' => (int)$count]);
    }
    
    public function startConversation($candidateId) {
        $user = getCurrentUser();
        if (!$user) {
            redirect('/login');
        }
        
        if ($user['type'] !== 'employer') {
            setFlash('error', 'Only employers can start conversations');
            redirect('/chat');
        }
        
        $jobId = $_GET['job_id'] ?? null;
        if (!$jobId) {
            setFlash('error', 'Job ID is required');
            redirect('/chat');
        }
        
        $conversation = $this->conversationModel->findByParticipants($user['id'], $candidateId, $jobId);
        
        if ($conversation) {
            redirect('/chat/' . $conversation['id']);
        } else {
            setFlash('error', 'No conversation found for this candidate. Please select them first.');
            redirect('/employer/jobs/' . $jobId . '/applications');
        }
    }
}
