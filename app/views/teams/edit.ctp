<div class="teams form">
<?php echo $this->Form->create('Team');?>
	<fieldset>
 		<legend><?php __('Edit Team'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('league_id');
		echo $this->Form->input('user_id');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('type');
		echo $this->Form->input('default_location');
		echo $this->Form->input('google_calendar');
		echo $this->Form->input('facebook_group');
		echo $this->Form->input('Manager');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Team.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Team.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Teams', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Leagues', true), array('controller' => 'leagues', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New League', true), array('controller' => 'leagues', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Creator', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Events', true), array('controller' => 'events', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Event', true), array('controller' => 'events', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Players', true), array('controller' => 'players', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Player', true), array('controller' => 'players', 'action' => 'add')); ?> </li>
	</ul>
</div>