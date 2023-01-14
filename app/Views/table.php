<header class="header">
    <div class="container-fluid">
    <div class="d-flex justify-content-between mb-4">
        <h2><?=$title?></h2>
        <div>
            <button type="button" class="btn" onclick="document.location=this.getAttribute('data-href')" data-href="./new">New</button>
        </div>
    </div>
    <?php
    helper(['html','form']);
    echo form_filters($filters);
    echo $pager->links($pager_group); // $pager->simpleLinks($pager_group);
    echo htmlTable($items,$columns,["width"=>"100%","class"=>"table table-striped table-sm"]);
    if (!$items) echo "<div class=\"\">No records found</div>";
    ?>
    </div>
</header>
