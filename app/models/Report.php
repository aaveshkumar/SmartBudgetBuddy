<?php
require_once __DIR__ . '/../../config/database.php';

class Report {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function create($data) {
        $sql = "INSERT INTO reports (reporter_id, reported_type, reported_id, message) 
                VALUES (:reporter_id, :reported_type, :reported_id, :message)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':reporter_id' => $data['reporter_id'],
            ':reported_type' => $data['reported_type'],
            ':reported_id' => $data['reported_id'],
            ':message' => $data['message']
        ]);
        return $this->db->lastInsertId();
    }
    
    public function findById($id) {
        $sql = "SELECT r.*, 
                u.name as reporter_name, u.email as reporter_email
                FROM reports r
                JOIN users u ON r.reporter_id = u.id
                WHERE r.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    public function getAll($status = null, $limit = 50, $offset = 0) {
        $sql = "SELECT r.*, 
                u.name as reporter_name, u.email as reporter_email
                FROM reports r
                JOIN users u ON r.reporter_id = u.id";
        
        $params = [];
        if ($status) {
            $sql .= " WHERE r.status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " ORDER BY r.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getPendingCount() {
        $sql = "SELECT COUNT(*) as count FROM reports WHERE status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function getCountByStatus() {
        $sql = "SELECT status, COUNT(*) as count FROM reports GROUP BY status";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    public function updateStatus($id, $status, $adminNotes = null) {
        $sql = "UPDATE reports SET status = :status, admin_notes = :notes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':status' => $status,
            ':notes' => $adminNotes
        ]);
    }
    
    public function hasReported($reporterId, $reportedType, $reportedId) {
        $sql = "SELECT id FROM reports 
                WHERE reporter_id = :reporter_id 
                AND reported_type = :reported_type 
                AND reported_id = :reported_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':reporter_id' => $reporterId,
            ':reported_type' => $reportedType,
            ':reported_id' => $reportedId
        ]);
        return $stmt->fetch() !== false;
    }
    
    public function delete($id) {
        $sql = "DELETE FROM reports WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    public function getReportedJobDetails($jobId) {
        $sql = "SELECT j.*, u.name as employer_name, u.email as employer_email
                FROM jobs j
                JOIN users u ON j.employer_id = u.id
                WHERE j.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $jobId]);
        return $stmt->fetch();
    }
    
    public function getReportedUserDetails($userId) {
        $sql = "SELECT id, name, email, type, status, created_at FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch();
    }
}
