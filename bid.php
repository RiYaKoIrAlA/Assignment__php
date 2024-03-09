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
$bidQuery = $connection->prepare('SELECT * FROM bids WHERE auction_id = 1');
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
        <p>Auction created by user1</p>
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
    </section>

    <section class="description">
        <p>
            <!-- Car description -->
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sodales ornare purus, non laoreet dolor sagittis id. Vestibulum lobortis laoreet nibh, eu luctus purus volutpat sit amet. Proin nec iaculis nulla. Vivamus nec tempus quam, sed dapibus massa. Etiam metus nunc, cursus vitae ex nec, scelerisque dapibus eros. Donec ac diam a ipsum accumsan aliquet non quis orci. Etiam in sapien non erat dapibus rhoncus porta at lorem. Suspendisse est urna, egestas ut purus quis, facilisis porta tellus. Pellentesque luctus dolor ut quam luctus, nec porttitor risus dictum. Aliquam sed arcu vehicula, tempor velit consectetur, feugiat mauris. Sed non pellentesque quam. Integer in tempus enim.
        </p>
    </section>

    <section class="reviews">
        <h2>Reviews of User.Name </h2>
        <ul>
            <!-- Sample reviews -->
            <li><strong>John said </strong> great car seller! Car was as advertised and delivery was quick <em>29/01/2024</em></li>
            <li><strong>Dave said </strong> disappointing, Car was slightly damaged and arrived slowly.<em>22/12/2023</em></li>
            <li><strong>Susan said </strong> great value but the delivery was slow <em>22/07/2023</em></li>
            <!-- Placeholder for newly added review -->
            <li><?php echo 'review got added here'; ?></li>
        </ul>

        <!-- Review submission form -->
        <form action="review.php" method="post" style="display: grid;width: 50%;margin:20px;margin: 50px 0px 120px 60px;">
            <label>Add your review</label> <textarea name="reviewtext"></textarea>
            <input type="submit" name="submit" value="Add Review" style="margin: 20px" />
        </form>
    </section>
</article>
</body>
</html>
