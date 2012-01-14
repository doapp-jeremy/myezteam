<?php
//Make sure these are always added first into asset before your stuff in views
//$this->Html->script(array('plugins'),array('inline'=>false));
?>
<!doctype html>
<?= $facebook->html(); ?>
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

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <link rel="shortcut icon" href="/favicon.ico">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
                
  <?php   	  	
	$this->Html->css(array('style','cake.generic','custom'),NULL,array('inline'=>false));	
	
	//Thanks to http://github.com/mcurry/asset
	//There is a bug currently in the asset plugin where it will not just output css or just js
	//This is only a problem if debug is > 0, and only causes problems if your developing on an old browser (which ur not)
	echo $asset->scripts_for_layout('css');	

	//Don't include handheld in asset because it needs media="handheld"
	echo $this->Html->css(array('handheld'),null,array('media'=>'handheld'));
  ?>
  <?php // for some reason this did not work when it was at the end of the body for jquery.dataTables ?>
  <!-- Grab Google CDN's jQuery. fall back to local if necessary -->
  <!-- NOTE: dont use https unless you have to!  -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
  <script>!window.jQuery && document.write(unescape('%3Cscript src="/js/jquery-1.4.4.min.js"%3E%3C/script%3E'))</script>

  <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js"></script>
  <script>!window.jQuery && document.write(unescape('%3Cscript src="/js/jquery-ui-1.8.6.custom.min.js"%3E%3C/script%3E'))</script>
  
  <?php echo $this->Html->css(array('jquery-ui'),null); ?>
<!--  <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/themes/ui-lightness/jquery-ui.css">-->

  <!-- jquery overrides -->
  <?php $this->Html->script(array('jquery_overrides'), array('inline'=>false));?>
  
</head>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->

<!--[if lt IE 7 ]> <body class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <body class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <body class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <body class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <body> <!--<![endif]-->
  <div id="container">
    <header id="header">
	    <?php echo $this->element('header');?>
      <div style="float: right; padding-right: 20px;">
      	<?php if ($session->read('FB')): ?>
      		<?php $fbUser = $session->read('FB'); ?>
      		<div style="float: left"><?php echo $this->element('fb_pic', array('fbId' => $fbUser['Me']['id'])); ?></div>
      		<div class="login" style="float: right; padding-left: 20px;" id="logout"><?php echo $facebook->logout(array('redirect' => array('controller' => 'users', 'action' => 'logout'))); ?></div>
      	<?php else: ?>
      		<div class="login" id="login"><?php echo $this->element('fb_login'); ?></div>
      	<?php endif; ?>
      </div>
    </header>
    
    <div id="content" class="clearfix">
    	<?php echo $session->flash(); ?>
    	<?php echo $session->flash('auth');?>
	    <?php echo $content_for_layout ?>
    </div>
    
    <footer id="footer">
	    <?php echo $this->element('footer');?>
    </footer>
  </div> <!--! end of #container -->


  <!-- Javascript at the bottom for fast page loading -->

  <?php if (false): // for some reason this did not work when it was at the end of the body for jquery.dataTables ?>
  <!-- Grab Google CDN's jQuery. fall back to local if necessary -->
  <!-- NOTE: dont use https unless you have to!  -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
  <script>!window.jQuery && document.write(unescape('%3Cscript src="/js/jquery-1.4.4.min.js"%3E%3C/script%3E'))</script>
  <?php endif; ?>
 
  <?php 
	echo $asset->scripts_for_layout(array('js'));
	echo $asset->scripts_for_layout(array('codeblock'));
  ?>

  <div id="loadingDialog" title="Working...." style='display: none;'>
  	<p>Processing your request.  Please be patient.</p>
  </div>

<?php //echo $this->element('sql_dump'); ?>
<?= $facebook->init(); ?>
</body>
</html>