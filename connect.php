<!DOCTYPE html>
<html>
<head>
    <title>Portal</title>
    <style>
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

        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Set the body to fill the viewport height */
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        .container {
            margin: 200px auto;
            text-align: center;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 15px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            position: relative;
        }
        
        .login {
            background: blue; 
            color: white;
            padding: 8px 16px;
            margin-right: 10px;
            text-decoration: none;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            top: 10px; /* Adjust top position */
            left: 50px; /* Adjust left position */
            transition: background-color 0.3s, color 0.3s; /* Smooth transition effect */
            /*background-image: linear-gradient(45deg, transparent 50%, rgba(255, 255, 255, 0.4) 50%);*/
            background-size: 200%;
            background-position: 100%;
        }
        .login:hover {
            background-color: rgb(15, 122, 255);
            color: white; 
            background-position: 0;
        }

        .register {
            background: rgb(254, 220, 0); 
            color: black;
            padding: 8px 16px;
            margin-right: 10px;
            text-decoration: none;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            top: 10px; /* Adjust top position */
            left: 50px; /* Adjust left position */
            transition: background-color 0.3s, color 0.3s; /* Smooth transition effect */
            /*background-image: linear-gradient(45deg, transparent 50%, rgba(255, 255, 255, 0.4) 50%);*/
            background-size: 200%;
            background-position: 100%;
        }
        .register:hover {
            background-color: rgb(254, 247, 88); 
            color: black; 
            background-position: 0;
        }
    </style>
    <link rel="icon" type="image/png" href="/project/favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/project/favicons/favicon.svg" />
    <link rel="shortcut icon" href="/project/favicons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/project/favicons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="NoteBlocks" />
    <link rel="manifest" href="/project/favicons/site.webmanifest" />
    <link rel="stylesheet" href="master/footer-style.css">
</head>
<body>

    <div class="fade-in container">
        <h1>Welcome to our Website</h1>
        <p>Please log in or register to continue.</p>
        <a href="Login/construct.php" class="login">Login</a>
        <a href="Register/register-construct.php" class="register">Register</a>
    </div>

    <?php include 'master/footer.php'; ?>

</body>
</html>

