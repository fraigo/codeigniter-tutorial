<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
    <h2>Login</h2>
    <?php
    helper('form');
    echo form_open('/auth/login', []);
    echo form_item([
        'label' => 'Email',
        'name'      => 'email',
        'id'        => 'email',
        'value'     => @$_REQUEST["email"],
        'errors'    => @$errors['email']
    ]);
    echo form_item([
        'type' => 'password',
        'label' => 'Password',
        'name'      => 'password',
        'id'        => 'password',
        'value'     => '',
        'errors'    => @$errors['password']
    ]);
    echo form_item([
        'type' => 'submit',
        'value' => 'Log In',
    ]);
    echo form_close();
    ?>
<?= $this->endSection() ?>
