<div class="container mt-4">
    <h2>Thêm người mượn mới</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?c=borrowers&a=store">
        <div class="form-group">
            <label>Họ tên *</label>
            <input type="text" name="full_name" class="form-control" 
                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" name="phone" class="form-control" 
                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        
        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="index.php?c=borrowers&a=index" class="btn btn-secondary">Hủy</a>
    </form>
</div>