<?php
session_start();
require 'connection.php';
$_SESSION['admin_logged_in'] = true;
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
            if(isset($_SESSION['admin_logged_in'])) {
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
    <ul style="width:80vw ;">
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
		<li> <a href="adminCategories.php">Admin</a></li>
		<li>
		<?php
             echo "<a href='javascript:void(0)' onclick=\"confirmLogout()\">Logout</a>";
            ?></a>
		</li>

    </ul>
</nav>

		<img src="banners/1.jpg" alt="Banner" style="width: 100vw; height: 400px;" />
                </body>
                </html>
