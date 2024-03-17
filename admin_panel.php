<?php
session_start();
require 'connection.php';
$_SESSION['admin_logged_in'] = true;

// Check if the user is logged in and is an admin
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    
    // Delete operation
    if(isset($_POST['delete_category']) && isset($_POST['category_id'])) {
        $categoryId = $_POST['category_id'];
        $deleteQuery = $connection->prepare("DELETE FROM categories WHERE categoryId = :categoryId");
        $deleteQuery->bindParam(':categoryId', $categoryId);
        $deleteQuery->execute();
    
        // After deletion, update the IDs of the remaining categories
        $updateIdsQuery = $connection->prepare("SET @counter = 0;");
        $updateIdsQuery->execute();
    
        $updateIdsQuery = $connection->prepare("UPDATE categories SET categoryId = @counter := (@counter + 1);");
        $updateIdsQuery->execute();

        $updateIdsQuery = $connection->prepare("ALTER TABLE categories AUTO_INCREMENT = 1;");
        $updateIdsQuery->execute();

        // Redirect to refresh the page after deletion and renumbering
        header("Location: adminCategories.php");
        exit();
    }
    
    // Update operation
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_category'])) {
        $categoryId = $_POST['category_id'];
        $newName = $_POST['new_name'];
        
        // Update the name of the category
        $updateQuery = $connection->prepare("UPDATE categories SET name = :newName WHERE categoryId = :categoryId");
        $updateQuery->bindParam(':newName', $newName);
        $updateQuery->bindParam(':categoryId', $categoryId);
        $updateQuery->execute();
    
        // Redirect to refresh the page after update
        header("Location: adminCategories.php");
        exit();
    }    

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate form data
        if (!empty($_POST['category_name'])) {
            // Check if the category already exists
            $category_name = $_POST['category_name'];
            $checkQuery = $connection->prepare("SELECT COUNT(*) FROM categories WHERE name = :name");
            $checkQuery->bindParam(':name', $category_name);
            $checkQuery->execute();
            $count = $checkQuery->fetchColumn();
            
            if($count > 0) {
                echo "Category already exists.";
            } else {
                // Prepare and execute the INSERT statement
                $insertRecord = $connection->prepare("INSERT INTO categories (name) VALUES (:name)");
                $insertRecord->bindParam(':name', $category_name);
                $insertRecord->execute();
                echo "Category added successfully.";
            }
        } else {
            echo "Category name is required.";
        }
    }

    // Fetch categories from the database
    $categoryQuery = $connection->query("SELECT categoryId, name FROM categories");
    $categories = $categoryQuery->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Only admin users can add categories.";
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
<style>
    table {
            border-collapse: collapse;
            margin: 0 auto;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        tr{
            background-color: #ffdede;

        }
</style>
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
            <li> <a href="adminCategories.php">Admin</a></li>
            <li>
                <?php
                    echo "<a href='javascript:void(0)' onclick=\"confirmLogout()\">Logout</a>";
                ?>
            </li>
        </ul>
    </nav>
    <div class= "box" style="margin-top: 30px">
    <h2 >Add Category</h2>
    <form method="post" action="" >
        <label for="category_name">Category Name:</label><br>
        <input type="text" id="category_name" name="category_name" required><br><br>
        <input type="submit" value="Add Category">
    </form>
    </div>
    
<div class="box" style="width:auto">
<h2>All Categories</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category) : ?>
            <tr>
                <td><?php echo $category['categoryId']; ?></td>
                <td><?php echo $category['name']; ?></td>
                <td>
                     <!-- Update form -->
                     <form method="post" action="">
                        <input type="hidden" name="category_id" value="<?php echo $category['categoryId']; ?>">
                        <input type="text" name="new_name" value="<?php echo $category['name']; ?>">
                        <input type="submit" name="update_category" value="Update">
                    </form>
        </td>
        <td>
                    <!-- Delete form -->
                    <form method="post" action="">
                        <input type="hidden" name="category_id" value="<?php echo $category['categoryId']; ?>">
                        <input type="submit" name="delete_category" value="Delete">
                    </form>

                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

    
</body>
</html>
