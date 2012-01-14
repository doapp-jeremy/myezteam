<?php 
if (!empty($team['Team']['facebook_group']))
{
  $groupUrl = "http://www.facebook.com/home.php?sk=group_{$team['Team']['facebook_group']}";
  echo $html->link($html->image("https://graph.facebook.com/{$team['Team']['facebook_group']}/picture"), $groupUrl, array('target' => '_blank', 'escape' => false));
}
else if (!isset($showLinkForm) || ($showLinkForm != false))
{
  echo $html->link('Link to Facebook Group', 'javascript: void', array('onclick' => "linkTeamToFacebook({$team['Team']['id']}, '{$team['Team']['name']}'); return false;"));
}
?>
