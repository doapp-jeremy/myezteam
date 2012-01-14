<?php $html->script(array('teams/facebook'), array('inline'=>false)); ?>
<div class="teams index">
	<h2><?php __('Teams');?></h2>
	<?php echo $this->element('teams_jquery_table'); ?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Team', true), array('action' => 'add')); ?></li>
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

<?php echo $this->element('team_fb_link_form'); ?>
