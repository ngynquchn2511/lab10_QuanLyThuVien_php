<div class="container mt-4">
    <h2>Danh sách phiếu mượn</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="mb-3">
        <a href="index.php?c=borrows&a=create" class="btn btn-success">+ Tạo phiếu mượn mới</a>
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Người mượn</th>
                <th>Ngày mượn</th>
                <th>Ghi chú</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($borrows)): ?>
                <tr>
                    <td colspan="5" class="text-center">Chưa có phiếu mượn nào</td>
                </tr>
            <?php else: ?>
                <?php foreach ($borrows as $borrow): ?>
                    <tr>
                        <td><?= $borrow['id'] ?></td>
                        <td><?= htmlspecialchars($borrow['borrower_name']) ?></td>
                        <td><?= date('d/m/Y', strtotime($borrow['borrow_date'])) ?></td>
                        <td><?= htmlspecialchars($borrow['note']) ?></td>
                        <td>
                            <a href="index.php?c=borrows&a=show&id=<?= $borrow['id'] ?>" 
                               class="btn btn-sm btn-info">Chi tiết</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>