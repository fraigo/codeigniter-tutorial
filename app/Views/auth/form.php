    <?php
    helper('form');
    echo form_open('/auth/login', ["class" => "form-signin"]);
    ?>
    <h2 class="text-center">Login</h2>
    <?php
    echo form_input([
        'label' => null,
        'name'      => 'email',
        'placeholder' => 'Email Address',
        'class'     => 'form-control',
        'id'        => 'email',
        'type'      => 'email',
        'value'     => @$_REQUEST["email"]
    ]);
    echo form_input([
        'type' => 'password',
        'label' => null,
        'class'     => 'form-control',
        'name'      => 'password',
        'placeholder' => 'Password',
        'id'        => 'password',
        'value'     => '',
    ]);
    echo form_errors($errors);
    echo form_item([
        'type' => 'submit',
        'value' => 'Log In',
        'class' => 'btn btn-primary col-12'
    ]);
    echo form_close();
    ?>