<?= $this->include('templates/header') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title mb-0"><i class="bi bi-arrow-left-right me-1"></i> Manage Borrowings</div>
    </div>

    <!-- Issue Book -->
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-plus-lg me-1"></i> Issue a Book</div>
        <div class="card-body">
            <form action="<?= base_url($user['role'] . '/manage_borrowings?action=issue') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Book <span class="text-danger">*</span></label>
                        <select name="book_id" class="form-select form-select-sm" required>
                            <option value="">Select Book</option>
                            <?php foreach ($availableBooks as $book): ?>
                                <option value="<?= $book['id'] ?>">
                                    <?= esc($book['title']) ?> (<?= $book['available_copies'] ?> available)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Borrower <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-select form-select-sm" required>
                            <option value="">Select Borrower</option>
                            <?php foreach ($borrowers as $b): ?>
                                <option value="<?= $b['id'] ?>">
                                    <?= esc($b['name']) ?> (<?= ucfirst($b['role']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" name="due_date" class="form-control form-control-sm"
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                               value="<?= date('Y-m-d', strtotime('+14 days')) ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Remarks</label>
                        <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Optional">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark btn-sm w-100"><i class="bi bi-check-lg me-1"></i>Issue</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body py-2">
            <div class="d-flex gap-2 align-items-center">
                <small class="fw-semibold text-muted me-2">Filter:</small>
                <a href="<?= base_url($user['role'] . '/manage_borrowings') ?>"
                   class="btn btn-sm <?= empty($statusFilter) ? 'btn-dark' : 'btn-outline-dark' ?>">All</a>
                <a href="<?= base_url($user['role'] . '/manage_borrowings?status=borrowed') ?>"
                   class="btn btn-sm <?= ($statusFilter ?? '') === 'borrowed' ? 'btn-dark' : 'btn-outline-dark' ?>">Borrowed</a>
                <a href="<?= base_url($user['role'] . '/manage_borrowings?status=overdue') ?>"
                   class="btn btn-sm <?= ($statusFilter ?? '') === 'overdue' ? 'btn-dark' : 'btn-outline-dark' ?>">Overdue</a>
                <a href="<?= base_url($user['role'] . '/manage_borrowings?status=returned') ?>"
                   class="btn btn-sm <?= ($statusFilter ?? '') === 'returned' ? 'btn-dark' : 'btn-outline-dark' ?>">Returned</a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header"><i class="bi bi-list-ul me-1"></i> Borrowing Records (<?= count($borrowings) ?>)</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Book</th><th>Borrower</th><th>Borrow Date</th><th>Due Date</th><th>Return Date</th><th>Status</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($borrowings)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-3">No borrowing records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($borrowings as $i => $b): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= esc($b['book_title'] ?? 'Unknown') ?></td>
                            <td>
                                <?= esc($b['borrower_name'] ?? 'Unknown') ?>
                                <span class="badge bg-dark"><?= ucfirst($b['borrower_role'] ?? '') ?></span>
                            </td>
                            <td><?= date('M j, Y', strtotime($b['borrow_date'])) ?></td>
                            <td><?= date('M j, Y', strtotime($b['due_date'])) ?></td>
                            <td><?= $b['return_date'] ? date('M j, Y', strtotime($b['return_date'])) : '-' ?></td>
                            <td><span class="badge bg-dark"><?= ucfirst($b['status']) ?></span></td>
                            <td>
                                <?php if ($b['status'] !== 'returned'): ?>
                                    <a href="<?= base_url($user['role'] . '/manage_borrowings?action=return&id=' . $b['id']) ?>"
                                       class="btn btn-sm btn-dark"
                                       onclick="return confirm('Return this book?')">
                                        <i class="bi bi-box-arrow-in-left me-1"></i>Return
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
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
