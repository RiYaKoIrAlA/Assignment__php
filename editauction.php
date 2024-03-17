<?php
session_start();
require 'connection.php';

// Initialize $auction to avoid warnings
$auction = [];

// Check if the user is logged in
if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    // Redirect the user to the login page or display an error message
    echo "Error: User not logged in.";
    exit;
}

// Fetch user information from the session
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$user_id = isset($user['user_id']) ? $user['user_id'] : null;

// Fetch categories from the database
$categoryQuery = $connection->query("SELECT categoryId, name FROM categories");
$categories = $categoryQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch the auction to be edited
if (isset($_POST['edit'])) {
    $auction_id = $_POST['edit']; // Retrieve auction_id from the form
    $stmt_fetch_auction = $connection->prepare("SELECT * FROM auctions WHERE auction_id = :auction_id AND user_id = :user_id");
    $stmt_fetch_auction->bindParam(':auction_id', $auction_id);
    $stmt_fetch_auction->bindParam(':user_id', $user_id);
    $stmt_fetch_auction->execute();
    $auction = $stmt_fetch_auction->fetch(PDO::FETCH_ASSOC);
    if (!$auction) {
        echo "Error: Auction not found or you don't have permission to edit it.";
        exit;
    }
}

// Handle form submission for editing auction
if (isset($_POST['edit_submit'])) {
    // Retrieve form data
    $auction_id = $_POST['auction_id']; // Retrieve auction_id from the form
    $title = $_POST['title'];
    $description = $_POST['description'];
    $end_date = $_POST['end_date'];
    $current_bid = isset($_POST['current_bid']) ? $_POST['current_bid'] : 0;
    $categoryId = isset($_POST['categoryId']) ? $_POST['categoryId'] : null;

// Initialize image paths
$existing_image_path = isset($_POST['image_path']) ? $_POST['image_path'] : '';
$new_image_path = '';

    // Handle image upload only if a file has been selected
    if ($_FILES['uploadImg']['name'] != '') {
        // Handle image upload
        $uploadDir = 'banners/';
        $uploadFile = $uploadDir . basename($_FILES['uploadImg']['name']);
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        // File upload handling
        if ($_FILES["uploadImg"]["error"] === 4) {
            echo "Please upload an image.";
            exit;
        } else {
            $fileName = $_FILES["uploadImg"]["name"];
            $fileSize = $_FILES["uploadImg"]["size"];
            $tmpName = $_FILES["uploadImg"]["tmp_name"];

            $validImageExtension = ['jpg', 'jpeg', 'png'];
            $imageExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($imageExtension, $validImageExtension)) {
                echo "Invalid image format. Please upload a JPG, JPEG, or PNG file.";
                exit;
            } elseif ($fileSize > 10000000) {
                echo "Image file size is too large. Please upload an image smaller than 10MB.";
                exit;
            } else {
                $newImageName = uniqid() . '.' . $imageExtension;
                $target_dir = "banners/";
                $target_file = $target_dir . $newImageName;

                if (move_uploaded_file($tmpName, $target_file)) {
                    // Set the new image path
                    $new_image_path = $target_file;
                } else {
                    echo "Error uploading file.";
                    exit;
                }
            }
        }
    } else {
        // If no image is uploaded, retain the existing image path
        $new_image_path = $existing_image_path;
    }

    // Convert end_date to proper format
    $end_date_formatted = date('Y-m-d H:i:s', strtotime($end_date));

    // Update the auction in the database
    $stmt_update_auction = $connection->prepare("UPDATE auctions SET title = :title, description = :description, end_date = :end_date, current_bid = :current_bid, categoryId = :categoryId, image_path = IF(:new_image_path != '', :new_image_path, image_path) WHERE auction_id = :auction_id AND user_id = :user_id");
    $stmt_update_auction->bindParam(':title', $title);
    $stmt_update_auction->bindParam(':description', $description);
    $stmt_update_auction->bindParam(':end_date', $end_date_formatted);
    $stmt_update_auction->bindParam(':current_bid', $current_bid);
    $stmt_update_auction->bindParam(':categoryId', $categoryId);
    $stmt_update_auction->bindParam(':auction_id', $auction_id);
    $stmt_update_auction->bindParam(':user_id', $user_id);
    $stmt_update_auction->bindParam(':new_image_path', $new_image_path);

    if ($stmt_update_auction->execute()) {
        echo "<script> window.location.href = 'product.php';</script>";
    } else {
        echo "Error updating auction.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Auction - Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css">
    <link rel="stylesheet" href="style.css">
    <script>
function previewImage(input) {
    var preview = document.getElementById('imagePreview');
    var imagePath = '<?php echo isset($auction['image_path']) ? $auction['image_path'] : ''; ?>';
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        // Display the current image path if no new file is chosen
        preview.src = imagePath;
    }
}
</script>

</head>
<body>
<header>
    <!-- Header content -->
</header>
<nav>
    <!-- Navigation content -->
</nav>

<div style="display: flex; margin: 20px;">
    <img id="imagePreview" src="<?php echo isset($auction['image_path']) ? $auction['image_path'] : ''; ?>" alt="Preview" style="max-width: 500px; max-height: 200px;">
    <div class="box" style="margin-left:40px;">
        <form action="" method="post" style="display: grid" enctype="multipart/form-data">
            <input type="hidden" name="auction_id" value="<?php echo isset($auction['auction_id']) ? $auction['auction_id'] : ''; ?>"> <!-- Hidden input field for auction_id -->
            <label for="title">Title</label>
            <input type="text" name="title" class="gap" value="<?php echo isset($auction['title']) ? $auction['title'] : ''; ?>">
            <br>
            <label for="description">Description</label>
            <input type="textarea" class="gap" name="description" value="<?php echo isset($auction['description']) ? $auction['description'] : ''; ?>">
            <br>
            <label for="category">Category</label>
            <select name="categoryId">
                <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo $category['categoryId']; ?>" <?php echo (isset($auction['categoryId']) && $auction['categoryId'] == $category['categoryId']) ? 'selected' : ''; ?>><?php echo $category['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="end_date">Auction end date</label>
            <input type="datetime-local" class="gap" name="end_date" value="<?php echo isset($auction['end_date']) ? date('Y-m-d\TH:i', strtotime($auction['end_date'])) : ''; ?>">
            <br>
            <label for="current_bid">Current Bid Amount</label>
            <input type="number" class="gap" name="current_bid" value="<?php echo isset($auction['current_bid']) ? $auction['current_bid'] : ''; ?>">
            <br>
            <label for="uploadImg"></label>
            <input type="file" accept="image/jpeg, image/png, image/jpg" name="uploadImg" id="uploadImg" onchange="previewImage(this);">
            <br>
            <button type="submit" name="edit_submit">Update Auction</button>
        </form>
    </div>
</div>
</body>
</html>
