<?php 
$fbUser = $session->read('FB');
foreach ($fbUser['Me'] as $field => $value)
{
  if (!is_array($value))
  {
    echo $this->Form->input("FB.{$field}", array('value' => $value, 'type' => 'hidden'));
  }
}

?>
