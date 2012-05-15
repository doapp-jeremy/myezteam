<!DOCTYPE html>
<?= $facebook->html(); ?>
<html>
	<head>
  	<title><?php echo $title_for_layout;?></title> 
  	<meta name="viewport" content="width=device-width, initial-scale=1"> 
  	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
  	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
  	<script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js"></script>
	</head>
	<body> 

		<div data-role="page">
      <div id="content" class="clearfix">
      	<?php echo $session->flash(); ?>
      	<?php echo $session->flash('auth');?>
  	    <?php echo $content_for_layout ?>
      </div>
		</div>

		<?= $facebook->init(); ?>
	</body>
</html>
