<?= $this->include('templates/header') ?>

<div class="container py-4">
    <div class="page-title"><i class="bi bi-list-ul me-1"></i> My Borrowings</div>

    <!-- Unpaid Fines Alert -->
    <?php if ($totalUnpaidFines > 0): ?>
    <div class="alert alert-warning d-flex align-items-center py-2">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <div>
            <strong>You have unpaid fines: P<?= number_format($totalUnpaidFines, 2) ?></strong>
            <br><small>Please settle your fines at the library counter to continue borrowing.</small>
        </div>
    </div>
    <?php endif; ?>

    <!-- Currently Borrowed -->
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-book me-1"></i> Currently Borrowed</div>
        <div class="card-body p-0">
            <?php if (empty($activeBorrowings)): ?>
                <div class="text-center py-4">
                    <i class="bi bi-book text-muted icon-empty"></i>
                    <p class="text-muted mt-2 mb-2">No books currently borrowed.</p>
                    <a href="<?= base_url($user['role'] . '/catalog') ?>" class="btn btn-dark btn-sm">Browse Catalog</a>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Book</th><th>Author</th><th>Borrow Date</th><th>Due Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($activeBorrowings as $i => $b):
                        $isOverdue = strtotime($b['due_date']) < strtotime(date('Y-m-d'));
                        $daysLeft = (int)((strtotime($b['due_date']) - strtotime(date('Y-m-d'))) / 86400);
                    ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= esc($b['book_title']) ?></td>
                            <td><?= esc($b['author']) ?></td>
                            <td><?= date('M j, Y', strtotime($b['borrow_date'])) ?></td>
                            <td><?= date('M j, Y', strtotime($b['due_date'])) ?></td>
                            <td>
                                <?php if ($isOverdue): ?>
                                    <span class="badge bg-dark">Overdue (<?= abs($daysLeft) ?> day(s))</span>
                                <?php else: ?>
                                    <span class="badge bg-dark"><?= $daysLeft ?> day(s) left</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Unpaid Fines Detail -->
    <?php if (!empty($unpaidFines)): ?>
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-cash-stack me-1"></i> Unpaid Fines</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Book</th><th>Amount</th><th>Reason</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($unpaidFines as $i => $fine): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= esc($fine['book_title'] ?? 'Unknown') ?></td>
                            <td class="fw-semibold">P<?= number_format($fine['amount'], 2) ?></td>
                            <td><?= esc($fine['reason'] ?? '-') ?></td>
                            <td><?= date('M j, Y', strtotime($fine['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Borrowing History -->
    <div class="card">
        <div class="card-header"><i class="bi bi-clock-history me-1"></i> Borrowing History</div>
        <div class="card-body p-0">
            <?php if (empty($borrowingHistory)): ?>
                <p class="text-center text-muted py-3">No borrowing history yet.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Book</th><th>Author</th><th>Borrow Date</th><th>Due Date</th><th>Return Date</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($borrowingHistory as $i => $b): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($b['book_title']) ?></td>
                            <td><?= esc($b['author']) ?></td>
                            <td><?= date('M j, Y', strtotime($b['borrow_date'])) ?></td>
                            <td><?= date('M j, Y', strtotime($b['due_date'])) ?></td>
                            <td><?= $b['return_date'] ? date('M j, Y', strtotime($b['return_date'])) : '-' ?></td>
                            <td><span class="badge bg-dark"><?= ucfirst($b['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
