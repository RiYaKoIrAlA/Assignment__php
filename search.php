<?php
// Start the session
session_start();

// Check if user_id is set in the session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="carbuy.css">
    <link rel="stylesheet" href="style.css"> 
</head>
<body>
    <nav>
        <ul>
            <?php
            if($user_id) {
                // If user is logged in
                echo "<li><a href='addauction.php'>Add auction</a></li>";
                echo "<li><a href='product.php'>Own product</a></li>";
                echo "<li><a href='logout.php'>Logout</a></li>"; // Assuming there's a logout page
            } elseif (isset($_SESSION['admin_logged_in'])) {
                // If admin is logged in
                echo "<li><a href='adminCategories.php'>Admin</a></li>";
                echo "<li><a href='logout.php'>Logout</a></li>"; // Assuming there's a logout page
            } else {
                // If no user is logged in
                echo "<li><a href='Register.php'>Register</a></li>";
                echo "<li><a href='Login.php'>Login</a></li>";
            }
            ?>
        </ul>
    </nav>

<?php
// Include the header file
require 'connection.php';

// Check if the search form is submitted
if(isset($_GET['submit_search']) && isset($_GET['search'])) {
    // Retrieve the search query
    $search = $_GET['search'];
    // Execute SQL query to fetch auctions matching the search query
    $auctions = $connection->prepare('SELECT * FROM auctions WHERE LOWER(title) LIKE LOWER(?) OR LOWER(description) LIKE LOWER(?) ORDER BY end_date LIMIT 10');
    $auctions->execute(["%$search%", "%$search%"]); // Execute the prepared statement
    ?>

    <h1>Search Results for "<?php echo $search; ?>"</h1>

    <?php
    // Count the number of retrieved auctions
    $count = $auctions->rowCount();
    // Check if any auctions are found
    if($count == 0){
        echo '<p>No auctions found</p>';
    } else {
        // Display the list of found auctions
        echo '<ul class="carList">';
        $results = $auctions->fetchAll(); // Fetch all the rows
        foreach($results as $auction){
            // Output the auction in the specified format
            ?>
            <li>
                <img src="<?php echo $auction['image_path']; ?>" class="auctionItem" alt="<?php echo $auction['title']; ?>">
                <article class="contain">
                    <h2><?php echo $auction['title']; ?></h2>
                    <h3><?php echo $auction['description']; ?></h3>
                    <p class="price">Current bid: £<?php echo $auction['current_bid']; ?></p>
                   <a href="product.php" class="more auctionLink">More &gt;&gt;</a>
                </article>
            </li>
            <?php
        }
        echo '</ul><hr />';
    }
}
?>

</body>
</html>
