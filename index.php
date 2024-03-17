<?php
session_start(); // Initialize session
// Fetch categories from the database

require 'connection.php';
// Fetch categories from the database
$checkRec = $connection->prepare('SELECT * FROM categories');
$checkRec->execute();
$categories = $checkRec->fetchAll(PDO::FETCH_ASSOC);

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
// Insert user_id into the auctions table
$stmt_insert_user_auction = $connection->prepare("INSERT INTO auctions (user_id) VALUES (:user_id)");
$stmt_insert_user_auction->bindParam(':user_id', $user_id);

// Fetch auctions associated with the current user
$stmt_user_auctions = $connection->prepare("SELECT * FROM auctions WHERE user_id = :user_id");
$stmt_user_auctions->execute(array('user_id' => $user_id));
$user_auctions = $stmt_user_auctions->fetchAll(PDO::FETCH_ASSOC);

$stmt_all_auctions = $connection->prepare("SELECT * FROM auctions ORDER BY creation_time DESC");
$stmt_all_auctions->execute();
$all_auctions = $stmt_all_auctions->fetchAll(PDO::FETCH_ASSOC);
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
        <form action="search.php">
            <input type="text" name="search" placeholder="Search for a car" />
            <input type="submit" name="submit_search" value="Search" />
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
            <!-- add item created by user -->
		<img src="banners/1.jpg" alt="Banner" style="width: 100vw; height: 400px;" />

		<main>
			<h1>Latest Car Listings</h1>
            <ul class="carList">
        <?php foreach ($all_auctions as $auction): ?>
            <li>
                <img src="<?php echo $auction['image_path']; ?>" class="auctionItem" alt="<?php echo $auction['title']; ?>">
                <article class="contain">
                    <h2><?php echo $auction['title']; ?></h2>
                    <h3><?php echo $auction['description']; ?></h3>
                    <p class="price">Current bid: £<?php echo $auction['current_bid']; ?></p>
                    <?php
                if ($user_id === $auction['user_id']) {
                    echo '<a href="edit_auction.php?auction_id=' . $auction['auction_id'] . '&user_id=' . $auction['user_id'] .'" class="edit auctionLink">Edit</a>';
                } else {
                    echo '<a href="bid.php?auction_id=' . $auction['auction_id'] . '&user_id=' . $auction['user_id'] . '" class="more auctionLink">More &gt;&gt;</a>';
                }
                ?>
            </li>
        <?php endforeach; ?>
			</ul>
<footer>
    &copy; Carbuy 2024
</footer>

		</main>
	</body>
</html>
