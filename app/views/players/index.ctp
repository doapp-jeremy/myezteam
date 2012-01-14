<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Team', true), array('action' => 'edit', $team['Team']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Team', true), array('action' => 'delete', $team['Team']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $team['Team']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Teams', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Team', true), array('action' => 'add')); ?> </li>
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
