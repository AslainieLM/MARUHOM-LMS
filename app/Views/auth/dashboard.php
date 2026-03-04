<?= $this->include('templates/header') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="page-title mb-0">
                <?php if ($user['role'] === 'admin'): ?>
                    <i class="bi bi-speedometer2 me-1"></i> Admin Dashboard
                <?php elseif ($user['role'] === 'librarian'): ?>
                    <i class="bi bi-speedometer2 me-1"></i> Librarian Dashboard
                <?php elseif ($user['role'] === 'teacher'): ?>
                    <i class="bi bi-speedometer2 me-1"></i> Teacher Dashboard
                <?php else: ?>
                    <i class="bi bi-speedometer2 me-1"></i> Student Dashboard
                <?php endif; ?>
            </div>
            <span class="text-caption">Welcome, <?= esc($user['name']) ?></span>
        </div>
    </div>

    <?php if ($user['role'] === 'admin'): ?>
    <!-- ADMIN -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body text-center py-3">
                    <div class="stat-value"><?= $totalUsers ?></div>
                    <p class="stat-label"><i class="bi bi-people me-1"></i>Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body text-center py-3">
                    <div class="stat-value"><?= $totalBooks ?></div>
                    <p class="stat-label"><i class="bi bi-book me-1"></i>Total Books</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body text-center py-3">
                    <div class="stat-value"><?= $totalBorrowings ?></div>
                    <p class="stat-label"><i class="bi bi-arrow-left-right me-1"></i>Active Borrowings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body text-center py-3">
                    <div class="stat-value"><?= $totalOverdue ?></div>
                    <p class="stat-label"><i class="bi bi-exclamation-triangle me-1"></i>Overdue</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center py-3">
                <div class="section-title" style="color: var(--ml-primary);"><?= $totalAdmins ?></div>
                <span class="text-caption">Admins</span>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center py-3">
                <div class="section-title" style="color: var(--ml-primary);"><?= $totalLibrarians ?></div>
                <span class="text-caption">Librarians</span>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center py-3">
                <div class="section-title" style="color: var(--ml-primary);"><?= $totalTeachers ?></div>
                <span class="text-caption">Teachers</span>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card"><div class="card-body text-center py-3">
                <div class="section-title" style="color: var(--ml-primary);"><?= $totalStudents ?></div>
                <span class="text-caption">Students</span>
            </div></div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-people me-1"></i> Recent Users</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentUsers as $u): ?>
                            <tr>
                                <td><?= esc($u['name']) ?></td>
                                <td><?= esc($u['email']) ?></td>
                                <td><span class="badge bg-dark"><?= ucfirst($u['role']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><i class="bi bi-arrow-left-right me-1"></i> Recent Borrowings</div>
                <div class="card-body p-0">
                    <?php if (!empty($recentBorrowings)): ?>
                    <table class="table table-hover mb-0">
                        <thead class="table-light"><tr><th>Book</th><th>Borrower</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($recentBorrowings as $b): ?>
                            <tr>
                                <td><?= esc($b['book_title']) ?></td>
                                <td><?= esc($b['borrower_name']) ?></td>
                                <td><span class="badge bg-dark"><?= ucfirst($b['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p class="text-center text-muted py-3">No borrowings yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-center py-3">
            <small class="text-muted">Total Unpaid Fines:</small>
            <span class="fw-semibold ms-2" style="color: var(--ml-primary);">P<?= number_format($totalFinesUnpaid, 2) ?></span>
        </div>
    </div>

    <?php elseif ($user['role'] === 'librarian'): ?>
    <!-- LIBRARIAN -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card stat-card"><div class="card-body text-center py-3">
                <div class="stat-value"><?= $totalBooks ?></div>
                <p class="stat-label"><i class="bi bi-book me-1"></i>Books</p>
            </div></div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card"><div class="card-body text-center py-3">
                <div class="stat-value"><?= $totalAvailableBooks ?></div>
                <p class="stat-label"><i class="bi bi-check-circle me-1"></i>Available</p>
            </div></div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card"><div class="card-body text-center py-3">
                <div class="stat-value"><?= $totalBorrowings ?></div>
                <p class="stat-label"><i class="bi bi-arrow-left-right me-1"></i>Borrowed</p>
            </div></div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card"><div class="card-body text-center py-3">
                <div class="stat-value"><?= $totalOverdue ?></div>
                <p class="stat-label"><i class="bi bi-exclamation-triangle me-1"></i>Overdue</p>
            </div></div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card"><div class="card-body text-center py-3">
                <div class="stat-value"><?= $totalReservations ?></div>
                <p class="stat-label"><i class="bi bi-bookmark me-1"></i>Reserved</p>
            </div></div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card"><div class="card-body text-center py-3">
                <div class="stat-value">P<?= number_format($totalFinesUnpaid, 2) ?></div>
                <p class="stat-label"><i class="bi bi-cash-stack me-1"></i>Unpaid Fines</p>
            </div></div>
        </div>
    </div>

    <?php if (!empty($overdueBorrowings)): ?>
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-exclamation-triangle me-1"></i> Overdue Books</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Book</th><th>Borrower</th><th>Due Date</th><th>Days Overdue</th></tr></thead>
                <tbody>
                <?php foreach ($overdueBorrowings as $b):
                    $daysOverdue = (int)((strtotime(date('Y-m-d')) - strtotime($b['due_date'])) / 86400);
                ?>
                    <tr>
                        <td><?= esc($b['book_title']) ?></td>
                        <td><?= esc($b['borrower_name']) ?> <span class="badge bg-dark"><?= ucfirst($b['borrower_role']) ?></span></td>
                        <td><?= date('M j, Y', strtotime($b['due_date'])) ?></td>
                        <td><span class="badge bg-dark"><?= $daysOverdue ?> day(s)</span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><i class="bi bi-arrow-left-right me-1"></i> Recent Borrowings</div>
        <div class="card-body p-0">
            <?php if (!empty($recentBorrowings)): ?>
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Book</th><th>Borrower</th><th>Borrow Date</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($recentBorrowings as $b): ?>
                    <tr>
                        <td><?= esc($b['book_title']) ?></td>
                        <td><?= esc($b['borrower_name']) ?></td>
                        <td><?= date('M j, Y', strtotime($b['borrow_date'] ?? $b['created_at'])) ?></td>
                        <td><span class="badge bg-dark"><?= ucfirst($b['status']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="text-center text-muted py-3">No borrowings yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php elseif ($user['role'] === 'teacher' || $user['role'] === 'student'): ?>
    <!-- TEACHER / STUDENT -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card"><div class="card-body text-center py-3">
                <div class="stat-value"><?= $totalBorrowed ?></div>
                <p class="stat-label"><i class="bi bi-book me-1"></i>Books Borrowed</p>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card"><div class="card-body text-center py-3">
                <div class="stat-value"><?= $totalHistory ?></div>
                <p class="stat-label"><i class="bi bi-clock-history me-1"></i>Total History</p>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card"><div class="card-body text-center py-3">
                <div class="stat-value"><?= count($activeReservations ?? []) ?></div>
                <p class="stat-label"><i class="bi bi-bookmark me-1"></i>Reservations</p>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card"><div class="card-body text-center py-3">
                <div class="stat-value">P<?= number_format($unpaidFines, 2) ?></div>
                <p class="stat-label"><i class="bi bi-cash-stack me-1"></i>Unpaid Fines</p>
            </div></div>
        </div>
    </div>

    <?php if (!empty($activeBorrowings)): ?>
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-book me-1"></i> Currently Borrowed</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Book</th><th>Author</th><th>Borrow Date</th><th>Due Date</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($activeBorrowings as $b):
                    $isOverdue = strtotime($b['due_date']) < strtotime(date('Y-m-d'));
                ?>
                    <tr>
                        <td class="fw-semibold"><?= esc($b['book_title']) ?></td>
                        <td><?= esc($b['author']) ?></td>
                        <td><?= date('M j, Y', strtotime($b['borrow_date'])) ?></td>
                        <td><?= date('M j, Y', strtotime($b['due_date'])) ?></td>
                        <td><span class="badge bg-dark"><?= $isOverdue ? 'Overdue' : 'Borrowed' ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="card mb-4">
        <div class="card-body text-center py-4">
            <i class="bi bi-book text-muted icon-empty"></i>
            <p class="text-muted mt-2 mb-2">No books currently borrowed.</p>
            <a href="<?= base_url($user['role'] . '/catalog') ?>" class="btn btn-dark btn-sm">Browse Catalog</a>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($activeReservations)): ?>
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-bookmark me-1"></i> Active Reservations</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Book</th><th>Author</th><th>Reserved On</th><th>Expires</th></tr></thead>
                <tbody>
                <?php foreach ($activeReservations as $r): ?>
                    <tr>
                        <td><?= esc($r['book_title']) ?></td>
                        <td><?= esc($r['author']) ?></td>
                        <td><?= date('M j, Y g:i A', strtotime($r['reservation_date'])) ?></td>
                        <td><?= date('M j, Y g:i A', strtotime($r['expiry_date'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-3">
                    <span class="text-caption d-block mb-1">Borrowing Limit</span>
                    <span class="section-title" style="color: var(--ml-primary);"><?= $totalBorrowed ?></span>
                    <span class="text-muted"> / <?= $user['role'] === 'teacher' ? '5' : '3' ?></span>
                    <span class="text-caption d-block mt-1"><?= $user['role'] === 'teacher' ? 'Teachers can borrow up to 5 books' : 'Students can borrow up to 3 books' ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center py-3">
                    <span class="text-caption d-block mb-2">Quick Actions</span>
                    <a href="<?= base_url($user['role'] . '/catalog') ?>" class="btn btn-dark btn-sm me-1"><i class="bi bi-search me-1"></i>Catalog</a>
                    <a href="<?= base_url($user['role'] . '/my_borrowings') ?>" class="btn btn-outline-dark btn-sm"><i class="bi bi-list-ul me-1"></i>My Borrowings</a>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>
