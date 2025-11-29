<?php
/**
 * Category Model
 */

require_once __DIR__ . '/../../config/database.php';

class Category {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    // Get all categories
    public function getAll() {
        $sql = "SELECT c.*, COUNT(j.id) as job_count 
                FROM categories c
                LEFT JOIN jobs j ON c.id = j.category_id AND j.status = 'Approved'
                GROUP BY c.id
                ORDER BY c.name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Find category by ID
    public function findById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    // Find category by slug
    public function findBySlug($slug) {
        $sql = "SELECT * FROM categories WHERE slug = :slug LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch();
    }
    
    // Create category
    public function create($name, $slug) {
        $sql = "INSERT INTO categories (name, slug) VALUES (:name, :slug)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':name' => $name, ':slug' => $slug]);
        return $this->db->lastInsertId();
    }
    
    // Update category
    public function update($id, $name, $slug) {
        $sql = "UPDATE categories SET name = :name, slug = :slug WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':name' => $name, ':slug' => $slug]);
    }
    
    // Delete category
    public function delete($id) {
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
