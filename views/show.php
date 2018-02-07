<?php include __DIR__."/layouts/header.php"; ?>

<div class="row">
    <div class="col-sm-12">
        <div class="compare-header">
            <h3>Show <mark><?= $database1 ?></h1>
        </div>
        <hr>
        <h5><?= $message ?><h5>
    </div>
</div>  
<div class="clearfix"></div>

<div class="row">
    <?php foreach ($tables as $name => $columns) : ?>
            <div class="col-sm-5 border window" style="margin-right: 5px; margin-top: 10px;">
                <p><b><?= $name ?></b></p>
                <table class="table table-striped table-sm" style="table-layout: fixed;">
                    <thead class="thead-light">
                        <tr class="head">
                            <td class="col-md-5">Column Name</td>
                            <td class="col-md-6 col">Column Type</td>
                            <td class="col-md-1 log">Extra</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($columns as $key => $column) { ?>
                        <tr>
                            <td class="col-md-5"><?= $column[0] ?></td>
                            <td class="col-md-6 col"><?= $column[1] ?></td>
                            <td class="col-md-1 log"><?= $column[2]; ?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>
    <?php  endforeach; ?>
</div>

<?php include __DIR__."/layouts/footer.php";