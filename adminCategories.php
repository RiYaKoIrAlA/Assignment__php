<?php
session_start();
require 'connection.php';

// Check if the user is logged in and is an admin
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    
    if(isset($_POST['delete_category']) && isset($_POST['category_id'])) {
        $categoryId = $_POST['category_id'];
        $deleteQuery = $connection->prepare("DELETE FROM category WHERE categoryId = :categoryId");
        $deleteQuery->bindParam(':categoryId', $categoryId);
        $deleteQuery->execute();
    
        // After deletion, update the IDs of the remaining categories
        $updateIdsQuery = $connection->prepare("SET @counter = 0;");
        $updateIdsQuery->execute();
    
        $updateIdsQuery = $connection->prepare("UPDATE category SET categoryId = @counter := (@counter + 1);");
        $updateIdsQuery->execute();

        $updateIdsQuery = $connection->prepare("ALTER TABLE category AUTO_INCREMENT = 1 ;"); //https://www.youtube.com/watch?v=2_ozHYarwHU&ab_channel=DZ4Team time = 0:12
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
        $updateQuery = $connection->prepare("UPDATE category SET name = :newName WHERE categoryId = :categoryId");
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
            $checkQuery = $connection->prepare("SELECT COUNT(*) FROM category WHERE name = :name");
            $checkQuery->bindParam(':name', $category_name);
            $checkQuery->execute();
            $count = $checkQuery->fetchColumn();
            
            if($count > 0) {
                echo "Category already exists.";
            } else {
                // Prepare and execute the INSERT statement
                $insertRecord = $connection->prepare("INSERT INTO category (name) VALUES (:name)");
                $insertRecord->bindParam(':name', $category_name);
                $insertRecord->execute();
                echo "Category added successfully.";
            }
        } else {
            echo "Category name is required.";
        }
    }

    // Fetch categories from the database
    $categoryQuery = $connection->query("SELECT categoryId, name FROM category");
    $categories = $categoryQuery->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Only admin users can add categories.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Category</title>
</head>
<body>
    <h2>Add Category</h2>
    <form method="post" action="">
        <label for="category_name">Category Name:</label><br>
        <input type="text" id="category_name" name="category_name" required><br><br>
        <input type="submit" value="Add Category">
    </form>

    <h2>All Categories</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category) : ?>
                <tr>
                    <td><?php echo $category['categoryId']; ?></td>
                    <td><?php echo $category['name']; ?></td>
                    <td>
                        <!-- Delete form -->
                        <form method="post" action="">
                            <input type="hidden" name="category_id" value="<?php echo $category['categoryId']; ?>">
                            <input type="submit" name="delete_category" value="Delete">
                        </form>

                        <!-- Update form -->
                        <form method="post" action="">
                            <input type="hidden" name="category_id" value="<?php echo $category['categoryId']; ?>">
                            <input type="text" name="new_name" value="<?php echo $category['name']; ?>">
                            <input type="submit" name="update_category" value="Update">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
