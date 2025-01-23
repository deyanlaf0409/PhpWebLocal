function checkRegister(event) {
    event.preventDefault();
    var username = document.getElementById("username").value.trim();
    var email = document.getElementById("email").value.trim();
    var password = document.getElementById("password").value.trim();
    var confirmPassword = document.getElementById("confirm-password").value;
    var checkbox = document.getElementById("agree");

    var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!username || !email || !password || !confirmPassword) {
        alert("Please fill in all the fields.");
        return false;
    }

    if (!document.getElementById("email").checkValidity() || !emailRegex.test(email)) {
        console.log(email);
        alert("Please enter a valid email address.");
        return false;
    }

    if (password.length < 8) {
        alert("Password must be at least 8 characters long.");
        return false;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match.");
        return false;
    }

    if (!checkbox.checked) {
        alert("Please agree to the terms and conditions.");
        return false;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "db_conn_reg.php", true);
    
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = xhr.responseText;

                console.log("Server Response:", response);

                if (response.trim() === "success") {
                    // Send verification email after successful registration
                    sendVerificationEmail(email);
                } else if (response.trim() === "user_exists") {
                    alert("Email is already registered. Please use a different email.");
                } else if(response.trim() === "failure"){
                    alert("Registration failed. Please try again later.");
                }
            } else {
                alert("Error during registration. Please try again later.");
            }
        }
    };
    
    // Construct the data as a URL-encoded string
    var data = "username=" + encodeURIComponent(username) +
                "&email=" + encodeURIComponent(email) +
                "&password=" + encodeURIComponent(password);

    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send(data);
}

// Function to send verification email
function sendVerificationEmail(email) {
    var xhrEmail = new XMLHttpRequest();
    xhrEmail.open("POST", "Confirmation/send_verification.php", true);

    xhrEmail.onreadystatechange = function () {
        if (xhrEmail.readyState === XMLHttpRequest.DONE) {
            if (xhrEmail.status === 200) {
                var response = xhrEmail.responseText;
                console.log("Response from PHP script:", response);
                if (response.trim() === "success") {
                    console.log("Verification email sent successfully.");
                    // Redirect to verification page after sending email successfully
                    window.location.href = "Confirmation/verify-page.php";
                } else {
                    console.error("Error sending verification email:", response);
                    // Handle the error case here, if needed
                    // For example, display an error message to the user
                    alert("Failed to send verification email. Please try again later.");
                }
            } else {
                console.error("Error sending verification email. Status code:", xhrEmail.status);
                // Handle the error case here, if needed
                // For example, display an error message to the user
                alert("Failed to send verification email. Please try again later.");
            }
        }
    };

    // Construct the data as a URL-encoded string
    var emailData = "email=" + encodeURIComponent(email);

    xhrEmail.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhrEmail.send(emailData);
}



  
  
  
  
  var registerButton = document.getElementById("register-button");
  var regForm = document.getElementById("reg-form");
  // Set form opacity to 1
  regForm.style.opacity = 1;

  var xPos = registerButton.offsetLeft; // Get the initial left position of the button
  var isLeft = true;
  
  registerButton.onmouseover = function(e) {
    var username = document.getElementById("username").value;
    var email = document.getElementById("email");
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm-password").value;
    var checkbox = document.getElementById("agree");
  
    if (!username || !email.value || !email.checkValidity() || !password || !confirmPassword || !checkbox.checked || password.length < 8) {
      if (isLeft) {
        xPos = 260;
      } else {
        xPos = 80;
      }
      registerButton.style.left = xPos + "px";
      isLeft = !isLeft;
    }
  };