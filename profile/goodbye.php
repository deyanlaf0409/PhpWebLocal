<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bye Bye !</title>
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
            background: red; /* Initial background color */
            color: white;
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
            background-color: rgb(255, 97, 97); /* Change background color on hover */
            color: white; /* Change text color on hover */
            background-position: 0;
        }
        @media screen and (max-width: 700px) {
            form {
                width: 80%; /* Adjust width to 90% for smaller screens */
                max-width: 350px; /* You can adjust the max width as per your preference */
            }
        }
    </style>
    <link rel="icon" type="image/x-icon" href="/project/favicon.ico">
    <link rel="stylesheet" href="/project/master/footer-style.css">
</head>
<body>
    <form class="fade-in" id="success-container">
        <h1>Deletion Completed</h1>
        <p>Your account has been removed successfully!</p>
        <a href="/project/connect.php" class="btn">Main page</a>
    </form>

    <?php include '/project/master/footer.php'; ?>

    <script>
        var form = document.getElementById("success-container");
        // Set form opacity to 1
        form.style.opacity = 1;
    </script>

</body>
</html>