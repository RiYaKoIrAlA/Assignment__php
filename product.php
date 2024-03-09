<?php
session_start();
?>

<?php
// Include the database connection file
require 'connection.php';

// Check if the bid form is submitted
if(isset($_POST['bid_submit'])) {
    // Prepare and execute the SQL query to insert a new bid into the database
    $insertRecord = $connection->prepare('INSERT INTO bids (auction_id, current_bid, end_date) VALUES (1, 90, NOW())');
    $criteria=[
        'current_bid'=>$_POST['current_bid']
    ];
    $insertRecord->execute($criteria);
    // Check if the bid insertion was successful
    if($insertRecord){
        echo ''; // Output can be customized here if needed
    }
}

// Fetch bid details from the database
$bidQuery = $connection->query('SELECT * FROM bids WHERE auction_id = 1');
$bid = $bidQuery->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="carbuy.css">
</head>
<body>

<article class="car" style="margin: 5%">

    <img src="car.png" alt="car name">
    <section class="details">
        <h2>Car model and make</h2>
        <h3>Car category</h3>
        <p>Auction created by 
            <?php
            echo 'Hua Cheng';

            ?>
        </p>
        <!-- Display current bid amount -->
        <p class="price">Current bid: £3</p>
        <!-- Display remaining time for the auction -->
        <time >Time left: 8 hours 30 min 49 sec</time>
        <!-- Bid form -->
        <form action="bid.php" method="post" style="display: grid; width: 50%;">
            <label for="bid">Bid Amount:</label>
            <input type="text" id="bid" name="current_bid" required style="margin:10px 0px;">
            <input type="hidden" name="id" value="1">
            <input type="submit" value="Place Bid" style="margin:10px 0px;" name="bid_submit">
</form>
</article>
</body>
</html>
