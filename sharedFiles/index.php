<!DOCTYPE html>
<html lang="en">
<!-- Purpose: Home page of the website -->

<head>
    <title>File Sharing</title>
    <?php include_once '__head.php'; ?>
</head>

<body>
    <?php
    include 'config.php';
    include '__body.php';

    # Get Last 5 documents from the db
    $categories = [];
    $sql_categories = "SELECT * FROM category";
    $result = $conn->query($sql_categories);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[$row['ID']] = $row['category_name'];
        }
    }
    ?>
    <!--  -----------page banner--------------- -->
    <div class="banner fade-in">
        <img class="bannerpic" src="./img/dna2.jpg">
    </div>


        
    <!--  -----------file types cards--------------- -->
    <div class="all-file-types">
        <?php if (count($categories) > 0): ?>
            <?php foreach ($categories as $k => $v): ?>
                <a class="categories" href="filelist.php?cat=<?= $k ?>">
                    <img src="img/folder.png">
                    <br>
                    <?= $v ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php

?>


    <!-- -----------cards--------------- -->
    <div class="rows">
        <?php if (count($categories) > 0): ?>
            <?php foreach ($categories as $k => $v): ?>
                <div class="column">
                    <div class="card ">
                        <!-- Card title with a link to filelist.php with the category parameter -->
                        <a class="card-title truncate" href="filelist.php?cat=<?= $k ?>">
                            <?= $v ?>
                        </a>
                        <?php
                        $sql = "SELECT * FROM sharedfiles WHERE file_category = '{$k}' ORDER BY date_created DESC LIMIT 5";
                        $result = $conn->query($sql);
                        ?>
                        <?php if ($result->num_rows > 0): ?>
                            <ul>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <li class="card-file truncate"><span>
                                        <!-- Display file type icon based on the file extension -->
                                        <?php if ($row['file_type'] == 'pdf'): ?>
                                            <img src="bootstrap-icons/filetype-pdf.svg" alt="pdf" style="color: #3e76d0;">
                                        <?php elseif ($row['file_type'] == 'doc' || $row['file_type'] == 'docx'): ?>
                                            <img src="bootstrap-icons/filetype-docx.svg" alt="doc" style="color: #c43636;">
                                        <?php elseif ($row['file_type'] == 'xls' || $row['file_type'] == 'xlsx'): ?>
                                            <img src="bootstrap-icons/filetype-xlsx.svg" alt="excel" style="color: #64966a;">
                                        <?php elseif ($row['file_type'] == 'csv' ): ?>
                                            <img src="bootstrap-icons/filetype-csv.svg" alt="csv" style="color: #64966a;">
                                        <?php elseif ($row['file_type'] == 'txt'): ?>
                                            <img src="bootstrap-icons/filetype-text.svg" alt="txt" style="color: #828487;">
                                        <?php else: ?>
                                            <img src="bootstrap-icons/file-earmark.svg" style="color: #828487;">
                                        <?php endif; ?>
                                    </span> <a href="<?= $row['file_path'] ?>" target="_blank"><?= $row['file_name'] ?></a> </li>
                                <?php endwhile; ?>

                            <?php else: ?>
                                <li> No files found </li>
                            <?php endif; ?>

                        </ul>
                        <!-- View all link to filelist.php with the category parameter -->
                        <a href="filelist.php?cat=<?= $k ?>">
                            <h5 class="view bottom"> view all </h5>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</body>
</html>