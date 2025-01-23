var form = document.getElementById("success-container");
// Set form opacity to 1
form.style.opacity = 1;

function submitForm(event) {
    event.preventDefault(); // Prevent the default form submission

    var emailInput = document.getElementById("email");
    var email = emailInput.value.trim(); // Trim leading and trailing spaces

    // Update the displayed email input value
    emailInput.value = email;

    // Send the email to the PHP file using AJAX
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Handle the response here
            var response = xhr.responseText;

            console.log("Server response: ", response);

            // Do something with the 'exists' variable
            if (response.trim() === "true") {
                console.log("Email exists");
                console.log(email);
                sendPassword();
            } else {
                alert("Email is not registered or does not exist");
                console.log("Email does not exist");
            }
        }
    };

    xhr.open("POST", "check_email.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("email=" + encodeURIComponent(email));
}



function sendPassword() {

    var emailInput = document.getElementById("email");
    var email = emailInput.value.trim();

    var xhrPassword = new XMLHttpRequest();
    xhrPassword.onreadystatechange = function() {
        if (xhrPassword.readyState == 4 && xhrPassword.status == 200) {
            // Handle the response here
            var responsePassword = xhrPassword.responseText;
            console.log("Server response: ", responsePassword);
            if(responsePassword === 'success'){
                window.location.href = "success_mail_page.php";
            }
        }
    };

    xhrPassword.open("POST", "testmail.php", true);
    xhrPassword.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhrPassword.send("email=" + encodeURIComponent(email));
}