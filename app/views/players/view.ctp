<div class="players view">
<h2><?php  __('Player');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $player['Player']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('User'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($player['User']['email'], array('controller' => 'users', 'action' => 'view', $player['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Player Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($player['PlayerType']['name'], array('controller' => 'player_types', 'action' => 'view', $player['PlayerType']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Team'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($player['Team']['name'], array('controller' => 'teams', 'action' => 'view', $player['Team']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $player['Player']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $player['Player']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Player', true), array('action' => 'edit', $player['Player']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Player', true), array('action' => 'delete', $player['Player']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $player['Player']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Players', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Player', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Player Types', true), array('controller' => 'player_types', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Player Type', true), array('controller' => 'player_types', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Teams', true), array('controller' => 'teams', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Team', true), array('controller' => 'teams', 'action' => 'add')); ?> </li>
	</ul>
</div>
