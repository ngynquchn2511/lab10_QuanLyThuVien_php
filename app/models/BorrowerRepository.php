<?php
require_once __DIR__ . '/../config/db.php';

class BorrowerRepository {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }
    
    /**
     * Lấy tất cả người mượn
     */
    public function getAll() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM borrowers ORDER BY id DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("BorrowerRepository::getAll Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy 1 người mượn theo ID
     */
    public function findById($id) {
        $id = (int)$id;
        
        if ($id <= 0) {
            return null;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM borrowers WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("BorrowerRepository::findById Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Thêm người mượn mới
     */
    public function create($data) {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO borrowers (full_name, phone) VALUES (:full_name, :phone)"
            );
            
            return $stmt->execute([
                'full_name' => $data['full_name'],
                'phone' => $data['phone']
            ]);
        } catch (PDOException $e) {
            error_log("BorrowerRepository::create Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật người mượn
     */
    public function update($id, $data) {
        $id = (int)$id;
        
        if ($id <= 0) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE borrowers SET full_name = :full_name, phone = :phone WHERE id = :id"
            );
            
            return $stmt->execute([
                'id' => $id,
                'full_name' => $data['full_name'],
                'phone' => $data['phone']
            ]);
        } catch (PDOException $e) {
            error_log("BorrowerRepository::update Error: " . $e->getMessage());
            return false;
        }
    }
}