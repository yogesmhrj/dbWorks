<?php

include __DIR__."/layouts/header.php"; ?>
<div class="row">
	<div class="col-sm-12">
		<div class="compare-header">
			<h3><mark><?= $database1 ?></mark> VS <mark><?= $database2 ?></mark></h1>
		</div>
		<hr>
		<h5><?= $message ?><h5>
	</div>
</div>	
<div class="clearfix"></div>
<div class="row">
	<?php foreach ($tableDifferences as $name => $columns) : ?>
			<div class="col-sm-5 border window" style="margin-right: 5px; margin-top: 10px;">
	            <p><b><?= $name ?></b> (<?= $columns['descp'] ?>)</p>
	            <table class="table table-striped table-sm" style="table-layout: fixed;">
	            	<thead class="thead-light">
	                	<tr class="head">
	                		<td class="col-md-5">Column Name</td>
	                		<td class="col-md-6 col">Column Type</td>
	                		<td class="col-md-1 log">Log</td>
	                	</tr>
	                </thead>
	                <tbody>
	                <?php foreach ($columns as $key => $column) { 
	                	if($key != "descp" && $key != 'ddl') : ?>
	                    <tr>
	                    	<td class="col-md-5"><?= $column[0] ?></td>
	                    	<td class="col-md-6 col"><?= $column[1] ?></td>
	                    	<td class="col-md-1 log"><?= extractFromArray('decsp',$column,""); ?></td>
	                    </tr>
	                <?php endif; }?>
	                </tbody>
	            </table>
            </div>
            <div class="col-sm-6 border ddl-window" style="margin-top: 10px;">
        		<p><b>DDL</b></p>
        		<p> 
        			<?= nl2br(extractFromArray('ddl',$columns,"")); ?>
        		</p>
        	</div>
    <?php  endforeach; ?>
</div>

<div class="row">
	<div class="col-sm-6">
		<p><?= count($skippedTables)?> Skipped Tables</p>
		<ul>
			<li>
		<?php echo implode("<li>",$skippedTables); ?>
		</ul>
	</div>
</div>


<?php include __DIR__."/layouts/footer.php";