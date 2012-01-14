<div class="players form">
<?php echo $this->Form->create('Player');?>
	<fieldset>
 		<legend><?php __('Edit Player'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id');
		echo $this->Form->input('player_type_id');
		echo $this->Form->input('team_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Player.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Player.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Players', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Player Types', true), array('controller' => 'player_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Player Type', true), array('controller' => 'player_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Teams', true), array('controller' => 'teams', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Team', true), array('controller' => 'teams', 'action' => 'add')); ?> </li>
	</ul>
</div>