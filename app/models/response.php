<?php
class Response extends AppModel {
	var $name = 'Response';
	var $displayField = 'comment';

	var $belongsTo = array(
		'Event' => array(
			'className' => 'Event',
			'foreignKey' => 'event_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Player' => array(
			'className' => 'Player',
			'foreignKey' => 'player_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ResponseType' => array(
			'className' => 'ResponseType',
			'foreignKey' => 'response_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
?>