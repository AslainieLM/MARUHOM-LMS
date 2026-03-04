<?= $this->include('templates/header') ?>

<div class="container py-4">
    <div class="text-center mb-4">
        <div class="page-title"><i class="bi bi-book me-1"></i> Welcome to Maruhom Library</div>
        <p class="text-desc">Your gateway to knowledge and discovery</p>
    </div>

    <div class="row g-3 justify-content-center mb-4">
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body py-4">
                    <i class="bi bi-collection icon-feature"></i>
                    <div class="section-title mt-3">Extensive Collection</div>
                    <span class="text-desc">Browse thousands of books across various categories and genres.</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body py-4">
                    <i class="bi bi-arrow-left-right icon-feature"></i>
                    <div class="section-title mt-3">Easy Borrowing</div>
                    <span class="text-desc">Borrow and return books with our streamlined management system.</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 text-center">
                <div class="card-body py-4">
                    <i class="bi bi-bell icon-feature"></i>
                    <div class="section-title mt-3">Stay Updated</div>
                    <span class="text-desc">Get notifications about due dates, new arrivals, and reservations.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center">
        <a href="<?= base_url('login') ?>" class="btn btn-dark me-2"><i class="bi bi-box-arrow-in-right me-1"></i>Sign In</a>
        <a href="<?= base_url('register') ?>" class="btn btn-outline-dark"><i class="bi bi-person-plus me-1"></i>Register</a>
    </div>
</div>
