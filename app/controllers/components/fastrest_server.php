<?php

class FastrestServerComponent extends Object {
  var $currAppId = null;	//What is the app ID of the current consumer of the service

  //called before Controller::beforeFilter()
  function initialize(&$controller, $settings = array()) {
    // saving the controller reference for later use
    $this->controller =& $controller;
  }

  /**
   *
   * Decodes the POST body data, and sets the $this->data in the controller.
   * @param string $type json|xml
   */
  function decodePOSTAndSetData($type='json'){
    $this->controller->data = NULL;
    switch ($type) {
      case 'xml':
        if (!class_exists('XmlNode')) {
          App::import('Core', 'Xml');
        }
        $xml = new Xml(trim(file_get_contents('php://input')));
        if (count($xml->children) == 1 && is_object($dataNode = $xml->child('data'))) {
          //don't camelize
          $this->controller->data = $dataNode->toArray(false);
        } else {
          //don't camelize
          $this->controller->data = $xml->toArray(false);
        }
        break;
        	
      default:
        $jsonData = json_decode(utf8_encode(trim(file_get_contents('php://input'))), true);

        if(!is_null($jsonData) and $jsonData !== false) {
          $this->controller->data = $jsonData;
        }
        break;
    }
  }

  /**
   *
   * This handles settting up the response header, and serializing data passed into the
   * format specified by extenstion (ext)
   *
   * @param string $ext json|xml
   * @param array $thedata the array representation of data to be serialized
   */
  function renderData($ext='json',$thedata=array()){
    $this->controller->autoRender = false;	//Don't even hit a view/layout
    header('HTTP/1.0 200 OK');
    switch ($ext) {
      case 'xml':
        App::import('Core', array('Xml'));
        header('Content-type: application/xml');
        echo '<?xml version="1.0" encoding="UTF-8" ?>';
        $options = array('attributes' => false, 'format' => 'tags', 'cdata' => true);
        $thedata =& new Xml($thedata, $options);
        echo $thedata->toString($options + array('header' => false));
        break;
        	
      default:
        header('Content-type: application/json');
        echo json_encode($thedata);
        break;
    }
  }

  /**
   *
   * This will directly echo out a simple text error and a status code header.
   *
   *
   * See http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html for status codes.
   *
   * @param mixed $responseCode int if supported status code, if not suppored will use this string as the response header
   * @param string $error the error string returned to the client
   */
  function renderError($responseCode=400,$error='ERROR:'){
    $this->controller->autoRender = false;	//Don't even hit a view/layout
    switch ($responseCode) {
      case 400:
        $header = 'HTTP/1.0 400 Bad Request';
        break;
      case 403:
        $header = 'HTTP/1.0 403 Forbidden';
        break;
      case 500:
        $header = 'HTTP/1.0 500 Internal Server Error';
        break;
      default:
        $header = $responseCode;
        break;
    }
    header($header);
    echo $error;

    if(isset($this->currAppId)){
      $this->log('The following error was caused by appId: '.$this->currAppId.'. Error: '.$error.'. ['.__METHOD__.'::'.__LINE__.']');
    }
  }

  /**
   * Returns true if the current call a POST request
   *
   * @return boolean True if call is a POST
   * @access public
   */
  function isPost() {
    return (strtolower(env('REQUEST_METHOD')) == 'post');
  }

  /**
   * Returns true if the current call a PUT request
   *
   * @return boolean True if call is a PUT
   * @access public
   */
  function isPut() {
    return (strtolower(env('REQUEST_METHOD')) == 'put');
  }

  /**
   * Returns true if the current call a GET request
   *
   * @return boolean True if call is a GET
   * @access public
   */
  function isGet() {
    return (strtolower(env('REQUEST_METHOD')) == 'get');
  }

  /**
   * Returns true if the current call a DELETE request
   *
   * @return boolean True if call is a DELETE
   * @access public
   */
  function isDelete() {
    return (strtolower(env('REQUEST_METHOD')) == 'delete');
  }

  /**
   *
   * Validate the API key. If invalid, erorr will be rendered automatically.
   * @param mixed $data must contain ['doappauth']['apiauth'] array
   * @return boolean
   */
  function isApiKeyValid($data)
  {
    return true;
    
    if(!isset($data['doappauth']['apiauth']['key'])){
      $this->renderError(400,'ERROR: Empty API Key');
      return false;
    }

    App::import('Lib', 'doappapi/DoappApiKey');
    $this->DoappApiKey = new DoappApiKey();

    if(false===$this->DoappApiKey->isValidKey($data['doappauth']['apiauth']['key'])){
      $this->log('Invalid API KEY:'.$data['doappauth']['apiauth']['key'].' ['.__METHOD__.'::'.__LINE__.']');
      $this->renderError(400,'ERROR: Invalid API Key');
      return false;
    }
    else{
      //Store off the app id for logging or whatever else
      $this->currAppId = $this->DoappApiKey->getAppIdByApiKey($data['doappauth']['apiauth']['key']);
      return true;
    }
  }

}

?>