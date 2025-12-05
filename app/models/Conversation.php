<?php
require_once __DIR__ . '/../../config/database.php';

class Conversation {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        $sql = "INSERT INTO conversations (employer_id, candidate_id, job_id, application_id, status) 
                VALUES (:employer_id, :candidate_id, :job_id, :application_id, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':employer_id' => $data['employer_id'],
            ':candidate_id' => $data['candidate_id'],
            ':job_id' => $data['job_id'],
            ':application_id' => $data['application_id'],
            ':status' => $data['status'] ?? 'active'
        ]);
        return $this->db->lastInsertId();
    }
    
    public function findById($id) {
        $sql = "SELECT c.*, 
                j.title as job_title,
                emp.name as employer_name,
                cand.name as candidate_name
                FROM conversations c
                JOIN jobs j ON c.job_id = j.id
                JOIN users emp ON c.employer_id = emp.id
                JOIN users cand ON c.candidate_id = cand.id
                WHERE c.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function findByParticipants($employerId, $candidateId, $jobId) {
        $sql = "SELECT * FROM conversations 
                WHERE employer_id = :employer_id 
                AND candidate_id = :candidate_id 
                AND job_id = :job_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':employer_id' => $employerId,
            ':candidate_id' => $candidateId,
            ':job_id' => $jobId
        ]);
        return $stmt->fetch();
    }
    
    public function getByUserId($userId, $role) {
        if ($role === 'employer') {
            $sql = "SELECT c.*, 
                    j.title as job_title,
                    cand.name as other_party_name,
                    cand.id as other_party_id,
                    (SELECT COUNT(*) FROM conversation_messages cm 
                     WHERE cm.conversation_id = c.id AND cm.sender_id != :user_id1 AND cm.is_read = 0) as unread_count,
                    (SELECT message FROM conversation_messages cm2 
                     WHERE cm2.conversation_id = c.id 
                     ORDER BY cm2.created_at DESC LIMIT 1) as last_message,
                    (SELECT created_at FROM conversation_messages cm3 
                     WHERE cm3.conversation_id = c.id 
                     ORDER BY cm3.created_at DESC LIMIT 1) as last_message_time
                    FROM conversations c
                    JOIN jobs j ON c.job_id = j.id
                    JOIN users cand ON c.candidate_id = cand.id
                    WHERE c.employer_id = :user_id2 AND c.status = 'active'
                    ORDER BY last_message_time DESC";
        } else {
            $sql = "SELECT c.*, 
                    j.title as job_title,
                    emp.name as other_party_name,
                    emp.id as other_party_id,
                    (SELECT COUNT(*) FROM conversation_messages cm 
                     WHERE cm.conversation_id = c.id AND cm.sender_id != :user_id1 AND cm.is_read = 0) as unread_count,
                    (SELECT message FROM conversation_messages cm2 
                     WHERE cm2.conversation_id = c.id 
                     ORDER BY cm2.created_at DESC LIMIT 1) as last_message,
                    (SELECT created_at FROM conversation_messages cm3 
                     WHERE cm3.conversation_id = c.id 
                     ORDER BY cm3.created_at DESC LIMIT 1) as last_message_time
                    FROM conversations c
                    JOIN jobs j ON c.job_id = j.id
                    JOIN users emp ON c.employer_id = emp.id
                    WHERE c.candidate_id = :user_id2 AND c.status = 'active'
                    ORDER BY last_message_time DESC";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id1' => $userId, ':user_id2' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function getTotalUnreadCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM conversation_messages cm
                JOIN conversations c ON cm.conversation_id = c.id
                WHERE (c.employer_id = :user_id OR c.candidate_id = :user_id2)
                AND cm.sender_id != :user_id3
                AND cm.is_read = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':user_id2' => $userId,
            ':user_id3' => $userId
        ]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function canAccess($conversationId, $userId) {
        $sql = "SELECT id FROM conversations 
                WHERE id = :id AND (employer_id = :user_id OR candidate_id = :user_id2)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $conversationId,
            ':user_id' => $userId,
            ':user_id2' => $userId
        ]);
        return $stmt->fetch() !== false;
    }
    
    public function close($id) {
        $sql = "UPDATE conversations SET status = 'closed' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
