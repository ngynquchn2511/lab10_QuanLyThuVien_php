<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/BookRepository.php';

class BooksController extends Controller {
    private $bookRepo;
    
    public function __construct() {
        $this->bookRepo = new BookRepository();
    }
    
    /**
     * Danh sách sách: search + sort
     */
    public function index() {
        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'id';
        $dir = $_GET['dir'] ?? 'asc';
        
        $books = $this->bookRepo->getAll($search, $sort, $dir);
        
        $this->view('books/index', [
            'books' => $books,
            'search' => $search,
            'sort' => $sort,
            'dir' => $dir,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }
    
    /**
     * Hiển thị form thêm sách (GET)
     */
    public function create() {
        $this->view('books/create', [
            'errors' => $this->getFlash('errors') ?? []
        ]);
    }
    
    /**
     * Xử lý thêm sách (POST)
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?c=books&a=create');
            return;
        }
        
        // VALIDATE dữ liệu
        $errors = [];
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $price = $_POST['price'] ?? 0;
        $qty = $_POST['qty'] ?? 0;
        
        if (empty($title)) {
            $errors[] = "Tiêu đề không được để trống";
        } elseif (strlen($title) > 200) {
            $errors[] = "Tiêu đề không quá 200 ký tự";
        }
        
        if (empty($author)) {
            $errors[] = "Tác giả không được để trống";
        } elseif (strlen($author) > 120) {
            $errors[] = "Tác giả không quá 120 ký tự";
        }
        
        if (!is_numeric($price) || $price < 0) {
            $errors[] = "Giá phải là số không âm";
        }
        
        if (!is_numeric($qty) || $qty < 0 || $qty != (int)$qty) {
            $errors[] = "Số lượng phải là số nguyên không âm";
        }
        
        if (!empty($errors)) {
            $this->setFlash('errors', $errors);
            $this->redirect('index.php?c=books&a=create');
            return;
        }
        
        // Lưu vào DB
        $result = $this->bookRepo->create([
            'title' => $title,
            'author' => $author,
            'price' => (float)$price,
            'qty' => (int)$qty
        ]);
        
        if ($result) {
            $this->setFlash('success', 'Thêm sách thành công!');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
        }
        
        // Post-Redirect-Get pattern
        $this->redirect('index.php?c=books&a=index');
    }
    
    /**
     * Hiển thị form sửa (GET)
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            $this->setFlash('error', 'ID không hợp lệ');
            $this->redirect('index.php?c=books&a=index');
            return;
        }
        
        $book = $this->bookRepo->findById($id);
        
        if (!$book) {
            $this->setFlash('error', 'Không tìm thấy sách');
            $this->redirect('index.php?c=books&a=index');
            return;
        }
        
        $this->view('books/edit', [
            'book' => $book,
            'errors' => $this->getFlash('errors') ?? []
        ]);
    }
    
    /**
     * Xử lý cập nhật (POST)
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?c=books&a=index');
            return;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $this->setFlash('error', 'ID không hợp lệ');
            $this->redirect('index.php?c=books&a=index');
            return;
        }
        
        // VALIDATE giống store()
        $errors = [];
        $title = trim($_POST['title'] ?? '');
        $author = trim($_POST['author'] ?? '');
        $price = $_POST['price'] ?? 0;
        $qty = $_POST['qty'] ?? 0;
        
        if (empty($title) || strlen($title) > 200) {
            $errors[] = "Tiêu đề không hợp lệ";
        }
        if (empty($author) || strlen($author) > 120) {
            $errors[] = "Tác giả không hợp lệ";
        }
        if (!is_numeric($price) || $price < 0) {
            $errors[] = "Giá không hợp lệ";
        }
        if (!is_numeric($qty) || $qty < 0 || $qty != (int)$qty) {
            $errors[] = "Số lượng không hợp lệ";
        }
        
        if (!empty($errors)) {
            $this->setFlash('errors', $errors);
            $this->redirect("index.php?c=books&a=edit&id=$id");
            return;
        }
        
        $result = $this->bookRepo->update($id, [
            'title' => $title,
            'author' => $author,
            'price' => (float)$price,
            'qty' => (int)$qty
        ]);
        
        if ($result) {
            $this->setFlash('success', 'Cập nhật thành công!');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra');
        }
        
        $this->redirect('index.php?c=books&a=index');
    }
    
    /**
     * Xóa sách (POST)
     */
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?c=books&a=index');
            return;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $this->setFlash('error', 'ID không hợp lệ');
            $this->redirect('index.php?c=books&a=index');
            return;
        }
        
        $result = $this->bookRepo->delete($id);
        
        if ($result) {
            $this->setFlash('success', 'Xóa thành công!');
        } else {
            $this->setFlash('error', 'Không thể xóa. Sách có thể đang được mượn.');
        }
        
        $this->redirect('index.php?c=books&a=index');
    }
}