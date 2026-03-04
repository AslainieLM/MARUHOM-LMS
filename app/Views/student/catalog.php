<?= $this->include('templates/header') ?>

<div class="container py-4">
    <div class="page-title"><i class="bi bi-search me-1"></i> Book Catalog</div>

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form action="<?= base_url($user['role'] . '/catalog') ?>" method="GET" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm"
                           placeholder="Search by title, author, or ISBN..."
                           value="<?= esc($search ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($categoryFilter ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-dark btn-sm w-100"><i class="bi bi-search me-1"></i>Search</button>
                </div>
            </form>
            <?php if (!empty($search) || !empty($categoryFilter)): ?>
                <div class="mt-2">
                    <a href="<?= base_url($user['role'] . '/catalog') ?>" class="btn btn-sm btn-outline-dark"><i class="bi bi-x-lg me-1"></i>Clear</a>
                    <small class="text-muted ms-2"><?= count($books) ?> book(s) found</small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Book Cards -->
    <?php if (empty($books)): ?>
        <div class="card">
            <div class="card-body text-center py-4">
                <i class="bi bi-search text-muted icon-empty"></i>
                <p class="text-desc mt-2">No books found. Try adjusting your search.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($books as $book): ?>
            <div class="col-md-4 col-lg-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="text-center mb-2">
                            <i class="bi bi-book icon-feature"></i>
                        </div>
                        <div class="section-title mb-1"><?= esc($book['title']) ?></div>
                        <small class="text-muted d-block mb-1"><i class="bi bi-pen me-1"></i><?= esc($book['author']) ?></small>
                        <?php if (!empty($book['category_name'])): ?>
                            <small class="d-block mb-1"><span class="badge bg-dark"><?= esc($book['category_name']) ?></span></small>
                        <?php endif; ?>
                        <?php if (!empty($book['isbn'])): ?>
                            <small class="text-muted d-block mb-1">ISBN: <?= esc($book['isbn']) ?></small>
                        <?php endif; ?>
                        <?php if (!empty($book['shelf_location'])): ?>
                            <small class="text-muted d-block mb-1"><i class="bi bi-geo-alt me-1"></i><?= esc($book['shelf_location']) ?></small>
                        <?php endif; ?>
                        <div class="mt-auto pt-2">
                            <div class="mb-2">
                                <span class="badge bg-dark"><?= $book['available_copies'] ?? 0 ?>/<?= $book['total_copies'] ?> available</span>
                            </div>
                            <?php if (!empty($book['is_borrowed'])): ?>
                                <button class="btn btn-sm btn-outline-dark w-100" disabled>
                                    <i class="bi bi-check-circle me-1"></i>Already Borrowed
                                </button>
                            <?php elseif (!empty($book['is_reserved'])): ?>
                                <button class="btn btn-sm btn-outline-dark w-100" disabled>
                                    <i class="bi bi-clock me-1"></i>Reserved
                                </button>
                            <?php elseif (($book['available_copies'] ?? 0) > 0): ?>
                                <button class="btn btn-sm btn-dark w-100 reserve-btn"
                                        data-book-id="<?= $book['id'] ?>" data-book-title="<?= esc($book['title']) ?>">
                                    <i class="bi bi-bookmark-plus me-1"></i>Reserve
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-dark w-100" disabled>
                                    <i class="bi bi-x-circle me-1"></i>Unavailable
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    var csrfToken = '<?= csrf_hash() ?>';

    $('.reserve-btn').on('click', function() {
        var btn = $(this);
        var bookId = btn.data('book-id');
        var bookTitle = btn.data('book-title');

        if (!confirm('Reserve "' + bookTitle + '"?')) return;

        btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Reserving...');

        $.ajax({
            url: '<?= base_url('book/reserve') ?>',
            type: 'POST',
            data: { book_id: bookId, csrf_test_name: csrfToken },
            dataType: 'json',
            success: function(response) {
                csrfToken = response.csrf_hash;
                if (response.success) {
                    btn.removeClass('btn-dark').addClass('btn-outline-dark')
                       .html('<i class="bi bi-clock me-1"></i>Reserved');
                    alert(response.message);
                } else {
                    btn.prop('disabled', false).html('<i class="bi bi-bookmark-plus me-1"></i>Reserve');
                    alert(response.message);
                }
            },
            error: function() {
                btn.prop('disabled', false).html('<i class="bi bi-bookmark-plus me-1"></i>Reserve');
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>
