<?php 
class FacebookApiTask extends Shell
{
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

	public function fbRest($params)
	{
    $FB = $this->getFB();
    return $FB->api($params);
	}
	
	public function fbAPI($path, $method = "GET", $params = array())
	{
		$FB = $this->getFB();
		return $FB->api($path, $method, $params);
	}
	
	public function setSession($session)
	{
		$this->getFB()->setSession($session, false, false);
	}

  public function __call($method, $params){
//    try {
      return call_user_func_array(array(self::getFB(), $method), $params);
//    } catch (FacebookApiException $e) {
//      error_log($e);
//    }
  }
	
}
?>