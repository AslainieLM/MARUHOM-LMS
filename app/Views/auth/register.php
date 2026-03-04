<?= $this->include('templates/header') ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card">
                <div class="card-header text-center">
                    <i class="bi bi-person-plus me-1"></i> Create Account
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('register') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control form-control-sm" id="name" name="name"
                                   value="<?= old('name') ?>" required placeholder="Enter your full name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control form-control-sm" id="email" name="email"
                                   value="<?= old('email') ?>" required placeholder="Enter your email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control form-control-sm" id="password" name="password"
                                   required placeholder="Minimum 6 characters">
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control form-control-sm" id="password_confirm" name="password_confirm"
                                   required placeholder="Re-enter your password">
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Register</button>
                    </form>
                    <div class="text-center mt-3">
                        <small class="text-muted">Already have an account?
                            <a href="<?= base_url('login') ?>" class="text-dark fw-semibold">Sign in here</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
