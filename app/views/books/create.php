<div class="container mt-4">
    <h2>Thêm sách mới</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?c=books&a=store">
        <div class="form-group">
            <label>Tiêu đề *</label>
            <input type="text" name="title" class="form-control" 
                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Tác giả *</label>
            <input type="text" name="author" class="form-control" 
                   value="<?= htmlspecialchars($_POST['author'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Giá (VNĐ) *</label>
            <input type="number" name="price" class="form-control" 
                   value="<?= htmlspecialchars($_POST['price'] ?? '0') ?>" 
                   min="0" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label>Số lượng *</label>
            <input type="number" name="qty" class="form-control" 
                   value="<?= htmlspecialchars($_POST['qty'] ?? '0') ?>" 
                   min="0" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="index.php?c=books&a=index" class="btn btn-secondary">Hủy</a>
    </form>
</div>