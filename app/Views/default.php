<?php helper('auth'); ?>
<?= $this->extend('layouts/default') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="/css/style.css" >
<?= $this->endSection() ?>
<?= $this->section('header') ?>
<nav class="navbar container navbar-expand d-flex flex-column flex-sm-row align-items-sm-center border-bottom box-shadow mb-3">
  <a class="navbar-brand" >My Web Application</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainMenu" aria-controls="mainMenu" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
    <ul class="navbar-nav ml-auto mr-auto mr-sm-1">
      <li class="nav-item active">
        <a class="nav-link text-dark" href="/">Home</a>
      </li>
      <?php if(session("auth")) { ?>
      <li class="nav-item active">
        <a class="nav-link text-dark" href="/auth/profile">My Profile</a>
      </li>
      <?php } ?>
      <?php if(module_access("users",1)) { ?>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-dark" href="#" id="dropdown03" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</a>
        <div class="dropdown-menu" aria-labelledby="dropdown03">
        <?php if(module_access("users",1)) { ?>
          <a class="dropdown-item" href="/users">Users</a>
        <?php } ?>
        <?php if(module_access("profiles",1)) { ?>
          <a class="dropdown-item" href="/usertypes">Profiles</a>
        <?php } ?>
        <?php if(module_access("permissions",1)) { ?>
          <a class="dropdown-item" href="/permissions">Permissions</a>
        <?php } ?>
        </div>
      </li>
      <?php } ?>
      <li class="ml-2">
      <?php if(session('auth')) { ?>
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