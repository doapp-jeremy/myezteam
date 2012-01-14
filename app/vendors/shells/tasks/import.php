<?php
class ImportTask extends Shell
{
  // Model class used to query the old data
  var $OldModel = false;
  // Model class to insert the new data
  var $NewModel = false;
  var $tableMap = array();
  var $autoIncUpdate = 100000;
  var $database;
  var $special = array();

  function getOldModelToUse()
  {
    debug("Subclasses should override: getOldModelToUse");
    $this->_stop();
  }

  function getNewModelToUse()
  {
    debug("Subclasses should override: getNewModelToUse");
    $this->_stop();
  }

  function execute()
  {
    // get model to use
    $this->OldModel = $this->getOldModelToUse();
    $this->OldModel->cacheQueries = false;
    $this->OldModel->cacheSQL = false;
    $this->NewModel = $this->getNewModelToUse();
    $this->NewModel->cacheQueries = false;
    $this->NewModel->cacheSQL = false;
    
    $tableMap = $this->getTableMap();
    debug($tableMap);
    if (!$tableMap)
    {
      $this->print_instructions();
      exit();
    }

    foreach ($tableMap as $table => $data)
    {
      if (!$this->specialProcessing($table, $data))
      {
        $this->import($table, $data);
      }
    }
  }
  
  function import($table, $data)
  {
    $queryString = $this->getQueryString($data);
    debug($queryString);
    $oldData = $this->OldModel->query($queryString, false);
    $database = $this->getNewDatabase($data);
    $autoIncUpdate = $this->autoIncUpdate;
    if (isset($data['auto_inc_update']))// || ($data['auto_inc_update'] == 0))
    {
      $autoIncUpdate = $data['auto_inc_update'];
    }
    $this->update($table, $data['old_table']['table'], $data['field_map'], $oldData, $database, $autoIncUpdate);
  }

  function getQueryString($data)
  {
    if (!empty($data['old_table']['query']))
    {
      return $data['old_table']['query'];
    }
    else
    {
      return "SELECT * FROM {$data['old_table']['database']}.{$data['old_table']['table']}";
    }
  }

  function getNewDatabase($data)
  {
    if (!empty($data['new_database']))
    {
      return $data['new_database'];
    }
    if (!$this->database)
    {
      debug("DATABASE IS NOT SET AND NOT SPECIFIED IN OPTIONS");
      $this->_stop();
    }
    return $this->database;
  }

  function update($table, $oldTable, $map, $oldData, $database, $autoIncUpdate)
  {
    $count = 0;
    foreach ($oldData as $data)
    {
      $count++;
      $created = isset($map['created']);
      $modified = isset($map['modified']);
      $insertString = "REPLACE INTO {$database}.{$table} (" . implode(", ", array_keys($map));// . ", created, modified) VALUES (";
      if (!$created)
      {
        $insertString.= ", created";
      }
      if (!$modified)
      {
        $insertString.= ", modified";
      }
      $insertString .= ") VALUES (";
      $insert = true;
      $count = 0;
      foreach ($map as $newKey => $oldKey)
      {
        if ($count > 0)
        {
          $insertString.= ", ";
        }
        $count++;
        if (key_exists($oldKey, $data[$oldTable]))
        {
          if (isset($data[$oldTable][$oldKey]))
          {
            $insertString.= "'" . mysql_real_escape_string($data[$oldTable][$oldKey]) . "'";
          }
          else
          {
            $insertString.= "NULL";
          }
        }
        else if (isset($data[0][$oldKey]))
        {
          $insertString.= "'" . mysql_real_escape_string($data[0][$oldKey]) . "'";
        }
        else if (method_exists($this, $oldKey))
        {
          $newValue = $this->{$oldKey}($data);
          if ($newValue === false)
          {
            // don't insert
            $insert = false;
          }
          else
          {
            debug($newValue);
          }
          if ($newValue === 'NULL')
          {
            $insertString.= 'NULL';
          }
          else
          {
            $insertString.= "'{$newValue}'";
          }
        }
        else
        {
          debug($data);
          $this->out("\n\n**** No value and no method to get value: {$oldTable}:{$oldKey} ***");
          exit();
        }
      }
      //      $insertString.= "NOW(), NOW())";
      if (!$created)
      {
        $insertString.= ", NOW()";
      }
      if (!$modified)
      {
        $insertString.= ", NOW()";
      }
      $insertString.= ")";
      if ($insert)
      {
        debug($insertString);
        $res = $this->NewModel->query($insertString, false);
      }
    }

    // update auto increment id
    if ($autoIncUpdate)
    {
      $lastId = mysql_insert_id();
      $autoIncUpdate += $lastId;
      $updateAuto = "ALTER TABLE {$database}.{$table} AUTO_INCREMENT = {$autoIncUpdate};";
      debug($updateAuto);
      $this->NewModel->query($updateAuto, false);
    }
  }

  function print_instructions()
  {
    $this->out("import <table(s)>");
    foreach ($this->tableMap as $table => $data)
    {
      $this->out("\t{$table}");
    }
    $this->out("\tall: import all of them");
  }

//  function getTableMap()
//  {
//    $tableMap = array();
//    if ((count($this->args) == 1) && ($this->args[0] == 'all'))
//    {
//      $tableMap = $this->tableMap;
//    }
//    else
//    {
//      foreach ($this->args as $arg)
//      {
//        if (!empty($this->tableMap[$arg]))
//        {
//          $tableMap[$arg] = $this->tableMap[$arg];
//        }
//      }
//    }
//    return $tableMap;
//  }
  
  function getTableMap()
  {
    $tableMap = array();
    if ((count($this->args) == 1) && ($this->args[0] == 'all'))
    {
      $tableMap = array_merge($this->tableMap, $this->special);
    }
    else
    {
      foreach ($this->args as $arg)
      {
        if (!empty($this->tableMap[$arg]))
        {
          $tableMap[$arg] = $this->tableMap[$arg];
        }
        else if (in_array($arg, array_merge($this->special)))
        {
          $tableMap[] = $arg;
        }
      }
    }
    return $tableMap;
  }
  
//  function specialProcessing($table, $data)
//  {
//    return false;
//  }
  
  function specialProcessing($table, $data)
  {
  	if (!is_array($data))
  	{
  		if (in_array($data, $this->special))
  		{
  			$methodName = "import" . Inflector::camelize($data);
  			if (method_exists($this, $methodName))
  			{
	  			$this->{$methodName}();
  			}
  			else
  			{
  				$this->out("Method {$methodName} does not exist.");
  				$this->print_instructions();
  				exit();
  			}
  		}
  		else
  		{
  			$this->print_instructions();
  			exit();
  		}
  		return true;
  	}
  	return false;
  }
  
}
?>
