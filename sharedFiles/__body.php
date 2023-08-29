<!--  -----------Navbar--------------- -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <img class="logo" src="./img/logo.png" alt="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- First page -->
                <li class="nav-item">
                    <a class="nav-link " aria-current="page" href="index.php">File Sharing</a>
                </li>
                <!-- File Upload -->
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="FileUploadPage.php">File Upload</a>
                </li>
                <!-- Categories -->
                <li class="nav-item">
                    <a class="nav-link" href="categoriesPage.php">Categories</a>
                </li>
            </ul>
            <!--  -----------Search Query--------------- -->
                <form action="searchpage.php" method="GET" class="d-flex ms-auto">
                    <input class="form-control  me-2" type="search" name="search" placeholder="Search for files">
                    <button type="submit" name="search2" class="btn btn-outline-secondary ">Search</button>
                </form>
        </div>
    </div>
</nav>