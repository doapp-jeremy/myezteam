<script type="text/javascript">
	<?php echo generateFbFriendJSArray($fbFriends); ?>
	<?php echo generateFriendJSArray($friends); ?>
</script>
<?php $html->script(array('players/list'), array('inline'=>false)); ?>
<div class="players">
	<h3><?php __('Players');?></h3>
	<div>
		<?php echo $html->link('add player', array('controller' => 'Players', 'action' => 'add'), array('onclick' => '$("#addPlayerForm").dialog("open"); return false; ')); ?>
	</div>
	<?php if (!empty($team['Player'])):?>
	  <?php echo $this->element('players_jquery_table', array('players' => $team['Player'])); ?>
	<?php endif; ?>
</div>
<?php echo $this->Form->input('User.first_name', array('type' => 'hidden')); ?>
<?php echo $this->Form->input('User.last_name', array('type' => 'hidden')); ?>
<form id="UserFacebookForm">
	<?php echo $this->Form->input('User.id', array('type' => 'hidden')); ?>
	<?php echo $this->Form->input('User.facebook_id', array('type' => 'hidden')); ?>
</form>

<div id="linkUserForm" title="Link to Facebook" style="display: none">
	<?php if (!$session->read('FB')): ?>
		<p>Please login to facebook first</p>
	<?php else: ?>
	<form>
  	<p id="linkUserFormText"></p>
  	<fieldset>
    	<div>
  			<p>Start typing your friends name to get a list</p>
    		<input type="text" id="friendSelectionLinkUser" class="text ui-widget-content ui-corner-all" />
    	</div>
    	<div id="friendPics">
    	</div>
  	</fieldset>
	</form>
	<?php endif; ?>
</div>
<div id="addPlayerForm" title="Add Player" style="display: none">
	<form id='AddPlayerForm'>
		<fieldset>
			<?php echo $form->input('Player.player_type'); ?>
			<?php echo $form->input('Player.team_id', array('type' => 'hidden', 'value' => $team['Team']['id'])); ?>
 			<p>Start typing your friends name to get a list</p>
   		<input type="text" id="friendSelectionAddPlayer" class="text ui-widget-content ui-corner-all" />
			<?php echo $form->input('Player.user_id', array('type' => 'hidden')); ?>
			<?php echo $form->input('User.email', array('id' => 'AddPlayerUserEmail')); ?>
			<?php echo $form->input('User.first_name', array('id' => 'AddPlayerUserFirstName')); ?>
			<?php echo $form->input('User.last_name', array('id' => 'AddPlayerUserLastName')); ?>
  	<?php if (!$session->read('FB')): ?>
  		<p>Please login to facebook to get a list of friends</p>
  	<?php else: ?>
    	<p id="linkUserFormText"></p>
    	<fieldset>
      	<div>
    			<p>Start typing your friends name to get a list</p>
      		<input type="text" id="friendSelectionAddFbPlayer" class="friendSelection text ui-widget-content ui-corner-all" />
      	</div>
      	<div id="friendPics">
      	</div>
    	</fieldset>
  	<?php endif; ?>
		</fieldset>
	</form>
</div>
