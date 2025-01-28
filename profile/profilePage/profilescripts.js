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





document.getElementById('create-folder').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default button behavior

    // Show a prompt for the folder name
    const folderName = prompt("Enter the name of the new folder:");
    if (!folderName) {
        alert("Folder name cannot be empty.");
        return;
    }

    // Send the folder name to the server
    fetch('Folders/create-folder.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ name: folderName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the dropdown with the new folder
            const folderSelect = document.getElementById('folder-select');
            const newOption = document.createElement('option');
            newOption.value = data.folder_id;
            newOption.textContent = folderName;
            folderSelect.appendChild(newOption);

            alert('Folder created successfully!');
        } else {
            alert('Error creating folder: ' + data.error);
        }
    })
    .catch(error => console.error('Error:', error));
});

