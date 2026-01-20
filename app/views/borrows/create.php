<div class="container mt-4">
    <h2>Tạo phiếu mượn sách</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="index.php?c=borrows&a=store" id="borrowForm">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Người mượn *</label>
                    <select name="borrower_id" class="form-control" required>
                        <option value="">-- Chọn người mượn --</option>
                        <?php foreach ($borrowers as $borrower): ?>
                            <option value="<?= $borrower['id'] ?>">
                                <?= htmlspecialchars($borrower['full_name']) ?> 
                                (<?= htmlspecialchars($borrower['phone']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="form-group">
                    <label>Ngày mượn *</label>
                    <input type="date" name="borrow_date" class="form-control" 
                           value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label>Ghi chú</label>
            <textarea name="note" class="form-control" rows="2"></textarea>
        </div>
        
        <hr>
        
        <h4>Danh sách sách mượn</h4>
        <div id="bookItems">
            <div class="row book-item mb-2">
                <div class="col-md-8">
                    <select name="book_ids[]" class="form-control" required>
                        <option value="">-- Chọn sách --</option>
                        <?php foreach ($books as $book): ?>
                            <option value="<?= $book['id'] ?>" data-qty="<?= $book['qty'] ?>">
                                <?= htmlspecialchars($book['title']) ?> 
                                (Còn: <?= $book['qty'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="qtys[]" class="form-control" 
                           placeholder="Số lượng" min="1" required>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item" 
                            onclick="removeItem(this)">X</button>
                </div>
            </div>
        </div>
        
        <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="addBookItem()">
            + Thêm sách
        </button>
        
        <hr>
        
        <button type="submit" class="btn btn-primary">Tạo phiếu mượn</button>
        <a href="index.php?c=borrows&a=index" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<script>
function addBookItem() {
    const template = document.querySelector('.book-item').cloneNode(true);
    template.querySelectorAll('select, input').forEach(el => el.value = '');
    document.getElementById('bookItems').appendChild(template);
}

function removeItem(btn) {
    const items = document.querySelectorAll('.book-item');
    if (items.length > 1) {
        btn.closest('.book-item').remove();
    } else {
        alert('Phải có ít nhất 1 sách');
    }
}
</script>