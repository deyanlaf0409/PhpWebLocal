document.getElementById("logout-btn").addEventListener('click', function (event) {
    // Prevent the default behavior of the anchor element
    event.preventDefault();

    // Show confirmation dialog
    document.getElementById("confirmationDialog").style.display = "block";

    event.preventDefault();

    // Ensure that browser's default confirmation dialog is hidden
    return false;
});

document.getElementById("confirmLogout").addEventListener('click', function () {
    // If user clicks "Yes", proceed with logout
    window.location.href = "logout.php";
});

document.getElementById("cancelLogout").addEventListener('click', function () {
    // If user clicks "Cancel", hide confirmation dialog
    document.getElementById("confirmationDialog").style.display = "none";
});