<div class="container mt-4">
    <h2>Chi tiết phiếu mượn #<?= $borrow['id'] ?></h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5>Thông tin người mượn</h5>
            <p><strong>Họ tên:</strong> <?= htmlspecialchars($borrow['full_name']) ?></p>
            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($borrow['phone']) ?></p>
            <p><strong>Ngày mượn:</strong> <?= date('d/m/Y', strtotime($borrow['borrow_date'])) ?></p>
            <p><strong>Ghi chú:</strong> <?= htmlspecialchars($borrow['note']) ?></p>
        </div>
    </div>
    
    <h5>Danh sách sách đã mượn</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên sách</th>
                <th>Tác giả</th>
                <th>Số lượng</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($borrow['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td><?= htmlspecialchars($item['author']) ?></td>
                    <td><?= $item['qty'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <a href="index.php?c=borrows&a=index" class="btn btn-secondary">Quay lại</a>
</div>