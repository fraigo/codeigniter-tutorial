    <?php
    helper(['form','cookie']);
    echo form_open('/auth/reset/'.$token, ["class" => "form-signin"]);
    ?>
    <h2 class="text-center">Recover your account</h2>
    <?php if (@$success){ ?>
        <div class="alert alert-success p-1 mt-1 text-center"><?=$success?></div>
        <?php 
            echo form_input([
                'type' => 'button',
                'value' => 'Log In',
                'class' => 'btn btn-primary col-12',
                'onclick' => "document.location.replace('/auth/login')"
            ]);
        ?>
    <?php  } else if ($user){ ?>
        <p class="text-center">
            Welcome back, <?=$user["name"]?>.
            Create a new password for your account.
        </p>
        <?php
        echo form_input([
            'name'      => 'token',
            'type'      => 'hidden',
            'value'     => @$token
        ]);
        ?>
        <?php
        echo form_input([
            'label' => null,
            'name'      => 'new_password',
            'type'      => 'password',
            'placeholder' => 'New Password',
            'class'     => 'form-control top-control',
            'id'        => 'new_password',
            'onfocus'   => 'this.select()',
            'value'     => set_value('new_password','')
        ]);
        echo form_input([
            'label' => null,
            'name'      => 'repeat_password',
            'type'      => 'password',
            'placeholder' => 'Repeat Password',
            'class'     => 'form-control bottom-control',
            'id'        => 'repeat_password',
            'onfocus'   => 'this.select()',
            'value'     => set_value('repeat_password','')
        ]);
        ?>
        <script>document.getElementById('password').focus();</script>
        <?php
        echo form_errors($errors);
        echo form_input([
            'type' => 'submit',
            'value' => 'Set password',
            'class' => 'btn btn-primary col-12'
        ]);
        ?>
        <p class="text-center mt-4">
            <a href="/auth/login">Back to Log In</a>
        </p>
    <?php } else { ?>
        <p class="text-center alert alert-warning ">
            The recovery token is invalid or has expired.
        </p>
        <p class="text-center">
            <a href="/auth/recover">Recover your account</a>
        </p>
        <p class="text-center mt-4">
            <a href="/auth/login">Back to Log In</a>
        </p>
    <?php } ?>
    <?php
    echo form_close();
    ?>
