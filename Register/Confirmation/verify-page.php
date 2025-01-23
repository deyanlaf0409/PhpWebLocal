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
            background: rgb(254, 220, 0); /* Initial background color */
            color: black;
            padding: 8px 16px;
            margin-right: 2px;
            text-decoration: none;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            top: 10px; /* Adjust top position */
            left: 50px; /* Adjust left position */
            transition: background-color 0.3s, color 0.3s; /* Smooth transition effect */
            /*background-image: linear-gradient(45deg, transparent 50%, rgba(255, 255, 255, 0.4) 50%);*/
            background-size: 200%;
            background-position: 100%;
        }
        .btn:hover {
            background-color: rgb(254, 247, 88); /* Change background color on hover */
            color: black; /* Change text color on hover */
            background-position: 0;
        }
    </style>
    <link rel="icon" type="image/x-icon" href="/project/favicon.ico">
    <link rel="stylesheet" href="../../master/footer-style.css">
</head>
<body>
    <form class="fade-in" id="success-container">
        <h1>E-mail has been send</h1>
        <p>Please verify your email within 24 hours.</p>
        <a href="../../Login/construct.php" class="btn">Login</a>
    </form>

    <?php include '../../master/footer.php'; ?>

    <script>
        var form = document.getElementById("success-container");
        // Set form opacity to 1
        form.style.opacity = 1;
    </script>

</body>
</html>