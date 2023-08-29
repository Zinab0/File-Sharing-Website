<?php
require_once 'config.php';

// Get the category ID from the URL parameter
$cat_id = $_GET['cat'] ?? false;

if ($cat_id) {
	// Retrieve the category name from the database using the category ID
	$sql = "SELECT category_name FROM category WHERE ID = '{$cat_id}'";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$cat_name = $row['category_name'];
		$error = false;
	} else {
		$error = true;
	}
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
	<?php require_once '__body.php' ?>
	<?= $output ?>
	<?php if ($cat_id && !$error): ?>

		<!--  -----------files title--------------- -->
		<h1 class="title">
			<?= ucwords($cat_name) ?> Files
		</h1>
		<?php
		$sql = "SELECT * FROM sharedfiles WHERE file_category = '{$cat_id}'";
		$result = $conn->query($sql);
		?>
		<table>
			<thead>
				<tr>
					<td>#</td>
					<td>File Name</td>
					<td>File Type</td>
					<td>Date Created</td>
					<td>Download File</td>
					<td>Edit</td>
				</tr>
			</thead>
			<tbody>
				<?php if ($result->num_rows > 0): ?>
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
							<td><a href="<?= $row['file_path'] ?>" target="_blank"><img class="icon"
										src="bootstrap-icons/file-earmark-arrow-down-fill.svg" alt="Download"></a></td>
							<td>
							<div class="edit-buttons">
								<!-- Modify file name -->
								<form method='POST' action=''>
									<div class="input-group mb-2">
										<input class="form-control" type="text" name="newName" placeholder="New Name">
										<input type='hidden' name='modify' value='<?= $row['ID'] ?>'>
										<input class="btn btn-outline-warning" type='submit' value='Modify'>
									</div>
								</form>
								<!-- Delete file -->
								<form method='POST' action=''>
									<input type='hidden' name='delete' value='<?= $row['ID'] ?> '>
									<input class="btn btn-outline-danger" type='submit' value='Delete'
										onclick="return confirmDelete('<?= $row['ID'] ?>');">
								</form>
							</div>
							</td>
						</tr>
					<?php endwhile; ?>
				<?php else: ?>
					<tr>
						<td colspan="6">No files found</td>
										</tr>
				<?php endif; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<a href="index.php" class="btn btn-light m-4"><img src="bootstrap-icons/arrow-left-short.svg" class="icon">File
		Sharing</a>
</body>

</html>

<script>
	// JavaScript function to confirm file deletion
	function confirmDelete(fileName) {
		return confirm("Are you sure you want to delete the file: " + fileName + " ?");
	}
</script>