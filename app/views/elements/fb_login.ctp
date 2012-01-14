<?php $fbPerms = 'email, read_stream, publish_stream, create_event, rsvp_event, sms, offline_access, publish_checkins'; ?>
<?php $fbPerms.= ', user_events, friends_events, user_location, friends_location, user_checkins, friends_checkins, user_groups'; ?>
<?php echo $facebook->login(array('perms' => $fbPerms)); ?>
