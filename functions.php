<?php
// functions.php

function updateUserData($connection, $userid, $data) {
    $stmt = $connection->prepare("UPDATE user SET name = ?, username = ?, password = ?, confirm_password = ?, address = ?, role = ? WHERE userid = ?");
    $stmt->bind_param("ssssssi", $data['name'], $data['username'], $data['password'], $data['confirm_password'], $data['address'], $data['role'], $userid);
    $stmt->execute();
    $stmt->close();
}

function getUserData($connection, $userid) {
    $stmt = $connection->prepare("SELECT * FROM user WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $userData = $result->fetch_assoc();
    $stmt->close();
    return $userData;
}

function deleteUser($connection, $userid) {
    $stmt = $connection->prepare("DELETE FROM user WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $stmt->close();
}


// functions.php

function getAnnouncements($connection) {
    $query = "SELECT title, content, created_at FROM announcements ORDER BY created_at DESC";
    $result = mysqli_query($connection, $query);
    
    if (!$result) {
        die("Error fetching announcements: " . mysqli_error($connection));
    }
    
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function logActivity($username, $action, $role) {
    global $connection;
    $timestamp = date("Y-m-d H:i:s");
    $query = "INSERT INTO user_activity_log (username, action, timestamp, role) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'ssss', $username, $action, $timestamp, $role);
    mysqli_stmt_execute($stmt);
}


?>
