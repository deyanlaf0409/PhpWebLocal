document.getElementById("delete").addEventListener('click', function (event) {
    // Prevent the default behavior of the anchor element
    event.preventDefault();

    // Show confirmation dialog
    document.getElementById("confirmationDialog").style.display = "block";

    event.preventDefault();

    // Ensure that browser's default confirmation dialog is hidden
    return false;
});

document.getElementById("confirmDelete").addEventListener('click', function () {
    // If user clicks "Yes", delete database record for the user
    window.location.href = "user-delete.php";
});

document.getElementById("cancelDelete").addEventListener('click', function () {
    // If user clicks "Cancel", hide confirmation dialog
    document.getElementById("confirmationDialog").style.display = "none";
});