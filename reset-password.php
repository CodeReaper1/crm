<?php
session_start();
require_once 'functions.php';

// Initialize variables
$email = $message = '';
$message_type = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    
    // Here you would typically:
    // 1. Check if the email exists in your database
    // 2. Generate a reset token and store it with an expiration time
    // 3. Send an email with a link containing the token
    
    // For now, we'll just show a success message
    $message = 'If an account with that email exists, a password reset link has been sent.';
    $message_type = 'success';
}
?>
<!doctype html>
<html lang="en" class="light-theme">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Enqueue assets registered via functions.php -->
    <?php enqueue_crm_assets(); ?>
    <?php print_styles(); ?>

    <!-- Additional fonts or manual links can be added if needed -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <title>Apex CRM - Reset Password</title>
</head>

<body>

<!--start wrapper-->
<div class="wrapper">
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-transparent p-3">
            <div class="container-fluid">
                <a href="javascript:;"><img src="assets/images/logo-icon-3.png" alt="" /></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3">
                        <li class="nav-item"><a class="nav-link" href="javascript:;">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="javascript:;">About Us</a></li>
                        <li class="nav-item"><a class="nav-link" href="javascript:;">Team</a></li>
                        <li class="nav-item"><a class="nav-link" href="javascript:;">Products</a></li>
                        <li class="nav-item"><a class="nav-link" href="javascript:;">Blog</a></li>
                        <li class="nav-item"><a class="nav-link" href="javascript:;">Contact</a></li>
                    </ul>
                    <form class="d-flex">
                        <a href="javascript:;" class="btn btn-sm btn-primary px-4 radius-30">Buy Now</a>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    <div class="container">
        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-7 mx-auto mt-5">
                <div class="card radius-10">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <h4>Reset Password</h4>
                            <p>Enter your email to reset your password</p>
                        </div>

                        <?php if (!empty($message)): ?>
                            <div class="alert alert-<?php echo $message_type ? $message_type : 'info'; ?>"><?php echo htmlspecialchars($message); ?></div>
                        <?php endif; ?>

                        <form class="form-body row g-3" method="POST" action="">
                            <div class="col-12">
                                <label for="inputEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="inputEmail" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                            <div class="col-12 col-lg-12">
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Reset Password</button>
                                </div>
                            </div>
                            <div class="col-12 col-lg-12 text-center">
                                <p class="mb-0">Remember your password? <a href="login.php">Sign in</a></p>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="my-5">
        <div class="container">
            <div class="d-flex align-items-center gap-4 fs-5 justify-content-center social-login-footer">
                <a href="javascript:;"><ion-icon name="logo-twitter"></ion-icon></a>
                <a href="javascript:;"><ion-icon name="logo-linkedin"></ion-icon></a>
                <a href="javascript:;"><ion-icon name="logo-github"></ion-icon></a>
                <a href="javascript:;"><ion-icon name="logo-facebook"></ion-icon></a>
                <a href="javascript:;"><ion-icon name="logo-pinterest"></ion-icon></a>
            </div>
            <div class="text-center">
                <p class="my-4">Copyright © 2021 UI Admin by Codervent.</p>
            </div>
        </div>
    </footer>
</div>
<!--end wrapper-->

<?php print_footer_scripts(); ?>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
</body>
</html>
