<!DOCTYPE html>
<html>
<head>
    <title>Profile Page</title>
    <link rel="icon" type="image/png" href="/project/favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/project/favicons/favicon.svg" />
    <link rel="shortcut icon" href="/project/favicons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/project/favicons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="NoteBlocks" />
    <link rel="manifest" href="/project/favicons/site.webmanifest" />
    <link rel="stylesheet" href="profile-style.css">
    <link rel="stylesheet" href="del-dialog-style.css">
    <link rel="stylesheet" href="/project/master/footer-style.css">
</head>
<body>

    <div class="custom-arrow">
        <a href="../user-page.php" class="back-link"></a>
    </div>

    <form class="fade-in" id="success-container" method="post" action="update-username.php">
        <h1 class="profile-picture">
            <img src="../../res/Default_pfp.png" width="110" height="110">
        </h1>

        <h2 class="user-name">
        <?php
        session_start();

        if (isset($_SESSION['username'])) {
            $username = $_SESSION['username'];
            $user_id = $_SESSION['id'];
            echo '<input type="text" name="new_username" value="' . htmlspecialchars($username) . '" required>';
        } else {
            header("Location: /project/Login/construct.php");
            exit();
        }
        ?>
        </h2>
        <button type="submit" class="save-username-button">Save Username</button>

        <!-- Change Password Button -->
        <div class="change-password-container">
            <a href="change-password.php" class="change-password-button">Change Password</a>
        </div>

        <!-- Developer Token Button -->
        <div class="developer-token-container">
            <a href="Developer/token.php" class="developer-token-button">Developer Token</a>
        </div>
        
        <div class="delete-container">
            <button class="delete-button" id="delete">Delete Account</button>
        </div>

        <h3 style="text-align: left;">My Notes:</h3>
        <div class="notes">
            <?php
            include '../../conn_db.php';

            try {
                $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);

                $query = $db->prepare('SELECT text, date_created, date_modified FROM data WHERE user_id = :user_id ORDER BY date_modified DESC');
                $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $query->execute();

                $notes = $query->fetchAll(PDO::FETCH_ASSOC);

                if ($notes) {
                    foreach ($notes as $note) {
                        $formattedDateCreated = (new DateTime($note['date_created']))->format('d/m/Y H:i');
                        $formattedDateModified = (new DateTime($note['date_modified']))->format('d/m/Y H:i');
                        echo "<div class='note'>";
                        echo "<p>" . htmlspecialchars($note['text']) . "</p>";
                        echo "<small>Created on: " . htmlspecialchars($formattedDateCreated) . "</small>";
                        echo "<small>Last modified on: " . htmlspecialchars($formattedDateModified) . "</small>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No notes found.</p>";
                }
            } catch (PDOException $e) {
                echo "<p>Error fetching notes: " . $e->getMessage() . "</p>";
            }
            ?>
        </div>

    </form>

    <?php include '../../master/footer.php'; ?>
    <?php include 'delete-dialog.html'; ?>

    <script>
        var form = document.getElementById("success-container");
        form.style.opacity = 1;
    </script>

    <script>
        document.getElementById("success-container").addEventListener("submit", function(event) {
            const currentUsername = "<?php echo htmlspecialchars($username); ?>";
            const newUsername = document.querySelector('input[name="new_username"]').value;

            if (currentUsername === newUsername) {
                event.preventDefault(); // Prevent form submission
                //alert("The new username is the same as the current username. Please choose a different one.");
            }
        });
    </script>


    <script src="profilescripts.js"></script>

</body>
</html>
