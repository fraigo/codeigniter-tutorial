<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo @$title?:'My App' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico"/>
    <link rel="stylesheet" href="/css/style.css" >
</head>
<body>
<header>
    <div>My Web Application</div>
    <nav>
        <a href="/" >Home</a>
        <?php if(session('admin')) { ?>
            <a href="/users/" >Users</a>
        <?php } ?>
        <?php if(session('auth')) { ?>
            <a href="/auth/logout" >Logout</a>
        <?php } else { ?>
            <a href="/auth/login" >Login</a>
        <?php } ?>
    </nav>
</header>
<main>
    <?= $this->renderSection('content') ?>
</main>
<footer>
    Site Footer
</footer>
</body>
</html>