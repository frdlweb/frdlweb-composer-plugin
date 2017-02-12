<?php
/**
 * This file belongs to php software provided by Webfan.de.
 * (c) Copyright by Till Wehowski, http://www.webfan.de
 * (c) Urheberrecht Till Wehowski, http://www.webfan.de
 * Alle Rechte vorbehalten - All rights reserved
 * 
 * License/Lizenz: webdof license
 * You can read the terms and conditions of the license online at: 
 * http://look-up.webfan.de/1.3.6.1.4.1.37553.8.1.8.4.5
 * Die Lizenzbedingungen sind hier einsehbar:
 * http://look-up.webfan.de/1.3.6.1.4.1.37553.8.1.8.4.5
 * */
namespace webdof\Webfan;


class APIClient extends \webdof\Http\Client
{

   const VERSION = '2.0';
   const API_SERVER = 'interface.api.webfan.de';

   public $response;

   public $protocoll;
   public $host;
   public $verb;
   public $interface;
   public $endpoint;
   public $post;
   public $send_cookies;
   public $auth_user;
   public $auth_pass;
   public $resource;
   public $format;
   public $func;
   public $args;
   public $get;
   public $version;

   protected $E = E_USER_WARNING;
   
   

   function __construct($debug = 0, $html_debug = 1, $follow_redirect = 1)
     {
          parent::__construct($debug, $html_debug, $follow_redirect);
         
     }


  public function prepare($protocoll = 'https',
                          $host = self::API_SERVER,
                          $verb = 'POST',
                          $interface = 'x123',   
                          $endpoint = 'test',
                          $post = array(),
                          $send_cookies = array(),
                          $auth_user = '',
                          $auth_pass = '',
                          $resource = 'auth',
                          $format = 'json',
                          $func = 'debug',
                          $args = array(),
                          $get = array(),
                          $version = 1,
                          $E = E_USER_WARNING)
    {
      if($protocoll !== NULL)$this->protocoll = $protocoll;
      if($host !== NULL)$this->host = $host;
      if($verb !== NULL)$this->verb = $verb;
      if($interface !== NULL)$this->interface = $interface;
      if($endpoint !== NULL)$this->endpoint = $endpoint;
      if($post !== NULL)$this->post = $post;
      if($send_cookies !== NULL)$this->send_cookies = $send_cookies;
      if($auth_user !== NULL)$this->auth_user = $auth_user;
      if($auth_pass !== NULL)$this->auth_pass = $auth_pass;
      if($resource !== NULL)$this->resource = $resource;
      if($format !== NULL)$this->format = $format;
      if($func !== NULL)$this->func = $func;
      if($args !== NULL)$this->args = $args;
      if($get !== NULL)$this->get = $get;
      if($version !== NULL)$this->version = $version;
      if($E !== NULL)$this->E = $E;

    }

  public function set($settings = array() )
    {
      foreach($settings as $key => $value)
        {
          $this->{$key} = $value;
        }
    }


  public function request($url = null, $method = 'POST', $post = array(), $send_cookies = array(), $E = E_USER_WARNING)
    {
      $fa = func_get_args();

		
      $protocoll = $this->protocoll;
      $host = $this->host;
      $verb = $this->verb;
      $interface = $this->interface;
      $endpoint = $this->endpoint;
      $post = $this->post;
      $send_cookies = $this->send_cookies;
      $auth_user = $this->auth_user;
      $auth_pass = $this->auth_pass;
      $resource = $this->resource;
      $format = $this->format;
      $func = $this->func;
      $args = $this->args;
      $get = $this->get;
      $version = $this->version;
      $E = $this->E;

      $method = $verb;
      $authentication=(strlen($auth_user) ? UrlEncode($auth_user).":".UrlEncode($auth_pass)."@" : "");
      $url = $protocoll. '://'
      //       .$authentication
             .$host;
      $url.= '/';
      $url.= 'v'.$version.'/';
      $url.= $interface.'/';
      $url.= $endpoint.'/';
      $url.= $resource.'/';
      foreach($args as $k => $arg)
       {
         $url.= $arg.'/';
       }
      $url.= $func.'.'.$format;
      if(count($get) > 0)$url.= '?';
      $url.= http_build_query($get);

	  if(count($fa)>0){
          trigger_error('Use of any arguments when calling '.__METHOD__.' is deprecated. Fallback calling parents method!', E_USER_DEPRECATED);
	      return parent::request($url, $method, $post, $send_cookies, $E);
	  }	
	  
	   if(null===$this->http){
          return $this->fallback_auth($url, $method,$post, $auth_user, $auth_pass);
	  }	
	  
	  
      $error=$this->http->GetRequestArguments($url,$arguments);
      $arguments['RequestMethod']= $method;
       if(count($post) > 0)
        {
           $arguments['PostValues']=array();
           foreach($post as $key => $value)
             {
                $arguments['PostValues'][$key] = $value;
             }
        }

      $arguments["AuthUser"]=UrlDecode($auth_user);
      $arguments["AuthPassword"] = UrlEncode($auth_pass);

      $errorstr = '';
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $this->E);

      $this->http->RestoreCookies($send_cookies, 1);



      $error=$this->http->Open($arguments);
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $this->E);

      $error=$this->http->SendRequest($arguments);
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $this->E);

      $headers=array();
      $error= $this->http->ReadReplyHeaders($headers);
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $this->E);

      $error = $this->http->ReadWholeReplyBody($responsebody );
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $this->E);

      if(strlen($error)==0)
      {

        $this->http->SaveCookies($site_cookies);
        if(strlen($error=$this->http->RestoreCookies($site_cookies, 1))==0)
        {
            $this->http->SaveCookies($saved_cookies);
            if(strcmp(serialize($saved_cookies), serialize($site_cookies)))
            {
              
            }
            else{
                   

                
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

      $this->response = $r;
      return $r;
    }

}
