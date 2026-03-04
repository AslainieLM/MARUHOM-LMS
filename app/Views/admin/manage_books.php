<?= $this->include('templates/header') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title mb-0"><i class="bi bi-book me-1"></i> Manage Books</div>
        <div>
            <?php if (empty($showCreateForm) && empty($showEditForm)): ?>
                <a href="<?= base_url($user['role'] . '/manage_books?create=true') ?>" class="btn btn-dark btn-sm">
                    <i class="bi bi-plus-lg me-1"></i>Add Book
                </a>
            <?php else: ?>
                <a href="<?= base_url($user['role'] . '/manage_books') ?>" class="btn btn-outline-dark btn-sm">
                    <i class="bi bi-arrow-left me-1"></i>Back to List
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($showCreateForm)): ?>
    <!-- Categories -->
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-folder me-1"></i> Book Categories</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form action="<?= base_url($user['role'] . '/manage_categories?action=create') ?>" method="POST" class="d-flex gap-2">
                        <?= csrf_field() ?>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Category name" required>
                        <input type="text" name="description" class="form-control form-control-sm" placeholder="Description (optional)">
                        <button type="submit" class="btn btn-dark btn-sm">Add</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-wrap gap-1 mt-2 mt-md-0">
                        <?php foreach ($categories as $cat): ?>
                            <span class="badge bg-dark d-flex align-items-center gap-1">
                                <?= esc($cat['name']) ?>
                                <a href="<?= base_url($user['role'] . '/manage_categories?action=delete&id=' . $cat['id']) ?>"
                                   class="text-white ms-1" onclick="return confirm('Delete this category?')" title="Delete">
                                    <i class="bi bi-x-circle"></i>
                                </a>
                            </span>
                        <?php endforeach; ?>
                        <?php if (empty($categories)): ?>
                            <small class="text-muted">No categories yet.</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Book -->
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-plus-lg me-1"></i> Add New Book</div>
        <div class="card-body">
            <form action="<?= base_url($user['role'] . '/manage_books?action=create') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-sm" value="<?= old('title') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Author <span class="text-danger">*</span></label>
                        <input type="text" name="author" class="form-control form-control-sm" value="<?= old('author') ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ISBN</label>
                        <input type="text" name="isbn" class="form-control form-control-sm" value="<?= old('isbn') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Publisher</label>
                        <input type="text" name="publisher" class="form-control form-control-sm" value="<?= old('publisher') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Year</label>
                        <input type="number" name="publication_year" class="form-control form-control-sm" value="<?= old('publication_year') ?>" min="1900" max="<?= date('Y') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Copies <span class="text-danger">*</span></label>
                        <input type="number" name="total_copies" class="form-control form-control-sm" value="<?= old('total_copies', 1) ?>" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-select-sm" required>
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">No Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Shelf Location</label>
                        <input type="text" name="shelf_location" class="form-control form-control-sm" value="<?= old('shelf_location') ?>" placeholder="e.g., A-1-3">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control form-control-sm" rows="1"><?= old('description') ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark btn-sm"><i class="bi bi-check-lg me-1"></i>Add Book</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($showEditForm) && !empty($editBook)): ?>
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-pencil me-1"></i> Edit Book: <?= esc($editBook['title']) ?></div>
        <div class="card-body">
            <form action="<?= base_url($user['role'] . '/manage_books?action=edit&id=' . $editBook['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-sm" value="<?= esc($editBook['title']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Author <span class="text-danger">*</span></label>
                        <input type="text" name="author" class="form-control form-control-sm" value="<?= esc($editBook['author']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ISBN</label>
                        <input type="text" name="isbn" class="form-control form-control-sm" value="<?= esc($editBook['isbn'] ?? '') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Publisher</label>
                        <input type="text" name="publisher" class="form-control form-control-sm" value="<?= esc($editBook['publisher'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Year</label>
                        <input type="number" name="publication_year" class="form-control form-control-sm" value="<?= esc($editBook['publication_year'] ?? '') ?>" min="1900" max="<?= date('Y') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Total Copies <span class="text-danger">*</span></label>
                        <input type="number" name="total_copies" class="form-control form-control-sm" value="<?= esc($editBook['total_copies']) ?>" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select form-select-sm" required>
                            <option value="available" <?= $editBook['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="unavailable" <?= $editBook['status'] === 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">No Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($editBook['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Shelf Location</label>
                        <input type="text" name="shelf_location" class="form-control form-control-sm" value="<?= esc($editBook['shelf_location'] ?? '') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control form-control-sm" rows="1"><?= esc($editBook['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark btn-sm"><i class="bi bi-check-lg me-1"></i>Update Book</button>
                        <a href="<?= base_url($user['role'] . '/manage_books') ?>" class="btn btn-outline-dark btn-sm ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><i class="bi bi-list-ul me-1"></i> All Books (<?= count($books) ?>)</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Title</th><th>Author</th><th>Category</th><th>ISBN</th><th>Copies</th><th>Location</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($books)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-3">No books found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($books as $i => $book): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= esc($book['title']) ?></td>
                            <td><?= esc($book['author']) ?></td>
                            <td><?= esc($book['category_name'] ?? 'Uncategorized') ?></td>
                            <td><small><?= esc($book['isbn'] ?? '-') ?></small></td>
                            <td><span class="badge bg-dark"><?= $book['available_copies'] ?? 0 ?>/<?= $book['total_copies'] ?></span></td>
                            <td><small><?= esc($book['shelf_location'] ?? '-') ?></small></td>
                            <td><span class="badge bg-dark"><?= ucfirst($book['status']) ?></span></td>
                            <td>
                                <a href="<?= base_url($user['role'] . '/manage_books?action=edit&id=' . $book['id']) ?>"
                                   class="btn btn-sm btn-outline-dark" title="Edit"><i class="bi bi-pencil"></i></a>
                                <a href="<?= base_url($user['role'] . '/manage_books?action=delete&id=' . $book['id']) ?>"
                                   class="btn btn-sm btn-outline-dark ms-1"
                                   onclick="return confirm('Delete book: <?= esc($book['title']) ?>?')" title="Delete"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
