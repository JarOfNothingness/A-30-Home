<?php
session_start(); // Start the session at the very top



include('headeradmin.php'); 
include("../LoginRegisterAuthentication/connection.php"); 

$userid = $_SESSION['userid'];

// Debugging output to check the user ID
if (empty($userid)) {
    echo "User ID is not set. Session userid: " . htmlspecialchars($userid);
    exit();
}

// Query to fetch user data for the admin
$query = "SELECT * FROM user WHERE userid = ?";
$stmt = mysqli_prepare($connection, $query);
mysqli_stmt_bind_param($stmt, 'i', $userid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Fetch and display the data
while ($row = mysqli_fetch_assoc($result)) {
    echo "Admin Name: " . htmlspecialchars($row['name']);
}
?>

<br>
<div class="box1">
    <a href="../Home/dashboard.php" class="btn btn-secondary">Back</a>
    <button class="btn btn-primary" data-toggle="modal" data-target="#addAnnouncementModal">ADD ANNOUNCEMENT</button>
    <button class="btn btn-success" data-toggle="modal" data-target="#manageUsersModal">MANAGE USERS</button>
</div>
</br>

<!-- Filter and Search Form -->
<form method="GET" action="" class="mb-3">
    <div class="d-flex flex-wrap justify-content-center align-items-center">
        <!-- Filter Options -->
        <div class="dropdown mb-2 mr-2">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="schoolLevelDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo isset($_GET['school_level']) && $_GET['school_level'] ? $_GET['school_level'] : 'School Level'; ?>
            </button>
            <ul class="dropdown-menu" aria-labelledby="schoolLevelDropdown">
                <li><a class="dropdown-item" href="?school_level=SHS<?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">SHS</a></li>
                <li><a class="dropdown-item" href="?school_level=JHS<?php echo isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>">JHS</a></li>
                <li><a class="dropdown-item" href="?school_level=">All Levels</a></li>
            </ul>
        </div>

        <!-- Other Filters (e.g., School Year, Grade, Section, Gender) -->
        <!-- Similar dropdown filters can be added here -->
        
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by Learners Name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <div class="input-group-append">
                <input type="submit" class="btn btn-primary" value="Search">
            </div>
        </div>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-hover table-bordered table-striped">
        <thead>
            <tr>
                <th>Learners Name</th>
                <th>Section</th>
                <th>Grade</th>
                <th>School Level</th>
                <th>Region</th>
                <th>Division</th>
                <th>School ID</th>
                <th>School Year</th>
                <th>Gender</th>
                <th>Subject</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $school_level = isset($_GET['school_level']) ? $_GET['school_level'] : '';
        $school_year = isset($_GET['school_year']) ? $_GET['school_year'] : '';
        $grade = isset($_GET['grade']) ? $_GET['grade'] : '';
        $section = isset($_GET['section']) ? $_GET['section'] : '';
        $gender = isset($_GET['gender']) ? $_GET['gender'] : '';
        $subject = isset($_GET['subject']) ? $_GET['subject'] : '';

        $query = "SELECT * FROM students WHERE 1=1";

        if ($search) {
            $query .= " AND learners_name LIKE '%$search%'";
        }
        if ($school_level) {
            $query .= " AND school_level = '$school_level'";
        }
        if ($school_year) {
            $query .= " AND school_year = '$school_year'";
        }
        if ($grade) {
            $query .= " AND grade = '$grade'";
        }
        if ($section) {
            $query .= " AND section = '$section'";
        }
        if ($gender) {
            $query .= " AND gender = '$gender'";
        }
        if ($subject) {
            $query .= " AND subject = '$subject'";
        }

        $query .= " ORDER BY id DESC";

        $result = mysqli_query($connection, $query);

        if (!$result) {
            die("Query failed: " . mysqli_error($connection));
        } else {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['learners_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['section']); ?></td>
                    <td><?php echo htmlspecialchars($row['grade']); ?></td>
                    <td><?php echo htmlspecialchars($row['school_level']); ?></td>
                    <td><?php echo htmlspecialchars($row['region']); ?></td>
                    <td><?php echo htmlspecialchars($row['division']); ?></td>
                    <td><?php echo htmlspecialchars($row['school_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['school_year']); ?></td>
                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                    <td><?php echo htmlspecialchars($row['subject']); ?></td>
                    <td>
                        <a href="updateStudent.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="deleteStudent.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>

<!-- Modal for Adding Announcement -->
<div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAnnouncementModalLabel">Add New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="AdminCrud.php">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="submit_announcement" class="btn btn-primary">Add Announcement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Managing Users -->
<div class="modal fade" id="manageUsersModal" tabindex="-1" aria-labelledby="manageUsersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="manageUsersModalLabel">Manage Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($pendingUsersResult)) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <a href="approveUser.php?id=<?php echo $row['userid']; ?>" class="btn btn-success">Approve</a>
                                    <a href="disapproveUser.php?id=<?php echo $row['userid']; ?>" class="btn btn-danger">Disapprove</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
include('footer.php'); 

if (isset($_POST['submit_announcement'])) {
    $title = mysqli_real_escape_string($connection, $_POST['title']);
    $content = mysqli_real_escape_string($connection, $_POST['content']);
    $created_at = date("Y-m-d H:i:s");

    $query = "INSERT INTO announcements (title, content, created_at) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'sss', $title, $content, $created_at);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "<script>alert('Announcement added successfully.'); window.location.href = 'AdminCrud.php';</script>";
    } else {
        echo "<script>alert('Failed to add announcement.'); window.location.href = 'AdminCrud.php';</script>";
    }
}
?>

