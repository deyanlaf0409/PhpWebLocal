<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Developer Token</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }

        .back-link {
            top: 100px;
            left: 100px;
            display: inline-block;
            padding: 25px 25px;
            background-color: white;
            text-decoration: none;
            border-radius: 25px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            position: relative;
            transition: transform 0.3s ease;
        }

        .back-link::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 11px;
            width: 25px;
            height: 25px;
            background-image: url('../../../res/arrow-left.png');
            background-size: contain;
            background-repeat: no-repeat;
            transform: translateY(-50%);
        }

        .back-link:hover {
            transform: scale(1.1);
        }

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
            margin: 150px auto;
            text-align: center;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 15px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            position: relative;
            width: 90%;
            max-width: 400px;
        }

        input[type="text"] {
            width: 80%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            box-sizing: border-box;
            text-align: center;
        }

        .generate {
            background: orange;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            margin: 5px;
        }

        .generate:hover {
            background-color: rgb(255, 99, 0);
        }

        button {
            background: #f2f2f2;
            color: black;
            padding: 8px 16px;
            text-decoration: none;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
            margin: 5px;
        }

        button:hover {
            
        }

        @media screen and (max-width: 700px) {
            .back-link {
                top: 20px;
                left: 10px;
            }

            form {
                width: 80%; /* Adjust width to 90% for smaller screens */
                max-width: 350px; /* You can adjust the max width as per your preference */
            }
        }
    </style>
    <link rel="icon" type="image/png" href="/project/favicons/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/project/favicons/favicon.svg" />
    <link rel="shortcut icon" href="/project/favicons/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/project/favicons/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="NoteBlocks" />
    <link rel="manifest" href="/project/favicons/site.webmanifest" />
    <link rel="stylesheet" href="../../../master/footer-style.css">
</head>
<body>
    <div class="custom-arrow">
        <a href="../profile-page.php" class="back-link"></a>
    </div>
    <form class="fade-in">
        <h2>Generate Developer Token</h2>
        <p>Save your token to a secure place!</p>
        <div>
            <input type="text" id="tokenField" readonly placeholder="Your token will appear here">
        </div>
        <div>
            <button type="button" class="generate" id="generateButton">Generate Token</button>
            <button type="button" id="copyButton" disabled>Copy to Clipboard</button>
        </div>
    </form>

    <script>
        document.getElementById('generateButton').addEventListener('click', async () => {
            try {
                const response = await fetch('generate-token.php', { method: 'POST' });
                const result = await response.json();

                if (result.success) {
                    const tokenField = document.getElementById('tokenField');
                    tokenField.value = result.token;
                    document.getElementById('copyButton').disabled = false;
                    alert('Token generated successfully!');
                } else {
                    alert('Error generating token: ' + result.message);
                }
            } catch (error) {
                alert('An error occurred: ' + error.message);
            }
        });

        document.getElementById('copyButton').addEventListener('click', () => {
            const tokenField = document.getElementById('tokenField');
            tokenField.select();
            navigator.clipboard.writeText(tokenField.value)
                .then(() => alert('Token copied to clipboard!'))
                .catch(err => alert('Failed to copy token: ' + err));
        });
    </script>

    <?php include '../../../master/footer.php'; ?>
</body>
</html>

