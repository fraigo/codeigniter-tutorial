<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo @$title?:(getenv('app.name')?:'My App') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if (@$description && !@$meta['description']) { ?>
      <meta name="description" content="<?=$description?>" >
    <?php } ?>
    <?php if (@$meta) foreach($meta as $name=>$content) { ?>
      <meta name="<?=$name?>" content="<?=$content?>" >
    <?php } ?>
    <link rel="shortcut icon" type="image/png" href="<?=getenv('app.icon')?:'/img/icon.png'?>"/>
    <link rel="stylesheet" href="/css/bootstrap.min.css" >
    <?= $this->renderSection('head') ?>
</head>
<body>
<?= $this->renderSection('header') ?>
<?= $this->renderSection('content') ?>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="/js/jquery-3.2.1.slim.min.js" ></script>
    <script src="/js/popper.min.js" ></script>
    <script src="/js/bootstrap.min.js" ></script>
    <script src="/js/moment.js" ></script>
  </body>
</html>