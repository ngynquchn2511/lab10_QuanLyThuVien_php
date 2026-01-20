<?php
require_once __DIR__ . '/../config/db.php';

class BookRepository {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }
    
    /**
     * Lấy danh sách sách với tìm kiếm và sắp xếp
     * CHỐNG SQL INJECTION: dùng prepared statement + whitelist sort
     */
    public function getAll($search = '', $sort = 'id', $dir = 'asc') {
        // WHITELIST cho sort và dir (BẮT BUỘC)
        $allowedSort = ['id', 'title', 'author', 'price', 'qty', 'created_at'];
        $allowedDir = ['asc', 'desc'];
        
        if (!in_array($sort, $allowedSort)) {
            $sort = 'id';
        }
        if (!in_array(strtolower($dir), $allowedDir)) {
            $dir = 'asc';
        }
        
        try {
            $sql = "SELECT * FROM books WHERE 1=1";
            $params = [];
            
            // Tìm kiếm an toàn với LIKE và prepared statement
            if (!empty($search)) {
                $sql .= " AND (title LIKE :search OR author LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            // Ghép ORDER BY (đã whitelist nên an toàn)
            $sql .= " ORDER BY $sort $dir";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("BookRepository::getAll Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy 1 sách theo ID
     * CHỐNG SQL INJECTION: ép kiểu int + prepared statement
     */
    public function findById($id) {
        $id = (int)$id; // ÉP KIỂU BẮT BUỘC
        
        if ($id <= 0) {
            return null;
        }
        
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM books WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("BookRepository::findById Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Thêm sách mới
     * CHỐNG SQL INJECTION: prepared statement
     */
    public function create($data) {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO books (title, author, price, qty) 
                 VALUES (:title, :author, :price, :qty)"
            );
            
            return $stmt->execute([
                'title' => $data['title'],
                'author' => $data['author'],
                'price' => $data['price'],
                'qty' => $data['qty']
            ]);
            
        } catch (PDOException $e) {
            error_log("BookRepository::create Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật sách
     * CHỐNG SQL INJECTION: prepared statement
     */
    public function update($id, $data) {
        $id = (int)$id;
        
        if ($id <= 0) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE books 
                 SET title = :title, author = :author, price = :price, qty = :qty 
                 WHERE id = :id"
            );
            
            return $stmt->execute([
                'id' => $id,
                'title' => $data['title'],
                'author' => $data['author'],
                'price' => $data['price'],
                'qty' => $data['qty']
            ]);
            
        } catch (PDOException $e) {
            error_log("BookRepository::update Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa sách
     * CHỐNG SQL INJECTION: ép kiểu + prepared statement
     */
    public function delete($id) {
        $id = (int)$id;
        
        if ($id <= 0) {
            return false;
        }
        
        try {
            $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("BookRepository::delete Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Giảm số lượng sách khi mượn
     * Dùng trong transaction
     */
    public function decreaseQty($bookId, $qty) {
        $bookId = (int)$bookId;
        $qty = (int)$qty;
        
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE books SET qty = qty - :qty WHERE id = :id AND qty >= :qty"
            );
            return $stmt->execute(['id' => $bookId, 'qty' => $qty]);
        } catch (PDOException $e) {
            error_log("BookRepository::decreaseQty Error: " . $e->getMessage());
            return false;
        }
    }
}