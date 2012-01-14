<div class="users">
	<h2>Enter your information to register, or enter in an existing email address to sync with Facebook.</h2>
	<?php echo $form->create('User'); ?>
		<?php echo $form->input('User.facebook_id', array('type' => 'hidden')); ?>
		<?php echo $form->input('User.email'); ?>
		<?php echo $form->input('User.legacy_password', array('label' => 'Password', 'type' => 'password')); ?>
		<?php echo $form->input('User.first_name'); ?>
		<?php echo $form->input('User.last_name'); ?>
	<?php echo $form->end('Register'); ?>
	<?php echo $this->Session->flash('auth'); ?>
</div>