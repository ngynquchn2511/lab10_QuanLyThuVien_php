<?php
require_once __DIR__ . '/../config/db.php';

class BorrowRepository {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }
    
    /**
     * Lấy tất cả phiếu mượn
     */
    public function getAll() {
        try {
            $sql = "SELECT b.*, br.full_name as borrower_name 
                    FROM borrows b 
                    JOIN borrowers br ON b.borrower_id = br.id 
                    ORDER BY b.id DESC";
            
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("BorrowRepository::getAll Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy chi tiết phiếu mượn theo ID
     */
    public function findById($id) {
        $id = (int)$id;
        
        if ($id <= 0) {
            return null;
        }
        
        try {
            // Lấy thông tin phiếu mượn
            $stmt = $this->pdo->prepare(
                "SELECT b.*, br.full_name, br.phone 
                 FROM borrows b 
                 JOIN borrowers br ON b.borrower_id = br.id 
                 WHERE b.id = :id"
            );
            $stmt->execute(['id' => $id]);
            $borrow = $stmt->fetch();
            
            if (!$borrow) {
                return null;
            }
            
            // Lấy chi tiết sách đã mượn
            $stmt = $this->pdo->prepare(
                "SELECT bi.*, bk.title, bk.author 
                 FROM borrow_items bi 
                 JOIN books bk ON bi.book_id = bk.id 
                 WHERE bi.borrow_id = :id"
            );
            $stmt->execute(['id' => $id]);
            $borrow['items'] = $stmt->fetchAll();
            
            return $borrow;
            
        } catch (PDOException $e) {
            error_log("BorrowRepository::findById Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Tạo phiếu mượn với TRANSACTION
     * Đây là phần QUAN TRỌNG NHẤT - chống SQL Injection + Transaction
     */
    public function createBorrow($borrowerId, $borrowDate, $items, $note = '') {
        $borrowerId = (int)$borrowerId;
        
        // Validate dữ liệu đầu vào
        if ($borrowerId <= 0 || empty($items)) {
            return [
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ'
            ];
        }
        
        try {
            // ==========================================
            // BẮT ĐẦU TRANSACTION (QUAN TRỌNG)
            // ==========================================
            $this->pdo->beginTransaction();
            
            // BƯỚC 1: Tạo phiếu mượn (borrows)
            $stmt = $this->pdo->prepare(
                "INSERT INTO borrows (borrower_id, borrow_date, note) 
                 VALUES (:borrower_id, :borrow_date, :note)"
            );
            $stmt->execute([
                'borrower_id' => $borrowerId,
                'borrow_date' => $borrowDate,
                'note' => $note
            ]);
            
            // Lấy ID của phiếu mượn vừa tạo
            $borrowId = $this->pdo->lastInsertId();
            
            // BƯỚC 2: Xử lý từng sách trong danh sách
            foreach ($items as $item) {
                $bookId = (int)$item['book_id'];
                $qty = (int)$item['qty'];
                
                // Validate từng item
                if ($bookId <= 0 || $qty <= 0) {
                    throw new Exception("Dữ liệu sách không hợp lệ");
                }
                
                // Kiểm tra tồn kho
                $stmt = $this->pdo->prepare("SELECT qty, title FROM books WHERE id = :id");
                $stmt->execute(['id' => $bookId]);
                $book = $stmt->fetch();
                
                if (!$book) {
                    throw new Exception("Sách ID $bookId không tồn tại");
                }
                
                if ($book['qty'] < $qty) {
                    throw new Exception("Sách '{$book['title']}' không đủ số lượng (còn {$book['qty']}, yêu cầu $qty)");
                }
                
                // Thêm vào borrow_items
                $stmt = $this->pdo->prepare(
                    "INSERT INTO borrow_items (borrow_id, book_id, qty) 
                     VALUES (:borrow_id, :book_id, :qty)"
                );
                $stmt->execute([
                    'borrow_id' => $borrowId,
                    'book_id' => $bookId,
                    'qty' => $qty
                ]);
                
                // Trừ số lượng sách trong kho
                $stmt = $this->pdo->prepare(
                    "UPDATE books SET qty = qty - :qty WHERE id = :id"
                );
                $stmt->execute([
                    'id' => $bookId,
                    'qty' => $qty
                ]);
            }
            
            // ==========================================
            // COMMIT TRANSACTION (mọi thứ thành công)
            // ==========================================
            $this->pdo->commit();
            
            return [
                'success' => true,
                'message' => 'Tạo phiếu mượn thành công!',
                'borrow_id' => $borrowId
            ];
            
        } catch (Exception $e) {
            // ==========================================
            // ROLLBACK nếu có bất kỳ lỗi nào
            // ==========================================
            $this->pdo->rollBack();
            error_log("BorrowRepository::createBorrow Error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}