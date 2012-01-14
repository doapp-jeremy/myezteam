<?php 
/**
 * Parameters
 */

$html->script(array('plugins/dataTables/jquery.dataTables.min'),array('inline'=>false));
$html->script(array('common'),array('inline'=>false));

if (empty($css)) $css = array('dataTables/demo_table_jui');

$html->css($css,NULL,array('inline'=>false));

if (!isset($divId))
{
  $tableId = "eventsTable" . rand(0, 100);
}
else
{
  $tableId = $divId;
}

if (!isset($length))
{
  $length = 3;
}

$responseTypes = getResponseTypes();
?>

<table id="<?php echo $tableId; ?>" class="display">
	<thead>
		<tr>
			<th class="no_sort"></th>
			<th>Name</th>
			<th>When</th>
			<?php foreach ($responseTypes as $responseType): ?><th><?php echo Inflector::humanize($responseType['ResponseType']['name']); ?></th><?php endforeach; ?>
			<th>Location</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($events as $event): ?>
			<?php	$event = getEvent($event); ?>
			<tr>
				<td>
					<?php if (!empty($event['Event']['facebook_event'])): ?>
					  <?php echo $html->image("https://graph.facebook.com/{$event['Event']['facebook_event']}/picture"); ?>
				  <?php endif; ?>
				</td>
				<td><?php echo $event['Event']['name']; ?></td>
				<td><?php echo $event['Event']['start']; ?></td>
				<?php foreach ($responseTypes as $responseType): ?><td><?php echo count($event['ResponseByResponseType'][$responseType['ResponseType']['id']]); ?></td><?php endforeach; ?>
				<td><?php echo $event['Event']['location']; ?></td>
				<td><?php echo $event['Event']['description']; ?></td>
			</tr>	
		<?php endforeach; ?>
	</tbody>
</table>

<?php 
$codeBlock = "
  	var dontSort = [];
  	var i = 0;
  	$('#{$tableId} thead th').each(function() {
  		if ($(this).hasClass('no_sort'))
  		{
  			dontSort.push({'bSortable': false});
  		}
  		else
  		{
    		dontSort.push(null);
  		}
  	});
  $(document).ready(function() {
    $('#{$tableId}').dataTable({'aoColumns': dontSort, 'aaSorting':[[2, 'asc']], 'sPaginationType': 'full_numbers', 'iDisplayLength':{$length}, 'aLengthMenu':[{$length}, 5, 10, 20, 30, 40, 50, 100]});
  });
";
$this->Javascript->codeBlock($codeBlock, array('inline' => false));
?>
