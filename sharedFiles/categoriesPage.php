<!DOCTYPE html>
<html lang="en">

<head>
	<title>Categories</title>
	<?php include_once '__head.php'; ?>
</head>

<body>
	<?php
	include 'config.php';
	include '__body.php';
	$output = "";
	// DELETE CATEGORY
	if (isset($_POST['delete'])) {
		// Get the category name to delete
		$categoryToDelete = $_POST['delete'];

		// Perform deletion from the database
		$deleteSql = "DELETE FROM category WHERE ID = '$categoryToDelete'";
		mysqli_query($conn, $deleteSql);
		$output = '<div class="alert alert-success" role="alert"> Category name successfully deleted. </div>';
	}

	// MODIFY CATEGORY NAME
	if (isset($_POST['modify'])) {
		// Get the category name to modify
		$categoryToModify = $_POST['modify'];
		$newName = $_POST['newName'];

		// Check if the new name is already used in the database
		$checkResult = checkDuplicate($newName, $conn);
		if ($newName == '') {
			$output = '<div class="alert alert-warning" role="alert"> Please enter a new name. </div>';
		} elseif ($checkResult && $checkResult->num_rows > 0) {
			$output = '<div class="alert alert-warning" role="alert"> Category name already exists. Please choose a different name. </div>';
		} else {
			// Perform modification in the database
			$modifySql = "UPDATE category SET category_name = '$newName' WHERE ID = '$categoryToModify'";
			mysqli_query($conn, $modifySql);
			$output = '<div class="alert alert-success" role="alert"> Category name successfully modified. </div>';
		}
	}

	// ADD NEW CATEGORY
	if (isset($_POST['category-name'])) {
		$newName = $_POST['category-name'];

		// Check if the new name is already used in the database
		$checkResult = checkDuplicate($newName, $conn);
		if ($newName == '') {
			$output = '<div class="alert alert-warning" role="alert"> Please enter a new name. </div>';
		} elseif ($checkResult && $checkResult->num_rows > 0) {
			$output = '<div class="alert alert-warning" role="alert"> Category name already exists. Please choose a different name. </div>';
		} else {
			// create new category
			$addSql = "INSERT INTO category (category_name) VALUES ('$newName')";
			mysqli_query($conn, $addSql);
			$output = '<div class="alert alert-success" role="alert"> Category successfully added. </div>';
		}

	}

	function checkDuplicate($newName, $conn)
	{
		// Check if the new name is already used in the database
		$checkSql = "SELECT * FROM category WHERE category_name = '$newName'";
		$checkResult = $conn->query($checkSql);
		return $checkResult;
	}

	// Execute a SELECT query to retrieve categories
	$sql = "SELECT * FROM category";
	$result = $conn->query($sql);
	?>

	<!-- FORM ADD CATEGORY -->
	<?= $output ?>
	<div class="category_form">
		<h3 class="title">Add Category</h3>
		<form action="" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<input type="text" class="form-control" id="category-name" name="category-name"
					placeholder="Enter Category name">
				<br>
				<small id="categoryHelp" class="form-text text-muted">This category will be added to categories
					list.</small>
			</div>
			<input class="btnn" name="submit" type="submit" value="Submit">
		</form>
	</div>

	<hr>

	<!-- Categories List -->
	<?php if ($result->num_rows > 0): ?>
		<?php $num = 0; ?>
		<h3 class="title">Categories List:</h3>
		<table>
			<thead>
				<tr>
					<th>#</th>
					<th>Category Name</th>
					<th>Edit</th>
				</tr>
			</thead>
			<tbody>
				<?php while ($row = $result->fetch_assoc()):
					$categoryName = $row['category_name'];
					$categoryID = $row['ID'];
					?>
					<tr>
						<td>

							<?= ++$num ?>

						</td>
						<td>

							<?= $categoryName ?>

						</td>
						<td>
							<div class="edit-buttons">
								<!-- modify category name -->
								<form method='POST' action=''>
									<div class="input-group">
										<input class="form-control" type="text" name="newName" placeholder="New Name">
										<input type='hidden' name='modify' value='<?= $categoryID ?>'>
										<input class="btn btn-outline-warning" type='submit' value='Modify'>
									</div>
								</form>
								<!-- delete category -->
								<form method='POST' action=''>
									<input type='hidden' name='delete' value='<?= $categoryID ?> '>
									<input class="btn btn-outline-danger" type='submit' value='Delete'
										onclick="return confirmDelete('<?= $categoryName ?>');">
								</form>
							</div>
						</td>
					</tr>
				<?php endwhile; ?>
		</table>
	<?php else: ?>
		<div class="title">No categories found &#9785;</div>
	<?php endif; ?>
</body>

</html>

<style>
	.category_form {
		padding: 40px;
	}

	#category-name {
		width: 500px;
	}
</style>
<script>
	function confirmDelete(categoryName) {
		return confirm("Are you sure you want to delete the category: " + categoryName + " ?");
	}
</script>