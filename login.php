<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
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
            <form action="input_data.php" >
                <div class="word">
                     <label for="username">Username</label>
                     <input type="text" name="name" id="username" class="gap">

                 </div>
                 <div class="word">
                     <label for="pw">Password</label>
                     <input type="password" id="pw" class="gap">
                     <input type="checkbox" onclick="togglePasswordVisibility()"><span class="sh">Show Password</span>
                 </div>
                 <button type="submit" class="button">Login</button>
                 
                 <p>Don't have account?   <a href="regeister.php">Register here!!</a></p>
             </form>
        </div>
</div>
    
</body>
</html>