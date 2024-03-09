<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
    <?php
    require 'connection.php';
    ?>
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("pw");

            if (passwordInput.type === "password") {
                passwordInput.type = "text"; 
            } else {
                passwordInput.type = "password";
            }
        }
    </script>
</head>
<body>
<div class="outbox">
    <h3 class="title">Login</h3>
    <div class="box">
        <form action="log_data.php" method="post">
            <div class="word">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="gap" required>
            </div>
            <div class="word">
                <label for="pw">Password</label>
                <input type="password" id="pw" class="gap" name="pw">
                <input type="checkbox" onclick="togglePasswordVisibility()"><span class="sh">Show Password</span>
            </div>
            <!-- Add input field for the unique code -->
            <div class="word">
                <label for="unique_code">Unique Code (FOR ADMIN ONLY!)</label>
                <input type="password" id="unique_code" class="gap" name="unique_code">
            </div>
            <button type="submit" class="button" value="login" name="submit">Login</button>
            <p>Don't have an account? <a href="register.php">Register here!!</a></p>
        </form>
    </div>
</div>
    
</body>
</html>
