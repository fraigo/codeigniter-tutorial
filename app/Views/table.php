<header class="header">
    <div class="<?=@$container?:'container'?>">
    <div class="d-flex justify-content-between mb-4">
        <h2><?=$title?></h2>
        <div>
            <?php if (module_access($route,3) && $newUrl) { ?>
            <button type="button" class="btn btn-secondary" onclick="document.location=this.getAttribute('data-href')" data-href="<?=$newUrl?>">New</button>
            <?php } ?>
        </div>
    </div>
    <?php
    helper(['html','form']);
    echo form_filters($filters);
    if (@$pager)
    echo $pager->links($pager_group); // $pager->simpleLinks($pager_group);
    echo htmlTable($items,$columns,["width"=>"100%","class"=>"table table-striped table-sm"]);
    if (!$items) echo "<div class=\"\">No records found</div>";
    ?>
    </div>
</header>
