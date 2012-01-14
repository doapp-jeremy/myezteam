<?php
class FacebookShell extends Shell
{
  var $tasks = array(/* has to be first for subclasses to find */'FacebookApi','SyncEvents', 'UpdatePlayers', 'MergePlayers', 'SyncRsvps');
  
  function main()
  {
    Configure::write("Cache.check", false);
  	if (count($this->args) > 0)
  	{
  		if ($this->args[0] == 'all')
  		{
  			foreach ($this->tasks as $task)
  			{
  				$this->executeTask($task);
  			}
  		}
  		else if (in_array(Inflector::camelize($this->args[0]), $this->tasks))
  		{
  			$this->executeTask(Inflector::camelize($this->args[0]));
  		}
  		else
  		{
  			$this->print_instructions();
  		}
  	}
  	else
  	{
	  	$this->print_instructions();
  	}
  }
  
  private function executeTask($task)
  {
  	array_shift($this->args);
  	$this->{$task}->_loadModels();
  	$this->{$task}->execute();
  }
  
  function print_instructions()
  {
    $this->out("\nCommands");
    $this->hr();

    $this->out($this->shell . " facebook all\t Run all facebook tasks " . implode (', ', $this->tasks) . "\n");
    
    foreach($this->tasks AS $t)
    {
      $description = isset($this->{$t}->description) ? $this->{$t}->description : '';
      $this->out($this->shell . ' facebook ' . Inflector::underscore($t) . "\t$description\n");
    }
  }
}
?>