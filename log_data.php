<?php
session_start();
require 'connection.php';

if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['pw'];
    $uniqueCode = $_POST['unique_code'];

    // Check if the user provided a unique code
    if (!empty($uniqueCode)) {
        if ($uniqueCode === 'admin') {
            // Check if the user exists in the 'admins' table
            $checkAdminExistence = $connection->prepare('SELECT * FROM admins WHERE email = :email');
            $checkAdminExistence->execute(array('email' => $email));
            $existingAdmin = $checkAdminExistence->fetch(PDO::FETCH_ASSOC);

            if (!$existingAdmin) {
                // Insert user as admin in 'admins' table
                $insertAdminRecord = $connection->prepare('INSERT INTO admins (email, password) VALUES (:email, :password)');
                $insertAdminRecord->execute(array('email' => $email, 'password' => $password));

                if ($insertAdminRecord) {
                    $_SESSION['admin_logged_in'] = true;
                    header("Location: admin_panel.php");
                    exit();
                } else {
                    echo "Error inserting data into admins table.";
                }
            } else {
                header("Location: admin_panel.php");
                exit();
            }
        } else {
            echo "<script>alert('Invalid unique code.'); window.location.href = 'login.php';</script>";
            exit();
        }
    } else {
        // Regular user login
        // Check if the user exists in the 'registers' table
        $checkUserExistence = $connection->prepare('SELECT * FROM registers WHERE email = :email AND password = :password');
        $checkUserExistence->execute(array('email' => $email, 'password' => $password));
        $existingUser = $checkUserExistence->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            // Set session variables for the logged-in user
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user'] = array(
                'username' => $existingUser['username'],
                'email' => $email
            );

            // Redirect to the user.php page
            header("Location: user.php");
            exit();
        } else {
            echo "<script>alert('Email and password do not match what was previously registered.'); window.location.href = 'login.php';</script>";
            exit();
        }
    }
} else {
    echo "Form submission failed.";
    exit();
}
?>
