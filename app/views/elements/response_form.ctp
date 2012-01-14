<?php 
if (empty($formTitle))
{
  $formTitle = "RSVP";
}
if (empty($formId))
{
  $formId = "ResponseEdit";
}
if (empty($divId))
{
  $divId = 'ResponseForm';
}
if (empty($responseTypes))
{
  $responseTypes = getResponseTypeList();
}
?>

<div id='<?= $divId; ?>' title='<?= $formTitle; ?>'>
	<?php echo $form->create('Response', array('id' => $formId)); ?>
  	<fieldset>
  		<?php echo $form->input('Response.event_id', array('type' => 'hidden', 'value' => $event['id'])); ?>
  		<?php echo $form->input('Response.player_id', array('type' => 'hidden', 'value' => $player['id'])); ?>
  		<?php echo $form->input('Response.response_type_id', array('options' => $responseTypes)); ?>
  		<?php echo $form->input('Response.comment', array('type' => 'textarea', 'cols' => '3')); ?>
  		<?php if (!empty($redirect)): ?>
  		<?php echo $form->input('redirect', array('type' => 'hidden', 'value' => $redirect)); ?>
  		<?php endif; ?>
  	</fieldset>
	<?php echo $form->end(); ?>
</div>
