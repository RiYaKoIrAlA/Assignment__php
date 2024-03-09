<?php
session_start();
require 'connection.php';

// Check if the user is logged in and is an admin
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    // Check if form is submitted for adding a category
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

    // Check if category deletion is requested
    if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $categoryId = $_GET['id'];
        $deleteQuery = $connection->prepare("DELETE FROM category WHERE categoryId = :categoryId");
        $deleteQuery->bindParam(':categoryId', $categoryId);
        $deleteQuery->execute();
        echo "Category deleted successfully.";
    }
    if (!empty($_POST['category_name']) && isset($_POST['category_id'])) {
        $category_name = $_POST['category_name'];
        $category_id = $_POST['category_id'];

        // Prepare and execute the UPDATE statement
        $updateQuery = $connection->prepare("UPDATE category SET name = :name WHERE categoryId = :categoryId");
        $updateQuery->bindParam(':name', $category_name);
        $updateQuery->bindParam(':categoryId', $category_id);
        $updateQuery->execute();
        echo "Category updated successfully.";
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
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category) : ?>
                <tr>
                    <td><?php echo $category['categoryId']; ?></td>
                    <td><?php echo $category['name']; ?></td>
                    <td>
                        <a href="?action=delete&id=<?php echo $category['categoryId']; ?>">Delete</a>
                        <a href="action=edit&idid=<?php echo $category['categoryId']; ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>


