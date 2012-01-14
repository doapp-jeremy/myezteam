<?php echo $this->Form->create('Event');?>
	<fieldset>
 		<legend><?php __('Add Event'); ?></legend>
	  <?php
	  if (!empty($this->data['Event']['id']))
	  {
	    echo $this->Form->input('id');
	  }
	  if (isset($this->data['Event']['team_id']))
  	{
  	  echo $this->Form->input('team_id', array('type' => 'hidden'));
  	}
  	else
  	{
  	  echo $this->Form->input('team_id');
  	}
		echo $this->Form->input('date');
		echo $this->Form->input('time');
		echo $this->Form->input('name');
		echo $this->Form->input('description');
		echo $this->Form->input('location');
		echo $this->Form->input('default_response', array('options' => $defaultResponses));
		echo $this->Form->input('Team.facebook_group', array('type' => 'hidden'));
		echo $this->Form->input('facebook_event', array('type' => 'hidden'));
		echo $this->Form->input('google_calendar', array('type' => 'hidden'));
		echo $this->element('fb_form_inputs');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
	