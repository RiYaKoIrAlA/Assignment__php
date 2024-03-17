<?php
session_start();

require 'connection.php';

// Fetch categories from the database
$checkRec = $connection->prepare('SELECT categoryId, name FROM categories');
$checkRec->execute();
$categories = $checkRec->fetchAll(PDO::FETCH_ASSOC);

// Function to fetch the creator's username and user_id
function fetchCreator($connection, $user_id) {
    $stmt_fetch_creator = $connection->prepare("SELECT username, user_id FROM users WHERE user_id = :user_id");
    $stmt_fetch_creator->execute(['user_id' => $user_id]);
    return $stmt_fetch_creator->fetch(PDO::FETCH_ASSOC);
}

// Check if the user ID and auction ID are provided via GET
if(isset($_GET['user_id']) && isset($_GET['auction_id'])) {
    // Extract the user ID and auction ID from the GET data
    $user_id = $_GET['user_id'];
    $auction_id = $_GET['auction_id'];

    // Fetch the auction details from the database based on the auction ID
    $stmt_fetch_auction = $connection->prepare("SELECT * FROM auctions WHERE auction_id = :auction_id");
    $stmt_fetch_auction->execute(['auction_id' => $auction_id]);
    $auction = $stmt_fetch_auction->fetch(PDO::FETCH_ASSOC);

    // Check if the auction exists
    if (!$auction) {
        echo "Error: Auction not found.";
        exit;
    }

    // Fetch the category ID along with other auction details
    $categoryId = $auction['categoryId'];

    // Fetch the category name from the categories table based on the categoryId
    $stmt_fetch_category = $connection->prepare("SELECT name FROM categories WHERE categoryId = :categoryId");
    $stmt_fetch_category->execute(['categoryId' => $categoryId]);
    $category = $stmt_fetch_category->fetch(PDO::FETCH_ASSOC);

    // Check if the category exists
    if (!$category) {
        echo "Error: Category not found.";
        exit;
    }

    // Fetch the auction creator's information
    $creator = fetchCreator($connection, $auction['user_id']);
        
    // Check if the bid form is submitted
    if(isset($_POST['bid_submit'])) {
        // Only logged-in users can bid
        if (!isset($_SESSION['user']['user_id'])) {
            echo "<script>alert('Please login before you try to bid.');</script>";
            echo "<script>window.location.href = 'register.php';</script>";
            exit;
        }
        
        // Extract bid amount from POST data
        $bid_amount = $_POST['current_bid'];

        // Check if the bid amount is higher than the current bid
        if($bid_amount > $auction['current_bid']) {
            // Update the current bid in the auctions table
            $stmt_update_bid = $connection->prepare("UPDATE auctions SET current_bid = :current_bid WHERE auction_id = :auction_id");
            $stmt_update_bid->execute(['current_bid' => $bid_amount, 'auction_id' => $auction_id]);
            echo "";
        } else {
            // Alert the user that the amount is lower than the current bid
            echo "<script>alert('Please bid an amount higher than the current bid.');</script>";
        }
    }
    
    // Check if the review form is submitted
    if (isset($_POST['submit_review'])) {
        // Proceed with review submission
        // Sanitize and validate input
        $review_text = trim($_POST['textreview']);
        if (!empty($review_text)) {
            // Insert review into the database
            $stmt = $connection->prepare("INSERT INTO reviews (user_id, textreview, date) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $review_text, date("Y-m-d")]);
            
            // Refresh the page to show the updated reviews
            header("Location: {$_SERVER['PHP_SELF']}?user_id=$user_id&auction_id=$auction_id");
            exit;
        } else {
            echo "";
        }
    }
    
    // Fetch reviews for the current auction
    $stmt_reviews = $connection->prepare("SELECT * FROM reviews WHERE user_id = :user_id");
    $stmt_reviews->execute(['user_id' => $user_id]);
    $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Error: User ID or Auction ID is not provided.";
    exit;
}
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
                $user = $_SESSION['user'];
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
<article class="car" style="margin: 5%; display: flex;">
    <img id="imagePreview" src="<?php echo $auction['image_path'] ?? ''; ?>" alt="Preview" style="max-width: 500px; max-height: 200px;">
    <section class="details" style="margin: 5%;">
        <h2><?php echo $auction['title'] ?? ''; ?></h2>
        <h3><?php echo $category['name'] ?? ''; ?></h3>
        <?php
        if($creator) {
            echo '<p>Auction created by <a href="profile.php?user_id=' . $auction['user_id'] . '">' . $creator['username'] . '</a></p>';
        } else {
            echo '<p>Auction created by Unknown</p>';
        }
        ?>
        <?php if(isset($bid_amount)) : ?>
            <p class="price">Current bid: £<?php echo $bid_amount; ?></p>
        <?php else : ?>
            <p class="price">Current bid: £<?php echo $auction['current_bid'] ?? ''; ?></p>
        <?php endif; ?>
        <time>Time left: <?php echo $auction['end_date'] ?? ''; ?></time>
        <form action="" method="post" style=" width: 50%;">
            <label for="bid">Bid Amount:</label>
            <input type="number" id="bid" name="current_bid" required style="margin:10px 0px;">
            <input type="hidden" name="auction_id" value="<?php echo $auction_id ?? ''; ?>">
            <input type="hidden" name="user_id" value="<?php echo $auction['user_id'] ?? ''; ?>">
            <input type="submit" value="Place Bid" style="margin:10px 0px;" name="bid_submit">
        </form>
        
    </section>
    <section id="reviewsContainer" class="reviews">
    <h2>Reviews of <?php echo isset($_SESSION['user']['username']) ? $_SESSION['user']['username']: 'NULL'; ?></h2>
    <ul id="reviewsList">
        <?php foreach ($reviews as $review): ?>
            <li><strong>User said: </strong><?php echo $review['textreview']; ?><em><?php echo $review['date']; ?></em></li>
        <?php endforeach; ?>
    </ul>

    <form action="<?php echo $_SERVER['PHP_SELF'] . "?user_id=$user_id&auction_id=$auction_id"; ?>" method="post" style="display: grid; width: 50%">
        <label>Add your review</label>
        <textarea id="reviewText" name="textreview"></textarea>
        <input type="submit" id="submitReview" name="submit_review" value="Add Review" />
    </form>
        </section>

</article>
</body>
</html>
