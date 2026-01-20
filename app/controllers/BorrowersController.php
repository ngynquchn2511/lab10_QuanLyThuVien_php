<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/BorrowerRepository.php';

class BorrowersController extends Controller {
    private $borrowerRepo;
    
    public function __construct() {
        $this->borrowerRepo = new BorrowerRepository();
    }
    
    /**
     * Danh sách người mượn
     */
    public function index() {
        $borrowers = $this->borrowerRepo->getAll();
        
        $this->view('borrowers/index', [
            'borrowers' => $borrowers,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }
    
    /**
     * Hiển thị form thêm
     */
    public function create() {
        $this->view('borrowers/create', [
            'errors' => $this->getFlash('errors') ?? []
        ]);
    }
    
    /**
     * Xử lý thêm người mượn
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?c=borrowers&a=create');
            return;
        }
        
        // Validate
        $errors = [];
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        if (empty($fullName)) {
            $errors[] = "Họ tên không được để trống";
        } elseif (strlen($fullName) > 120) {
            $errors[] = "Họ tên không quá 120 ký tự";
        }
        
        if (!empty($phone) && strlen($phone) > 20) {
            $errors[] = "Số điện thoại không quá 20 ký tự";
        }
        
        if (!empty($errors)) {
            $this->setFlash('errors', $errors);
            $this->redirect('index.php?c=borrowers&a=create');
            return;
        }
        
        // Lưu
        $result = $this->borrowerRepo->create([
            'full_name' => $fullName,
            'phone' => $phone
        ]);
        
        if ($result) {
            $this->setFlash('success', 'Thêm người mượn thành công!');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra');
        }
        
        $this->redirect('index.php?c=borrowers&a=index');
    }
    
    /**
     * Hiển thị form sửa
     */
    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        
        if ($id <= 0) {
            $this->setFlash('error', 'ID không hợp lệ');
            $this->redirect('index.php?c=borrowers&a=index');
            return;
        }
        
        $borrower = $this->borrowerRepo->findById($id);
        
        if (!$borrower) {
            $this->setFlash('error', 'Không tìm thấy người mượn');
            $this->redirect('index.php?c=borrowers&a=index');
            return;
        }
        
        $this->view('borrowers/edit', [
            'borrower' => $borrower,
            'errors' => $this->getFlash('errors') ?? []
        ]);
    }
    
    /**
     * Xử lý cập nhật
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?c=borrowers&a=index');
            return;
        }
        
        $id = (int)($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            $this->setFlash('error', 'ID không hợp lệ');
            $this->redirect('index.php?c=borrowers&a=index');
            return;
        }
        
        // Validate
        $errors = [];
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        if (empty($fullName) || strlen($fullName) > 120) {
            $errors[] = "Họ tên không hợp lệ";
        }
        if (!empty($phone) && strlen($phone) > 20) {
            $errors[] = "Số điện thoại không hợp lệ";
        }
        
        if (!empty($errors)) {
            $this->setFlash('errors', $errors);
            $this->redirect("index.php?c=borrowers&a=edit&id=$id");
            return;
        }
        
        $result = $this->borrowerRepo->update($id, [
            'full_name' => $fullName,
            'phone' => $phone
        ]);
        
        if ($result) {
            $this->setFlash('success', 'Cập nhật thành công!');
        } else {
            $this->setFlash('error', 'Có lỗi xảy ra');
        }
        
        $this->redirect('index.php?c=borrowers&a=index');
    }
}