<div class="users">
	<?php if ($this->data['edit_email']): ?>
		<h2>Enter an email address to send an activation email to.</h2>
	<?php elseif ($this->data['edit_password']): ?>
		<h2>Enter a password to activate your account</h2>
	<?php else: ?>
		<h2>Re-send an activation code</h2>
	<?php endif; ?>

	<?php echo $form->create('User'); ?>
		<?php echo $form->input('User.id'); ?>
		<?php echo $form->input('User.md5email', array('type' => 'hidden')); ?>
		<?php echo $form->input('User.activation', array('type' => 'hidden')); ?>
		<?php if ($this->data['edit_email']): ?>
		  <?php echo $form->input('User.email'); ?>
		<?php else: ?>
  		<?php echo $form->input('User.email', array('disabled' => 'disabled')); ?>
  		<?php if ($this->data['edit_password']): ?>
    		<?php echo $form->input('User.password'); ?>
    		<?php echo $form->input('User.confirm_password', array('label' => 'Confirm Password', 'type' => 'password')); ?>
  		<?php endif; ?>
		<?php endif; ?>
	<?php if ($this->data['edit_email'] || !$this->data['edit_password']): ?>
 		<?php echo $form->input('send_activation', array('type' => 'hidden', 'value' => '1')); ?>
 		<?php echo $form->end('Re-send activation'); ?>
 	<?php else: ?>
 	  <?php echo $form->end('Set Password'); ?>
 	<?php endif; ?>
 	<?php echo $this->Session->flash('auth'); ?>
</div>