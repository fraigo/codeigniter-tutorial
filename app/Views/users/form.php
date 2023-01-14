<header class="header">
    <div class="container">
    <?php
    helper(['html','form']);
    echo form_open(isset($item["id"])?'/users/edit/'.$item["id"]:'/users/new', []);
    ?>
    <div class="d-flex justify-content-between mb-4">
        <h2>Edit User</h2>
        <div>
            <button type="button" class="btn" onclick="window.history.back(-1)" >Back</button>
            <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </div>
    <?php
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
        'label' => 'Type',
        'type'      => 'user_type',
        'name'      => 'user_type',
        'id'        => 'user_type',
        'options'   => $userTypes,
        'selected' => [set_value('user_type',@$item["user_type"])],
        'errors'    => @$errors['user_type']
    ],"form_dropdown");
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
    echo form_close();
?>
    </div>
</header>