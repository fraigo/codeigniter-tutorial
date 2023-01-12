<?= $this->extend('layouts/default') ?>
<?= $this->section('head') ?>
<link rel="stylesheet" href="/css/style.css" >
<link rel="stylesheet" href="/css/login.css" >
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?= $content ?>
<?= $this->endSection() ?>