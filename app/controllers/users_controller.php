<?php
class UsersController extends AppController
{
	public $name = 'Users';
	public $scaffold = "admin";
	public $components = array('FastrestServer');
	
	var $allowedActions = array('login', 'logout', 'register', 'activate', 'facebook_login');
	
	function beforeFilter()
	{
	  parent::beforeFilter();
	  
	  // We need to map the username field to email for the auth component because
	  // we use email to login...then it will automatically hash passwords when new users are created.
	  $this->Auth->fields = array('username' => 'email', 'password' => 'password_new');
	  $this->Auth->allowedActions = $this->allowedActions;
	}
	
	function save($ext = 'json')
	{
		//Set the default response to bad request, so any unhandled errors dont return 200
		header('HTTP/1.0 400 Bad Request');
		
		if (!$this->FastrestServer->isPost())
		{
			$this->log('POST not used'.' ['.__METHOD__.'::'.__LINE__.']');
			$this->FastrestServer->renderError(400,'ERROR: Must POST data');
			return;
		}
		
		//decode POST body and put in to $this->data
		// don't need to do this, call is coming from inside app
//		$this->FastrestServer->decodePOSTAndSetData($ext);
		if (empty($this->data))
		{
			$this->FastrestServer->renderError(400,'ERROR: No data specified');
			return;
		}
		
		if (!empty($this->data['User']['id']))
		{
		  $userId = $this->data['User']['id'];
		  $user = false;
		  if (!$this->User->save($this->data))
		  {
		    $this->FastrestServer->renderError(403,'ERROR: Could not save user');
		    return;
		  }
//		  $this->User->contain();
//		  $user = $this->User->read(null, $this->data['User']['id']);
//		  if (!$this->User->save($this->data))
//		  {
//		    $this->FastrestServer->renderError(403,'ERROR: Could not find user');
//		    return;
//		  }
//		  $this->FastrestServer->renderData($ext, $user);
		  $this->FastrestServer->renderData($ext, $this->data);
			Configure::write('debug', 0);
		}
		else
		{
			$this->FastrestServer->renderError(400,'ERROR: Request data not formatted correctly.');
			$this->log('Incorrectly formatted data: '.var_export(utf8_encode(trim(file_get_contents('php://input'))),true).' ['.__METHOD__.'::'.__LINE__.']');
			return;
		}
	  
	}
	
	function activate($md5Email = null, $activation = null)
	{
	  debug($md5Email);
	  debug($activation);
	  if (!$md5Email || !$activation && empty($this->data))
	  {
	    $this->Session->setFlash("Invalid request");
	    $this->redirect("/");
	  }
	  $user = array();
	  $this->data['edit_email'] = false;
	  $this->data['edit_password'] = true;
	  if (!empty($this->data['User']))
	  {
	    // verify passwords match
	    debug($this->data);
	    $this->User->contain();
	    $user = $this->User->read(null, $this->data['User']['id']);
	    $md5Email = $this->data['User']['md5email'];
	    $activation = $this->data['User']['activation'];
	    if (empty($this->data['User']['password_new']) || empty($this->data['User']['confirm_password']))
	    {
	      $this->Session->setFlash("Please enter a password and confirm it");
	    }
	    else if ($this->data['User']['password_new'] != $this->data['User']['confirm_password'])
	    {
	      $this->Session->setFlash("Passwords do not match");
	    }
	    else
	    {
	      // save password
	      $this->User->id = $this->data['User']['id'];
	      if ($this->User->saveField('password_new', $this->Auth->password($this->data['User']['password_new'])))
	      {
	        $this->Session->setFlash("Account has been activated.");
	        $this->redirect('/');
	      }
	      else
	      {
	        $this->Session->setFlash("Could not save password.");
	      }
	    }
	  }
	  else
	  {
  	  $conditions = array("md5(User.email)" => $md5Email);
  	  $this->User->contain();
  	  $user = $this->User->find('first', compact('conditions'));
  	  if ($user['User']['activation'] != $activation)
  	  {
  	    $this->data['edit_password'] = false;
  	    $this->Session->setFlash("Could not activate user: activation code does not match.");
  	  }
	  }
	  debug($user);
	  if (empty($user))
	  {
	    $this->data['edit_email'] = true;
	    $this->data['edit_password'] = false;
	    $this->Session->setFlash("Could not activate user: could not find matching email.");
	  }
	  else
	  {
	    $this->data['User'] = $user['User'];
	  }
	  // reset activation code
	  if (!empty($user['User']['activation']))
	  {
	    $user['User']['activation'] = null;
	    $this->User->id = $user['User']['id'];
	    $this->User->saveField('activation', null);
	  }
	  
	  $this->data['User']['md5email'] = $md5Email;
	  $this->data['User']['activation'] = $activation;
	}
	
	function facebook_login()
	{
	  $fbUser = $this->Session->read('FB');
	  if (!empty($fbUser['Me']['id']))
	  {
	    $this->Session->setFlash('You are already logged in via Facebook');
	    $this->redirect('/');
	  }
	}
	
	function __login($data)
	{
	  $data['User']['password_new'] = $this->Auth->password($data['User']['password']);
	  if ($this->Auth->login($data))
	  {
	    return true;
	  }
	  else
	  {
	    $user = $this->User->validateOldLogin($data);
	    debug($user);
	    if ($user && $this->Auth->login($user))
	    {
	      return true;
	    }
	  }
	  return false;
	}
	
	function register()
	{
	  if ($this->Session->read('Auth.User'))
	  {
	    $this->Session->setFlash("You are already registered.");
	    $this->redirect('/');
	  }
	  
	  if (!empty($this->data))
	  {
	    if (empty($this->data['User']['password']))
	    {
	      $this->Session->setFlash("You must enter a password.");
	    }
	    else
	    {
	      if ($this->__login($this->data))
	      {
	        debug("Logged in");
	        debug($this->Session->read());
	        $this->Session->setFlash("Logged in");
	        // save facebook id
	        $user = $this->Session->read('Auth.User');
	        $this->redirect('/');
	        return;
	      }
	      else
	      {
	        $this->User->contain();
	        $user = $this->User->findByEmail($this->data['User']['email']);
	        if (!empty($user))
	        {
	          $this->Session->setFlash("Invalid login info");
	        }
	        else
	        {
	          // create new user
	          $this->User->createNewUser($this->data);
	        }
	      }
	    }
	  }

	  $fbUser = $this->Connect->user();
	  foreach (array('email', 'first_name', 'last_name') as $field)
	  {
	    if (!empty($fbUser[$field]))
	    {
	      if (!isset($this->data[$this->User->alias][$field]))
	      {
	        $this->data[$this->User->alias][$field] = $fbUser[$field];
	      }
	    }
	  }
	  $this->data['User']['facebook_id'] = $fbUser['id'];
	  $this->data['User']['password_new'] = '';
	}
	
  protected function saveSessionToDB($session)
  {
  	$user = $this->Session->read("Auth.User");
  	if ($session && $user)
  	{
  	  $this->User->FacebookSession->contain();
  	  $fbSession = $this->User->FacebookSession->read(null, $session['uid']);
  	  if (empty($fbSession))
  	  {
  	    $this->User->FacebookSession->create();
  	    $fbSession['FacebookSession'] = array();
  	  }
  	  foreach ($session as $key => $value)
  	  {
  	    $fbSession['FacebookSession'][$key] = $value;
  	  }
  	  if ($this->User->FacebookSession->save($fbSession))
  	  {
  	    return true;
  	  }
  	}
  	return false;
  }
  
	
	
	function login()
	{
	  $fbUser = $this->Connect->user();
	  $user = $this->Session->read('Auth.User');
	  
	  if (!empty($fbUser['timezone']) || ($fbUser['timezone'] == '0'))
	  {
	    $this->Session->write('timezone_offset', $fbUser['timezone']);
	  }
	  
	  if ($fbUser && $user)
	  {
	    // save facebook user info to db
	    $this->saveSessiontoDB($this->Connect->FB->getSession());
	  }
	  $redirectUrl = $this->Session->read('Auth.redirect');
	  if (!$redirectUrl)
	  {
	    $redirectUrl = $this->Auth->loginRedirect;
	  }
	  if ($user)
		{
		  if (!$fbUser)
		  {
		    $this->Session->setFlash("Please login via facebook.");
		    $this->redirect(array('action' => 'facebook_login'));
		  }
		  if (!$this->Connect->hasAccount)
		  {
		    debug("User created did not have an account");
		  }
			$this->Session->setFlash('You are already logged in!');
			$this->redirect($redirectUrl);
		}
		else if ($fbUser)
		{
			$this->Session->setFlash("We did not find a user with email '{$fbUser['email']}' in our system.");
		  $this->redirect(array('action' => 'register'));
		}
		if (!empty($this->data))
		{
		  $this->data['User']['password_new'] = $this->Auth->password($this->data['User']['password']);
		  $this->User->cacheQueries = false;
  		if ($this->Auth->login($this->data))
  		{
  		  $this->Session->setFlash("You are logged in manually.");
  		  $this->redirect($redirectUrl);
  		}
  		else
  		{
  		  $user = $this->User->validateOldLogin($this->data);
  		  if ($user && $this->Auth->login($user))
  		  {
  		    $this->Session->setFlash("Logged in");
  		    $this->redirect($redirectUrl);
  		  }
  		  else
  		  {
  		    $this->Session->setFlash("Could not login");
  		  }
  		}
		}
	}
	
	function logout()
	{
	  $this->Session->delete('FB');
	  $this->Session->delete('Auth');
	  $this->Session->delete($this->friendsKey);
		$this->Session->setFlash('Good-Bye');
		$this->Session->destroy();
		$this->redirect($this->Auth->logout());
	}
		
}
