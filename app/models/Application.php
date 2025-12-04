<?php
/**
 * Application Model
 */

require_once __DIR__ . '/../../config/database.php';

class Application {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    // Create new application
    public function create($jobId, $userId, $resumePath) {
        $sql = "INSERT INTO applications (job_id, user_id, resume_path) 
                VALUES (:job_id, :user_id, :resume_path)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':job_id' => $jobId,
            ':user_id' => $userId,
            ':resume_path' => $resumePath
        ]);
        
        return $this->db->lastInsertId();
    }
    
    // Check if user already applied
    public function hasApplied($jobId, $userId) {
        $sql = "SELECT COUNT(*) as count FROM applications 
                WHERE job_id = :job_id AND user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':job_id' => $jobId, ':user_id' => $userId]);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
    
    // Get applications for a job
    public function getByJob($jobId, $limit = null, $offset = 0) {
        $sql = "SELECT a.*, u.name as applicant_name, u.email as applicant_email,
                jp.phone as applicant_phone
                FROM applications a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN jobseeker_profiles jp ON a.user_id = jp.user_id
                WHERE a.job_id = :job_id
                ORDER BY a.applied_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':job_id', $jobId, PDO::PARAM_INT);
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Get applications by user
    public function getByUser($userId, $limit = null, $offset = 0) {
        $sql = "SELECT a.*, j.title as job_title, j.location, j.type, j.salary,
                u.name as employer_name, c.name as category_name
                FROM applications a
                JOIN jobs j ON a.job_id = j.id
                JOIN users u ON j.employer_id = u.id
                LEFT JOIN categories c ON j.category_id = c.id
                WHERE a.user_id = :user_id
                ORDER BY a.applied_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Count applications for a job
    public function countByJob($jobId) {
        $sql = "SELECT COUNT(*) as total FROM applications WHERE job_id = :job_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':job_id' => $jobId]);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    // Count applications by user
    public function countByUser($userId) {
        $sql = "SELECT COUNT(*) as total FROM applications WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    // Delete application
    public function delete($id) {
        $sql = "DELETE FROM applications WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    // Find application by ID
    public function findById($id) {
        $sql = "SELECT a.*, u.name as applicant_name, u.email as applicant_email,
                jp.phone as applicant_phone
                FROM applications a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN jobseeker_profiles jp ON a.user_id = jp.user_id
                WHERE a.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    // Update application status
    public function updateStatus($id, $status) {
        $sql = "UPDATE applications SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':status' => $status]);
    }
    
    // Get all applications for employer
    public function getByEmployer($employerId, $limit = null, $offset = 0) {
        $sql = "SELECT a.*, j.title as job_title, u.name as applicant_name, u.email as applicant_email
                FROM applications a
                JOIN jobs j ON a.job_id = j.id
                JOIN users u ON a.user_id = u.id
                WHERE j.employer_id = :employer_id
                ORDER BY a.applied_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':employer_id', $employerId, PDO::PARAM_INT);
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
