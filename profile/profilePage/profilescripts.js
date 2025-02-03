document.addEventListener("DOMContentLoaded", function () {
    // Ensure elements exist before attaching event listeners
    const deleteButton = document.getElementById("delete");
    const confirmDeleteButton = document.getElementById("confirmDelete");
    const cancelDeleteButton = document.getElementById("cancelDelete");
    const confirmationDialog = document.getElementById("confirmationDialog");
    const scrollToTopBtn = document.getElementById("scrollToTopBtn");
    const addDialog = document.getElementById("addDialog");
    const confirmAddButton = document.getElementById("confirmAdd");
    const cancelAddButton = document.getElementById("cancelAdd");
    const createFolderButton = document.getElementById("create-folder");
    const noteTextInput = document.getElementById("note-text");
    const folderSelect = document.getElementById("folderSelect");

    // DELETE CONFIRMATION
    if (deleteButton && confirmationDialog) {
        deleteButton.addEventListener("click", function (event) {
            event.preventDefault();
            confirmationDialog.style.display = "block";
        });

        if (confirmDeleteButton) {
            confirmDeleteButton.addEventListener("click", function () {
                window.location.href = "user-delete.php";
            });
        }

        if (cancelDeleteButton) {
            cancelDeleteButton.addEventListener("click", function () {
                confirmationDialog.style.display = "none";
            });
        }
    }

    // SHOW ADD NOTE DIALOG
    if (scrollToTopBtn && addDialog) {
        scrollToTopBtn.addEventListener("click", function (event) {
            event.preventDefault();
            addDialog.style.display = "block";
        });
    }

    // CLOSE ADD NOTE DIALOG
    if (cancelAddButton && addDialog) {
        cancelAddButton.addEventListener("click", function () {
            addDialog.style.display = "none";
        });
    }

    // CREATE FOLDER
    if (createFolderButton && folderSelect) {
        createFolderButton.addEventListener("click", function (event) {
            event.preventDefault();

            const folderName = prompt("Enter the name of the new folder:");
            if (!folderName) {
                alert("Folder name cannot be empty.");
                return;
            }

            fetch("Folders/create-folder.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ name: folderName }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        const newOption = document.createElement("option");
                        newOption.value = data.folder_id;
                        newOption.textContent = folderName;
                        folderSelect.appendChild(newOption);
                        alert("Folder created successfully!");
                    } else {
                        alert("Error creating folder: " + data.error);
                    }
                })
                .catch((error) => console.error("Error:", error));
        });
    }

    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0,
                v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
    

    // ADD NOTE FUNCTION
    if (confirmAddButton && noteTextInput && folderSelect) {
        confirmAddButton.addEventListener("click", async function () {
            const noteText = noteTextInput.value.trim();
            const folderId = folderSelect.value;

            if (!noteText) {
                alert("Note cannot be empty.");
                return;
            }

            const userId = window.userId; // Fetch from global variable set in PHP
            console.log(userId);

            const currentTime = new Date();

            // Use Intl.DateTimeFormat to get the user's local timezone
            const userTimeZoneOffset = new Date().getTimezoneOffset(); // Difference from UTC in minutes
            console.log("User's Timezone Offset (in minutes):", userTimeZoneOffset);

            // Adjust the current time by the user's timezone offset
            const userLocalTime = new Date(currentTime.getTime() - userTimeZoneOffset * 60000); // Subtract offset in milliseconds

            // Format time in ISO format (in user's local timezone)
            const dateCreated = userLocalTime.toISOString();
            const dateModified = dateCreated; // Assuming the note is created immediately

            const noteData = {
                note_id: generateUUID().toUpperCase(),
                user_id: userId, // Retrieved from global JS variable
                text: noteText,
                dateCreated: dateCreated,
                dateModified: dateModified,
                folderId: folderId !== "NULL" ? folderId : null,
            };

            console.log("Note data to be sent:", JSON.stringify(noteData));

            try {
                const response = await fetch("/project/API/addNote.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(noteData),
                });

                const result = await response.json();
                if (response.ok) {
                    alert("Note added successfully!");
                    location.reload();
                } else {
                    alert("Error: " + result.message);
                }
            } catch (error) {
                console.error("Error:", error);
                alert("Failed to add note.");
            }
        });
    }
});
