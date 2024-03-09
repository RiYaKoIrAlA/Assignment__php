<?php
session_start();
require 'connection.php';
// Fetch categories from the database
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
                    } 
                    ?>

			</form>
		</div>
			
		</header>
	<nav>
    <ul style="width: 70vw;">
		<li><a href="index.php">Home</a></li>
        <li class="dropdown">
            <a class="categoryLink" href="#">Categories</a>
            <ul class="dropdown-content">
			<li><a class="categoryLink  " href="#">Estate</a></li>
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
		<li> <a href="addauction.php">Add own auction?!!</a></li>

		</a></li>
		<li><?php
             echo "<a href='javascript:void(0)' onclick=\"confirmLogout()\">Logout</a>";
            ?></a></li>      
    </ul>
</nav>


		<img src="banners/1.jpg" alt="Banner" style="width: 80vw; height: 400px; padding-left: 120px" />

		<main>
			<h1>Latest Car Listings / Search Results / Category listing</h1>
			<ul class="carList">
					<img src="car.png" alt="car name">
					<article>
						<h2>Car model and make</h2>
						<h3>Car category</h3>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sodales ornare purus, non laoreet dolor sagittis id. Vestibulum lobortis laoreet nibh, eu luctus purus volutpat sit amet. Proin nec iaculis nulla. Vivamus nec tempus quam, sed dapibus massa. Etiam metus nunc, cursus vitae ex nec, scelerisque dapibus eros. Donec ac diam a ipsum accumsan aliquet non quis orci. Etiam in sapien non erat dapibus rhoncus porta at lorem. Suspendisse est urna, egestas ut purus quis, facilisis porta tellus. Pellentesque luctus dolor ut quam luctus, nec porttitor risus dictum. Aliquam sed arcu vehicula, tempor velit consectetur, feugiat mauris. Sed non pellentesque quam. Integer in tempus enim.</p>

						<p class="price">Current bid: £1234.00</p>
						<a href="bid.php" class="more auctionLink">More &gt;&gt;</a>
					</article>
				</li>
				<li>
					<img src="car.png" alt="car name">
					<article>
						<h2>Car model and make</h2>
						<h3>Car category</h3>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sodales ornare purus, non laoreet dolor sagittis id. Vestibulum lobortis laoreet nibh, eu luctus purus volutpat sit amet. Proin nec iaculis nulla. Vivamus nec tempus quam, sed dapibus massa. Etiam metus nunc, cursus vitae ex nec, scelerisque dapibus eros. Donec ac diam a ipsum accumsan aliquet non quis orci. Etiam in sapien non erat dapibus rhoncus porta at lorem. Suspendisse est urna, egestas ut purus quis, facilisis porta tellus. Pellentesque luctus dolor ut quam luctus, nec porttitor risus dictum. Aliquam sed arcu vehicula, tempor velit consectetur, feugiat mauris. Sed non pellentesque quam. Integer in tempus enim.</p>

						<p class="price">Current bid: £2000</p>
						<a href="bid.php" class="more auctionLink">More &gt;&gt;</a>
					</article>
				</li>
				<li>
					<img src="car.png" alt="car name">
					<article>
						<h2>Car model and make</h2>
						<h3>Car category</h3>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sodales ornare purus, non laoreet dolor sagittis id. Vestibulum lobortis laoreet nibh, eu luctus purus volutpat sit amet. Proin nec iaculis nulla. Vivamus nec tempus quam, sed dapibus massa. Etiam metus nunc, cursus vitae ex nec, scelerisque dapibus eros. Donec ac diam a ipsum accumsan aliquet non quis orci. Etiam in sapien non erat dapibus rhoncus porta at lorem. Suspendisse est urna, egestas ut purus quis, facilisis porta tellus. Pellentesque luctus dolor ut quam luctus, nec porttitor risus dictum. Aliquam sed arcu vehicula, tempor velit consectetur, feugiat mauris. Sed non pellentesque quam. Integer in tempus enim.</p>

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

					<hr />
					<h1>Sample Form</h1>

					<form action="#">
						<label>Text box</label> <input type="text" />
						<label>Another Text box</label> <input type="text" />
						<input type="checkbox" /> <label>Checkbox</label>
						<input type="radio" /> <label>Radio</label>
						<input type="submit" value="Submit" />

					</form>



			<footer>
				&copy; Carbuy 2024
			</footer>
		</main>
	</body>
</html>