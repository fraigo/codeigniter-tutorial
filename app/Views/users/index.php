<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico"/>
    <link rel="stylesheet" href="/css/style.css" >
</head>
<body>
<header>
    <div>My Web Application</div>
    <nav></nav>
</header>
<main>
    <h2>Users</h2>
    <?php
    helper('html');
    echo htmlTable($items,null,["border"=>1]);
    ?>
</main>
<footer>
    Site Footer
</footer>
</body>
</html>