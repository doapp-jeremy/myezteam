<?php
class ResponsesController extends AppController
{
	public $name = 'Responses';
	public $scaffold = 'admin';
	public $components = array('FastrestServer');
	
	function add($ext = 'json')
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
		
		Configure::write('debug', 0);
		if (!empty($this->data['Response']['player_id']) && !empty($this->data['Response']['event_id']) && !empty($this->data['Response']['response_type_id']))
		{
		  $this->Response->create();
		  if (!$this->Response->save($this->data))
		  {
		    $this->FastrestServer->renderError(403,'ERROR: Could not save response');
		    return;
		  }
		  $this->data['Response']['id'] = $this->Response->getLastInsertID();
		  $this->FastrestServer->renderData($ext, $this->data);
		}
		else
		{
			$this->FastrestServer->renderError(400,'ERROR: Request data not formatted correctly.');
			$this->log('Incorrectly formatted data: '.var_export(utf8_encode(trim(file_get_contents('php://input'))),true).' ['.__METHOD__.'::'.__LINE__.']');
			return;
		}
	}
}
