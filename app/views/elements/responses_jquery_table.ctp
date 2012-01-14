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
  $tableId = "jqueryTable" . rand(0, 100);
}
else
{
  $tableId = $divId;
}
?>

<table id="<?php echo $tableId; ?>" class="display">
	<thead>
		<tr>
			<th></th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Email</th>
			<th>Player Type</th>
			<th>Response</th>
			<th>Comment</th>
			<th>Response Time</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($responses as $response): ?>
			<tr>
				<td id="userPic<? echo $response['Player']['user_id']; ?>">
					<?php if (!empty($response['Player']['User']['facebook_id'])): ?>
					  <?php echo $this->element('fb_pic', array('fbId' => $response['Player']['User']['facebook_id'])); ?>
				  <?php else: ?>
				  	<?php echo $this->Html->link('link', 'javascript: void', array('onclick' => "linkUserToFacebook('{$response['Player']['User']['id']}', '{$response['Player']['User']['first_name']}', '{$response['Player']['User']['last_name']}', '{$response['Player']['User']['email']}');")); ?>
				  <?php endif; ?>
				</td>
				<td><?php echo $response['Player']['User']['first_name']; ?></td>
				<td><?php echo $response['Player']['User']['last_name']; ?></td>
				<td><a href="mailto:<?php echo $response['Player']['User']['email']; ?>"><?php echo $response['Player']['User']['email']; ?></a></td>
				<td><?php echo $response['Player']['PlayerType']['name']; ?></td>
				<td><?php echo Inflector::humanize($response['ResponseType']['name']); ?></td>
				<td><?php echo $response['comment']; ?></td>
				<td><?php echo $response['created']; ?></td>
			</tr>	
		<?php endforeach; ?>
	</tbody>
</table>

<?php 
$codeBlock = "
  $(document).ready(function() {
    $('#{$tableId}').dataTable({'sPaginationType': 'full_numbers', 'iDisplayLength':20, 'aLengthMenu':[20, 30, 40, 50, 100]});
  });
";
$this->Javascript->codeBlock($codeBlock, array('inline' => false));
?>
