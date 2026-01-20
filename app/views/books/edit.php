<div class="container mt-4">
    <h2>Sửa thông tin sách</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?c=books&a=update">
        <input type="hidden" name="id" value="<?= $book['id'] ?>">
        
        <div class="form-group">
            <label>Tiêu đề *</label>
            <input type="text" name="title" class="form-control" 
                   value="<?= htmlspecialchars($book['title']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Tác giả *</label>
            <input type="text" name="author" class="form-control" 
                   value="<?= htmlspecialchars($book['author']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Giá (VNĐ) *</label>
            <input type="number" name="price" class="form-control" 
                   value="<?= $book['price'] ?>" min="0" step="0.01" required>
        </div>
        
        <div class="form-group">
            <label>Số lượng *</label>
            <input type="number" name="qty" class="form-control" 
                   value="<?= $book['qty'] ?>" min="0" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="index.php?c=books&a=index" class="btn btn-secondary">Hủy</a>
    </form>
</div>