<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/BorrowRepository.php';
require_once __DIR__ . '/../models/BorrowerRepository.php';
require_once __DIR__ . '/../models/BookRepository.php';

class BorrowsController extends Controller {
    private $borrowRepo;
    private $borrowerRepo;
    private $bookRepo;
    
    public function __construct() {
        $this->borrowRepo = new BorrowRepository();
        $this->borrowerRepo = new BorrowerRepository();
        $this->bookRepo = new BookRepository();
    }
    
    /**
     * Danh sách phiếu mượn
     */
    public function index() {
        $borrows = $this->borrowRepo->getAll();
        
        $this->view('borrows/index', [
            'borrows' => $borrows,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }
    
    /**
     * Form tạo phiếu mượn (GET)
     */
    public function create() {
        $borrowers = $this->borrowerRepo->getAll();
        $books = $this->bookRepo->getAll(); // Lấy tất cả sách
        
        $this->view('borrows/create', [
            'borrowers' => $borrowers,
            'books' => $books,
            'errors' => $this->getFlash('errors') ?? []
        ]);
    }
    
    /**
     * Xử lý tạo phiếu mượn (POST) - DÙNG TRANSACTION
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?c=borrows&a=create');
            return;
        }
        
        // Validate input
        $errors = [];
        $borrowerId = (int)($_POST['borrower_id'] ?? 0);
        $borrowDate = $_POST['borrow_date'] ?? '';
        $note = trim($_POST['note'] ?? '');
        $bookIds = $_POST['book_ids'] ?? [];
        $qtys = $_POST['qtys'] ?? [];
        
        if ($borrowerId <= 0) {
            $errors[] = "Vui lòng chọn người mượn";
        }
        
        if (empty($borrowDate)) {
            $errors[] = "Vui lòng chọn ngày mượn";
        }
        
        if (empty($bookIds) || empty($qtys)) {
            $errors[] = "Vui lòng chọn ít nhất 1 sách";
        }
        
        // Validate từng sách
        $items = [];
        foreach ($bookIds as $index => $bookId) {
            $bookId = (int)$bookId;
            $qty = (int)($qtys[$index] ?? 0);
            
            if ($bookId > 0 && $qty > 0) {
                $items[] = [
                    'book_id' => $bookId,
                    'qty' => $qty
                ];
            }
        }
        
        if (empty($items)) {
            $errors[] = "Không có sách hợp lệ nào được chọn";
        }
        
        if (!empty($errors)) {
            $this->setFlash('errors', $errors);
            $this->redirect('index.php?c=borrows&a=create');
            return;
        }
        
        // GỌI REPOSITORY để tạo phiếu mượn với TRANSACTION
        $result = $this->borrowRepo->createBorrow(
            $borrowerId,
            $borrowDate,
            $items,
            $note
        );
        
        if ($result['success']) {
            $this->setFlash('success', $result['message']);
            $this->redirect('index.php?c=borrows&a=show&id=' . $result['borrow_id']);
        } else {
            $this->setFlash('error', $result['message']);
            $this->redirect('index.php?c=borrows&a=create');
        }
    }
    
    /**
     * Xem chi tiết phiếu mượn
     */
    public function show() {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            $this->setFlash('error', 'ID không hợp lệ');
            $this->redirect('index.php?c=borrows&a=index');
            return;
        }
        
        $borrow = $this->borrowRepo->findById($id);
        
        if (!$borrow) {
            $this->setFlash('error', 'Không tìm thấy phiếu mượn');
            $this->redirect('index.php?c=borrows&a=index');
            return;
        }
        
        $this->view('borrows/show', [
            'borrow' => $borrow,
            'success' => $this->getFlash('success')
        ]);
    }
}