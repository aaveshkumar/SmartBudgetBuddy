<?php
require_once __DIR__ . '/../../config/database.php';

class Notification {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        $sql = "INSERT INTO notifications (recipient_id, actor_id, related_type, related_id, type, title, message) 
                VALUES (:recipient_id, :actor_id, :related_type, :related_id, :type, :title, :message)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':recipient_id' => $data['recipient_id'],
            ':actor_id' => $data['actor_id'] ?? null,
            ':related_type' => $data['related_type'] ?? 'system',
            ':related_id' => $data['related_id'] ?? null,
            ':type' => $data['type'],
            ':title' => $data['title'],
            ':message' => $data['message']
        ]);
    }
    
    public function getByUserId($userId, $limit = 20, $offset = 0) {
        $sql = "SELECT n.*, u.name as actor_name 
                FROM notifications n
                LEFT JOIN users u ON n.actor_id = u.id
                WHERE n.recipient_id = :user_id
                ORDER BY n.created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE recipient_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function markAsRead($id, $userId) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id AND recipient_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }
    
    public function markAllAsRead($userId) {
        $sql = "UPDATE notifications SET is_read = 1 WHERE recipient_id = :user_id AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }
    
    public function delete($id, $userId) {
        $sql = "DELETE FROM notifications WHERE id = :id AND recipient_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':user_id' => $userId]);
    }
    
    public function getUnreadSince($userId, $since) {
        $sql = "SELECT n.*, u.name as actor_name 
                FROM notifications n
                LEFT JOIN users u ON n.actor_id = u.id
                WHERE n.recipient_id = :user_id AND n.created_at > :since
                ORDER BY n.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId, ':since' => $since]);
        return $stmt->fetchAll();
    }
    
    public function notifyJobSeekerSelected($candidateId, $employerId, $jobId, $applicationId, $jobTitle, $companyName) {
        return $this->create([
            'recipient_id' => $candidateId,
            'actor_id' => $employerId,
            'related_type' => 'application',
            'related_id' => $applicationId,
            'type' => 'job_selected',
            'title' => 'Congratulations! You have been selected',
            'message' => "You have been selected for the position of $jobTitle at $companyName. You can now chat with the employer."
        ]);
    }
    
    public function notifyNewJob($candidateId, $jobId, $jobTitle, $companyName) {
        return $this->create([
            'recipient_id' => $candidateId,
            'actor_id' => null,
            'related_type' => 'job',
            'related_id' => $jobId,
            'type' => 'new_job',
            'title' => 'New Job Posted',
            'message' => "A new job has been posted: $jobTitle at $companyName"
        ]);
    }
    
    public function notifySystemUpdate($userId, $title, $message) {
        return $this->create([
            'recipient_id' => $userId,
            'actor_id' => null,
            'related_type' => 'system',
            'related_id' => null,
            'type' => 'system',
            'title' => $title,
            'message' => $message
        ]);
    }
    
    public function notifyNewChatMessage($recipientId, $senderId, $conversationId, $senderName) {
        return $this->create([
            'recipient_id' => $recipientId,
            'actor_id' => $senderId,
            'related_type' => 'application',
            'related_id' => $conversationId,
            'type' => 'chat_message',
            'title' => 'New Message',
            'message' => "You have a new message from $senderName"
        ]);
    }
    
    public function getJobSeekersByRole() {
        $sql = "SELECT id FROM users WHERE role = 'jobseeker'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function getEmployersByRole() {
        $sql = "SELECT id FROM users WHERE role = 'employer'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
