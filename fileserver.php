<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include("../LoginRegisterAuthentication/connection.php");
include("functions.php");
include("../crud/header.php"); // Adjust if needed for user interface

// Get the user's ID from the session
$userid = $_SESSION['userid']; // Assuming you store the user's ID in the session

// Fetch folders for the logged-in user
$query = "SELECT folder_id, folder_name FROM userfileserverfolders WHERE userid = ? ORDER BY folder_name ASC";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User File Server</title>
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
            object-fit: cover;
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
        User File Server
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
                <button class="btn btn-danger delete-folder" data-folder-id="<?php echo htmlspecialchars($row['folder_id']); ?>">Delete</button>
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
                        <h5 class="modal-title" id="passwordModalLabel">Enter Your Folder Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="password" class="form-control" id="folderPassword" placeholder="Password" required>
                        <input type="hidden" id="folderId" name="folder_id">
                        <input type="hidden" id="actionType" name="action_type">
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
                <form action="useraddfolder.php" method="post">
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
                <form action="useruploadfile.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="uploadFileModalLabel">Upload File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="folderSelect" class="form-label">Select Folder</label>
                            <select class="form-control" id="folderSelect" name="folder_id" required>
                                <?php
                                $folder_query = "SELECT folder_id, folder_name FROM userfileserverfolders";
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
    <script>
        document.querySelectorAll('.folder').forEach(folder => {
            folder.addEventListener('click', function() {
                document.getElementById('folderId').value = this.getAttribute('data-folder-id');
                document.getElementById('actionType').value = 'view';
                new bootstrap.Modal(document.getElementById('passwordModal')).show();
            });
        });

        document.querySelectorAll('.delete-folder').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('folderId').value = this.getAttribute('data-folder-id');
                document.getElementById('actionType').value = 'delete';
                new bootstrap.Modal(document.getElementById('passwordModal')).show();
            });
        });

        document.getElementById('passwordForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const folderId = document.getElementById('folderId').value;
            const folderPassword = document.getElementById('folderPassword').value;
            const actionType = document.getElementById('actionType').value;

            fetch('check_folder_password.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    folder_id: folderId,
                    folder_password: folderPassword,
                    action_type: actionType
                })
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    if (actionType === 'view') {
                        window.location.href = 'userviewfolder.php?folder_id=' + folderId;
                    } else if (actionType === 'delete') {
                        window.location.href = 'userdeletefolder.php?folder_id=' + folderId;
                    }
                } else {
                    alert('Incorrect password.');
                }
            });
        });
    </script>
</body>
</html>