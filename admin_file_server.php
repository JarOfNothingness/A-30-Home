<?php
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

include("../LoginRegisterAuthentication/connection.php");
include("functions.php");
include("headeradmin.php");

// Fetch folders
$query = "SELECT folder_id, folder_name FROM fileserver_folders ORDER BY folder_name ASC";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin File Server</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007bff;
            padding: 20px;
            text-align: center;
            color: #fff;
            font-size: 24px;
        }

        .container {
            padding: 20px;
        }

        .folder {
            cursor: pointer;
            transition: transform 0.3s;
            text-align: center;
            margin-bottom: 20px;
        }

        .folder img {
            width: 100px;
            height: 100px;
            object-fit: cover; /* Ensure image scales correctly */
        }

        .folder:hover {
            transform: scale(1.05);
        }

        .action-buttons {
            margin-bottom: 20px;
        }

        .action-buttons button {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <header>
        Admin File Server
    </header>
    <div class="container">
        <h2>Folders</h2>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-sm-4">
                <div class="folder" data-folder-id="<?php echo htmlspecialchars($row['folder_id']); ?>">
                    <img src="folder2_icon.jpg" alt="Folder">
                    <p><?php echo htmlspecialchars($row['folder_name']); ?></p>
                </div>
                <button type="button" class="btn btn-danger btn-sm delete-folder" data-folder-id="<?php echo htmlspecialchars($row['folder_id']); ?>">Delete Folder</button>
            </div>
            <?php } ?>
        </div>

        <div class="action-buttons">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFolderModal">Add Folder</button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadFileModal">Upload File</button>
        </div>
    </div>

    <!-- Password Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="passwordForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="passwordModalLabel">Enter Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="password" class="form-control" id="folderPassword" placeholder="Password" required>
                        <input type="hidden" id="folderId">
                        <input type="hidden" id="actionType">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Folder Modal -->
    <div class="modal fade" id="addFolderModal" tabindex="-1" aria-labelledby="addFolderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_folder.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFolderModalLabel">Add New Folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="folderName" class="form-label">Folder Name</label>
                            <input type="text" class="form-control" id="folderName" name="folder_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="folderPassword" class="form-label">Folder Password</label>
                            <input type="password" class="form-control" id="folderPassword" name="folder_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Folder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload File Modal -->
    <div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="upload_file.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadFileModalLabel">Upload File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="folderSelect" class="form-label">Select Folder</label>
                            <select class="form-control" id="folderSelect" name="folder_id" required>
                                <?php
                                $folder_query = "SELECT folder_id, folder_name FROM fileserver_folders";
                                $folder_result = mysqli_query($connection, $folder_query);
                                while ($folder_row = mysqli_fetch_assoc($folder_result)) {
                                    echo "<option value='" . htmlspecialchars($folder_row['folder_id']) . "'>" . htmlspecialchars($folder_row['folder_name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="fileUpload" class="form-label">Choose File</label>
                            <input type="file" class="form-control" id="fileUpload" name="file_upload" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload File</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Event listener for clicking on a folder
    document.querySelectorAll('.folder').forEach(folder => {
        folder.addEventListener('click', function() {
            const folderId = this.getAttribute('data-folder-id');
            document.getElementById('folderId').value = folderId;
            document.getElementById('actionType').value = 'access';
            new bootstrap.Modal(document.getElementById('passwordModal')).show();
        });
    });

    // Event listener for clicking on delete button
    document.querySelectorAll('.delete-folder').forEach(button => {
        button.addEventListener('click', function() {
            const folderId = this.getAttribute('data-folder-id');
            document.getElementById('folderId').value = folderId;
            document.getElementById('actionType').value = 'delete';
            new bootstrap.Modal(document.getElementById('passwordModal')).show();
        });
    });

    // Submit the password form for both access and delete actions
    document.getElementById('passwordForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const folderId = document.getElementById('folderId').value;
        const password = document.getElementById('folderPassword').value;
        const actionType = document.getElementById('actionType').value;

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "validate_password.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    if (actionType === 'access') {
                        // Redirect to the folder contents page if the password is correct
                        window.location.href = `view_folder.php?folder_id=${folderId}`;
                    } else if (actionType === 'delete') {
                        // Redirect to the delete folder page if the password is correct
                        window.location.href = `deletefolder.php?folder_id=${folderId}`;
                    }
                } else {
                    alert(response.message); // Show error message if password is incorrect
                }
            }
        };
        xhr.send(`folder_id=${folderId}&password=${password}&action_type=${actionType}`);
    });
</script>
</body>
</html>
