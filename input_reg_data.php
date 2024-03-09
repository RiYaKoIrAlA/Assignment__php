<?php
session_start();
?>

<?php
require 'connection.php';
?>

<?php
if(isset($_POST['submit'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['pw'];
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $confirmPassword = $_POST['confirm_pw'];

    if($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
        exit; 
    }


    // Prepare and execute the insert statement
    $insertRecord = $connection->prepare('INSERT INTO register(user_id,username, email, password) VALUES (user_id,:username, :email, :password)');
    $insertRecord->execute(array(
        'username' => $username,
        'email' => $email,
        'password' => $password // Insert the retrieved password into the database
    ));

    // Check if insertion was successful
    if($insertRecord) {
        // Registration successful, print JavaScript to redirect to login page
        echo "<script>alert('Registration successful. Redirecting to login page.'); window.location.href = 'login.php';</script>";
        exit; // Make sure to exit after redirection
    } else {
        // Display error message in an alert
        echo "<script>alert('Error registering user.');</script>";
    }
} else {
    // Redirect or display an error message if the form was not submitted
    echo "<script>alert('Form submission failed.');</script>";
}
?>
