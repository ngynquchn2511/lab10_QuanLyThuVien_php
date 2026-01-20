<div class="container mt-4">
    <h2>Quản lý sách</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <!-- Form tìm kiếm -->
    <div class="row mb-3">
        <div class="col-md-8">
            <form method="GET" class="form-inline">
                <input type="hidden" name="c" value="books">
                <input type="hidden" name="a" value="index">
                <input type="text" name="search" class="form-control mr-2" 
                       placeholder="Tìm theo tên hoặc tác giả..."
                       value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                <a href="index.php?c=books&a=index" class="btn btn-secondary ml-2">Reset</a>
            </form>
        </div>
        <div class="col-md-4 text-right">
            <a href="index.php?c=books&a=create" class="btn btn-success">+ Thêm sách mới</a>
        </div>
    </div>
    
    <!-- Bảng danh sách -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>
                    <a href="?c=books&a=index&sort=id&dir=<?= $dir === 'asc' ? 'desc' : 'asc' ?>&search=<?= urlencode($search) ?>">
                        ID <?= $sort === 'id' ? ($dir === 'asc' ? '▲' : '▼') : '' ?>
                    </a>
                </th>
                <th>
                    <a href="?c=books&a=index&sort=title&dir=<?= $dir === 'asc' ? 'desc' : 'asc' ?>&search=<?= urlencode($search) ?>">
                        Tiêu đề <?= $sort === 'title' ? ($dir === 'asc' ? '▲' : '▼') : '' ?>
                    </a>
                </th>
                <th>Tác giả</th>
                <th>
                    <a href="?c=books&a=index&sort=price&dir=<?= $dir === 'asc' ? 'desc' : 'asc' ?>&search=<?= urlencode($search) ?>">
                        Giá <?= $sort === 'price' ? ($dir === 'asc' ? '▲' : '▼') : '' ?>
                    </a>
                </th>
                <th>
                    <a href="?c=books&a=index&sort=qty&dir=<?= $dir === 'asc' ? 'desc' : 'asc' ?>&search=<?= urlencode($search) ?>">
                        Số lượng <?= $sort === 'qty' ? ($dir === 'asc' ? '▲' : '▼') : '' ?>
                    </a>
                </th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($books)): ?>
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                </tr>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= $book['id'] ?></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= number_format($book['price'], 0, ',', '.') ?> đ</td>
                        <td><?= $book['qty'] ?></td>
                        <td>
                            <a href="index.php?c=books&a=edit&id=<?= $book['id'] ?>" 
                               class="btn btn-sm btn-warning">Sửa</a>
                            
                            <form method="POST" action="index.php?c=books&a=delete" 
                                  style="display:inline;"
                                  onsubmit="return confirm('Bạn chắc chắn muốn xóa?')">
                                <input type="hidden" name="id" value="<?= $book['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>