<!doctype html>
<html lang="en" class="no-js">
<head>
  <meta charset="utf-8">
  
  <!-- www.phpied.com/conditional-comments-block-downloads/ -->
  <!--[if IE]><![endif]-->

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  
  <title><?php echo $title_for_layout;?></title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!--  Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width; initial-scale=1.0">
</head>
<body>
  <div id="container">
    <header id="header">
	<?php echo $this->element('header');?>
    </header>
    
    <div id="content" class="clearfix">
    <h1>Oops there was an error</h1>
	<?php echo $content_for_layout ?>
    </div>
    
    <footer id="footer">
	<?php echo $this->element('footer');?>
    </footer>
  </div>
<?php //echo $this->element('sql_dump'); ?>  
</body>
</html>