<div class="container mt-4">
    <h2>Quản lý người mượn sách</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="mb-3">
        <a href="index.php?c=borrowers&a=create" class="btn btn-success">+ Thêm người mượn</a>
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Số điện thoại</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($borrowers)): ?>
                <tr>
                    <td colspan="5" class="text-center">Chưa có người mượn nào</td>
                </tr>
            <?php else: ?>
                <?php foreach ($borrowers as $borrower): ?>
                    <tr>
                        <td><?= $borrower['id'] ?></td>
                        <td><?= htmlspecialchars($borrower['full_name']) ?></td>
                        <td><?= htmlspecialchars($borrower['phone']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($borrower['created_at'])) ?></td>
                        <td>
                            <a href="index.php?c=borrowers&a=edit&id=<?= $borrower['id'] ?>" 
                               class="btn btn-sm btn-warning">Sửa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>