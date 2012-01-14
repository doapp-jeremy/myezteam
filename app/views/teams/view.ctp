<?php $html->script(array('teams/view', 'teams/facebook', 'rsvp_form'), array('inline'=>false)); ?>
<?php 
$nextEvent = getNextEvent($team);
$yourPlayer = getYourPlayer($team['Player'], $session->read('Auth.User.id'));
$rsvpFormDivId = 'changeRsvp';
$rsvpFormId = 'ReponseEdit';
$rsvpFormTitle = 'RSVP';
if ($nextEvent)
{
  $rsvpFormTitle = "RSVP for {$nextEvent['Event']['name']}";
}
?>
<div class="teams view">
<?php if ($nextEvent): ?>
  <?php echo $this->element('event_responses', array('event' => $nextEvent)); ?>
  <?php echo $this->element('team_events_list', array('eventsTitle' => 'Upcoming Events', 'events' => $team['UpcomingEvent'])); ?>
<?php else: ?>
	<h3>No upcoming events</h3>
<?php endif; ?>
<?php echo $this->element('team_players_list'); ?>
</div>

<div class="actions">
	<ul>
		<li><?php echo $html->link('Add Event', array('controller' => 'Events', 'action' => 'add')); ?></li>
		<li><?php echo $html->link('Add Player', array('controller' => 'Players', 'action' => 'add')); ?></li>
		<li><?php echo $html->link('Edit Team', array('controller' => 'Teams', 'action' => 'edit', $team['Team']['id'])); ?></li>
	</ul>
</div>

<?php if (false): ?>
<div class="actions">
	<h3><?php echo $team['Team']['name']; ?></h3>
	<div><?php echo $team['Team']['type']; ?></div>
	<div class="clear"></div>
	<div>
		<?php echo $this->element("team_fb_pic"); ?>
	</div>
	<div class="clear"></div>
	<?php if ($nextEvent): ?>
	<div>
		<h4><?php echo $nextEvent['Event']['name']; ?></h4>
		<div>
			<span><?php echo date_create($nextEvent['Event']['start'])->format('M j'); ?></span>
			<span style="color:#999999; margin-left: 5px">
			<?php echo date_create($nextEvent['Event']['start'])->format('g:ia'); ?>
			-
	    <?php echo date_create($nextEvent['Event']['end'])->format('g:ia'); ?>
	    </span>
		</div>
	</div>
	<?php if ($yourPlayer): ?>
	<?php
	  $myRSVP = getMyRSVP($nextEvent, $yourPlayer);
	?>
	<div>
		<?php echo $html->link(Inflector::humanize($myRSVP['name']), array('controller' => 'Responses', 'action' => 'edit'), array('onclick' => "$('#{$rsvpFormDivId}').dialog('open'); return false;", 'style' => "color: #{$myRSVP['color']};")); ?>
	</div>
	<?php endif; ?>
	<div>
		<?php echo $this->element('response_pie_chart', array('chartSize' => '240x96', 'responses' => $nextEvent['ResponseByResponseType'], 'default' => $nextEvent['DefaultResponse']['name'])); ?>
	</div>
	<?php elseif(isTeamManager($team['Team']['id'], $session->read('teamsManagedKey'))): ?>
		New Event
	<?php endif; ?>
</div>
<div class="clear"></div>
<div>
  <?php echo $this->element('team_players_list'); ?>
</div>
<?php endif; ?>

<?php echo $this->element('team_fb_link_form'); ?>
	<?php if ($nextEvent): ?>
	  <?php echo $this->element('response_form', array('divId' => $rsvpFormDivId, 'formId' => $rsvpFormId, 'formTitle' => $rsvpFormTitle, 'event' => $nextEvent['Event'], 'player' => $yourPlayer)); ?>
	<?php endif; ?>
  <?php 
  $codeBlock = "
  var rsvpFormDivId = '{$rsvpFormDivId}';
  var rsvpFormId = '$rsvpFormId';
  ";
  $this->Javascript->codeBlock($codeBlock, array('inline' => false));
  ?>

