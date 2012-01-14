<div class="events">
	<h3><?php echo $event['Event']['name']; ?></h3>
		<div>
			<span><?php echo date_create($event['Event']['start'])->format('M j'); ?></span>
			<span style="color:#999999; margin-left: 5px">
			<?php echo date_create($event['Event']['start'])->format('g:ia'); ?>
			-
	    <?php echo date_create($event['Event']['end'])->format('g:ia'); ?>
	    </span>
		</div>
	<?php if (!empty($event['Response'])):?>
	  <?php echo $this->element('responses_jquery_table', array('responses' => $event['Response'])); ?>
	<?php endif; ?>
</div>


