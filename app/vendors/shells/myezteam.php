<?php
class MyezteamShell extends Shell
{
  var $tasks = array('Import', 'MyezteamImport');
  
  function main()
  {
    Configure::write("Cache.check", false);
    $this->print_instructions();
  }
   
  function print_instructions()
  {
    $this->out("\nCommands");
    $this->hr();

    foreach($this->tasks AS $t)
    {
      $description = isset($this->{$t}->description) ? $this->{$t}->description : '';
      $this->out($this->shell . ' ' . Inflector::underscore($t) . "\t$description\n");
    }
  }
}
?>