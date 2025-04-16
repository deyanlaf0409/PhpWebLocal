var myButton = document.getElementById("evil-button");
var loginForm = document.getElementById("login-form");
// Set form opacity to 1
loginForm.style.opacity = 1;

var xPos = myButton.offsetLeft;
var isLeft = true;

myButton.onmouseover = function (e) {
  var email = document.getElementById("email");
  var password = document.getElementById("password").value;

  if (!email.value || !password || !email.checkValidity() || password.length < 8) {
    if (isLeft) {
      xPos = 260;
    } else {
      xPos = 80;
    }
    myButton.style.left = xPos + "px";
    isLeft = !isLeft;
    
  }
};



function checkLogin(event) {
  event.preventDefault();

  var email = document.getElementById("email").value.trim();
  var password = document.getElementById("password").value.trim();



  var emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

  if (!email || !password) {
    alert("Please enter both e-mail and password.");
    return false;
  }

  if (!document.getElementById("email").checkValidity() || !emailRegex.test(email)) {
    console.log(email);
    alert("Please enter a valid email address.");
    return false;
  }

  if (password.length < 8) {
    alert("Invalid password.");
    return false;
  }
/*
  if (!captchaResponse) {
    alert("Please complete the CAPTCHA.");
    return false;
  }
    */

  var urlParams = new URLSearchParams(window.location.search);
  var appRequest = urlParams.get("AppRequest") === "true";

  // Create a new FormData object to send the form data
  var formData = new FormData(loginForm);
  formData.append("email", email);
  formData.append("password", password);
  //formData.append('g-recaptcha-response', captchaResponse);

  if (appRequest) {
    formData.append("AppRequest", "true");
  }


  // Log the FormData for debugging
  for (let pair of formData.entries()) {
    console.log(pair[0] + ': ' + pair[1]);
  }

  // Helper function to check if the response is valid JSON
  function isJson(str) {
    try {
      JSON.parse(str);
      return true;
    } catch (e) {

      return false;
    }
  }

  // Create a new XMLHttpRequest object
  var xhr = new XMLHttpRequest();

  // Configure it to send a POST request to your server-side script
  xhr.open("POST", "db_conn.php", true);

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        var response = xhr.responseText;
        console.log("Server Response:", response);
        
        if (isJson(response.trim())) {
          const data = JSON.parse(response.trim());
          var username = data.username;
          var userId = data.user_id;
          var notesData = data.notes;
          var folders = data.folders;
          var notesText = 'Your Notes:\n';

          notesData.forEach(function(note) {

            notesText += 'Note ID: ' + note.id + '\n';
            notesText += 'Text: ' + note.text + '\n';
            notesText += 'Note body: ' + note.body + '\n';
            notesText += 'Note image: ' + note.media + '\n';
            //notesText += 'Date Created: ' + note.dateCreated + '\n';
            //notesText += 'Date Modified: ' + note.dateModified + '\n';
            notesText += 'highlighted: ' + note.highlighted + '\n\n';
            notesText += 'folderId: ' + note.folderId + '\n\n';
            notesText += 'shared: ' + note.shared + '\n\n';

          });
          alert('Welcome,' + username + userId + notesText);
          const notesJSON = JSON.stringify(notesData);
          const foldersJSON = JSON.stringify(folders);

          const utf8Bytesnotes = new TextEncoder().encode(notesJSON);
          const utf8Bytesfolders = new TextEncoder().encode(foldersJSON); // Encode as UTF-8 bytes
          const notesBase64 = btoa(String.fromCharCode(...utf8Bytesnotes));
          const foldersBase64 = btoa(String.fromCharCode(...utf8Bytesfolders));

          const deepLink = `latenightnotes://auth?status=success&username=${encodeURIComponent(username)}&user_id=${encodeURIComponent(userId)}&notes=${encodeURIComponent(notesBase64)}&folders=${encodeURIComponent(foldersBase64)}`;
          window.location.href = deepLink;
        }else if (response.trim() === "success") {
          window.location.href = "/project/profile/user-page.php";
        } else if (response.trim() === "unverified") {
          alert("Please verify your email before logging in.");
        } else {
          alert("Invalid email or password.");
        }
      } else {
        console.error("Error:", xhr.status, xhr.statusText);
      }
    }
  };

  // Send the form data to the server
  xhr.send(formData);
}









