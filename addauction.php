<?php
session_start();
require 'connection.php';

if(isset($_POST['auction_submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['categoryId'];
    $end_date = $_POST['end_date'];
    $current_bid = $_POST['current_bid']; // Fetch the current bid amount

    // Convert end_date to proper format
    $end_date_formatted = date('Y-m-d H:i:s', strtotime($end_date));

    // File upload handling
    if($_FILES["uploadImg"]["error"] === 4) {
        echo "Please upload an image.";
    } else {
        $fileName = $_FILES["uploadImg"]["name"];
        $fileSize = $_FILES["uploadImg"]["size"];
        $tmpName = $_FILES["uploadImg"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if(!in_array($imageExtension, $validImageExtension)) {
            echo "Invalid image format. Please upload a JPG, JPEG, or PNG file.";
        } elseif($fileSize > 10000000) {
            echo "Image file size is too large. Please upload an image smaller than 10MB.";
        } else {
            $newImageName = uniqid() . '.' . $imageExtension;
            $target_dir = "banners/";
            $target_file = $target_dir . $newImageName;

            if(move_uploaded_file($tmpName, $target_file)) {
                // Prepare the SQL statement
                $stmt = $connection->prepare("INSERT INTO auction (title, description, category, end_date, image_path, current_bid)
                VALUES (:title, :description, :category, :end_date, :image_path, :current_bid)");

                // Bind parameters
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':category', $category);
                $stmt->bindParam(':end_date', $end_date_formatted);
                $stmt->bindParam(':image_path', $target_file);
                $stmt->bindParam(':current_bid', $current_bid);

                // Execute the statement
                if($stmt->execute()) {
                    echo "<script> window.location.href = 'auction.php';</script>";
                } else {
                    echo "Error: " . $stmt->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="box">
    <form action="" method="post" style="display: grid" enctype="multipart/form-data">
        <div>
            <label for="title">Title</label>
            <input type="text" name="title" class="gap">
        </div>
        <div>
            <label for="description">Description</label>
            <input type="text" class="gap" name="description">
        </div>
        <div class="word">
            <label for="category">Category</label>
            <select name="categoryId">
                <option value="Estate">Estate</option>
                <option value="Electric">Electric</option>
                <option value="Coupe">Coupe</option>
                <option value="Saloon">Saloon</option>
                <option value="4x4">4x4</option>
                <option value="Sports">Sports</option>
                <option value="Hybrid">Hybrid</option>
            </select>
        </div>
        <div class="word">
            <label for="end_date">Auction end date</label>
            <input type="date" class="gap" name="end_date">
        </div>
        <div class="word">
            <label for="current_bid">Current Bid Amount</label>
            <input type="number" class="gap" name="current_bid">
        </div>
        <div class="word">
            <input type="file" accept="image/jpeg, image/png, image/jpg" name="uploadImg" id="uploadImg">
        </div>
        <button type="submit" name="auction_submit">Auction</button>
    </form>
</div>
</body>
</html>
