<?php
require_once 'config.php';

// SEARCH QUERY
if (isset($_GET['search'])) {
    // Get the search query from the form and sanitize it
    $searchQuery = $_GET['search'];
    $sanitizedQuery = $conn->real_escape_string($searchQuery);

    // Perform search from the database using prepared statement
    $sql = "SELECT * FROM sharedfiles WHERE file_name COLLATE utf8_general_ci LIKE CONCAT('%', ?, '%')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sanitizedQuery);
    $stmt->execute();
    $result = $stmt->get_result();
}
$output = '';

// DELETE CATEGORY
if (isset($_POST['delete'])) {
    // Get the file ID to delete
    $fileToDelete = $_POST['delete'];

    // Perform deletion from the database
    $sql = "DELETE FROM sharedfiles WHERE ID = '$fileToDelete'";
    mysqli_query($conn, $sql);
    $output = '<div class="alert alert-success" role="alert"> File name successfully deleted. </div>';
}

// MODIFY CATEGORY NAME 
if (isset($_POST['modify'])) {
    // Get the file ID to modify
    $fileToModify = $_POST['modify'];
    $newName = $_POST['newName'];

    // Check if the new name is already used in the database
    $checkSql = "SELECT * FROM sharedfiles WHERE file_name = '$newName'";
    $checkResult = $conn->query($checkSql);

    if ($newName == '') {
        $output = '<div class="alert alert-warning" role="alert"> Please enter a new name. </div>';
    } elseif ($checkResult && $checkResult->num_rows > 0) {
        $output = '<div class="alert alert-warning" role="alert"> File name already exists. Please choose a different name. </div>';
    } else {
        // Perform modification in the database
        $sql = "UPDATE sharedfiles SET file_name = '$newName' WHERE ID = '$fileToModify'";
        mysqli_query($conn, $sql);
        $output = '<div class="alert alert-success" role="alert"> File name successfully modified. </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once '__head.php' ?>
</head>

<body>
    <?php require_once '__body.php'; ?>
    <?= $output ?>
    <div class="title">
        Search results for:
        <?= htmlspecialchars($searchQuery) ?>
    </div>

    <table>
        <thead>
            <tr>
                <td>#</td>
                <td>File Name</td>
                <td>File Type</td>
                <td>Date Created</td>
                <td>Download File</td>
                <td>Category</td>
                <td>Edit</td>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($result) && $result->num_rows > 0): ?>
                <?php $num = 0; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?= ++$num ?>
                        </td>
                        <td>
                            <?= $row['file_name'] ?>
                        </td>
                        <td>
                            <?= $row['file_type'] ?>
                        </td>
                        <td>
                            <?= date('d M Y', strtotime($row['date_created'])) ?>
                        </td>
                        <td>
                            <a href="<?= $row['file_path'] ?>" target="_blank">
                                <img class="icon" src="bootstrap-icons/file-earmark-arrow-down-fill.svg" alt="Download">
                            </a>
                        </td>
                        <td>
                            <?php
                            // Get the category associated with the file
                            $categoryId = $row['file_category'];
                            $sql = "SELECT category_name FROM category WHERE ID = $categoryId";
                            $result = $conn->query($sql);
                            if ($result && $result->num_rows > 0) {
                                $category = $result->fetch_assoc();
                                echo $category['category_name'];
                            } else {
                                echo "Unknown Category";
                            }
                            ?>
                        </td>
                        <td>
                        <div class="edit-buttons">
                            <!-- modify category name -->
                            <form method='POST' action=''>
                                <div class="input-group">
                                    <input class="form-control" type="text" name="newName" placeholder="New Name">
                                    <input type='hidden' name='modify' value='<?= $row['ID'] ?>'>
                                    <input class="btn btn-outline-warning" type='submit' value='Modify'>
                                </div>
                            </form>
                            <!-- delete category -->
                            <form method='POST' action=''>
                                <input type='hidden' name='delete' value='<?= $row['ID'] ?> '>
                                <input class="btn btn-outline-danger" type='submit' value='Delete'
                                    onclick="confirmDelete('<?= $row['file_name'] ?>');">
                            </form>
                        </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No results found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>
<script>
    function confirmDelete(categoryName) {
        return confirm("Are you sure you want to delete the category: " + categoryName + " ?");
    }
</script>