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

    // NOTE MODAL ELEMENTS
    const noteModal = document.getElementById("noteModal");
    const modalText = document.getElementById("modal-text");
    const modalBody = document.getElementById("modal-body");
    //const closeModalButton = document.querySelector(".close-button");
    const modalTextInput = document.getElementById("modal-text-input");
    const modalBodyInput = document.getElementById("modal-body-input");
    const saveNoteButton = document.getElementById("save-note-button");

    let currentNoteId = null;

    noteModal.style.display = "none";

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
                        location.reload();
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

            const currentTime = new Date();
            const userTimeZoneOffset = new Date().getTimezoneOffset();
            const userLocalTime = new Date(currentTime.getTime() - userTimeZoneOffset * 60000);
            const dateCreated = userLocalTime.toISOString();
            const dateModified = dateCreated;

            const noteData = {
                note_id: generateUUID().toUpperCase(),
                user_id: userId,
                text: noteText,
                dateCreated: dateCreated,
                dateModified: dateModified,
                folderId: folderId !== "NULL" ? folderId : null,
            };

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

    // OPEN NOTE MODAL (with editable fields)
    window.openNoteModal = function(noteElement) {
        const noteId = noteElement.getAttribute("data-id");

        fetch(`get-card.php?note_id=${noteId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate the modal fields with current note data
                modalTextInput.value = data.text; // Set the note text
                modalBodyInput.value = data.body; // Set the note body
                currentNoteId = noteId; // Store the note ID for later use
                noteModal.style.display = "flex"; // Show modal
            } else {
                alert("Error loading note.");
            }
        })
        .catch(error => console.error("Error fetching note:", error));
    };

    // SAVE NOTE CHANGES
    saveNoteButton.addEventListener("click", function () {
        const updatedText = modalTextInput.value.trim();
        const updatedBody = modalBodyInput.value.trim();
        
        if (!updatedText || !updatedBody) {
            alert("Both text and body must be filled out.");
            return;
        }

        const updatedNoteData = {
            id: currentNoteId,
            text: updatedText,
            body: updatedBody,
            dateModified: new Date().toISOString()
            //folderId: null,    // Set the folder ID as needed
        };

        fetch("/project/API/updateNote.php", {
            method: "PUT",  // Use PUT for updating
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(updatedNoteData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.message === "Note updated successfully") {
                alert("Note updated successfully!");
                location.reload();  // Reload page to reflect changes
            } else {
                alert("Error updating note: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error updating note:", error);
            alert("Failed to update note.");
        });
    });

    // CLOSE NOTE MODAL
    window.closeNoteModal = function() {
        noteModal.style.display = "none";  // This hides the modal
    };

    // Attach event listener to close button
    const closeModalButton = document.querySelector(".close-button");
    if (closeModalButton) {
        closeModalButton.addEventListener("click", closeNoteModal);
    }

    // Close modal when clicking outside of it
    window.addEventListener("click", function (event) {
        if (event.target === noteModal) {
            closeNoteModal();
        }
    });
});

