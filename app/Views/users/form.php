<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
    <h2>User</h2>
    <?php
    helper(['html','form']);
    echo form_open(isset($item["id"])?'/users/edit/'.$item["id"]:'/users/new', []);
    echo form_item([
        'label' => 'Name',
        'name'      => 'name',
        'id'        => 'name',
        'value'     => set_value('name',@$item["name"]),
        'errors'    => @$errors['name']
    ]);
    echo form_item([
        'label' => 'Email',
        'name'      => 'email',
        'id'        => 'email',
        'value'     => set_value('email',@$item["email"]),
        'errors'    => @$errors['email']
    ]);
    echo form_item([
        'label' => 'Password',
        'type'      => 'password',
        'name'      => 'password',
        'id'        => 'password',
        'value'     => set_value('password',@$item["password"]),
        'errors'    => @$errors['password']
    ]);
    echo form_item([
        'label' => 'Repeat Password',
        'type'      => 'password',
        'name'      => 'repeat_password',
        'id'        => 'repeat_password',
        'value'     => set_value('repeat_password',@$item["repeat_password"]),
        'errors'    => @$errors['repeat_password']
    ]);
    echo form_item([
        'type' => 'submit',
        'value' => 'Update',
    ]);
    echo form_close();
?>
<?= $this->endSection() ?>