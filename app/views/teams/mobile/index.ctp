<div data-role="content">
  <ul data-role="listview" data-inset="true" data-filter="true">
  	<?php foreach ($teams as $team): ?>
  					<?php if (!empty($team['Team']['facebook_group'])): ?>
  						<?php //echo $html->image("https://graph.facebook.com/{$team['Team']['facebook_group']}/picture"); ?>
  					<?php elseif (in_array($team['Team']['id'], $teamsManagedIds)): ?>
  						<?php //echo $html->link('Link to FB', 'javascript: void', array('onclick' => "linkTeamToFacebook({$team['Team']['id']}, '{$team['Team']['name']}'); return false;")); ?>
  					<?php endif; ?>
  	<li>
  		<a href="/Teams/view/<?= $team['Team']['id']?>"><?= $team['Team']['name']; ?></a>
  	</li>
  	<?php endforeach; ?>
  </ul>
</div>
