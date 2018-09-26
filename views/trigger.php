<?php include __DIR__."/layouts/header.php"; ?>
<div class="row">
	<div class="col-sm-6">
		<div class="card">
			<div class="card-header">
			    Input Trigger Parameters (DB : <?= $database ?>)
			 </div>
			<div class="card-block form-group">
				<form action="" method="POST">
					<div class="row">

						<div class="col-sm-6 form-group">
							<input type="text" name="m" placeholder="Module Name" value="<?= isset($MODULE)?$MODULE:'' ?>">
						</div>
						<div class="col-sm-6 form-group">
							<input type="text" name="t" placeholder="Table Name" value="<?= isset($TABLE_NAME)?$TABLE_NAME:'' ?>">
						</div>
						<div class="col-sm-6 form-group">
							<input type="text" name="pk" placeholder="Primary Key" value="<?= isset($PRIMARY_KEY)?$PRIMARY_KEY:'' ?>">
						</div>
						<div class="col-sm-6 form-group">
							<input type="text" name="sd" placeholder="Softdelete Enabled" value="false">
						</div>

						<div class="col-sm-6 form-group">
							<button type="submit" class="btn btn-sm btn-success">Generate Triggers</button>
						</div>	
						
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php 
if(strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
?>

<div class="row">
    <div class="col-sm-12">
        <div class="compare-header">
            <h3>Triggers for table : <mark><?= $TABLE_NAME ?></h1>
        </div>
        <hr>
    </div>
</div>  
<div class="clearfix"></div>

<div class="row">
           
    <div class="col-sm-8 border ddl-window" style="margin-top: 10px;">
		<p><b>Insert Triggers</b></p>
		<p> 
			<?= getInsertTrigger(); ?>
		</p>
	</div>

	<div class="col-sm-8 border ddl-window" style="margin-top: 10px;">
		<p><b>Update Triggers</b></p>
		<p> 
			<?= getUpdateTrigger(); ?>
		</p>
	</div>

	<div class="col-sm-8 border ddl-window" style="margin-top: 10px;">
		<p><b>Delete Triggers</b></p>
		<p> 
			<?= getDeleteTrigger(); ?>
		</p>
	</div>

	<div class="col-sm-8 border ddl-window" style="margin-top: 10px;">
		<p><b>Modal Fillables</b></p>
		<p> 
			<?= getFillables(); ?>
		</p>
	</div>
</div>

<?php } ?>

<?php include __DIR__."/layouts/footer.php";