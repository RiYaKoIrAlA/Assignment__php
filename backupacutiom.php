<?php
session_start();
// Assuming you have a connection to your database in connection.php
require 'connection.php';
$stmt = $connection->prepare("SELECT * FROM auction ORDER BY creation_time DESC");
$stmt->execute();
$auctions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$_SESSION['user'] = array(
    'user_id' => $user_id,
    'username' => $username,
    'email' => $email
);

$user = $_SESSION['user'];
$user_id = $user['user_id'];
// Fetch auctions associated with the current user
$auctions = $connection->prepare("SELECT * FROM auction WHERE user_id = :user_id");
$auctions->execute(array('user_id' => $user_id));
$auctions = $auctions->fetchAll(PDO::FETCH_ASSOC);

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
        <li><a href="user.php">Home</a></li>
        <li class="dropdown">
            <a class="categoryLink" href="#">Categories</a>
            <ul class="dropdown-content">
                <li><a class="categoryLink" href="#">Estate</a></li>
                <li><a class="categoryLink" href="#">Electric</a></li>
                <li><a class="categoryLink" href="#">Coupe</a></li>
                <li><a class="categoryLink" href="#">Saloon</a></li>
                <li><a class="categoryLink" href="#">4x4</a></li>
                <li><a class="categoryLink" href="#">Sports</a></li>
                <li><a class="categoryLink" href="#">Hybrid</a></li>
            </ul>
        </li>
        <li><a href="addauction.php">Add own auction?!!</a></li>
        <li><a href="javascript:void(0)" onclick="confirmLogout()">Logout</a></li>
    </ul>
</nav>


<img src="banners/1.jpg" alt="Banner" style="width: 80vw; height: 400px; padding-left: 120px" />

<main>
    <h1>Latest Car Listings</h1>
    <ul class="carList">
        <?php foreach ($auctions as $auction): ?>
				<li >
					<img src="<?php echo $auction['image_path']; ?>" class="auctionItem" alt="<?php echo $auction['title']; ?>">
                    
					<article class="contain">
						<h2><?php echo $auction['title']; ?></h2>
						<h3><?php echo $auction['description']; ?></h3>
						<p class="price">Current bid: £<?php echo $auction['current_bid']; ?></p>
						<a href="bid.php" class="more auctionLink">More &gt;&gt;</a>
					</article>
				</li>
                <?php endforeach; ?>
        </ul>
    <footer>
        &copy; Carbuy 2024
    </footer>
</main>
</body>
</html>
