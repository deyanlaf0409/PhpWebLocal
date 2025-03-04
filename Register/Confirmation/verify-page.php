<!DOCTYPE html>
<html>
<head>
    <title>Verification Required</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Set the body to fill the viewport height */
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        /* Add a class for the fade-in effect */
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }   

        form {
            margin: 200px auto;
            text-align: center;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 15px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            position: relative;
            opacity: 0;
        }

        .fade-in form {
            opacity: 1;
        }

        .btn {
            background: black; /* Initial background color */
            color: white;
            padding: 8px 16px;
            margin-right: 2px;
            text-decoration: none;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            top: 10px; /* Adjust top position */
            left: 50px; /* Adjust left position */
            background-size: 200%;
            background-position: 100%;
        }
        .btn:hover {
            background-color: rgb(75, 75, 75); /* Change background color on hover */
            color: white; /* Change text color on hover */
            background-position: 0;
        }
    </style>
    <link rel="icon" type="image/x-icon" href="/project/favicon.ico">
    <link rel="stylesheet" href="../../master/footer-style.css">
    <link rel="stylesheet" href="/project/master/background.css">
</head>
<body>
    <form class="fade-in" id="success-container">
        <h1>E-mail has been sent</h1>
        <p>Please verify your email within 24 hours.</p>
        <a id="login-link" href="../../Login/construct.php" class="btn">Login</a>
    </form>

    <?php include '../../master/footer.php'; ?>

    <script>
        var form = document.getElementById("success-container");
        // Set form opacity to 1
        form.style.opacity = 1;

        // Check if the previous URL contains '?AppRequest=true'
        var referrer = document.referrer;

        if (referrer.includes("?AppRequest=true")) {
            var currentUrl = window.location.href;

            // Check if the current URL already has query parameters
            if (currentUrl.indexOf('?') > -1) {
                // If the URL already has query parameters, append '&AppRequest=true'
                if (!currentUrl.includes("AppRequest=true")) {
                    currentUrl += "&AppRequest=true";
                }
            } else {
                // If the URL doesn't have query parameters, append '?AppRequest=true'
                currentUrl += "?AppRequest=true";
            }

            // Redirect to the updated URL
            window.history.replaceState(null, null, currentUrl);

            // Now, append the same parameter to the Login link
            var loginLink = document.getElementById("login-link");
            var loginHref = loginLink.href;

            // Append '?AppRequest=true' or '&AppRequest=true' to the Login link
            if (loginHref.indexOf('?') > -1) {
                if (!loginHref.includes("AppRequest=true")) {
                    loginLink.href = loginHref + "&AppRequest=true";
                }
            } else {
                loginLink.href = loginHref + "?AppRequest=true";
            }
        }
    </script>

</body>
</html>
