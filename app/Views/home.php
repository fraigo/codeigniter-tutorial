<header>
    <div class="container">
    Main Page Content
    <br>
    Current User: <?php echo @current_user()['email'] ?>
    <br>
    Profile: <?php echo @current_user()['profile']['name'] ?>
    </div>
</header>
