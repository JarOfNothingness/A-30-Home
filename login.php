<?php
include("../LoginRegisterAuthentication/connection.php");

$error_msg = ""; // Initialize error message variable


if (isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $remember_me = isset($_POST["remember_me"]);

    // Check if username and password are not empty
    if (!empty($username) && !empty($password)) {
        $sql = "SELECT * FROM user WHERE username=? AND password=?";
        $statement = mysqli_stmt_init($connection);
        if (mysqli_stmt_prepare($statement, $sql)) {
            mysqli_stmt_bind_param($statement, "ss", $username, $password);
            mysqli_stmt_execute($statement);
            $resultdata = mysqli_stmt_get_result($statement);
            if ($row = mysqli_fetch_assoc($resultdata)) {
                session_start();
                $_SESSION['user_id'] = $row['user_id']; // Set the session variable with the fetched user_id
                $_SESSION['userid'] = $row['userid'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role']; // Set the role session variable

                // Set cookies if "Remember Me" is checked
                if ($remember_me) {
                    setcookie('username', $username, time() + (86400 * 30), "/"); // 30 days
                    setcookie('password', $password, time() + (86400 * 30), "/"); // 30 days
                } else {
                    // Clear cookies if "Remember Me" is not checked
                    if (isset($_COOKIE['username'])) {
                        setcookie('username', '', time() - 3600, "/"); // Delete cookie
                    }
                    if (isset($_COOKIE['password'])) {
                        setcookie('password', '', time() - 3600, "/"); // Delete cookie
                    }
                }

                if ($row['role'] === 'Admin') {
                    header("location: adminhomepage.php");
                } else {
                    header("location: dashboard.php");
                }
                exit(); // Stop further execution
            } else {
                // Incorrect username or password
                $error_msg = "Invalid username or password.";
            }
        } else {
            // SQL statement preparation failed
            $error_msg = "An error occurred. Please try again later.";
        }
    } else {
        // Username or password is empty
        $error_msg = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e8f0fe; /* Light blue background for an educational theme */
            margin: 0;
            padding: 0;
        }

        .navbar {
            background-color: #002855; /* Dark blue for navbar */
            padding: 10px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .navbar h1 {
            margin: 0;
            font-size: 24px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .navbar a:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 50px);
        }

        .login-container {
            background-color: #ffffff; /* White background for the login form */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            max-width: 400px;
            width: 100%;
            box-sizing: border-box;
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 150px; /* Adjust logo size */
            height: auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #002855; /* Dark blue text for headings */
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background-color: #0056b3; /* Blue submit button */
            color: #fff;
            font-size: 18px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #003f7f; /* Darker blue on hover */
        }

        .error-box {
            display: <?php echo !empty($error_msg) ? 'block' : 'none'; ?>;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 30px;
            border: 2px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
            max-width: 300px;
            text-align: center;
            z-index: 1000;
        }

        .error-box p {
            margin-bottom: 20px;
            color: #333;
        }

        .error-box button {
            background-color: #ccc;
            color: #333;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .error-box button:hover {
            background-color: #999;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Education Portal</h1>
    <div>
        <a href="../LoginRegisterAuthentication/register.php">Create Account</a>
    </div>
</div>

<div class="container">
    <form class="login-container" method="POST">
        <img src="Images/Logo.png.png" alt="Logo" class="logo">
        <h1>Login</h1>

        <!-- Error message display -->
        <?php if (!empty($error_msg)): ?>
            <div class="error-box">
                <p><?php echo $error_msg; ?></p>
                <button onclick="document.querySelector('.error-box').style.display = 'none';">Okay</button>
            </div>
        <?php endif; ?>

        <input type="text" name="username" placeholder="Username" value="<?php echo isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : ''; ?>">
        <input type="password" name="password" placeholder="Password" value="<?php echo isset($_COOKIE['password']) ? htmlspecialchars($_COOKIE['password']) : ''; ?>">
        <div>
            <input type="checkbox" name="remember_me" id="remember_me" <?php echo isset($_COOKIE['username']) ? 'checked' : ''; ?>>
            <label for="remember_me">Remember Me</label>
        </div>
        <input type="submit" name="login" value="Login">
    </form>
</div>
</body>
</html>