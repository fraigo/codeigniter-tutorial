<?php helper(['auth','module']); ?>
<?= $this->extend('layouts/default') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="/css/style.css" >
<script src="/js/alpine.js" defer></script>
<?= $this->endSection() ?>
<?= $this->section('header') ?>
<nav class="navbar container navbar-expand d-flex flex-column flex-sm-row align-items-sm-center border-bottom box-shadow mb-3">
  <a class="navbar-brand" >
    <?php if (getenv('app.icon')) { ?><img class="navbar-icon" src="<?=getenv('app.icon')?>" height="32" align="middle" >&nbsp;<?php } ?>
    <?=getenv('app.name')?:'My App'?>
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
    <ul class="navbar-nav ml-auto mr-auto mr-sm-1">
      <li class="nav-item active">
        <a class="nav-link text-dark" href="/">Home</a>
      </li>
      <?php if(logged_in()) { ?>
      <li class="nav-item active">
        <a class="nav-link text-dark" href="/profile">My Profile</a>
      </li>
      <?php } ?>
      <?php if(module_access("users",1)) { ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-dark" href="#" id="dropdown03" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</a>
        <div class="dropdown-menu" aria-labelledby="dropdown03">
        <?php foreach (module_list() as $route => $label) { ?>
          <?php if(module_access($route,1)) { ?>
            <a class="dropdown-item" href="/<?=$route?>"><?=$label?></a>
          <?php } ?>
        <?php } ?>
        </div>
      </li>
      <?php } ?>
      <li class="ml-2">
      <?php if(logged_in()) { ?>
        <a class="btn menu-login btn-outline-primary" href="/auth/logout">Log Out</a>
      <?php } else { ?>
        <a class="btn menu-login btn-outline-primary" href="/auth/login">Log In</a>
      <?php } ?>
      </li>
    </ul>
</nav>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?= $content ?>
<?= $this->endSection() ?>