<?= $this->extend('layouts/default') ?>
<?= $this->section('content') ?>
<header class="header">
    <div class="container-fluid">
    <h2 class="mb-4">Users</h2>
    <?php
    helper(['html','form']);
    echo form_filters($filters);
    echo $pager->links($pager_group); // $pager->simpleLinks($pager_group);
    echo htmlTable($items,$columns,["width"=>"100%","class"=>"table table-striped table-sm"]);
    if (!$items) echo "<div class=\"\">No records found</div>";
    ?>
    </div>
</header>
<?= $this->endSection() ?>
