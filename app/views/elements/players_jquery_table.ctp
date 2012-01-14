<?php 
/**
 * Parameters
 */

$html->script(array('plugins/dataTables/jquery.dataTables.min'),array('inline'=>false));
$html->script(array('common'),array('inline'=>false));

if (empty($css)) $css = array('dataTables/demo_table_jui');

$html->css($css,NULL,array('inline'=>false));

if (!isset($columnNames))
{
  $columnNames = array();
}

if (!isset($divId))
{
  $tableId = "playersTable" . rand(0, 100);
}
else
{
  $tableId = $divId;
}
?>

<table id="<?php echo $tableId; ?>" class="display">
	<thead>
		<tr>
			<th class="no_sort"></th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Email</th>
			<th>Player Type</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($players as $player): ?>
			<tr>
				<td id="userPic<? echo $player['User']['id']; ?>">
					<?php if (!empty($player['User']['facebook_id'])): ?>
					  <?php echo $this->element('fb_pic', array('fbId' => $player['User']['facebook_id'])); ?>
				  <?php else: ?>
				  	<?php echo $this->Html->link('link', 'javascript: void', array('onclick' => "linkUserToFacebook('{$player['User']['id']}', '{$player['User']['first_name']}', '{$player['User']['last_name']}', '{$player['User']['email']}');")); ?>
				  <?php endif; ?>
				</td>
				<td><?php echo $player['User']['first_name']; ?></td>
				<td><?php echo $player['User']['last_name']; ?></td>
				<td><a href="mailto:<?php echo $player['User']['email']; ?>"><?php echo $player['User']['email']; ?></a></td>
				<td><?php echo $player['PlayerType']['name']; ?></td>
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
    $('#{$tableId}').dataTable({'aoColumns': dontSort, 'aaSorting':[[4, 'asc'],[1, 'asc']], 'sPaginationType': 'full_numbers', 'iDisplayLength':20, 'aLengthMenu':[20, 30, 40, 50, 100]});
  });
";
$this->Javascript->codeBlock($codeBlock, array('inline' => false));
?>
