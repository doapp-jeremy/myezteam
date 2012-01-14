<div class="users">
	<h2>Login with Facebook</h2>
	<?php echo $this->element('fb_login'); ?>

	<?php if (true): ?>	
	<div style="margin-top: 20px">
	or  ....<span style="cursor:pointer;" onclick="alert('onclick');">use regular login</span>
	</div>
	<div id="regularLogin" style="display: block;">
  	<?php echo $form->create('User'); ?>
  		<?php echo $form->input('User.email'); ?>
  		<?php echo $form->input('User.password', array('label' => 'Password', 'type' => 'password')); ?>
  	<?php echo $form->end('Login'); ?>
  	<?php echo $this->Session->flash('auth'); ?>
	</div>
	<?php endif; ?>
</div>