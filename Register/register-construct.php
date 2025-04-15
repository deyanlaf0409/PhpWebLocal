<!DOCTYPE html>
<html>
<head>
  <title>Register Page</title>
  <link rel="icon" type="image/png" href="/project/favicons/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/project/favicons/favicon.svg" />
  <link rel="shortcut icon" href="/project/favicons/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="/project/favicons/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-title" content="NoteBlocks" />
  <link rel="manifest" href="/project/favicons/site.webmanifest" />
  <link rel="stylesheet" href="register-style.css">
  <link rel="stylesheet" href="../master/footer-style.css">
  <link rel="stylesheet" href="/project/master/background.css">
</head>
<body>

  <div class="custom-arrow">
    <a href="../Login/construct.php" class="back-link"></a>
  </div>
  
  <form class="fade-in" id="reg-form" enctype="multipart/form-data">
    <h1>Register</h1>

    <label for="username"></label>
    <input type="text" id="username" name="username" maxlength="20" placeholder="Username">


    <label for="email">Email:</label>
    <input type="email" id="email" name="email" title="Please enter a valid email address">
    <span class="password-info">Password must be at least 8 symbols long.</span>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
   
    <label for="confirm-password">Confirm Password:</label>
    <input type="password" id="confirm-password" name="confirm-password">

    <div class="checkbox-container">
      <input type="checkbox" id="agree" name="agree">
      <label for="agree">I agree to the terms and conditions</label>
    </div>

    <?php include '../reCaptcha.php'; ?>
    <div class="g-recaptcha" data-sitekey="<?php echo $recaptchaSiteKey; ?>"></div>

    <button id="register-button" onclick="checkRegister(event)">Register</button>
  </form>

  <?php include '../master/footer.php'; ?>

  <script src="register-script.js"></script>

  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</body>
</html>