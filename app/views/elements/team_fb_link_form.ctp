<?php echo $this->Form->input('Team.name', array('type' => 'hidden')); ?>
<?php echo $this->Form->input('FacebookGroup.name', array('type' => 'hidden')); ?>
<form id="TeamFacebookForm">
	<?php echo $this->Form->input('Team.id', array('type' => 'hidden')); ?>
	<?php echo $this->Form->input('Team.facebook_group', array('type' => 'hidden')); ?>
</form>

<div id="linkTeamForm" title="Link Team to Facebook Group">
	<?php if (!$session->read('FB')): ?>
		<p>
			You must sign-in with your Facebook credentials first.
		</p>
		<?php echo $this->element('fb_login'); ?>
	<?php else: ?>
	<form>
  	<p id="linkTeamFormText"></p>
  	<fieldset>
    	<div>
    		<table>
    			<tbody>
    				<tr>
    				<? $i = 0; ?>
    				<? foreach ($fbGroups as $fbGroup): ?>
    					<?php if (in_array($fbGroup['id'], $teamFbGroupIds)) { continue; } ?>
    					<?php if (empty($fbGroup['administrator']) || !$fbGroup['administrator']) { continue; }?>
    					<td style="cursor: pointer;" onclick="selectGroup(<?= $fbGroup['id']; ?>, '<?= $fbGroup['name']; ?>');">
    					  <?php echo $fbGroup['name']; ?>
    					  <?php echo $html->image("https://graph.facebook.com/{$fbGroup['id']}/picture"); ?>
    					</td>
    					<?php if (($i > 0) && (($i % 2) == 0)): ?></tr><tr><?php endif; ?>
    					<?php $i++; ?>
    				<? endforeach; ?>
    				</tr>
    			</tbody>
    		</table>
    	</div>
  	</fieldset>
	</form>
	<?php endif; ?>
</div>
