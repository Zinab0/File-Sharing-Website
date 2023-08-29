<?php
include_once 'config.php';
$categories = [];
$sql_categories = "SELECT * FROM category";
$result = $conn->query($sql_categories);
if ($result->num_rows > 0) {
	while ($row = $result->fetch_assoc()) {
		$categories[$row['ID']] = $row['category_name'];
	}
}
$uploadPath = 'uploads/';
$output = "";

if (isset($_POST['submit'])) {
	if ($_FILES['file']['name'] != "") {
		$file = $_FILES['file'];
		$FileSize = $file['size'];
		$FileCategory = $_POST['category'];
		$date = date("Y-m-d H:i:s");
		$file_ID = time();
		$FileType = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

		// Alternatively, if you want to get the file type without the dot (e.g., 'jpg' instead of '.jpg'):
		$FileType = ltrim($FileType, '.');

		$File_name = $_FILES['file']['name']; // Original filename with extension

		// Extract only the filename without the extension
		$File_name = pathinfo($File_name, PATHINFO_FILENAME);

		// Check if a file with the same name already exists
		$existingFilesQuery = "SELECT * FROM sharedfiles WHERE file_name = '$File_name'";
		$existingFilesResult = $conn->query($existingFilesQuery);

		if ($existingFilesResult->num_rows > 0) {
			// A file with the same name already exists
			$output = '<div class="alert alert-danger" role="alert">A file with the same name already exists. Do you want to replace it?</div>';
			$output .= '<button onclick="replaceFile(\'' . $File_name . '\')">Replace</button>';

			if (isset($_POST['replace_file_name'])) {
				// Delete the existing file from the server
				$existingFilePath = $uploadPath . $_POST['replace_file_name'];
				unlink($existingFilePath);

				// Delete the existing file record from the database
				$deleteExistingQuery = "DELETE FROM sharedfiles WHERE file_name = '$File_name'";
				$deleteExistingResult = $conn->query($deleteExistingQuery);

				if ($deleteExistingResult) {
					// Continue with the upload process
					$targetPath = $uploadPath . $File_name . '_' . $file_ID . '.' . $FileType;
					move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

					// Insert file details into the database
					$sql = "INSERT INTO sharedfiles (file_name, file_type, file_size, file_category, date_created, file_path)
				VALUES ('$File_name', '$FileType', '$FileSize', '$FileCategory', '$date', '$targetPath')";
					// Execute the query
					$rs = mysqli_query($conn, $sql);

					// Check if the query was successful
					if ($rs) {
						$output = '<div class="alert alert-success" role="alert">
					  Replaced the file successfully!
					</div>';
					} else {
						$output = '<div class="alert alert-danger" role="alert">Error:  ' . mysqli_error($conn) . '</div>';
					}
				} else {
					$output = '<div class="alert alert-danger" role="alert">Error deleting existing file.</div>';
				}
			}
		} else {
			// Upload file to the specified path
			$targetPath = $uploadPath . $File_name . '_' . $file_ID . '.' . $FileType;
			move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

			// Insert file details into the database
			$sql = "INSERT INTO sharedfiles (file_name, file_type, file_size, file_category, date_created, file_path)
				VALUES ('$File_name', '$FileType', '$FileSize', '$FileCategory', '$date', '$targetPath')";
			// Execute the query
			$rs = mysqli_query($conn, $sql);

			// Check if the query was successful
			if ($rs) {
				$output = '<div class="alert alert-success" role="alert">
					  File uploaded successfully!
					</div>';
			} else {
				$output = '<div class="alert alert-danger" role="alert">Error:  ' . mysqli_error($conn) . '</div>';
			}
		}
	} else {
		$output = '<div class="alert alert-danger" role="alert">Please select a file first</div>';
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>File Upload</title>
	<?php include_once '__head.php' ?>
</head>

<body>
	<?php include '__body.php'; ?>
	<div id="filebody">
		<div class="wrapper">
			<div class="div_fileupload">
				<header>File Uploader</header>
				<!-- FORM -->
				<form action="" method="post" enctype="multipart/form-data">
					<div class="upload-icon">
						<i class="fas fa-cloud-upload-alt"></i>
						<p>Browse File to Upload</p>
					</div>

					<div class="input_files">
						<input class="file-input" type="file" name="file" multiple="false">
					</div>
					<div class="category_select">
						<label for="category">File category:</label>
						<select name="category" id="category" class="form-select mt-1">
							<?php foreach ($categories as $k => $v): ?>
								<option value="<?= $k ?>"><?= $v ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<section class="uploaded-area"></section>
					<?= $output ?>
					<div class="error"></div>
					<input class="btnn" name="submit" type="submit" value="Upload">
					<small>Please upload one file at a time</small>
					<input hidden name="replace_file_name" id="replace_file_name" value="">

				</form>
			</div>
			<!-- END FORM -->
			<section class="progress-area"></section>

		</div>
		<script src="JS/script.js"></script>
	</div>
</body>
<script>
	function replaceFile(fileName) {
		if (confirm("A file with the same name already exists. Are you sure you want to replace it?")) {
			// Proceed with file upload
			document.querySelector('form').submit();
		}
	}
</script>