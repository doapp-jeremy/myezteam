<div data-role="header">
	<h1><?= $team['Team']['name']; ?></h1>
</div>

  <div data-role="navbar">
  	<ul>
  		<li><a href='/'>My Teams</a></li>
  	</ul>
  </div>
<div data-role="content">
	
	<div>
	  <?php if (empty($team['UpcomingEvent'])): ?>
  		There are no upcoming events
  	<?php else: ?>
    	<?php 
    	$event = $team['UpcomingEvent'][0];
    	$eventStart = date_create($event['Event']['start']);
    	$eventTitle = "{$event['Event']['name']}: {$eventStart->format('m/d g:ia')}";
    	?>
			<a data-role="button" href="/Events/view/<?= $event['Event']['id']?>"><?= $eventTitle; ?></a>
		<?php endif; ?>
	</div>

	<div data-role="collapsible" data-theme="b" data-content-theme="c">
		<h3>Upcoming Events (<?= count($team['UpcomingEvent']); ?>)</h3>
  	<?php if (empty($team['UpcomingEvent'])): ?>
  	There are no upcoming events
  	<?php else: ?>
    <ul data-role="listview" data-inset="true" data-filter="true">
    	<?php foreach ($team['UpcomingEvent'] as $event): ?>
    	<?php 
    	$eventStart = date_create($event['Event']['start']);
    	$eventTitle = "{$event['Event']['name']}: {$eventStart->format('l, m/d g:ia')}";
    	?>
    	<li>
    		<a href="/Events/view/<?= $event['Event']['id']?>"><?= $eventTitle; ?></a>
    	</li>
    	<?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>
  
  <div data-role="collapsible" data-theme="b" data-content-theme="c">
  	<h3>Players</h3>
  	
  	<?php foreach ($team['Player'] as $player): ?>
  	<div data-role="collapsible" data-theme="c" data-content-theme="c">
  		<h3><?= $player['User']['display_name']; ?></h3>
  		<div>
  			<b>First Name</b>: <?= $player['User']['first_name']; ?>
  		</div>
  		<div>
  			<b>Last Name</b>: <?= $player['User']['last_name']; ?>
  		</div>
  		<div>
  			<b>Email</b>: <?= $player['User']['email']; ?>
  		</div>
  	</div>
  	<?php endforeach; ?>
  </div>
</div>
