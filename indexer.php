<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="./style.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500&display=swap" rel="stylesheet" />
    <script src='js/jquery-3.4.1.min.js'></script>
    <script src='js/indexer.js'></script>
    <title>Indexer</title>
</head>

<body>
    <header>
        <div class="logo-container">
            <img src="./img/logo.svg" alt="logo" srcset="" />
            <h4 class="logo">Internet & Web Technologies</h4>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a class="nav-link" href="index.html">Home</a></li>
                <li>
                    <div class="dropdown">
                        <a class="nav-link" href="#">Course</a>
                        <div class="dropdown-content">
                            <a href="https://learn.zybooks.com/zybook/CUNYCSCI355TeitelmanFall2019" target="_blank">Zybooks</a>
                            <a href="https://app.tophat.com/e/972963/lecture/" target="_blank">TopHat</a>
                            <a href="https://tinyurl.com/CSCI355-Summer2019" target=" _blank">Google Drive</a>
                            <a href="https://www.w3schools.com/" target=" _blank">W3Schools</a>
                        </div>
                    </div>
                </li>
                <li><a class="nav-link" href="browser.html">Browser</a></li>
                <li><a class="nav-link" href="about.html">About</a></li>
                <li><a class="nav-link" href="search.php">Search</a></li>
                <li>
                    <div class="dropdown">
                        <a class="nav-link active" href="#">Phase 2</a>
                        <div class="dropdown-content">
                            <a href="indexer.php">Indexer</a>
                            <a href="custom_search.php">Custom Search</a>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="header-text">
            <h4>URL Crawler / Indexer</h4>
        </div>
        <form class="box" id="indexer-form" method="POST" enctype="multipart/form-data">
            <input class="item" id="input_index_url" type="input" name="url_to_index" />
            <input class="item" id="button-submit" type="submit" name="button_index" value="Index URL" />
        </form>
        <div class="center-wrapper" id="indexer-loading">
            <div class="lds-ellipsis center-item">
                <div></div>
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
        <div id="results"></div>
    </main>
</body>

</html>
