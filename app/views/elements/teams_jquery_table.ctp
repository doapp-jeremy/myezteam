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
  $tableId = "jqueryTable" . rand(0, 100);
}
else
{
  $tableId = $divId;
}

function updateTeamEvents(&$team)
{
  if (!empty($team['UpcomingEvent'][0]))
  {
    $team['NextEvent'] = $team['UpcomingEvent'][0];
  }
  if (!empty($team['PastEvent'][0]))
  {
    $team['LastEvent'] = $team['PastEvent'][0];
  }
}
?>

<table id="<?php echo $tableId; ?>" class="display">
	<thead>
		<tr>
			<th class="no_sort" id="pic"></th>
			<th id="name">Name</th>
			<th id="nextEventName">Next Event</th>
			<th id="nextEventDate">Next Event Time</th>
			<th id="lastEventName">Last Event</th>
			<th id="lastEventDate">Last Event Time</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($teams as $team): ?>
			<?php updateTeamEvents($team); ?>
			<tr>
				<td id="teamFBpic<?= $team['Team']['id']; ?>">
					<?php if (!empty($team['Team']['facebook_group'])): ?>
						<?php echo $html->image("https://graph.facebook.com/{$team['Team']['facebook_group']}/picture"); ?>
					<?php elseif (in_array($team['Team']['id'], $teamsManagedIds)): ?>
						<?php echo $html->link('Link to FB', 'javascript: void', array('onclick' => "linkTeamToFacebook({$team['Team']['id']}, '{$team['Team']['name']}'); return false;")); ?>
					<?php endif; ?>
				</td>
				<td><?php echo $html->link($team['Team']['name'], array('controller' => 'Teams', 'action' => 'view', $team['Team']['id'])); ?></td>
				<td><? if (!empty($team['NextEvent']['id'])): ?><?php echo $html->link($team['NextEvent']['name'], array('controller' => 'Events', 'action' => 'view', $team['NextEvent']['id'])); ?><?php endif; ?></td>
				<td><? if (!empty($team['NextEvent']['date'])): ?><?php echo convertDate(date_create("{$team['NextEvent']['date']} {$team['NextEvent']['time']}"), $session->read('timezone_offset'))->format('m/d/Y H:i:s');?><?php endif; ?></td>
				<td><? if (!empty($team['LastEvent']['id'])): ?><?php echo $html->link($team['LastEvent']['name'], array('controller' => 'Events', 'action' => 'view', $team['LastEvent']['id'])); ?><?php endif; ?></td>
				<td><? if (!empty($team['LastEvent']['date'])): ?><?php echo date_create("{$team['LastEvent']['date']} {$team['LastEvent']['time']}")->format('m/d/Y H:i:s');?><?php endif; ?></td>
			</tr>	
		<?php endforeach; ?>
	</tbody>
</table>

<?php 
$codeBlock = "
  $(document).ready(function() {
//    $.fn.dataTableExt.afnSortData['dom-text'] = function  ( oSettings, iColumn )
//    {
//    	var aData = [];
//    	$( 'td:eq('+iColumn+') input', oSettings.oApi._fnGetTrNodes(oSettings) ).each( function () {
//    		//aData.push( this.value );
//    		var i = this.value;
//    		alert(i);
//    	} );
//    	return aData;
//    }
  	var dontSort = [];
  	var i = 0;
  	$('#{$tableId} thead th').each(function() {
  		if ($(this).hasClass('no_sort')) {
  			dontSort.push({'bSortable': false});
  		}
  		else
  		{
    		if (($(this).attr('id') == 'nextEventDate') || ($(this).attr('id') == 'lastEventDate'))
    		{
    			dontSort.push({'sType':'date'});
    		}
    		else { dontSort.push(null); }
  		}
  	});

    $('#{$tableId}').dataTable({'aoColumns': dontSort, 'sPaginationType': 'full_numbers', 'iDisplayLength':20, 'aLengthMenu':[20, 30, 40, 50, 100],'aaSorting':[[3, 'desc'],[1, 'asc']]});
  });
";
$this->Javascript->codeBlock($codeBlock, array('inline' => false));
?>
