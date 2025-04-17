<?php
require_once 'functions.php';
session_start();

$message = '';

// Process the form when it is submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use the email as the username for registration.
    $email = trim($_POST['inputEmail']);
    $password = trim($_POST['inputPassword']);

    // Optionally, you can capture the name field if you plan to use it later.
    $name = trim($_POST['inputName']); // Not used in registerUser() by default

    // Call the registerUser function. (Remember: static mode isn’t implemented.)
    if (registerUser($name, $email, $password)) {
        $message = 'Registration successful. You can now log in.';
    } else {
        $message = 'Registration failed. Please try again.';
    }
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

  <title>Apex CRM - Register</title>
</head>

<body>

  <div class="login-bg-overlay au-sign-up-basic"></div>

  <!--start wrapper-->
  <div class="wrapper">
    <header>
      <nav class="navbar navbar-expand-lg navbar-light bg-white p-3">
        <div class="container-fluid">
          <a href="javascript:;"><img src="assets/images/logo-icon-3.png" alt="" /></a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-3 login-menu-2">
              <li class="nav-item">
                <a class="nav-link" href="javascript:;">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;">About Us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;">Team</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;">Products</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;">Blog</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="javascript:;">Contact</a>
              </li>
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
        <div class="col-xl-5 col-lg-6 col-md-7 mx-auto mt-5">
          <div class="card radius-10">
            <div class="card-body p-4">
              <div class="text-center">
                <h4>Sign Up</h4>
                <p>Create New Account</p>
              </div>
              <!-- Display the message if set -->
              <?php if ($message): ?>
                  <div class="alert alert-info">
                      <?php echo htmlspecialchars($message); ?>
                  </div>
              <?php endif; ?>
              <!-- Registration form -->
              <form class="form-body row g-3" method="post" action="register.php">
                <div class="col-12 col-lg-12">
                  <div class="d-grid gap-2">
                    <a href="javascript:;" class="btn border border-2 border-primary">
                      <img src="assets/images/icons/google.png" width="20" alt="">
                      <span class="ms-3 fw-500">Sign up with Google</span>
                    </a>
                    <a href="javascript:;" class="btn border border-2 border-dark">
                      <img src="assets/images/icons/apple-black-logo.png" width="20" alt="">
                      <span class="ms-3 fw-500">Sign up with Apple</span>
                    </a>
                  </div>
                </div>
                <div class="col-12 col-lg-12">
                  <div class="position-relative border-bottom my-3">
                    <div class="position-absolute seperator-2 translate-middle-y">OR</div>
                  </div>
                </div>
                <div class="col-12">
                  <label for="inputName" class="form-label">Name</label>
                  <input type="text" class="form-control" id="inputName" name="inputName" placeholder="Your name">
                </div>
                <div class="col-12">
                  <label for="inputEmail" class="form-label">Email</label>
                  <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="abc@example.com" required>
                </div>
                <div class="col-12">
                  <label for="inputPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Your password" required>
                </div>
                <div class="col-12 col-lg-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" checked>
                    <label class="form-check-label" for="flexCheckChecked">
                      I agree to the Terms and Conditions
                    </label>
                  </div>
                </div>
                <div class="col-12 col-lg-12">
                  <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Sign Up</button>
                  </div>
                </div>
                <div class="col-12 col-lg-12 text-center">
                  <p class="mb-0">Already have an account? <a href="login.php">Sign in</a></p>
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
</body>
</html>
