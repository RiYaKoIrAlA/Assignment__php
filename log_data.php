<?php
session_start();
require 'connection.php';

if(isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['pw'];
    $uniqueCode = $_POST['unique_code'];

    // Check if the unique code is provided
    if (!empty($uniqueCode)) {
        if ($uniqueCode === 'admin') {
            // Insert user as admin
            // (Assuming 'register' table contains user details)
            $checkRec = $connection->prepare('SELECT * FROM register WHERE email = :email AND password = :password');
            $checkRec->execute(array(
                'email' => $email,
                'password' => $password 
            ));
            $result = $checkRec->fetch(PDO::FETCH_ASSOC);

            if($result) {
                // Insert user as admin in 'admins' table
                $insertAdminRecord = $connection->prepare('INSERT INTO admins (email, password, is_admin) VALUES (:email, :password, 1)');
                $insertAdminRecord->execute(array(
                    'email' => $email,
                    'password' => $password
                ));

                if ($insertAdminRecord) {
                    $_SESSION['admin_logged_in'] = true;
                    header("Location: admin_panel.php");
                    exit();
                } else {
                    echo "Error inserting data into admins table.";
                }
            } else {
                echo "<script>alert('Email and password do not match what was previously registered.'); window.location.href = 'login.php';</script>";
            }
        } else {
            // Insert user as regular user
            // (Assuming 'register' table contains user details)
            $checkRec = $connection->prepare('SELECT * FROM register WHERE email = :email AND password = :password');
            $checkRec->execute(array(
                'email' => $email,
                'password' => $password 
            ));
            $result = $checkRec->fetch(PDO::FETCH_ASSOC);

            if($result) {
                $insertRecord = $connection->prepare('INSERT INTO login (email, password) VALUES (:email, :password)');
                $insertRecord->execute(array(
                    'email' => $email,
                    'password' => $password
                ));

                if($insertRecord) {
                    $_SESSION['user'] = array(
                        'username' => $result['username'],
                        'email' => $result['email']
                    );
                    header("Location: user.php");
                    exit; 
                } else {
                    echo "Error inserting data into login table.";
                }
            } else {
                echo "<script>alert('Email and password do not match what was previously registered.'); window.location.href = 'login.php';</script>";
            }
        }
    } else {
        // Handle form submission for regular user without unique code
        // Insert user as regular user
        // (Assuming 'register' table contains user details)
        $checkRec = $connection->prepare('SELECT * FROM register WHERE email = :email AND password = :password');
        $checkRec->execute(array(
            'email' => $email,
            'password' => $password 
        ));
        $result = $checkRec->fetch(PDO::FETCH_ASSOC);

        if($result) {
            $insertRecord = $connection->prepare('INSERT INTO login (email, password) VALUES (:email, :password)');
            $insertRecord->execute(array(
                'email' => $email,
                'password' => $password
            ));

            if($insertRecord) {
                $_SESSION['user'] = array(
                    'username' => $result['username'],
                    'email' => $result['email']
                );
                header("Location: user.php");
                exit; 
            } else {
                echo "Error inserting data into login table.";
            }
        } else {
            echo "<script>alert('Email and password do not match what was previously registered.'); window.location.href = 'login.php';</script>";
        }
    }
} else {
    echo "Form submission failed.";
}
?>
