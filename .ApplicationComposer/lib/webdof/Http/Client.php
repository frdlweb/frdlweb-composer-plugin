<?php
/*################################################################################*
#
#   (c)Copyright Till Wehowski, http://Webfan.de
#   All rights reserved - Alle Rechte vorbehalten
#
#
#                 WEBFAN.de Software License
#
#                  * webdof license *
#                    http://look-up.webfan.de/1.3.6.1.4.1.37553.8.1.8.4.5
#
#                 Version 1.0.0
#
#  DIESE LIZENZ IST NUR G�LTIG IN VERBINDUNG MIT EINEM G�LTIGEN
#  WEBFAN SOFTWAREZERTIFIKAT.
#
#  THIS LICENSE REQUIRES A VALID WEBFAN SOFTWARE CERTIFICATE.
#
#  WEBFAN SOFTWARE DARF NICHT AUF EINEM NICHT
#  AUTHORISIERTEN SERVER ZUM DOWNLOAD ANGEBOTEN WERDEN!
#
#  YOU ARE NOT ALLOWED TO REDISTRIBUTE THIS SOFTWARE ON A NOT AUTHORIZED SERVER.
#
#  Diese Software wird dem Endbenutzer zur Benutzung im Rahmen der Zweckbestimmung
#  und im Rahmen der per Kaufvertrag oder Nutzungsvereinbarungen vereinbarten
#  Leistungen/Funktionen zur Verf�gung gestellt.
#  Quellcodekommentare sowie sichtbare bzw. klickbare Links d�rfen nicht entfernt
#  werden.
#  Eine Nutzung �ber die Rahmenvereinbarungen hinaus ist nicht erlaubt, die
#  Lizenz kann durch erg�nzende Lizenzen bzw. lizensierte Funktionen erweitert
#  werden. Teile der Software k�nnen erweitert oder abweichend lizensiert sein,
#  entsprechende Lizenzen sind in diesem Falle beigef�gt.
#
#  Im Falle der Modifikation der Software durch den Endbenutzer k�nnen vorgesehene
#  Funktionalit�ten oder Updatedienste unter Umst�nden nicht mehr gew�hrleistet werden.
#
#  Die Benutzung der Software erfolgt auf eigene Gefahr, jegliche Haftung ist
#  ausgeschlossen insofern Vorsatz, grobe Fahrl��igkeit oder sonstige
#  gesetzliche Haftungsverpflichtungen nicht in Betracht kommen.
#
#  (c)Webfan Software http://domainundhomepagespeicher.webfan.de/software-center/
#
*################################################################################*/
namespace webdof\Http;


class Client
{

  public $http;


  function __construct($debug = 0, $html_debug = 1, $follow_redirect = 1)
    {
      $this->http = (class_exists('\webdof\wHTTP')) ? new \webdof\wHTTP(NULL)
	                       : null;
	 if(null !== $this->http){					   
       $this->http->debug=$debug;
       $this->http->html_debug=$html_debug;
       $this->http->follow_redirect=$follow_redirect;
	 }
    }


  public function request($url = null, $method = 'POST', $post = array(), $send_cookies = array(), $E = E_USER_WARNING)
    {
      if(null===$this->http){
          return $this->fallback($url, $method,$post);
	  }	
		
      $error=$this->http->GetRequestArguments($url,$arguments);
      $arguments['RequestMethod']= $method;
       if($method !== 'GET')
        {
           $arguments['PostValues']=array();
           foreach($post as $key => $value)
             {
                $arguments['PostValues'][$key] = $value;
             }
        }

      $errorstr = '';
      $errorstr.= ($error == '') ? '' : $error . trigger_error($error.' in '. __CLASS__.' line '.__LINE__, $E);
      /*
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
*/
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
	  
	  if('GET' !== $method){
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