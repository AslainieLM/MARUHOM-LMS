<?= $this->include('templates/header') ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card">
                <div class="card-header text-center">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="<?= base_url('login') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control form-control-sm" id="email" name="email"
                                   value="<?= old('email') ?>" required placeholder="Enter your email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control form-control-sm" id="password" name="password"
                                   required placeholder="Enter your password">
                        </div>
                        <div class="mb-3">
                            <label for="captcha" class="form-label">CAPTCHA</label>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <img id="captchaImage" src="<?= base_url('captcha/image') ?>"
                                     alt="CAPTCHA" class="border rounded" style="height: 75px;">
                                <button type="button" class="btn btn-outline-dark btn-sm"
                                        onclick="document.getElementById('captchaImage').src='<?= base_url('captcha/image') ?>?'+Date.now()">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                            <input type="text" class="form-control form-control-sm" id="captcha" name="captcha"
                                   required placeholder="Enter CAPTCHA code">
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Sign In</button>
                    </form>
                    <div class="text-center mt-3">
                        <small class="text-muted">Don't have an account?
                            <a href="<?= base_url('register') ?>" class="text-dark fw-semibold">Register here</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
