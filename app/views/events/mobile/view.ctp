<div data-role="header">
	<?php 
 	$eventStart = date_create($event['Event']['start']);
 	$eventTitle = "{$event['Event']['name']}";
	?>
	<h1><?= $eventTitle; ?></h1>
</div>

<div data-role="navbar">
	<ul>
		<li><a href='/'>My Teams</a></li>
		<li><a href='/Teams/view/<?= $event['Team']['id']; ?>'><?= $event['Team']['name']; ?></a></li>
	</ul>
</div>

<div data-role="content">
	<div>
		<b>When</b>: <?= date_create($event['Event']['start'])->format('l, m/d g:ia'); ?>
	</div>
	<div>
		<b>Location</b>: <?= $event['Event']['location']; ?>
	</div>
	<div>
		<b>Default Response</b>: <?= Inflector::humanize($event['DefaultResponse']['name']); ?>
	</div>
	<div>
		<b>Your Response</b>: <?= $myResponse; ?>
	</div>
	
	<?php foreach ($responsesByType as $responseByType => $responses): ?>
	<div data-role="collapsible" data-theme="b" data-content-theme="c">
		<h3><?= Inflector::humanize($responseByType); ?> (<?= count($responses); ?>)</h3>
		<?php foreach ($responses as $response): ?>
		<div>
			<?= $response['Player']['User']['display_name']; ?> at <?= date_create($response['created'])->format('g:ia \o\n n/d'); ?>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endforeach; ?>
</div>
