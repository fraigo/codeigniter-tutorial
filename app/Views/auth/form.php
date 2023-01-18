    <?php
    helper(['form','cookie']);
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
        'onfocus'   => 'this.select()',
        'value'     => @$_REQUEST["email"]?:get_cookie('remember_email')
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
    echo "<div class='d-flex align-items-center form-item'>".form_input([
        'type' => 'checkbox',
        'label' => null,
        'class'     => '',
        'name'      => 'remember',
        'id'        => 'remember',
        'value'     => '1'
    ],'',get_cookie('remember_email') ? 'checked' : '')
    ."&nbsp;<label for=remember >Remember me</label></div>";
    echo form_errors($errors);
    echo form_item([
        'type' => 'submit',
        'value' => 'Log In',
        'class' => 'btn btn-primary col-12'
    ]);
    echo form_close();
    ?>
