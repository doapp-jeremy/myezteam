<?php
/**
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php
 *
 * This is an application wide file to load any function that is not used within a class
 * define. You can also use this to include or require any files in your application.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.config
 * @since         CakePHP(tm) v 0.10.8.2117
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * App::build(array(
 *     'plugins' => array('/full/path/to/plugins/', '/next/full/path/to/plugins/'),
 *     'models' =>  array('/full/path/to/models/', '/next/full/path/to/models/'),
 *     'views' => array('/full/path/to/views/', '/next/full/path/to/views/'),
 *     'controllers' => array('/full/path/to/controllers/', '/next/full/path/to/controllers/'),
 *     'datasources' => array('/full/path/to/datasources/', '/next/full/path/to/datasources/'),
 *     'behaviors' => array('/full/path/to/behaviors/', '/next/full/path/to/behaviors/'),
 *     'components' => array('/full/path/to/components/', '/next/full/path/to/components/'),
 *     'helpers' => array('/full/path/to/helpers/', '/next/full/path/to/helpers/'),
 *     'vendors' => array('/full/path/to/vendors/', '/next/full/path/to/vendors/'),
 *     'shells' => array('/full/path/to/shells/', '/next/full/path/to/shells/'),
 *     'locales' => array('/full/path/to/locale/', '/next/full/path/to/locale/')
 * ));
 *
 */

/**
 * As of 1.3, additional rules for the inflector are added below
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

function parseColumn($column)
{
  return explode(".", $column);
}

function getColumnName($columnKey, $columnNames)
{
  if (!empty($columnNames[$columnKey]))
  {
    return $columnNames[$columnKey];
  }
  list($model, $field) = parseColumn($columnKey);
  if ($field)
  {
    return Inflector::humanize($model) . " " . Inflector::humanize($field);
  }
  else
  {
    return Inflector::humanize($model);
  }
}

function generateFbFriendJSArray($friends)
{
  if (count($friends) > 0 )
  {
    // array sorting (friend list alphabetically)
//    usort($friends, 'cmpi');

    $return = "var fbFriends = []; ";

    foreach ($friends as $friend)
    {
      $uid = $friend['id'];
      // insert slashes to escape potential ' in names
      $name = addslashes($friend['name']);
      
      list($firstName, $lastName) = explode(" ", $name, 2);
            
      $return .= "
				fbFriends.push({id:'{$uid}', label:'{$name}', first_name:'{$firstName}', last_name:'{$lastName}'});
			";	
    }
    return $return;
  }
  else
  {
    return "var fbFriends = 'none';";
  }
}

function generateFriendJSArray($friends)
{
  if (count($friends) > 0 )
  {
    // array sorting (friend list alphabetically)
//    usort($friends, 'cmpi');

    $return = "var friends = []; ";

    foreach ($friends as $friend)
    {
      $uid = $friend['id'];
      $email = $friend['email'];
      
      // insert slashes to escape potential ' in names
      $firstName = addslashes($friend['first_name']);
      $lastName = addslashes($friend['last_name']);
//      list($firstName, $lastName) = explode(" ", $name, 2);
      $return .= "
				friends.push({id:'{$uid}', email:'{$email}', label:'{$firstName} {$lastName} ({$email})', first_name:'{$firstName}', last_name:'{$lastName}'});
			";	
    }
    return $return;
  }
  else
  {
    return "var friends = 'none';";
  }
}

function convertDate($date, $offset)
{
  if ($offset)
  {
    return $date->modify("{$offset} hours");
  }
  else
  {
    return $date;
  }
}

function getResponseTypes()
{
  $responseTypes = array(
    array('ResponseType' => array('id' => '1', 'name' => 'no_response', 'color' => '000000')),
    array('ResponseType' => array('id' => '2', 'name' => 'yes', 'color' => '33FF00')),
    array('ResponseType' => array('id' => '3', 'name' => 'probable', 'color' => '99CC00')),
    array('ResponseType' => array('id' => '4', 'name' => 'maybe', 'color' => 'FFFF00')),
    array('ResponseType' => array('id' => '5', 'name' => 'no', 'color' => 'CC0000')),
  );
  return $responseTypes;
}

function getResponseTypeList()
{
  $responseTypes = array(
    '2' => 'Yes',
    '3' => 'Probable',
    '4' => 'Maybe',
    '5' => 'No',
  );
  return $responseTypes;
}

function getEvent($event)
{
  if (empty($event['Event']))
  {
    if (!empty($event['UpcomingEvent']))
    {
      $event['Event'] = $event['UpcomingEvent'];
    }
    else if (!empty($event['PastEvent']))
    {
      $event['Event'] = $event['PastEvent'];
    }
    else
    {
      $newEvent = array('Event' => array());
      foreach ($event as $field => $value)
      {
        if (!is_array($value))
        {
          $newEvent['Event'][$field] = $value;
        }
        else
        {
          $newEvent[$field] = $value;
        }
      }
      $event = $newEvent;
    }
  }
  return $event;
}

function getNextEvent($team)
{
  if (!empty($team['UpcomingEvent'][0]))
  {
    return getEvent($team['UpcomingEvent'][0]);
  }
  return array();
}

function isTeamManager($teamId, $teamIds)
{
  return in_array($teamId, $teamIds);
}

function getYourPlayer($players, $userId)
{
  foreach ($players as $player)
  {
    if ($player['User']['id'] == $userId)
    {
      return $player;
    }
  }
  return false;
}

function getMyRSVP($event, $yourPlayer)
{
  if (!empty($event['ResponseByPlayer'][$yourPlayer['id']]['ResponseType']))
  {
    return $event['ResponseByPlayer'][$yourPlayer['id']]['ResponseType'];
  }
  else
  {
    return $event['DefaultResponse'];
  }
}
