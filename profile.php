<?php
require 'connection.php';

// Start session if not started already
session_start();

// Fetch user_id from the URL parameter
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

// Fetch categories from the database
$categoryQuery = $connection->query("SELECT categoryId, name FROM categories");
$categories = $categoryQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch auctions from the database
$stmt_fetch_auctions = $connection->prepare("SELECT * FROM auctions WHERE user_id = :user_id");
$stmt_fetch_auctions->bindParam(':user_id', $user_id);
$stmt_fetch_auctions->execute();
$auctions = $stmt_fetch_auctions->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Profile</title>
    <link rel="stylesheet" href="carbuy.css">
    <link rel="stylesheet" href="style.css">
</head>
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
<main>
    <h1>Profile</h1>
    <ul class="carList">
        <?php foreach ($auctions as $auction): ?>
            <li>
                <img src="<?php echo $auction['image_path']; ?>" class="auctionItem" alt="<?php echo $auction['title']; ?>">
                <article class="contain">
                    <h2><?php echo $auction['title']; ?></h2>
                    <h3><?php echo $auction['description']; ?></h3>
                    <p class="price">Current bid: £<?php echo $auction['current_bid']; ?></p>
                    <a href="bid.php?auction_id=<?php echo $auction['auction_id']; ?>&user_id=<?php echo $auction['user_id']; ?>" class="more auctionLink">More &gt;&gt;</a>
                </article>
            </li>
        <?php endforeach; ?>
    </ul>
</main>
<footer>
    &copy; Carbuy 2024
</footer>
</body>
</html>
