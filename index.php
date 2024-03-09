<?php
session_start(); // Initialize session
// Fetch categories from the database
require 'connection.php';
$checkRec = $connection->prepare('SELECT name FROM category');
$checkRec->execute();
$categories = $checkRec->fetchAll(PDO::FETCH_COLUMN);
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
            }elseif(isset($_SESSION['admin_logged_in'])){
				echo "Admin";
            }
            else{
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
                <li><a class="categoryLink " href="#">Estate</a></li>
                <li><a class="categoryLink" href="#">Electric</a></li>
                <li><a class="categoryLink" href="#">Coupe</a></li>
                <li><a class="categoryLink" href="#">Saloon</a></li>
                <li><a class="categoryLink" href="#">4x4</a></li>
                <li><a class="categoryLink" href="#">Sports</a></li>
                <li><a class="categoryLink" href="#">Hybrid</a></li>
				<?php
                foreach ($categories as $category) {
                    echo "<li><a class='categoryLink' href='#'>$category</a></li>";
                }
                ?>
				
            </ul>
        </li>
        <?php
        if(isset($_SESSION['user'])) {
			echo "<li><a href='addauction.php'>Add auction??</a></li>";
			echo "<li><a href='auction.php'>Own auction</a></li>";
            echo "<li><a href='javascript:void(0)' onclick=\"confirmLogout()\">Logout</a></li>";
        }elseif (isset($_SESSION['admin_logged_in'])) {
			echo "<li><a href='adminCategories.php'>Admin</a></li>";
			echo "<li><a href='javascript:void(0)' onclick=\"confirmLogout()\">Logout</a></li>";
		}else {
            echo "<li><a href='Register.php'>Register</a></li>";
            echo "<li><a href='Login.php'>Login</a></li>";
        }
        ?>
    </ul>
</nav>
		<img src="banners/1.jpg" alt="Banner" style="width: 100vw; height: 400px;" />

		<main>
			<h1>Latest Car Listings</h1>
			<ul class="carList">
				<li>
					<img src="car.png" alt="car name">
					<article>
						<h2>Car model and make</h2>
						<h3>Car category</h3>
						<p class="price">Current bid: £1234.00</p>
						<a href="bid.php" class="more auctionLink">More &gt;&gt;</a>
					</article>
				</li>
				<li>
					<img src="car.png" alt="car name">
					<article>
						<h2>Car model and make</h2>
						<h3>Car category</h3>
						<p class="price">Current bid: £2000</p>
						<a href="bid.php" class="more auctionLink">More &gt;&gt;</a>
					</article>
				</li>
				<li>
					<img src="car.png" alt="car name">
					<article>
						<h2>Car model and make</h2>
						<h3>Car category</h3>
						<p class="price">Current bid: £3000</p>
						<a href="bid.php" class="more auctionLink">More &gt;&gt;</a>
					</article>
				</li>
			</ul>

			<hr />

			<h1>Car Page</h1>
			<article class="car">

					<img src="car.png" alt="car name">
					<section class="details">
						<h2>Car model and make</h2>
						<h3>Car category</h3>
						<p>Auction created by user1

						</a></p>
						<p class="price">Current bid: £4000</p>
						<time>Time left: 8 hours 3 minutes</time>
						<?php
            if(isset($_SESSION['user']) && $_SESSION['user'] === true) {
                echo "<li><a href='bid.php'>Bid</a></li>"; // Display bid option if user is true
            }
        ?>
					
					</section>
					<section class="description">
					<p>
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sodales ornare purus, non laoreet dolor sagittis id. Vestibulum lobortis laoreet nibh, eu luctus purus volutpat sit amet. Proin nec iaculis nulla. Vivamus nec tempus quam, sed dapibus massa. Etiam metus nunc, cursus vitae ex nec, scelerisque dapibus eros. Donec ac diam a ipsum accumsan aliquet non quis orci. Etiam in sapien non erat dapibus rhoncus porta at lorem. Suspendisse est urna, egestas ut purus quis, facilisis porta tellus. Pellentesque luctus dolor ut quam luctus, nec porttitor risus dictum. Aliquam sed arcu vehicula, tempor velit consectetur, feugiat mauris. Sed non pellentesque quam. Integer in tempus enim.</p>


					</section>

					<section class="reviews">
						<h2>Reviews of User.Name </h2>
						<ul>
							<li><strong>John said </strong> great car seller! Car was as advertised and delivery was quick <em>29/01/2024</em></li>
							<li><strong>Dave said </strong> disappointing, Car was slightly damaged and arrived slowly.<em>22/12/2023</em></li>
							<li><strong>Susan said </strong> great value but the delivery was slow <em>22/07/2023</em></li>

						</ul>

						<form>
							<label>Add your review</label> <textarea name="reviewtext"></textarea>

							<input type="submit" name="submit" value="Add Review" />
						</form>
					</section>
				</article>
			<footer>
				&copy; Carbuy 2024
			</footer>
		</main>
	</body>
</html>