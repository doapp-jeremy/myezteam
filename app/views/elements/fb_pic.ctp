<?php 
//if (!isset($fbId))
//{
//  $fbUser = $session->read("FB");
//  if (!empty($fbUser['Me']['id']))
//  {
//    $fbId = $fbUser['Me']['id'];
//  }
//}
if (!empty($fbId))
{
  if (!isset($width))
  {
    $width = "50";
  }
  if (!isset($height))
  {
    $height = "37.5";
  }
  if (!isset($size))
  {
    $size = "normal";
  }
  echo $facebook->picture($fbId, compact('width', 'height', 'size'));
}
?>
