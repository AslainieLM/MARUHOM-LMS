<?= $this->include('templates/header') ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="page-title mb-0"><i class="bi bi-people me-1"></i> Manage Users</div>
        <?php if (empty($showCreateForm) && empty($showEditForm)): ?>
            <a href="<?= base_url('admin/manage_users?create=true') ?>" class="btn btn-dark btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Add User
            </a>
        <?php else: ?>
            <a href="<?= base_url('admin/manage_users') ?>" class="btn btn-outline-dark btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back to List
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($showCreateForm)): ?>
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-person-plus me-1"></i> Create New User</div>
        <div class="card-body">
            <form action="<?= base_url('admin/manage_users?action=create') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" value="<?= old('name') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-sm" value="<?= old('email') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control form-control-sm" required minlength="6">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select form-select-sm" required>
                            <option value="">Select Role</option>
                            <option value="admin">Admin</option>
                            <option value="librarian">Librarian</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control form-control-sm" value="<?= old('phone') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control form-control-sm" rows="2"><?= old('address') ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark btn-sm"><i class="bi bi-check-lg me-1"></i>Create User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($showEditForm) && !empty($editUser)): ?>
    <div class="card mb-4">
        <div class="card-header"><i class="bi bi-pencil me-1"></i> Edit User: <?= esc($editUser['name']) ?></div>
        <div class="card-body">
            <form action="<?= base_url('admin/manage_users?action=edit&id=' . $editUser['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" value="<?= esc($editUser['name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control form-control-sm" value="<?= esc($editUser['email']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">New Password <small class="text-muted">(leave blank to keep)</small></label>
                        <input type="password" name="password" class="form-control form-control-sm" minlength="6">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select form-select-sm" required>
                            <option value="admin" <?= $editUser['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="librarian" <?= $editUser['role'] === 'librarian' ? 'selected' : '' ?>>Librarian</option>
                            <option value="teacher" <?= $editUser['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                            <option value="student" <?= $editUser['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control form-control-sm" value="<?= esc($editUser['phone'] ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control form-control-sm" rows="2"><?= esc($editUser['address'] ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark btn-sm"><i class="bi bi-check-lg me-1"></i>Update User</button>
                        <a href="<?= base_url('admin/manage_users') ?>" class="btn btn-outline-dark btn-sm ms-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><i class="bi bi-list-ul me-1"></i> All Users (<?= count($users) ?>)</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Phone</th><th>Created</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $i => $u): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="fw-semibold"><?= esc($u['name']) ?></td>
                            <td><?= esc($u['email']) ?></td>
                            <td><span class="badge bg-dark"><?= ucfirst($u['role']) ?></span></td>
                            <td><?= esc($u['phone'] ?? '-') ?></td>
                            <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                            <td>
                                <?php if ($u['id'] != $currentAdminID): ?>
                                    <a href="<?= base_url('admin/manage_users?action=edit&id=' . $u['id']) ?>"
                                       class="btn btn-sm btn-outline-dark" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <?php if ($u['role'] !== 'admin'): ?>
                                        <a href="<?= base_url('admin/manage_users?action=delete&id=' . $u['id']) ?>"
                                           class="btn btn-sm btn-outline-dark ms-1"
                                           onclick="return confirm('Delete user <?= esc($u['name']) ?>?')" title="Delete"><i class="bi bi-trash"></i></a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-secondary">You</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
