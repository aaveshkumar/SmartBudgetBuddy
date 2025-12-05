<?php
require_once __DIR__ . '/../../config/database.php';

class Message {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        $hasAttachment = !empty($data['attachment_path']);
        
        if ($hasAttachment) {
            $sql = "INSERT INTO conversation_messages (conversation_id, sender_id, message, attachment_path, attachment_name, attachment_type) 
                    VALUES (:conversation_id, :sender_id, :message, :attachment_path, :attachment_name, :attachment_type)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':conversation_id' => $data['conversation_id'],
                ':sender_id' => $data['sender_id'],
                ':message' => $data['message'] ?? '',
                ':attachment_path' => $data['attachment_path'],
                ':attachment_name' => $data['attachment_name'],
                ':attachment_type' => $data['attachment_type']
            ]);
        } else {
            $sql = "INSERT INTO conversation_messages (conversation_id, sender_id, message) 
                    VALUES (:conversation_id, :sender_id, :message)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':conversation_id' => $data['conversation_id'],
                ':sender_id' => $data['sender_id'],
                ':message' => $data['message']
            ]);
        }
        return $this->db->lastInsertId();
    }
    
    public function getByConversationId($conversationId, $limit = 50, $offset = 0) {
        $sql = "SELECT m.*, u.name as sender_name 
                FROM conversation_messages m
                JOIN users u ON m.sender_id = u.id
                WHERE m.conversation_id = :conversation_id
                ORDER BY m.created_at ASC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':conversation_id', $conversationId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getNewMessages($conversationId, $lastMessageId) {
        $sql = "SELECT m.*, u.name as sender_name 
                FROM conversation_messages m
                JOIN users u ON m.sender_id = u.id
                WHERE m.conversation_id = :conversation_id AND m.id > :last_id
                ORDER BY m.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':conversation_id' => $conversationId,
            ':last_id' => $lastMessageId
        ]);
        return $stmt->fetchAll();
    }
    
    public function markAsRead($conversationId, $userId) {
        $sql = "UPDATE conversation_messages 
                SET is_read = 1 
                WHERE conversation_id = :conversation_id 
                AND sender_id != :user_id 
                AND is_read = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':conversation_id' => $conversationId,
            ':user_id' => $userId
        ]);
    }
    
    public function findById($id) {
        $sql = "SELECT m.*, u.name as sender_name 
                FROM conversation_messages m
                JOIN users u ON m.sender_id = u.id
                WHERE m.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
