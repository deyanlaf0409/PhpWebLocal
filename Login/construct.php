<!DOCTYPE html>
<html>
<head>
  <title>Login Page</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="/project/favicons/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/project/favicons/favicon.svg" />
  <link rel="shortcut icon" href="/project/favicons/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="/project/favicons/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-title" content="NoteBlocks" />
  <link rel="manifest" href="/project/favicons/site.webmanifest" />
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="../master/footer-style.css">
  <link rel="stylesheet" href="/project/master/background.css">

</head>
<body>

  <div class="custom-arrow">
    <a href="../connect.php" class="back-link"></a>
  </div>

  <form class="fade-in" id="login-form">
    <h1>Login</h1>

    <label for="email">E-mail:</label>
    <input type="email" id="email" name="email" title="Please enter a valid email address">

    <label for="password">Password:</label>
    <input type="password" id="password" name="password">


    <div class="resolve-section">
      <p>Forgot Password? <a href="../Login/ResolvePass/valid_page.php" class="forgot-pass">Resolve</a></p>
    </div>

    <!-- "Don't have an account?" text and register link -->
    <div class="register-section">
      <p>Don't have an account? <a href="../Register/register-construct.php" class="register-link">Create</a></p>
    </div>

    <div class="g-recaptcha" data-sitekey="6LdHnRgrAAAAAIyABrWvNQB3mtU89OlfKqxFBMlz"></div>

    <button id="evil-button" type="submit" onclick="checkLogin(event)">Login</button>
  </form>

  <script src="script.js"></script>

  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

  <?php include '../master/footer.php'; ?>

</body>
</html>




