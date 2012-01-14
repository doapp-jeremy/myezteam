<?php 
//require_once('facebook_api_task.php');
class SyncEventsTask extends FacebookApiTask
{
	var $uses = array('Event', 'Team');
	
	function execute()
	{
		$this->syncAllTeams();
	}
	
	function syncAllTeams()
	{
		$teams = $this->Team->getFacebookTeamsAndManagersAndUpcomingEvents();
		if (empty($teams))
		{
		  debug("No teams to process");
		  return;
		}
		foreach ($teams as $team)
		{
			debug($team['Team']['name']);
			$fbSession = $this->Team->getFacebookSessionForTeam($team);
			debug($fbSession);
			debug($team['Manager']);
			$timeZone = new DateTimeZone(date_default_timezone_get());
			debug($timeZone);
			foreach ($team['Manager'] as $manager)
			{
  			if (!empty($manager['TimeZone']['value']))
  			{
  			  $timeZone = new DateTimeZone($manager['TimeZone']['value']);
  			}
			}
			if ($fbSession && !empty($team['Team']['facebook_group']))
			{
        $this->setSession($fbSession, false);
        $path = "{$team['Team']['facebook_group']}/events";
        debug($path);
//        $facebookEvents = $this->fbAPI($path, 'GET', array('fields' => array('name', 'email')));
        // have to use old REST API..not available via GRAPH API yet:
        // http://bugs.developers.facebook.net/show_bug.cgi?id=13991
        $facebookEvents = $this->fbAPI(array('method' => 'events.get', 'uid' => $team['Team']['facebook_group']));
        debug($facebookEvents);
        
        foreach ($facebookEvents as $fbEvent)
        {
          debug("{$fbEvent['name']}: " . date("Y-m-d H:i:s", $fbEvent['start_time']));
        }
        
        $this->syncMyEzTeamEvents($team, $facebookEvents, $timeZone);
        $this->syncFBEvents($team, $facebookEvents, $timeZone);
			}
		}
	}
	
	function updateFBEvent($fbEvent, $event, $timeZone)
	{
	  $fbEvent['name'] = $event['name'];
	  $fbEvent['location'] = $event['location'];
	  $startDate = date_create("{$event['start']}");
	  $startDate->setTimezone($timeZone);
	  $startDate->modify("+7 hours"); // adjustment for FB bug:
	  $endDate = date_create("{$event['end']}");
	  $endDate->setTimezone($timeZone);
	  $endDate->modify("+7 hours"); // adjustment for FB bug:

	  $fbEvent['start_time'] = $startDate->format("c");
	  $fbEvent['end_time'] = $endDate->format("c");
//	  debug("TODO: Updating facebook event");
//	  return;
	  $result = $this->fbAPI($fbEvent['eid'], 'POST', $fbEvent);
	  debug($fbEvent);
	  debug($result);
	  if ($result)
	  {
	    // update modified time
	    $event['modified'] = date("Y-m-d H:i:s");
	    if (!$this->Event->save($event))
	    {
	      debug("Could not update events modified time");
	      debug($event);
	      exit();
	    }
	  }
	}
	
	function updateMyEZTeamEvent($event, $fbEvent)
	{
	  debug("Update myeztem event");
	  $event['name'] = $fbEvent['name'];
	  $event['location'] = $fbEvent['location'];
	  $startDate = date_create(date("Y-m-d H:i:s", $fbEvent['start_time']));
	  $startDate->modify("-7 hours"); // FB bug
	  $endDate = date_create(date("Y-m-d H:i:s", $fbEvent['end_time']));
	  $endDate->modify("-7 hours"); // FB bug
	  $event['start'] = $startDate->format("Y-m-d H:i:s");
	  $event['end'] = $endDate->format("Y-m-d H:i:s");
	  $event['modified'] = date("Y-m-d H:i:s");
	  debug($event);
	  if (!$this->Event->save($event))
	  {
	    debug("Could not update myezteam event");
	    debug($event);
	    exit();
	  }
	}
	
	function createFBEvent($event, $team, $timeZone)
	{
	  debug($timeZone);
	  $startDate = date_create("{$event['start']}");
	  $startDate->setTimezone($timeZone);
	  $startDate->modify("+7 hours"); // FB bug
	  $endDate = date_create("{$event['end']}");
	  $endDate->setTimezone($timeZone);
	  $endDate->modify("+7 hours"); // FB bug
	  $fbEvent = array('name' => $event['name'], 'location' => $event['location'], 'privacy' => 'CLOSED', 'start_time' => $startDate->format("c"), 'end_time' => $endDate->format("c"));
	  if (!empty($team['Team']['facebook_group']))
	  {
	    $fbEvent['page_id'] = $team['Team']['facebook_group'];
	  }
	  debug($fbEvent);
	  //   	 	return false;
	  debug($startDate->format("c"));

	  $result = $this->fbAPI("events", 'POST', $fbEvent);
	  debug($result);
	  if (!empty($result['id']))
	  {
	    // save events facebook id
	    $event['facebook_event'] = $result['id'];
	    // TODO: for some reason cake isn't updating modified value
	    $event['modified'] = date("Y-m-d H:i:s");
	    if (!$this->Event->save($event))
	    {
	      debug("Could not save Facebook event id after creating");
	      debug($event);
	      exit();
	    }
	  }
	}
	
	function syncMyEzTeamEvents($team, $facebookEvents, $timeZone)
	{
	  foreach ($team['UpcomingEvent'] as $event)
	  {
	    if (!empty($event['facebook_event']))
	    {
	      // get facebook event
	      $fbEvent = $this->getFacebookEvent($facebookEvents, array('eid' => $event['facebook_event']));
	      if (!$fbEvent)
	      {
	        // TODO: delete event
	        debug("TODO: delete event:");
	        debug($event);
	      }
	      else
	      {
	        // check if an update is needed
	        $fbUpdateTime = date_create(date("c", $fbEvent['update_time']));
	        $eventUpdateTime = date_create($event['modified']);
	        $difference = $eventUpdateTime->diff($fbUpdateTime, true);
	        debug($eventUpdateTime);
	        debug($fbUpdateTime);
	        debug($difference);
	        if ($difference->i > 1)
	        {
	          // allow for 1 minute time difference
	          if ($eventUpdateTime > $fbUpdateTime)
	          {
	            // update facebook event
	            $this->updateFBEvent($fbEvent, $event, $timeZone);
	          }
	          else
	          {
	            // update local event
	            $this->updateMyEZTeamEvent($event, $fbEvent);
	          }
	        }
	      }
	    }
	    else
	    {
	      debug($event);
	      $start = date_create($event['start'], new DateTimeZone("America/Chicago"))->format('U');
	      debug($start);
	      $fbEvent = $this->getFacebookEvent($facebookEvents, array('start_time' => $start));
	      debug($fbEvent);
//	      exit();
	      // create event
	      $this->createFBEvent($event, $team, $timeZone);
//	      exit();
	    }
	  }
	}
	
	function syncFBEvents($team, $facebookEvents, $timeZone)
	{
	  foreach ($facebookEvents as $fbEvent)
	  {
	    $startDate = date_create(date("Y-m-d H:i:s", $fbEvent['start_time']));
	    $startDate->modify("-7 hours"); // FB bug
	    $now = date_create();
	    // ignore past events
	    if ($startDate > $now)
	    {
	      $event = $this->getMyEZTeamEvent($team['UpcomingEvent'], array('facebook_event' => $fbEvent['eid']));
	      if (!$event)
	      {
	        // Create My EZ Team event
	        $this->Event->create();
	        $this->updateMyEZTeamEvent(array('team_id' => $team['Team']['id'], 'facebook_event' => $fbEvent['eid']), $fbEvent);
	        debug("Event created");
	      }
	    }
	  }
	}
	
	function getMyEZTeamEvent($events, $params)
	{
	  foreach ($events as $event)
	  {
	    foreach ($params as $key => $value)
	    {
	      if ($event[$key] == $value)
	      {
	        return $event;
	      }
	    }
	  }
	  return false;
	}
	
	function getFacebookEvent($facebookEvents, $params)
	{
	  foreach ($facebookEvents as $facebookEvent)
	  {
	    foreach ($params as $key => $value)
	    {
	      debug($facebookEvent['name'] . ":" . $value . ":" . $facebookEvent[$key]);
	      if (!empty($facebookEvent[$key]) && ($facebookEvent[$key] == $value))
	      {
	        return $facebookEvent;
	      }
	    }
	  }
	  return false;
	}
	
	function getEventStatus($eventInfo, $status)
	{
		return $this->fbAPI("{$eventInfo['id']}/{$status}");
	}
	
  function getNoReply($eventInfo)
  {
    return $this->getEventStatus($eventInfo, 'noreply');
  }
  
  function getMaybe($eventInfo)
  {
    return $this->getEventStatus($eventInfo, 'maybe');
  }
  
  function getInvited($eventInfo)
  {
    return $this->getEventStatus($eventInfo, 'invited');
  }
  
  function getAttending($eventInfo)
	{
		return $this->getEventStatus($eventInfo, 'attending');
	}
	
  function getDeclined($eventInfo)
  {
    return $this->getEventStatus($eventInfo, 'declined');
  }
  
  function getMembersNotInList($eventInfo, $list)
  {
    debug($eventInfo);
    $groupMembers = $this->fbAPI("{$eventInfo['owner']['id']}/members");
    debug($groupMembers);
  	$members = array();
  	foreach ($groupMembers['data'] as $groupMember)
  	{
  		if (!Set::extract("/data[id={$groupMember['id']}]", $list))
  		{
  			$members[] = $groupMember;
  		}
  	}
  	return $members;
  }
  
	function inviteMembers($eventInfo)
	{
		$invited = $this->getInvited($eventInfo);
		debug($invited);
		$membersToInvite = $this->getMembersNotInList($eventInfo, $invited);
		if ($membersToInvite)
		{
	    debug($membersToInvite);
	    $ids = Set::extract('/id', $membersToInvite);
	    debug($ids);
	    $this->eventInvite($eventInfo, $ids);
//	    $params = array('method' => 'events.invite', 'eid' => $eventInfo['id'], 'uids' => $ids, 'personal_message' => "Please RSVP to {$eventInfo['name']}");
//	    $result = $this->fbRest($params);
		}
		else
		{
			debug('No members to invite');
		}
	}
	
	function getDateFromFacebook($facebookEvent)
	{
		return date_create($facebookEvent['start_time'])->format("Y-m-d H:i:s");
	}
	
	function getLabelFromFacebook($facebookEvent)
	{
		return $facebookEvent['name'];
	}
	
	function getLocationIdFromFacebook($facebookEvent)
	{
		$this->Location->contain();
		$location = $this->Location->find('first', array('conditions' => array('Location.name' => $facebookEvent['location'])));
		if ($location)
		{
			return $location['Location']['id'];
		}
		else
		{
			return 0;
		}
	}
	
//	function getModifiedFromFacebook($facebookEvent)
//	{
//		// set modified time to the same as facebook updated time so we don't check again
//		$date = date_create($facebookEvent['updated_time']);
//		// TODO: adjust date based on timezone
//		debug($date->format("c"));
//		return $date->format("c");
////		return $date->format("Y-m-d H:i:s");
//	}
	
	function getStartTime($schedule)
	{
		$startTime = date_create($schedule['date']);
		// facebook bug
		$startTime->modify("+7 hours");
		return $startTime->format("c");
	}
	
  function getEndTime($schedule)
  {
    $endTime = date_create($schedule['date']);
  	$endTime->modify("+1 hours");
    // facebook bug
    $endTime->modify("+7 hours");
  	return $endTime->format("c");
  }
  
  function getLocation($schedule)
  {
  	return $schedule['Location']['name'];
  }
  
  function getName($schedule)
  {
  	return $schedule['label'];
  }
  
  function syncFacebookEvent($schedule, $facebookEvent)
  {
  	$eventData = array();

  	foreach ($facebookEvent as $key => $value)
  	{
  		$methodName = Inflector::camelize("get_{$key}");
  		if (method_exists($this, $methodName))
  		{
  			$scheduleValue = call_user_method($methodName, $this, $schedule);
  			if ($scheduleValue != $value)
  			{
          $eventData[$key] = $scheduleValue;
  			}
  		}
  	}
  	if (!empty($eventData))
  	{
  		$eventData = array_merge($facebookEvent, $eventData);
  		unset($eventData['id']);
  		$response = $this->fbAPI($facebookEvent['id'], 'POST', $eventData);
  		// update modified time for schedule so we dont' think the facebook event has been updated since
//  		$this->Schedule->save(array('Schedule' => array('id' => $schedule['id'], 'modified' => 'NOW()')));
      //          debug($response);
  		return $response;
  	}
  	return true;
  }
  
  function syncSchedule($schedule, $facebookEvent)
  {
  	$scheduleData = array();
  	
  	foreach ($schedule as $key => $value)
  	{
      $methodName = Inflector::camelize("get_{$key}") . "FromFacebook";
      if (method_exists($this, $methodName))
      {
        $facebookvalue = call_user_method($methodName, $this, $facebookEvent);
        if ($facebookvalue != $value)
        {
          $scheduleData[$key] = $facebookvalue;
        }
      }
  	}
  	
  	if (!empty($scheduleData))
  	{
  		$scheduleData['id'] = $schedule['id'];
  		debug($scheduleData);
  		return $this->Schedule->save(array('Schedule' => $scheduleData));
  	}
  	return true;
  }
  
	function syncEvent($schedule, $facebookEvent)
	{
		if ($schedule && $facebookEvent)
		{
			// sync data
      debug($facebookEvent);
			debug($schedule);
			$scheduleUpdatedDate = date_create($schedule['modified']);
			$facebookUpdatedDate = date_create($facebookEvent['updated_time']);
			$localTimezone = new DateTimezone(date_default_timezone_get());
			$offset = $localTimezone->getOffset($scheduleUpdatedDate);
      // convert facebook time to server time
			$facebookUpdatedDate->modify("{$offset} seconds");
      debug($scheduleUpdatedDate);
			debug($facebookUpdatedDate);
			if ($scheduleUpdatedDate > $facebookUpdatedDate)
			{
				$this->syncFacebookEvent($schedule, $facebookEvent);
			}
			else if ($facebookUpdatedDate > $scheduleUpdatedDate)
			{
				$this->syncSchedule($schedule, $facebookEvent);
			}
			else
			{
				// do nothing if updated at same time
			}
		}
		else if ($schedule)
		{
			/// facebook event must have been deleted...remove it
			$this->Schedule->delete($schedule['id']);
		}
		else if ($facebookEvent)
		{
      //TODO: remove facebook event
      $this->fbAPI($facebookEvent['id'], 'DELETE');
		}
	}
	
	function syncAllEvents()
	{
    $this->Schedule->contain(array('Hometeam', 'Awayteam', 'Location'));
    $schedules = $this->Schedule->getAllFacebookEvents();
    debug($schedules);
    
    foreach ($schedules as $schedule)
    {
      foreach (array('team1_facebook_event_id' => 'Hometeam', 'team2_facebook_event_id' => 'Awayteam') as $eventField => $team)
      {
        // if the team has a facebook group id set, get all it's events rather than 
        if (!empty($schedule[$team]['facebook_group_id']))
        {
          debug($schedule[$team]['facebook_group_id']);
        }
        else if (!empty($schedule['Schedule'][$eventField]))
        {
          debug($schedule['Schedule'][$eventField]);
        }
      }
    }
	}
}
?>