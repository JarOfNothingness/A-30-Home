<?php
session_start(); // Start the session at the very top

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Send an Email</h2>
    <form action="send.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="email">To:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="subject">Subject:</label>
            <input type="text" name="subject" id="subject" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="message">Message:</label>
            <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="attachment">Attachment:</label>
            <input type="file" name="attachment" id="attachment" class="form-control-file">
        </div>
        <button type="submit" name="send" class="btn btn-primary">Send Email</button>
        <a href="r.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
