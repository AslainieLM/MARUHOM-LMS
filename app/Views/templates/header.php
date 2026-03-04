<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>">
    <title><?= $title ?? 'Maruhom Library' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ── Type Scale ─────────────────────────────────────────
         *  Level           Class            Size
         *  Page title      .page-title      1.35rem  (21.6px)
         *  Section title   .section-title   1.05rem  (16.8px)
         *  Body / default  body             0.95rem  (15.2px)
         *  Description     .text-desc       0.95rem  (15.2px)
         *  Caption / small .text-caption    0.875rem (14px)
         *  Stat number     .stat-value      1.625rem (26px)
         *  Icon feature    .icon-feature    1.75rem  (28px)
         *  Icon empty      .icon-empty      2.00rem  (32px)
         * ────────────────────────────────────────────────────── */
        :root { --ml-primary: #232629; }

        body { font-size: 0.95rem; line-height: 1.6; }

        /* Navbar */
        .navbar { background-color: var(--ml-primary) !important; }

        /* Page title – main heading per page */
        .page-title { font-size: 1.35rem; font-weight: 600; color: #212529; margin-bottom: 1rem; }

        /* Section title – card headers, sub-sections */
        .section-title { font-size: 1.05rem; font-weight: 600; color: #212529; }

        /* Description text */
        .text-desc { font-size: 0.95rem; color: #6c757d; }

        /* Caption / fine-print */
        .text-caption { font-size: 0.875rem; color: #6c757d; }

        /* Cards */
        .card { border: 1px solid #dee2e6; border-radius: 0.5rem; }
        .card-header { background-color: #f8f9fa; font-weight: 600; font-size: 0.95rem; padding: 0.75rem 1rem; border-bottom: 1px solid #dee2e6; }

        /* Stat cards */
        .stat-card .stat-value { font-size: 1.625rem; font-weight: 700; color: var(--ml-primary); }
        .stat-card .stat-label { font-size: 0.875rem; color: #6c757d; margin: 0; }

        /* Tables */
        .table th { font-size: 0.875rem; font-weight: 600; text-transform: uppercase; color: #6c757d; }
        .table td { font-size: 0.95rem; vertical-align: middle; }

        /* Badges, labels, buttons */
        .badge { font-size: 0.8125rem; font-weight: 500; }
        .form-label { font-size: 0.95rem; font-weight: 500; }
        .btn { font-size: 0.95rem; }

        /* Icon sizes */
        .icon-feature { font-size: 1.75rem; color: var(--ml-primary); }
        .icon-empty { font-size: 2.00rem; }

        /* Alerts */
        .alert { font-size: 0.95rem; }
    </style>
</head>
<body class="bg-light">

    <?php
    $session = \Config\Services::session();
    $userRole = $session->get('role');
    $isLoggedIn = $session->get('isLoggedIn');
    ?>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?= $isLoggedIn ? base_url('dashboard') : base_url() ?>">
                <i class="bi bi-book me-1"></i> Maruhom Library
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('dashboard') ?>">Dashboard</a>
                        </li>
                        <?php if ($userRole === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/manage_users') ?>">Users</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/manage_books') ?>">Books</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/manage_borrowings') ?>">Borrowings</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/manage_fines') ?>">Fines</a></li>
                        <?php elseif ($userRole === 'librarian'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= base_url('librarian/manage_books') ?>">Books</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= base_url('librarian/manage_borrowings') ?>">Borrowings</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= base_url('librarian/manage_fines') ?>">Fines</a></li>
                        <?php elseif ($userRole === 'teacher'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= base_url('teacher/catalog') ?>">Catalog</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= base_url('teacher/my_borrowings') ?>">My Borrowings</a></li>
                        <?php elseif ($userRole === 'student'): ?>
                            <li class="nav-item"><a class="nav-link" href="<?= base_url('student/catalog') ?>">Catalog</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= base_url('student/my_borrowings') ?>">My Borrowings</a></li>
                        <?php endif; ?>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url() ?>">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('about') ?>">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('contact') ?>">Contact</a></li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
                        <!-- Notification Bell -->
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                      id="notification-badge" style="display:none;" class="text-caption">0</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" style="width:340px; max-height:420px; overflow-y:auto;">
                                <li class="dropdown-header fw-semibold">
                                    <i class="bi bi-bell me-1"></i> Notifications
                                    <span class="badge bg-dark float-end" id="notification-count">0</span>
                                </li>
                                <li><hr class="dropdown-divider m-0"></li>
                                <li id="notification-list">
                                    <div class="text-center py-4 text-muted"><i class="bi bi-inbox d-block mb-2 icon-feature"></i><small>No notifications</small></div>
                                </li>
                            </ul>
                        </li>
                        <!-- User Menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i><?= esc($session->get('name')) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><span class="dropdown-item-text"><small class="text-muted"><?= ucfirst($userRole) ?></small></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                                <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
                        <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-2 mt-1" href="<?= base_url('register') ?>">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container mt-3">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show py-2">
                <i class="bi bi-check-circle me-1"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2">
                <i class="bi bi-exclamation-circle me-1"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2">
                <i class="bi bi-exclamation-circle me-1"></i><strong>Please fix the following:</strong>
                <ul class="mb-0 mt-1">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <?php if ($isLoggedIn): ?>
    <script>
        function fetchNotifications() {
            $.get('<?= base_url('notifications') ?>', function(data) {
                if (data.success) {
                    updateNotificationBadge(data.unread_count);
                    displayNotifications(data.notifications);
                }
            });
        }
        function updateNotificationBadge(count) {
            var $b = $('#notification-badge'), $c = $('#notification-count');
            if (count > 0) { $b.text(count > 99 ? '99+' : count).show(); $c.text(count); }
            else { $b.hide(); $c.text('0'); }
        }
        function displayNotifications(notifications) {
            var $list = $('#notification-list');
            if (!notifications || !notifications.length) {
                $list.html('<div class="text-center py-4 text-muted"><i class="bi bi-inbox d-block mb-2 icon-feature"></i><small>No notifications</small></div>');
                return;
            }
            var html = '';
            notifications.forEach(function(n) {
                var cls = n.is_unread ? 'bg-light border-start border-3 border-dark' : '';
                html += '<div class="px-3 py-2 border-bottom notification-item ' + cls + '" data-notification-id="' + n.id + '" data-is-read="' + n.is_read + '" style="cursor:pointer;">';
                html += '<div class="d-flex justify-content-between"><small' + (n.is_unread ? ' class="fw-semibold"' : '') + '>' + n.message + '</small>';
                if (n.is_unread) html += '<button class="btn btn-sm btn-link p-0 ms-2 mark-read-btn" data-notification-id="' + n.id + '"><i class="bi bi-check2"></i></button>';
                html += '</div><small class="text-muted">' + n.formatted_date + '</small></div>';
            });
            $list.html(html);
            $('.mark-read-btn').on('click', function(e) { e.stopPropagation(); markAsRead($(this).data('notification-id')); });
            $('.notification-item').on('click', function() { if ($(this).data('is-read') === 0) markAsRead($(this).data('notification-id')); });
        }
        function markAsRead(id) {
            var meta = document.querySelector('meta[name^="csrf"]');
            var name = meta ? meta.getAttribute('name') : '<?= csrf_token() ?>';
            var hash = meta ? meta.getAttribute('content') : '<?= csrf_hash() ?>';
            var data = {}; data[name] = hash;
            $.post('<?= base_url('notifications/mark_read') ?>/' + id, data, function(resp) {
                if (resp.csrf_hash && meta) meta.setAttribute('content', resp.csrf_hash);
                if (resp.success) {
                    updateNotificationBadge(resp.unread_count);
                    $('.notification-item[data-notification-id="' + id + '"]').fadeOut(200, function() {
                        $(this).remove();
                        if (!$('#notification-list .notification-item').length) displayNotifications([]);
                    });
                }
            });
        }
        $(function() { fetchNotifications(); setInterval(fetchNotifications, 60000); });
        $('#notificationDropdown').on('click', function() { fetchNotifications(); });
    </script>
    <?php endif; ?>
</body>
</html>
