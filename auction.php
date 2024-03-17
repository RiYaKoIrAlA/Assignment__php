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

// Fetch auctions from the database
$stmt_fetch_auctions = $connection->prepare("SELECT * FROM auctions WHERE user_id = :user_id");
$stmt_fetch_auctions->bindParam(':user_id', $user_id);
$stmt_fetch_auctions->execute();
$auctions = $stmt_fetch_auctions->fetchAll(PDO::FETCH_ASSOC);

// Handle deletion of auctions
if(isset($_POST['delete'])) {
    $auction_id = $_POST['delete'];
    $stmt_delete_auction = $connection->prepare("DELETE FROM auctions WHERE auction_id = :auction_id AND user_id = :user_id");
    $stmt_delete_auction->bindParam(':auction_id', $auction_id);
    $stmt_delete_auction->bindParam(':user_id', $user_id);
    if($stmt_delete_auction->execute()) {
         // After deletion, update the IDs of the remaining categories
         $updateIdsQuery = $connection->prepare("SET @counter = 0;");
         $updateIdsQuery->execute();
     
         $updateIdsQuery = $connection->prepare("UPDATE auctions SET auction_Id = @counter := (@counter + 1);");
         $updateIdsQuery->execute();
 
         $updateIdsQuery = $connection->prepare("ALTER TABLE auctions AUTO_INCREMENT = 1;");
         $updateIdsQuery->execute();
        // Refresh the page after deletion
        header("Location: auction.php");
        exit;
    } else {
        echo "Error deleting auction.";
    }
}

// Handle editing of auctions
if(isset($_POST['edit'])) {
    $auction_id = $_POST['edit'];
    // Redirect to the editauction.php page with the auction ID as a parameter
    header("Location: editauction.php?edit=" . $auction_id);
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="carbuy.css">
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
                $user = $_SESSION['user'];// Display user's profile information
                echo "{$user['username']}";
            } elseif(isset($_SESSION['admin_logged_in'])) {
				echo "Admin";
            } else {
                echo 'profile';
            }
            ?>
        </form>
    </div>
</header>
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li class="dropdown">
            <a class="categoryLink" href="#">Categories</a>
            <ul class="dropdown-content">
            <?php
foreach ($categories as $category) {
    echo "<li><a class='categoryLink' href='categories.php?categoryId=" . $category['categoryId'] . "'>" . $category['name'] . "</a></li>";
}
?>
            </ul>
        </li>
        <?php
        if(isset($_SESSION['user'])) {
			echo "<li><a href='addauction.php'>Add auction??</a></li>";
			echo "<li><a href='product.php'>Own product</a></li>";
            echo "<li><a href='javascript:void(0)' onclick=\"confirmLogout()\">Logout</a></li>";
        } elseif (isset($_SESSION['admin_logged_in'])) {
			echo "<li><a href='adminCategories.php'>Admin</a></li>";
			echo "<li><a href='javascript:void(0)' onclick=\"confirmLogout()\">Logout</a></li>";
		} else {
            echo "<li><a href='Register.php'>Register</a></li>";
            echo "<li><a href='Login.php'>Login</a></li>";
        }
        ?>
    </ul>
</nav>

<ul class="carList">
    <?php foreach ($auctions as $auction): ?>
        <li>
            <img src="<?php echo $auction['image_path']; ?>" class="auctionItem" alt="<?php echo $auction['title']; ?>">
            <article class="contain">
                <h2><?php echo $auction['title']; ?></h2>
                <h3><?php echo $auction['description']; ?></h3>
                <p class="price">Current bid: £<?php echo $auction['current_bid']; ?></p>
                <form action="" method="post">
                    <input type="hidden" name="delete" value="<?php echo $auction['auction_id']; ?>">
                    <button type="submit">Delete</button>
                </form>
                <!-- Edit button to redirect to editauction.php -->
                <form action="editauction.php" method="post">
                    <input type="hidden" name="edit" value="<?php echo $auction['auction_id']; ?>">
                    <button type="submit">Edit</button>
                </form>
            </article>
        </li>
    <?php endforeach; ?>
</ul>
</body>
</html>
