database
-- Tạo database
CREATE DATABASE IF NOT EXISTS lab10_library CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lab10_library;

-- Xóa bảng cũ nếu có (để chạy lại script)
DROP TABLE IF EXISTS borrow_items;
DROP TABLE IF EXISTS borrows;
DROP TABLE IF EXISTS borrowers;
DROP TABLE IF EXISTS books;

-- Bảng sách
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(120) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    qty INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng người mượn
CREATE TABLE borrowers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng phiếu mượn
CREATE TABLE borrows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    borrower_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    note VARCHAR(255),
    FOREIGN KEY (borrower_id) REFERENCES borrowers(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng chi tiết mượn
CREATE TABLE borrow_items (
    borrow_id INT NOT NULL,
    book_id INT NOT NULL,
    qty INT NOT NULL,
    PRIMARY KEY (borrow_id, book_id),
    FOREIGN KEY (borrow_id) REFERENCES borrows(id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu 
-- Thêm sách mẫu
INSERT INTO books (title, author, price, qty) VALUES
('Lập trình PHP cơ bản', 'Nguyễn Văn A', 150000, 10),
('MySQL từ A đến Z', 'Trần Thị B', 120000, 8),
('Web Development 2025', 'Lê Văn C', 200000, 5),
('JavaScript nâng cao', 'Phạm Thị D', 180000, 12),
('Laravel Framework', 'Hoàng Văn E', 250000, 6),
('Bảo mật Web', 'Đỗ Thị F', 160000, 15),
('SQL Injection Defense', 'Vũ Văn G', 190000, 7),
('Clean Code', 'Robert C. Martin', 300000, 4),
('Design Patterns', 'Gang of Four', 280000, 5),
('Algorithms', 'Cormen et al.', 350000, 3);

-- Thêm người mượn mẫu
INSERT INTO borrowers (full_name, phone) VALUES
('Nguyễn Văn Nam', '0912345678'),
('Trần Thị Lan', '0987654321'),
('Lê Văn Hùng', '0901234567'),
('Phạm Thị Mai', '0976543210'),
('Hoàng Văn Đức', '0923456789');

-- Thêm phiếu mượn mẫu
INSERT INTO borrows (borrower_id, borrow_date, note) VALUES
(1, '2025-01-15', 'Mượn học tập'),
(2, '2025-01-16', 'Cần gấp cho dự án');

-- Thêm chi tiết mượn
INSERT INTO borrow_items (borrow_id, book_id, qty) VALUES
(1, 1, 1),
(1, 2, 1),
(2, 3, 2),
(2, 5, 1);

-- Trừ qty tương ứng (do đã mượn)
UPDATE books SET qty = qty - 1 WHERE id IN (1, 2);
UPDATE books SET qty = qty - 2 WHERE id = 3;
UPDATE books SET qty = qty - 1 WHERE id = 5;

-- TẠO USER RIÊNG (KHÔNG DÙNG ROOT)


-- Tạo user
CREATE USER IF NOT EXISTS 'library_user'@'localhost' IDENTIFIED BY 'library_pass_123';

-- Cấp quyền tối thiểu (chỉ CRUD, không có DROP, CREATE)
GRANT SELECT, INSERT, UPDATE, DELETE ON lab10_library.* TO 'library_user'@'localhost';

-- Áp dụng
FLUSH PRIVILEGES;

-- KIỂM TRA DỮ LIỆU

SELECT 'Books Count:' as Info, COUNT(*) as Total FROM books;
SELECT 'Borrowers Count:' as Info, COUNT(*) as Total FROM borrowers;
SELECT 'Borrows Count:' as Info, COUNT(*) as Total FROM borrows;

-- Xem sách còn lại
SELECT id, title, qty FROM books ORDER BY qty DESC;
