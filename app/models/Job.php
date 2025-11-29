<?php
/**
 * Job Model
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/constants.php';

class Job {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    // Create new job
    public function create($data) {
        $sql = "INSERT INTO jobs (title, description, category_id, employer_id, location, salary, type, status) 
                VALUES (:title, :description, :category_id, :employer_id, :location, :salary, :type, :status)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':category_id' => $data['category_id'],
            ':employer_id' => $data['employer_id'],
            ':location' => $data['location'],
            ':salary' => $data['salary'] ?? null,
            ':type' => $data['type'] ?? JOB_TYPE_FULLTIME,
            ':status' => $data['status'] ?? JOB_STATUS_PENDING
        ]);
        
        return $this->db->lastInsertId();
    }
    
    // Get job by ID with employer details
    public function findById($id) {
        $sql = "SELECT j.*, u.name as employer_name, u.email as employer_email, 
                c.name as category_name, c.slug as category_slug
                FROM jobs j
                LEFT JOIN users u ON j.employer_id = u.id
                LEFT JOIN categories c ON j.category_id = c.id
                WHERE j.id = :id LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    // Get all jobs with filters
    public function getAll($filters = []) {
        $sql = "SELECT j.*, u.name as employer_name, c.name as category_name, c.slug as category_slug
                FROM jobs j
                LEFT JOIN users u ON j.employer_id = u.id
                LEFT JOIN categories c ON j.category_id = c.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND j.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['employer_id'])) {
            $sql .= " AND j.employer_id = :employer_id";
            $params[':employer_id'] = $filters['employer_id'];
        }
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND j.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['type'])) {
            $sql .= " AND j.type = :type";
            $params[':type'] = $filters['type'];
        }
        
        if (!empty($filters['location'])) {
            $sql .= " AND j.location LIKE :location";
            $params[':location'] = '%' . $filters['location'] . '%';
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (j.title LIKE :search1 OR j.description LIKE :search2)";
            $params[':search1'] = '%' . $filters['search'] . '%';
            $params[':search2'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY j.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        if (!empty($filters['limit'])) {
            $stmt->bindValue(':limit', (int)$filters['limit'], PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)($filters['offset'] ?? 0), PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Count jobs
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM jobs WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }
        
        if (!empty($filters['employer_id'])) {
            $sql .= " AND employer_id = :employer_id";
            $params[':employer_id'] = $filters['employer_id'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    // Update job
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        $allowedFields = ['title', 'description', 'category_id', 'location', 'salary', 'type', 'status'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE jobs SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    // Delete job
    public function delete($id) {
        $sql = "DELETE FROM jobs WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    // Get latest jobs
    public function getLatest($limit = 10) {
        return $this->getAll(['status' => JOB_STATUS_APPROVED, 'limit' => $limit]);
    }
    
    // Search jobs (for autocomplete)
    public function search($query, $limit = 10) {
        $sql = "SELECT DISTINCT title FROM jobs 
                WHERE status = :status AND title LIKE :query 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':status', JOB_STATUS_APPROVED);
        $stmt->bindValue(':query', '%' . $query . '%');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    // Get jobs by category
    public function getByCategory($categoryId, $limit = null) {
        return $this->getAll([
            'category_id' => $categoryId,
            'status' => JOB_STATUS_APPROVED,
            'limit' => $limit
        ]);
    }
}
