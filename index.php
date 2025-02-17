<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Login</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <h2>Login with Social Media</h2>

        <?php if (isset($_SESSION['userData'])): ?>
            <div class="profile">
                <img src="<?php echo $_SESSION['userData']['picture']; ?>" alt="Profile Picture" width="80">
                <h3><?php echo $_SESSION['userData']['name']; ?></h3>
                <p><?php echo $_SESSION['userData']['email']; ?></p>
                <a href="logout.php"><button class="logout">Logout</button></a>
            </div>
        <?php else: ?>
            <a href="https://accounts.google.com/o/oauth2/auth?client_id=YOUR_GOOGLE_CLIENT_ID&redirect_uri=http://localhost/socialLinkAPI/google-callback.php&response_type=code&scope=email profile">
                <button class="google">Login with Google</button>
            </a>
            <br><br>
            <a href="https://www.facebook.com/v15.0/dialog/oauth?client_id=YOUR_FACEBOOK_APP_ID&redirect_uri=http://localhost/socialLinkAPI/facebook-callback.php&scope=email,public_profile">
                <button class="facebook">Login with Facebook</button>
            </a>
        <?php endif; ?>
    </div>
</body>
</html>
