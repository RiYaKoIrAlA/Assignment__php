<?php
session_start();
require 'connection.php';

if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['pw'];
    $uniqueCode = $_POST['unique_code'];

    // Check if the user exists in the 'registers' table
    $checkUser = $connection->prepare('SELECT * FROM users WHERE email = :email AND password = :password');
    $checkUser->execute(array(
        'email' => $email,
        'password' => $password 
    ));
    $user = $checkUser->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Check if the unique code is provided
        if (!empty($uniqueCode)) {
            if ($uniqueCode === 'admin') {
                // Check if the user is not already an admin
                $checkAdminExistence = $connection->prepare('SELECT * FROM admins WHERE email = :email');
                $checkAdminExistence->execute(array('email' => $email));
                $existingAdmin = $checkAdminExistence->fetch(PDO::FETCH_ASSOC);

                if (!$existingAdmin) {
                    // Insert user as admin in 'admins' table
                    $insertAdminRecord = $connection->prepare('INSERT INTO admins (email, password, is_admin) VALUES (:email, :password, 1)');
                    $insertAdminRecord->execute(array(
                        'email' => $email,
                        'password' => $password
                    ));

                    if ($insertAdminRecord) {
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin'] = $user;
                        header("Location: admin_panel.php");
                        exit();
                    } else {
                        echo "Error inserting data into admins table.";
                    }
                } else {
                    echo "<script> window.location.href = 'admin_panel.php';</script>";
                }
            } else {
                echo "<script>alert('Invalid unique code.'); window.location.href = 'login.php';</script>";
            }
        } else {
            // If the unique code is not provided, insert the user as a regular user
            if ($user) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user'] = $user;
                header("Location: user.php");
                exit; 
            } else {
                echo "Error inserting data into logins table.";
            }
        }
    } else {
        // If the user does not exist, show error
        echo "<script>alert('Email and password do not match what was previously registered.'); window.location.href = 'login.php';</script>";
    }
} else {
    echo "Form submission failed.";
}
