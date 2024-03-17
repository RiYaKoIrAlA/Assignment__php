<?php
session_start();
require 'connection.php';

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

// Handle form submission
if(isset($_POST['auction_submit'])) {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $end_date = $_POST['end_date'];
    $current_bid = isset($_POST['current_bid']) ? $_POST['current_bid'] : 0;
    $end_date_formatted = date('Y-m-d H:i:s', strtotime($end_date));
    $creation_time = date('Y-m-d H:i:s');
    $categoryId = isset($_POST['categoryId']) ? $_POST['categoryId'] : null;

    // File upload handling
    if($_FILES["uploadImg"]["error"] === 4) {
        echo "";
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
                
                $stmt_insert_auction = $connection->prepare("INSERT INTO auctions (title, description, end_date, current_bid, creation_time, image_path, user_id, categoryId) 
                VALUES (:title, :description, :end_date, :current_bid, :creation_time, :image_path, :user_id, :categoryId)");

                // Bind parameters
                $stmt_insert_auction->bindParam(':title', $title);
                $stmt_insert_auction->bindParam(':description', $description);
                $stmt_insert_auction->bindParam(':end_date', $end_date_formatted);
                $stmt_insert_auction->bindParam(':current_bid', $current_bid);
                $stmt_insert_auction->bindParam(':creation_time', $creation_time);
                $stmt_insert_auction->bindParam(':image_path', $target_file);
                $stmt_insert_auction->bindParam(':user_id', $user_id); 
                $stmt_insert_auction->bindParam(':categoryId', $categoryId); 

                if (empty($current_bid)) {
                    $current_bid = 0;
                }

               if($stmt_insert_auction->execute()) {
    $auction_id = $connection->lastInsertId();
    $_SESSION['auction_id'] = $auction_id;
    echo "<script> window.location.href = 'product.php';</script>";

    $updateIdsQuery = $connection->prepare("SET @counter = 0;");
    $updateIdsQuery->execute();

    $updateIdsQuery = $connection->prepare("UPDATE auctions SET auction_Id = @counter := (@counter + 1);");
    $updateIdsQuery->execute();

    $updateIdsQuery = $connection->prepare("ALTER TABLE auctions AUTO_INCREMENT = 1;");
    $updateIdsQuery->execute();
} else {
    echo "Error inserting auction data.";
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
    <link rel="stylesheet" href="carbuy.css">
    <link rel="stylesheet" href="style.css">
</head>
<script>
    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = 'logout.php';
        }
    }
</script>
<body>
<header>
    <h1>
        <span class="C">C</span>
        <span class="a">a</span>
        <span class="r">r</span>
        <span class="b">b</span>
        <span class="u">u</span>
        <span class="y">y</span>
    </h1>
    <div class="profile">
        <form action="#">
            <input type="text" name="search" placeholder="Search for a car" />
            <input type="submit" name="submit" value="Search" />
            <?php
            if(isset($_SESSION['user'])) {
                $user = $_SESSION['user'];
                echo "{$user['username']}";
            }
            else{
                echo 'profile';
            }
            ?>
        </form>
    </div>
</header>
<nav>
    <ul style="width: 80vw;">
        <li><a href="index.php">Home</a></li>
        <li class="dropdown">
            <a class="categoryLink" href="#">Categories</a>
            <ul class="dropdown-content">
                <?php
                foreach ($categories as $category) {
                    echo "<li><a class='categoryLink' href='#'>" . $category['name'] . "</a></li>";
                }
                ?>
            </ul>
        </li>
        <li><a href="product.php">Own product</a></li>
        <li><a href="javascript:void(0)" onclick="confirmLogout()">Logout</a></li>
    </ul>
</nav>

<div class="box">
    <form action="" method="post" style="display: grid" enctype="multipart/form-data" >
        <div>
            <label for="title">Title</label>
            <input type="text" name="title" class="gap" required>
        </div>
        <div>
            <label for="description">Description</label>
            <input type="text" class="gap" name="description">
        </div>
        <div class="word">
            <label for="category">Category</label>
            <select name="categoryId"> 
                <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo $category['categoryId']; ?>"><?php echo $category['name']; ?></option>
                <?php endforeach; ?>
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
            <?php
    if(isset($_POST['auction_submit']) && empty($_FILES['uploadImg']['name'])) {
        echo "<span style='color:red;'>Please upload an image.</span>";
    }
    ?>
        </div>
        <button type="submit" name="auction_submit">Auction</button>
    </form>
</div>
</body>
</html>
