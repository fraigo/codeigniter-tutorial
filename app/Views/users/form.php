<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
    <h2>User</h2>
    <?php
    helper(['html','form']);
    echo form_open('/users/edit/'.$item["id"], []);
    echo form_item([
        'label' => 'Name',
        'name'      => 'name',
        'id'        => 'name',
        'value'     => set_value('name',$item["name"]),
        'errors'    => @$errors['name']
    ]);
    echo form_item([
        'label' => 'Email',
        'name'      => 'email',
        'id'        => 'email',
        'value'     => set_value('email',$item["email"]),
        'errors'    => @$errors['email']
    ]);
    echo form_item([
        'type' => 'submit',
        'value' => 'Update',
    ]);
    echo form_close();
?>
<?= $this->endSection() ?>