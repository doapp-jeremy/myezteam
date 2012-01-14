<?php
/* Player Fixture generated on: 2011-01-08 06:01:30 : 1294467930 */
class PlayerFixture extends CakeTestFixture {
	var $name = 'Player';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'player_type_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'team_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'team_user' => array('column' => array('team_id', 'user_id'), 'unique' => 1), 'players_user' => array('column' => 'user_id', 'unique' => 0), 'players_player_type' => array('column' => 'player_type_id', 'unique' => 0), 'players_team' => array('column' => 'team_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	var $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'player_type_id' => 1,
			'team_id' => 1,
			'created' => '2011-01-08 06:25:30',
			'modified' => '2011-01-08 06:25:30'
		),
	);
}
?>