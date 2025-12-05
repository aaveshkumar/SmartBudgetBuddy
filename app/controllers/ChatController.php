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
        
        if (!$this->conversationModel->canAccess($conversationId, $user['id'])) {
            echo json_encode(['error' => 'Access denied']);
            return;
        }
        
        $message = trim($_POST['message'] ?? '');
        if (empty($message)) {
            echo json_encode(['error' => 'Message cannot be empty']);
            return;
        }
        
        $existingMessages = $this->messageModel->getByConversationId($conversationId, 1);
        if (empty($existingMessages) && $user['type'] !== 'employer') {
            echo json_encode(['error' => 'Only employers can send the first message. Please wait for the employer to contact you.']);
            return;
        }
        
        $messageId = $this->messageModel->create([
            'conversation_id' => $conversationId,
            'sender_id' => $user['id'],
            'message' => $message
        ]);
        
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
}
