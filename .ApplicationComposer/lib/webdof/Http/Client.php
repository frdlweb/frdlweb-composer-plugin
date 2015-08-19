<?php
/**
 * Copyright  (c) 2015, Till Wehowski
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. Neither the name of frdl/webfan nor the
 *    names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY frdl/webfan ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL frdl/webfan BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 */
namespace webdof\Http;


class Client
{

  public $http;
  
  protected $E;


  function __construct($debug = 0, $html_debug = 1, $follow_redirect = 1, $E = E_USER_ERROR)
    {
      $this->http = (class_exists('\webdof\wHTTP')) ? new \webdof\wHTTP(NULL)
	                       : null;
	 if(null !== $this->http){					   
       $this->http->debug=$debug;
       $this->http->html_debug=$html_debug;
       $this->http->follow_redirect=$follow_redirect;
	 }
	 
	 $this->E = $E;
    }


/*
  ->post( $url, $parameters, $send_cookies)
  ->put( $url, $parameters, $send_cookies) 
  ->delete( $url, $parameters, $send_cookies)
   
  ->get( $url, $parameters, $send_cookies)
  
  ...
  */
  public function __call($name, $args){
  	$args = func_get_args();
  	$arguments = $args;
  	
  	 if('post' === strtolower($name) || 'put' === strtolower($name) || 'delete' === strtolower($name)){
  	 	$arguments = array();
  	 	$arguments[] = array_shift($args);
  	 	$arguments[] = strtoupper($name);
  	 	if(count($args)>0)$arguments[] = array_shift($args);
  	 	if(count($args)>0)$arguments[] = array_shift($args);
  	 	if(count($args)>0)$arguments[] = array_shift($args);
  	 	return call_user_func_array(array($this, 'request'), $arguments);
	 }elseif('get' === strtolower($name)){
  	 	$arguments = array();
  	 	$arguments[] = array_shift($args);
  	 	$arguments[] = 'GET';
  	 	if(count($args)>0){
  	 		$arguments[] = array_shift($args);
  	 		if(!strpos($arguments[0], '?'))$arguments[0].= '?';
  	 		$arguments[0].= http_build_query($arguments[2]);
  	 	}	
  	 	if(count($args)>0)$arguments[] = array_shift($args);
  	 	if(count($args)>0)$arguments[] = array_shift($args);
  	 	return call_user_func_array(array($this, 'request'), $arguments);
	 }
  	 	 
  	 trigger_error('Undefined method and not able to autoload '.get_class($this).'->'.$name, $this->E);
  }
  
  
  public function request($url = null, $method = 'POST', $post = array(), $send_cookies = array(), $E = E_USER_WARNING)
    {
      if(null===$this->http){
          return $this->fallback($url, $method,$post);
	  }	
		
      $error=$this->http->GetRequestArguments($url,$arguments);
      $arguments['RequestMethod']= $method;
       if($method === 'POST' || $method === 'PUT' )
        {
           $arguments['PostValues']=array();
           foreach($post as $key => $value)
             {
                $arguments['PostValues'][$key] = $value;
             }
        }

      $errorstr = '';
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $E);
      
     foreach( $send_cookies as $section => $cookies)
      {
        foreach($cookies as $domain => $dirs)
           {

               foreach($dirs as $path => $c)
                 {
                   foreach($c as $cname => $cookie)
                     {
                        $this->http->SetCookie($cookie['name'], $cookie['value'], $cookie['expires'], $cookie['path'], $cookie['domain'] , $cookie['secure'], 1);
                     }
                 }
           }
      }

      $this->http->RestoreCookies($send_cookies, 1);



      $error=$this->http->Open($arguments);
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $E);

      $error=$this->http->SendRequest($arguments);
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $E);

      $headers=array();
      $error= $this->http->ReadReplyHeaders($headers);
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $E);

      $error = $this->http->ReadWholeReplyBody($responsebody);
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $E);

      if(strlen($error)==0)
      {

        $this->http->SaveCookies($site_cookies);
        if(strlen($error=$this->http->RestoreCookies($site_cookies, 1))==0)
        {
            $this->http->SaveCookies($saved_cookies);
            if(strcmp(serialize($saved_cookies), serialize($site_cookies)))
            {
               // $content.= "FAILED: the saved cookies do not match the restored cookies.\n";
            }
            else{
                   // $content.= "OK: the saved cookies match the restored cookies.\n";

                  // $content.= '<pre>'.print_r($saved_cookies, TRUE).'</pre>';
                }
        }
      }

      $this->http->Close();

      if($error != '')return $errorstr;

      $r = array();
      $r['status'] = intval($this->http->response_status);
      $r['headers'] = $headers;
      $r['body'] = $responsebody;
      $r['saved_cookies'] = $saved_cookies;
      $r['errorstr'] = $errorstr;
      return $r;
    }


  public function SimpleGet($url, $E = E_USER_WARNING)
    {
      if(null===$this->http){
          return $this->getZipContents($url);
	  }	
	  	
      $error=$this->http->GetRequestArguments($url,$arguments);
      $arguments['RequestMethod']='GET';
      $errorstr = '';
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $E);

      $error=$this->http->Open($arguments);
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $E);

      $error=$this->http->SendRequest($arguments);
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $E);

      $error = $this->http->ReadWholeReplyBody($responsebody );
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $E);

      $this->http->Close();
      if($error != '')return $errorstr;

      return $responsebody;
    }


  public function getZipContents($url, $timeout = 1200, $E = E_USER_WARNING)
    {

      $opts = array('http' =>
        array(
          'method'  => 'GET',
          'timeout' => $timeout,
          'user_agent'=>  __CLASS__ .'(Webfan.de Client Script)',
        )
      );

       $context  = stream_context_create($opts);
       $result = file_get_contents($url, false, $context);
       return $result;
    }


  public function fallback($url, $method = 'POST', $data = array(), $timeout = 1200, $E = E_USER_WARNING)
    {

      $opts = array('http' =>
        array(
         'method'  => $method,
         'timeout' => $timeout,
          'user_agent'=>  __CLASS__ .'(Webfan.de Client Script)',
        )
      );
	  
	  if($method === 'POST' || $method === 'PUT'){
	  	$opts['http']['header']= "Accept-language: en\r\n".
                    "Content-type: application/x-www-form-urlencoded\r\n";
					
		$opts['http']['content']= http_build_query($data);
      
	  }

       $context  = stream_context_create($opts);
       $result = file_get_contents($url, false, $context);
	   
	   
      $r = array();
      $r['status'] = (!empty($result)) ? 200 : 409;
      $r['headers'] = array();
      $r['body'] = $result;
      $r['saved_cookies'] = array();
      $r['errorstr'] = '';
   
       return $r;
    }



  public function fallback_auth($url, $method = 'POST', $data = array(),  $auth_user, $auth_pass, $timeout = 1200, $E = E_USER_WARNING)
    {
      $auth = base64_encode('user:password');
      $auth = array("Authorization: Basic ".base64_encode($auth_user.':'.$auth_pass));

      $opts = array('http' =>
        array(
         'method'  => $method,
         'timeout' => $timeout,
          'user_agent'=>  __CLASS__ .'(Webfan.de Client Script)',
        )
      );
	  
	  $opts['http']['header'] .= $auth."\r\n";
	  if('GET' !== $method){
	  	$opts['http']['header'] .= "Accept-language: en\r\n".
                    "Content-type: application/x-www-form-urlencoded\r\n";
					
		$opts['http']['content']= http_build_query($data);
      
	  }

       $context  = stream_context_create($opts);
       $result = file_get_contents($url, false, $context);
	   
      $r = array();
      $r['status'] = (!empty($result)) ? 200 : 409;
      $r['headers'] = array();
      $r['body'] = $result;
      $r['saved_cookies'] = array();
      $r['errorstr'] = '';
   
       return $r;
       return $result;
    }

}