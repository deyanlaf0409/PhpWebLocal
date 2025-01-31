<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>NoteBlocks</title>
    <link rel="icon" type="image/png" href="/project/favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/project/favicons/favicon.svg" />
    <link rel="shortcut icon" href="/project/favicons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/project/favicons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="NoteBlocks" />
    <link rel="manifest" href="/project/favicons/site.webmanifest" />
    <link rel="stylesheet" href="page-style.css">
    <link rel="stylesheet" href="../master/footer-style.css">
    <link rel="stylesheet" href="../master/user/user-content-style.css">
    <link rel="stylesheet" href="dialog-style.css">
</head>

<body>

    <nav>
        <a class="logout" id="logout-btn">Logout</a>
        <a href="profilePage/profile-page.php" class="profile" id="prof-btn">Profile</a>

        <a href="#Social/friends-page.php" onclick="closeDropdown()" class="social" id="social">Group</a>
    </nav>

    <header class="welcome">
        <?php
        // Start session
        session_start();

        // Check if the username is set in the session
        if (isset($_SESSION['username'])) {
            // Retrieve the username from the session
            $username = $_SESSION['username'];
            // Display the welcome message
            echo "<h1>Welcome, $username !</h1>";
        } else {
            // If username is not set, display default message
            header("Location: /project/Login/construct.php");
        }
        ?>
    </header>

    <div class="main-content">

        <!-- Container for user content -->
        <div>
            <?php include '../master/user/user-content.php'; ?>
        </div>
    </div>

    <button id="scrollToTopBtn" class="scroll-to-top-btn"></button>
    
    <?php include 'dialog-out.html'; ?>
    <?php include '../master/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add 'fade-in' class to elements with class 'login-btn' and 'register-btn'
            document.querySelector('.logout').classList.add('fade-in');
            document.querySelector('.profile').classList.add('fade-in');
        });

        var logBtn = document.getElementById("logout-btn");
        logBtn.style.opacity = 1;
        var regBtn = document.getElementById("prof-btn");
        regBtn.style.opacity = 1;
    </script>

    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();

                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);

                const offsetTop = targetElement.offsetTop;
                const headerHeight = document.querySelector('header').offsetHeight;
                const offset = offsetTop - headerHeight;

                window.scrollTo({
                    top: offset,
                    behavior: 'smooth'
                });
            });
        });
    </script>

    <!--
    <script>
        function closeDropdown() {
            document.querySelector('.dropdown-btn').classList.remove('active');
        }

        document.querySelector('.dropdown-btn').addEventListener('click', function () {
            this.classList.toggle('active');
        });

        // Add event listeners for links inside the dropdown content
        document.querySelectorAll('.dropdown-content a').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                closeDropdown(); // Close the dropdown when a link is clicked
            });
        });
    </script>
    -->
    

    <script src="userpagescripts.js"></script>

</body>

</html>
