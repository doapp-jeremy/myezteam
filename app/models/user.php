<?php
class User extends AppModel
{
	public $name = 'User';
	public $displayField = 'email';
	public $virtualFields = array(
	  'display_name' => "CONCAT(User.first_name, ' ', User.last_name, ' (', User.email, ')')"
	);
	public $validate = array(
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	
	public $belongsTo = array(
		'FacebookSession' => array(
			'className' => 'FacebookSession',
			'foreignKey' => 'facebook_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'TimeZone' => array(
			'className' => 'TimeZone',
			'foreignKey' => 'time_zone_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
	
	public $hasMany = array(
		'Player' => array(
			'className' => 'Player',
			'foreignKey' => 'user_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);
	
	public $hasAndBelongsToMany = array(
		'Team' => array(
			'className' => 'Team',
	    'joinTable' => 'teams_managers',
			'foreignKey' => 'user_id',
	    'associationForeignKey' => 'team_id',
	    'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);
		
  function getTempEmailForFacebookUserId($facebookUserId)
  {
  	return "{$facebookUserId}@facebook.com";
  }
	
	function generateActivation($user)
	{
	  return md5($user['User']['email'] . "_" . date("Y-m-d H:i:s"));
	}
	
	private $passwordSalt = 'PasswordSalt';
	
	function createNewUser($data)
	{
	  debug("TODO: create new user");
	  debug($data);
	  $this->contain();
	  $user = $this->findByEmail($data['User']['email']);
	  if (!empty($user))
	  {
	    return "A user with email '{$data['User']['email']}' already exists.";
	  }
	  $activation = $this->generateActivation($data);
	  $data['User']['activation'] = $activation;
	  $data['User']['password'] = $this->_hashLegacyPassword($data['User']['password']);
	  if (!$this->save($data))
	  {
	    return "Error: could not save user data";
	  }
	  $data['User']['id'] = $this->getLastInsertID();
	  
	  // send activation email
	  $to = $data['User']['email'];
	  $subject = "My EZ Team Activation";
	  $message = "Please follow the link below to activate your account.
	  ";
	  $emailMd5 = md5($to);
	  $linkPath = "http://{$_SERVER['SERVER_NAME']}/users/activate/{$emailMd5}/{$activation}";
	  $message.= "<a href='{$linkPath}'>{$linkPath}</a>";
	  $this->getMail()->mail($to, $subject, $message);
	  return $data;
	}
	
	function _hashLegacyPassword($password)
	{
	  return md5($password . $this->passwordSalt);
	}
	
	function validateOldLogin($data)
	{
	  debug($data);
	  $this->contain();
	  $user = $this->findByEmail($data['User']['email']);
	  
	  debug($user);
    if (!empty($user))
    {
      if (!isset($user['User']['password']) || !$user['User']['password'])
      {
        return $data['User']['email'];
      }
      else if ($this->_hashLegacyPassword($data['User']['password']) == $user['User']['password'])
      {
        // save new password
        $user['User']['password_new'] = $data['User']['password_new'];
        debug("Saving new user password...");
        debug($user);
        $this->save($user);
        return $user;
      }
    }
    debug("Could not validate login");
	  return false;
	}
	
	function checkFBAuth($fbUser)
	{
	  debug($fbUser);
	  if (!$fbUser)
	  {
	    debug("NO FB User");
	    exit();
	    return false;
	  }
	  
	  $conditions = array('or' => array('email' => $fbUser['email']));
	  $results = $this->checkAuthentication($conditions);
	  debug($results);
	  exit();
	  if ($results && !($results['User']['facebook_id'] == $fbUser['id']))
	  {
	    // set user's facebook id
	    $results['User']['facebook_id'] = $fbUser['id'];
	    $results['User']['fname'] = $fbUser['first_name'];
	    $results['User']['lname'] = $fbUser['last_name'];
//	    debug($results);
//	    exit();
	    $this->save($results);
	  }
	  return $results;
	}
		
}
