<?php
session_start();
require 'connection.php';

// Fetch categories with associated auctions for the specified categoryId
$category_id = isset($_GET['categoryId']) ? $_GET['categoryId'] : null;

$stmt_categories = $connection->prepare("SELECT DISTINCT c.categoryId, c.name FROM categories c JOIN auctions a ON c.categoryId = a.categoryId WHERE c.categoryId = :categoryId");
$stmt_categories->execute(['categoryId' => $category_id]);
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);

// Fetch auctions for the specified category
$stmt_auctions = $connection->prepare("SELECT * FROM auctions WHERE categoryId = :categoryId ORDER BY creation_time DESC");
$stmt_auctions->execute(['categoryId' => $category_id]);
$auctions = $stmt_auctions->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Categories</title>
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
        <li class="dropdown">
            <a class="categoryLink" href="#">Categories</a>
            <ul class="dropdown-content">
            <?php 
                if (!empty($categories)) { // Check if $categories is not empty
                    foreach ($categories as $category): ?>
                        <li><a class="categoryLink" href="categories.php?categoryId=<?php echo $category['categoryId']; ?>"><?php echo $category['name']; ?></a></li>
                    <?php endforeach;
                } else {
                    echo "<li><a class='categoryLink' href='#'>No categories found</a></li>";
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
<main>
    <h1>Categories</h1>
    <ul class="carList">
    <?php if (!empty($categories)): // Check if $categories is not empty ?>
        <h2><?php echo $categories[0]['name']; ?></h2> <!-- Display category name -->
        <ul class="auctionList">
            <?php foreach ($auctions as $auction): ?>
                <li>
                <img src="<?php echo $auction['image_path']; ?>" class="auctionItem" alt="<?php echo $auction['title']; ?>">
                <article class="contain">
                    <h2><?php echo $auction['title']; ?></h2>
                    <h3><?php echo $auction['description']; ?></h3>
                    <p class="price">Current bid: £<?php echo $auction['current_bid']; ?></p>
<a href="bid.php?auction_id=<?php echo $auction['auction_id']; ?>&user_id=<?php echo $auction['user_id']; ?>" class="more auctionLink">More &gt;&gt;</a>

                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
    <p class="price">No categories found.</p>
<?php endif; ?>
</main>
<footer>
    &copy; Carbuy 2024
</footer>
</body>
</html>
