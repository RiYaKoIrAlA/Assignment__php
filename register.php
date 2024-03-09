<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function validatePassword() {
            var password = document.getElementById("pw").value;
            var confirmPassword = document.getElementById("confirm_pw").value;
            var passwordWarning = document.getElementById("password_warning");

            if (password !== confirmPassword) {
                passwordWarning.innerHTML = "Passwords do not match.";
                return false;
            } else {
                passwordWarning.innerHTML = ""; // Clear any previous warning
                return true;
            }
        }

        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("pw");
            var confirmPasswordInput = document.getElementById("confirm_pw");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                confirmPasswordInput.type = "text";
            } else {
                passwordInput.type = "password";
                confirmPasswordInput.type = "password";
            }
        }

        function redirectToLogin() {
            var passwordsMatch = validatePassword();
            if (passwordsMatch) {
                window.location.href = "login.php";
            }
            return passwordsMatch;
        }

    </script>
</head>
<body class="bg">
<div class="outbox reg">
    <h3 class="title">Registration</h3>
    <div class="box">
        <form action="input_reg_data.php" method="post" onsubmit="return redirectToLogin()" >
            <div class="word">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" class="gap" required>
            </div>
            <div class="word">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="gap" required>
            </div>
            <div class="word">
                <label for="pw">Password</label>
                <input type="password" id="pw" class="gap" name="pw" required>
            </div>
            <div class="word">
                <label for="confirm_pw">Confirm Password</label>
                <input type="password" id="confirm_pw" class="gap" name="confirm_pw" required>
                <span id="password_warning" style="color: red;" ></span>
            </div>
            <div class="word">
                <input type="checkbox" onclick="togglePasswordVisibility()">
                <label for="show_password">Show Password</label>
            </div>
            <button type="submit" class="button" name="submit">Register</button>
            <p class="">Already have an account? <a href="login.php">Login here!!</a></p>
        </form>
    </div>
</div>
</body>
</html>
