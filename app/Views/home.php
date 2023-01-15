<header>
    <div class="container">
    Main Page Content
    <br>
    Current User: <?php echo @session('auth')['email'] ?>
    <br>
    Profile: <?php echo @session('profile')['name'] ?>
    </div>
</header>

