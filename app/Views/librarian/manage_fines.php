<?= $this->include('templates/header') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title mb-0"><i class="bi bi-cash-stack me-1"></i> Manage Fines</div>
    </div>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-body py-2">
            <div class="d-flex gap-2 align-items-center">
                <small class="fw-semibold text-muted me-2">Filter:</small>
                <a href="<?= base_url($user['role'] . '/manage_fines') ?>"
                   class="btn btn-sm <?= empty($statusFilter) ? 'btn-dark' : 'btn-outline-dark' ?>">All</a>
                <a href="<?= base_url($user['role'] . '/manage_fines?status=unpaid') ?>"
                   class="btn btn-sm <?= ($statusFilter ?? '') === 'unpaid' ? 'btn-dark' : 'btn-outline-dark' ?>">Unpaid</a>
                <a href="<?= base_url($user['role'] . '/manage_fines?status=paid') ?>"
                   class="btn btn-sm <?= ($statusFilter ?? '') === 'paid' ? 'btn-dark' : 'btn-outline-dark' ?>">Paid</a>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-header"><i class="bi bi-list-ul me-1"></i> Fine Records (<?= count($fines) ?>)</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>User</th><th>Book</th><th>Amount</th><th>Reason</th><th>Status</th><th>Paid Date</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                    <?php if (empty($fines)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-3">No fines recorded.</td></tr>
                    <?php else: ?>
                        <?php foreach ($fines as $i => $fine): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= esc($fine['user_name'] ?? 'Unknown') ?></td>
                            <td><?= esc($fine['book_title'] ?? 'Unknown') ?></td>
                            <td class="fw-semibold">P<?= number_format($fine['amount'], 2) ?></td>
                            <td><small><?= esc($fine['reason'] ?? '-') ?></small></td>
                            <td><span class="badge bg-dark"><?= ucfirst($fine['status']) ?></span></td>
                            <td><?= $fine['paid_date'] ? date('M j, Y', strtotime($fine['paid_date'])) : '-' ?></td>
                            <td>
                                <?php if ($fine['status'] === 'unpaid'): ?>
                                    <a href="<?= base_url($user['role'] . '/manage_fines?action=pay&id=' . $fine['id']) ?>"
                                       class="btn btn-sm btn-dark"
                                       onclick="return confirm('Mark this fine as paid?')">
                                        <i class="bi bi-cash me-1"></i>Pay
                                    </a>
                                <?php else: ?>
                                    <i class="bi bi-check-circle text-muted"></i>
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
