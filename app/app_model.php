<?php
App::import('Lib', 'LazyModel.LazyModel');
class AppModel extends LazyModel
{
  var $actsAs = array('Containable', 'Mi.OneQuery');

  var $FB = false;

  private function getFB()
  {
    if ($this->FB === false)
    {
    		App::import('Lib', 'Facebook.FB');
    		$this->FB = new FB();
    }
    return $this->FB;
  }

  public function fbAPI($path, $method = "GET", $params = array())
  {
    $FB = $this->getFB();
    return $FB->api($path, $method, $params);
  }

  function afterFind($results, $primary=false)
  {
    if($primary == true)
    {
      if(Set::check($results, '0.0'))
      {
        $fieldNames = array_keys($results[0][0]);
        foreach($results as $key=>$value)
        {
          foreach ($fieldNames as $fieldName)
          {
            $results[$key][$this->alias][$fieldName] = $value[0][$fieldName];
          }
          unset($results[$key][0]);
        }
      }
    }

    return $results;
  }

  public function getMail()
  {
    return Mail::instance();
  }
}
class Mail
{
  public $forceMail;					//there is a bug, when running shell. Debug gets set to 1.
  private static $m_pInstance;
  private $mails;
  public function __construct()
  {
    $this->mails = array();
    $tihs->forceMail = false;
  }
  public static function instance()
  {
    if (!self::$m_pInstance)
    {
      self::$m_pInstance = new Mail();
    }

    return self::$m_pInstance;
  }

  public function mail($to,$subject=null,$message=null,$headers=null,$txt=false)
  {
    if(defined('CORE_TEST_CASES'))
    {
      $this->mails[]=array('to'=>$to,'subject'=>$subject,'message'=>$message,'headers'=>$headers);
    }
    else
    {
      if(Configure::read('debug') == 0 || (true == $this->forceMail))
      {
        mail($to, $subject, $message, $headers,'-fno-reply@myezteam.com');
      }
      else
      {
        debug("TO: $to Message: $message Headers: $headers");
      }
    }
  }

  /***
   * This will mail a message to no-reply@domain.com and bcc the $bccTo list of emails
   * @param $bccTo who to bcc the email to
   * @param $subject
   * @param $message
   * @param $headers
   * @return unknown_type
   */
  public function mailBcc($bccTo,$subject=null,$message=null,$headers=null)
  {
    $this->mail('no-reply@leaguelogix.com',$subject,$message,'Bcc: '.$bccTo. "\r\n".$headers);
  }

  public function sendTxt($to,$subject=null,$message=null)
  {
    $this->mail('no-reply@leaguelogix.com',$subject,$message,'Bcc: '.$to. "\r\n",true);
  }

  public function getMessages()
  {
    return $this->mails;
  }
  public function clear()
  {
    $this->mails=array();
  }

}
?>