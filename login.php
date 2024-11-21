<?php 
session_start(); 
if(isset($_SESSION['username'])){
    header('Location: dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'templates/header.php' ?>
    <title>Login - BBQS BURU-UN</title>
</head>
<body class="login">
    <?php include 'templates/loading_screen.php' ?>
    <div class="wrapper wrapper-login">
        <div class="container container-login animated fadeIn">
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['success']; ?> <?= $_SESSION['success']=='danger' ? 'bg-danger text-light' : null ?>" role="alert">
                    <?= $_SESSION['message']; ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif ?>
            <Center><img src="assets/img/bbic1.png" height="80" width="80" ></center>
            <h2 class="text-center">BRGY BURU-UN QUEUEING SYSTEM</h2>
            <h4 class="text-center">LOG IN</h4>

            <div class="login-form">
                <form method="POST" action="model/login.php">
                    <div class="form-group form-floating-label">
                        <input id="username" name="username" type="text" class="form-control input-border-bottom" required>
                        <label for="username" class="placeholder">Username</label>
                    </div>
                    <div class="form-group form-floating-label">
                        <input id="password" name="password" type="password" class="form-control input-border-bottom" required>
                        <label for="password" class="placeholder">Password</label>
                        <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                    <div class="form-action mb-3">
                        <button type="submit" class="btn btn-primary btn-rounded btn-login">Sign In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'templates/footer.php' ?>
    
    <script>
        window.onload = function() {
            // Check if the user is logged in
            if (sessionStorage.getItem('loggedIn') === 'true') {
                // Redirect to dashboard if user is logged in
                window.location.href = 'dashboard.php';
            }

            // Clear session storage if coming from logout
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('clearStorage') === 'true') {
                sessionStorage.removeItem('loggedIn');
            }
        }
    </script>
</body>
</html>
