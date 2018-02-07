<?php include __DIR__."/../../app.php"; ?>
<!DOCTYPE html>
<html>
<head>
	<title>Table Comparisons</title>
	<link rel="stylesheet" type="text/css" href="<?= APP_URL ?>/public/assets/css/bootstrap.min.css" />
</head>
<style type="text/css">
	
	body{
		font-size: 13px;
	}

	.col{
		overflow: hidden;
		word-wrap: break-word;
	}

	.log{
		width: 35% !important;
	}

	.window{
		padding: 15px 15px 10px;
	}

	.ddl-window {
		padding: 15px 15px 10px;
		background-color: #FFF8DC !important;
		border: 1px solid #E0DCBF !important;
	}

	.compare-header {
		padding: 15px 0px;
	}

</style>
<body>
	<div class="container">