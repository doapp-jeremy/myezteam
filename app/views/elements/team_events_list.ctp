<div class="events">
	<h3><?php __($eventsTitle);?></h3>
	<?php if (!empty($events)):?>
	  <?php echo $this->element('events_jquery_table', compact('events')); ?>
	<?php endif; ?>
</div>

