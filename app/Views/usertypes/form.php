<header class="header">
    <div class="container">
    <?php
    helper(['html','form']);
    echo form_open(isset($item["id"])?'/usertypes/edit/'.$item["id"]:'/usertypes/new', []);
    ?>
    <div class="d-flex justify-content-between mb-4">
        <h2><?=$title?></h2>
        <div>
            <button type="button" class="btn" onclick="window.history.back(-1)" >Back</button>
            <button type="submit" class="btn btn-primary"><?=@$item["id"]?"Update":"Create"?></button>
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
        'label' => 'Access',
        'type'      => 'access',
        'name'      => 'access',
        'id'        => 'access',
        'options'   => $access_names,
        'selected' => [set_value('access',@$item["access"])],
        'errors'    => @$errors['access']
    ],"form_dropdown");
    echo form_close();
?>
    </div>
</header>