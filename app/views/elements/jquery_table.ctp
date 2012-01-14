<?php 
/**
 * Parameters
 */

$html->script(array('plugins/dataTables/jquery.dataTables.min'),array('inline'=>false));

if (empty($css)) $css = array('dataTables/demo_table_jui');

$html->css($css,NULL,array('inline'=>false));

if (!isset($columnNames))
{
  $columnNames = array();
}

// moved to bootstrap.php
//function parseColumn($column)
//{
//  return explode(".", $column);
//}
//function getColumnName($columnKey, $columnNames)
//{
//  if (!empty($columnNames[$columnKey]))
//  {
//    return $columnNames[$columnKey];
//  }
//  list($model, $field) = parseColumn($columnKey);
//  if ($field)
//  {
//    return Inflector::humanize($model) . " " . Inflector::humanize($field);
//  }
//  else
//  {
//    return Inflector::humanize($model);
//  }
//}

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
			<?php foreach ($columns as $columnKey): ?>
				<th>
				  <?php echo getColumnName($columnKey, $columnNames); ?>
				</th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($rows as $row): ?>
			<tr>
				<?php foreach ($columns as $columnKey): ?>
					<td>
					  <?
					  list($model, $field) = parseColumn($columnKey);
					  if (isset($row[$model]))
					  {
  					  if ($field)
  					  {
  					    echo $row[$model][$field];
  					  }
  					  else
  					  {
  					    echo $row[$model];
  					  }
					  }
					  ?>
					</td>
				<?php endforeach; ?>
			</tr>	
		<?php endforeach; ?>
	</tbody>
</table>

<?php 
$codeBlock = "
  $(document).ready(function() {
    $('#{$tableId}').dataTable({'sPaginationType': 'full_numbers'});
  });
";
$this->Javascript->codeBlock($codeBlock, array('inline' => false));
?>
