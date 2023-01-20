    <?php
    helper(['form','cookie']);
    echo form_open('/auth/recover', ["class" => "form-signin"]);
    ?>
    <h2 class="text-center">Recover your account</h2>
    <p class="text-center">
        Please enter the email associated with your account and we will send you an email with instructions to reset your password.
    </p>
    <?php if (@$success){ ?>
    <div class="alert alert-success p-1 mt-1 text-center"><?=$success?></div>
    <?php  } ?>
    <?php
    echo form_input([
        'label' => null,
        'name'      => 'email',
        'placeholder' => 'Email Address',
        'class'     => 'form-control',
        'id'        => 'email',
        'type'      => 'email',
        'onfocus'   => 'this.select()',
        'value'     => @$_REQUEST["email"]
    ]);
    echo form_errors($errors);
    echo form_item([
        'type' => 'submit',
        'value' => 'Request Password Reset',
        'class' => 'btn btn-primary col-12'
    ]);
    ?>
    <div class="text-center">
        <a href="/auth/login">Back to Log In</a>
    </div>
    <?
    echo form_close();
    ?>
