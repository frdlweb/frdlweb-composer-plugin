<?php
/**
 * 
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
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the frdl/webfan.
 * 4. Neither the name of frdl/webfan nor the
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
 * 
 *  @author 	Till Wehowski <php.support@webfan.de>
 *  @package    frdl\webfan\Autoloading\SourceLoader
 *  @uri        /v1/public/software/class/webfan/frdl.webfan.Autoloading.SourceLoader/source.php
 *  @file       frdl\webfan\Autoloading\SourceLoader.php
 *  @role       Autoloader 
 *  @copyright 	2015 Copyright (c) Till Wehowski
 *  @license 	http://look-up.webfan.de/bsd-license bsd-License 1.3.6.1.4.1.37553.8.1.8.4.9
 *  @license    http://look-up.webfan.de/webdof-license webdof-license 1.3.6.1.4.1.37553.8.1.8.4.5
 *  @link 	http://interface.api.webfan.de/v1/public/software/class/webfan/frdl.webfan.Autoloading.SourceLoader/doc.html
 *  @OID	1.3.6.1.4.1.37553.8.1.8.8 webfan-software
 *  @requires	PHP_VERSION 5.3 >= 
 *  @requires   webfan://frdl.webfan.App.code
 *  @api        http://interface.api.webfan.de/v1/public/software/class/webfan/
 *  @reference  http://www.webfan.de/install/
 *  @implements StreamWrapper
 * 
 */
namespace frdl\webfan\Autoloading;
use frdl\common;


class SourceLoader extends Loader
{
    const NS = __NAMESPACE__;
    const DS = DIRECTORY_SEPARATOR;
    const SESSKEY = __CLASS__;			
	/**
	 * PKI
	 */
    const DISABLED = 0;
    const OPENSSL = 1;
    const PHPSECLIB = 2;

    const E_NORSA = 'No RSA library selected or supported';
    const E_NOTIMPLEMENTED = 'Sorry thisd is not implemented yet';    
    

    const B_SIGNATURE = "-----BEGIN SIGNATURE-----\r\n";
    const E_SIGNATURE = "-----END SIGNATURE-----";

    const B_CERTIFICATE = "-----BEGIN CERTIFICATE-----\r\n";
    const E_CERTIFICATE = "-----END CERTIFICATE-----";

    const B_PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----\r\n";
    const E_PUBLIC_KEY = "-----END PUBLIC KEY-----";

    const B_RSA_PRIVATE_KEY = "-----BEGIN RSA PRIVATE KEY-----\r\n";
    const E_RSA_PRIVATE_KEY = "-----END RSA PRIVATE KEY-----";

    const B_KEY = "-----BEGIN KEY-----\r\n";
    const E_KEY = "-----END KEY-----";
 
    const B_LICENSEKEY = "-----BEGIN LICENSEKEY-----\r\n";
    const E_LICENSEKEY = "-----END LICENSEKEY-----";


    public $sid;
	
    protected $lib;
	
	
	 
	/**
	 * Stream Properties
	 */
	protected $Client;
	public $context = array();
	protected $data;
	protected $chunk;
	public $buflen;
	protected $pos = 0;
	protected $read = 0; 
	public static $id_repositroy;
	public static $id_interface;	
	public static $api_user;
	public static $api_pass;	
	protected $eof = false;
	protected $mode;
	
	
        protected $dir_autoload;
	protected static $config_source;
        protected $autoloaders = array();
        protected $autoloadersPsr0 = array();
	protected $classmap = array();
	protected $isAutoloadersRegistered = false;
		
	protected $interface;
	
	/**
	 *  "Run Time Cache" / Buffer
	 */
	protected static $rtc;
	
	protected static $instances = array();
	
	 
	protected $buf = array(
	  'config' => array(),
          'opt' => array(),
          'sources' => array(),
	);
	
	function __construct($pass = null) 
	 {
	   $this->sid = count(self::$instances);
	   self::$instances[$this->sid] = &$this;	
	   
	   $this->interface = null;	
	   self::$config_source = array( 
	 'install' =>  false,
         'dir_lib' => false,
         'session' => false,
         'zip_stream' => false,
         'append_eval_to_file' => false,
         
	   );
	   $this->dir_autoload = '';	
	   self::repository(((!isset($_SESSION[self::SESSKEY]['id_repository']))?'frdl':$_SESSION[self::SESSKEY]['id_repository']));	 
	   self::$id_interface =  'public';	 
	   self::$api_user = '';
	   self::$api_pass = '';
	   $this->Defaults(true);
	   $this->set_pass($pass);
	 }


  public function j(){
  	 return \webfan\App::God();
  }
	 
	 
  public static function top(){
  	  if(0 === count(self::$instances))return new self;
  	  return self::getStream(0);
  }	 
	 
	 
  public static function getStream($sid){
  	  return (isset(self::$instances[$sid])) ? self::$instances[$sid] : null;
  }	 	 
	 
  public static function repository($id = null){
  	if($id !== null)$_SESSION[self::SESSKEY]['id_repository'] = $id;
	self::$id_repositroy = &$_SESSION[self::SESSKEY]['id_repository'];
	return self::$id_repositroy;
  }	 
	 
  public function set_interface(Array &$interface = null){
  	 $this->interface = (is_array($interface)) ? $interface : null;
	 return $this;
  }	 
	 
  public function config_source($key = null, $value = null){
  	   if(!is_string($key))return self::$config_source;
	   if(!isset(self::$config_source[$key]))return false;
	   self::$config_source[$key] = $value;
	   return true;
  }  
	 

	 
  public function Defaults($set = false){
          $config = array( 
  'host' => null,
  'IP' => null,
  'uid' => 0,
  'encrypted' => true,
  'e_method' => 2,
  'c_method' => 1,
  'source' => $this->config_source(),
  'ERROR' => E_USER_WARNING,
  'ini' => array( 
      'display_errors_details' => false,
      'pev' => array( 
           'CUSTOM' => null,
           'REQUEST' => true,
	       'HOST' => $_SERVER['SERVER_NAME'],
	     //  'IPs' => $App->getServerIp(),
	       'PATH' => null,
	   ),
	  ), 
 
     
	); 
	
	
		  
		  if($set === true){
		  	  $this->set_config($config); 	
		  }
		  
		  return array(
		     'config' => $config,
		  );
		
	} 

	protected function set_pass($pass = null){
	   $this->pass = (is_string($pass)) ? $pass : mt_rand(10000,9999999).sha1($_SERVER['SERVER_NAME']).'faldsghdfshfdshjfdhjr5nq7q78bg2nda  jgf jtrfun56m8rtjgfjtfjtzurtnmrt tr765  $bbg r57skgmhmh';
	} 
	
	public function mkp(){
		
		$this->set_pass(null);
	     return $this;
	}
	
	public function set_config(&$config){
		$this->config = (is_array($config)) ? $config : $this->buf['config'];
		if(isset($this->config['source']) && is_array($this->config['source']))self::$config_source = &$this->config['source'];
        return $this;		
	}
	 



    public function installSource($class,&$code, &$error ='', &$config_source = null){
    	if($config_source === null)$config_source = &self::$config_source;
		if($config_source['install'] !== true)return null;
		if(!isset($code['php']))return false;
		if(isset($code['installed']) && $code['installed'] === true)return true;
		
	     $bs = new \frdl\webfan\Serialize\Binary\bin();
		 $code['doc'] = $bs->unserialize($this->unpack_license($code['d']));
			 		
		 $error = '';
		 $r = false;
		 
	    if(isset($config_source['dir_lib']) && is_string($config_source['dir_lib']) && is_dir($config_source['dir_lib'])){
	         $dir  = rtrim($config_source['dir_lib'],  self::DS . ' '). self::DS ;	
		     $filename = $dir.str_replace('\\', self::DS, $class).'.php'; 
			 $dir = realpath(dirname($filename)).self::DS;	
			 if(!is_dir($dir)){
			   if(!mkdir($dir, 0755, true)){
			   	  $error = 'Cannot create directory '.$dir.' and cannot save class '.$class.' in '.__METHOD__.' '.__LINE__;
			   	  trigger_error($error,E_USER_WARNING);
			   }
			 }		
             
			 if($error === ''){
               $file_header = "/**\n* File generated by frdl Application Composer : class : ".__CLASS__."\n**/\n";
			   $code = '<?php '."\n".$file_header."\n\$filemtime = ".time().";\n\$class_documentation = ".var_export($code['doc'], true).";\n".$code['php']."\n";
			 
			   $fp = fopen($filename, 'wb+');
	           fwrite($fp,$code);
	           fclose($fp);
			   if(file_exists($filename)){
			     $code['installed'] = true;
				 $r = true;  
			   }else{
			      $error = 'Cannot create file '.$filename.' and cannot save class '.$class.' in '.__METHOD__.' '.__LINE__;
			   	  trigger_error($error,E_USER_WARNING);
			   }
			 }
		}
			 
			 
			 
			 
	   return $r;	
    }



	
	public function patch_autoload_function($class){
		if(function_exists('__autoload'))return __autoload($class);
	}
		 
	public function autoload_register(){
		if(false !== $this->isAutoloadersRegistered){
		      trigger_error('Autoloadermapping is already registered.',E_USER_NOTICE);
			  return $this;
		}
        $this->addLoader(array($this,'Psr4'), true, true);	
        $this->addLoader(array($this,'Psr0'), true, false);				
	    $this->addLoader(array($this,'classMapping'), true, false);	
        $this->addLoader(array($this,'patch_autoload_function'), true, false);	
        $this->addLoader(array($this,'autoloadClassFromServer'), true, false);	
        $this->isAutoloadersRegistered = true;
        return $this;
	} 
    
    public function addLoader($Autoloader, $throw = true, $prepend = false){
       spl_autoload_register($Autoloader, $throw, $prepend);
	   return $this;
    }

    public function unregister( $Autoloader)
     {
        spl_autoload_unregister($Autoloader);
		return $this;
     } 	
	 
	 
	/**
	 * Psr-0
	 */ 				 
    public function addPsr0($prefix, $base_dir, $prepend = false)
    {
       $prefix = trim($prefix, '\\') . '\\';
       $base_dir = rtrim($base_dir, self::DS) . self::DS;	   
       if(isset($this->autoloadersPsr0[$prefix]) === false) {
            $this->autoloadersPsr0[$prefix] = array();
        }

      if($prepend) {
            array_unshift($this->autoloadersPsr0[$prefix], $base_dir);
        } else {
            array_push($this->autoloadersPsr0[$prefix], $base_dir);
        }
		
		return $this;
    }
	
	/**
	 * Psr-4
	 */ 			 
    public function addNamespace($prefix, $base_dir, $prepend = false)
    {
       return $this->addPsr4($prefix, $base_dir, $prepend);
    }
    public function addPsr4($prefix, $base_dir, $prepend = false)
    {
    
       $prefix = trim($prefix, '\\') . '\\';
       $base_dir = rtrim($base_dir, self::DS) . self::DS;	   
       if(isset($this->autoloaders[$prefix]) === false) {
            $this->autoloaders[$prefix] = array();
        }
	
      if($prepend) {
            array_unshift($this->autoloaders[$prefix], $base_dir);
        } else {
            array_push($this->autoloaders[$prefix], $base_dir);
        }
		
		return $this;
	}	 
    


    
    public function Psr4($class)
    {
    
        $prefix = $class;
        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relative_class = substr($class, $pos + 1);
            $file = $this->routeLoaders($prefix, $relative_class);
			if ($file) {
                return $file;
            }
            $prefix = rtrim($prefix, '\\');   
        }
		
        return false;       
    } 
    public function loadClass($class)
    {
       return $this->Psr4($class);
    }	
	
	
	
   public function Psr0($class)
    {
        $prefix = $class;
        while (false !== $pos = strrpos($prefix, '\\')) {
            $prefix = substr($class, 0, $pos + 1);
            $relative_class = substr($class, $pos + 1);
            $file = $this->routeLoadersPsr0($prefix, $relative_class);
            if ($file) {
                return $file;
            }
            $prefix = rtrim($prefix, '\\');   
        }
        return false;  
    }
  		
   public function routeLoadersPsr0($prefix, $relative_class)
    {
        if (!isset($this->autoloadersPsr0[$prefix])) {
            return false;
        }
        foreach ($this->autoloadersPsr0[$prefix] as $base_dir) {		
          if (null === $prefix || $prefix.'\\' === substr($relative_class, 0, strlen($prefix.'\\'))) {
            $fileName = '';
            $namespace = '';
            if (false !== ($lastNsPos = strripos($relative_class,  '\\'))) {
                $namespace = substr($relative_class, 0, $lastNsPos);
                $relative_class = substr($relative_class, $lastNsPos + 1);
                $fileName = str_replace('\\', self::DS, $namespace) . self::DS;
            }
            $fileName .= str_replace('_', self::DS, $relative_class) /* . '.php'  */;
            $file = ($base_dir !== null ? $base_dir . self::DS : '') . $fileName;
            if ($this->inc($file)) {
                return $file;
            }
          }
		}
	   return false;
    }		


    public function setAutloadDirectory($dir){
  	   if(!is_dir($dir))return false;
	   $this->dir_autoload = $dir;
	   if(substr($this->dir_autoload,-1,1) !== self::DS)$this->dir_autoload.=self::DS;
	   return true;	
    }	 
  		
    public function routeLoaders($prefix, $relative_class)
    {

        if (!isset($this->autoloaders[$prefix])) {
            return false;
        }
        foreach ($this->autoloaders[$prefix] as $base_dir) {
        	
            $file = $base_dir
                  . str_replace('\\', self::DS, $relative_class)
                  /* . '.php'  */
				   ;

		
            if ($this->inc($file)) {
                return $file;
            }
        }
        return false;
    }	
	
    protected function inc($file)
    {
    	if(substr($file,-4,4) === '.php'){
    		$file = $file; 
    	}else{
    		$file.= '.php';
    	}
		$file2= substr($file,0,-4).'.inc';
	
       if(file_exists($file)) {
             require $file;
            return true;
        }elseif(file_exists($file2)) {
             require $file2;
            return true;
        }
		
		
        return false;
    }	
		 
		 
	
	public function classMapping($class){
		if(isset($this->classmap[$class])){
            if ($this->inc($this->classmap[$class])) {
                return $this->classmap[$class];
            }			
		}
		
		return false;
	}
	
	
	public function class_mapping_add($class, $file, &$success = null){
		if(file_exists($file)){
		    $this->classmap[$class] = $file;
			$success = true;
	    }else{
			$success = false;
	    }
		
	   return $this;
	}
    
	public function class_mapping_remove($class){
		if(isset($this->classmap[$class]))unset($this->classmap[$class]);
	    return $this;
	}	
    		 
		 
		 
	protected function source_check($str){	 
		 $start = 'array';
		 $badwords = array('$',';', '?', '_', 'function ', 'class ');
	
		 foreach($badwords as $num => $s){
		 	if(strpos($str, $s)!== false)return false;
		 }
		 
		 if(substr($str,0,strlen($start)) !== $start)return false;
		 if(!preg_match('/[a-f0-9]{40}/', $str))return false;
		 
		 
		 return true;
	} 
	public function autoloadClassFromServer($className){
	
		  $classNameOrig = $className;
		  if(class_exists($className))return;	
		  if (!in_array('webfan', stream_get_wrappers())){
		  	trigger_error('Streamwrapper webfan is not registered. Call to webfan\App::init() required.', E_USER_ERROR);
			return;
		  }
		  $className = str_replace('\\', '.', $className);
		  $className = ltrim($className, ' .');
		  $RessourceFileName = 'webfan://'.$className.'.code';
		   
		  $fp = fopen($RessourceFileName, 'r');
		  $source = '';
		  if($fp){
		  	clearstatcache(); 
			clearstatcache(true,$RessourceFileName);   
			$stat = fstat($fp);
			$bufsize = ($stat['size'] <= 8192) ? $stat['size'] : 8192;
		  	while(!feof($fp) ){
		        $source .= fread($fp, $bufsize);
			}
		     fclose($fp);
		  }else{
		  	return false;
		  }
		  
		  
		if($source ===false || $source ==='' ){
	   			trigger_error('Cannot get source from the webfan code server ('.$RessourceFileName.')! '.__METHOD__.' '.__LINE__, E_USER_WARNING);
		     	return false;
            }
				  
	    $scheck = $this->source_check($source);			  
		if($scheck !== true){
	   			trigger_error('The source loaded from the code server looks malicious ('.$scheck.' '.$RessourceFileName.')! '.__METHOD__.' '.__LINE__, E_USER_WARNING);
		     	return false;			
		}  
		
		if(eval('$data = '.$source.';')===false){
	   			trigger_error('Cannot process the request to the source server by APIDClient ('.$RessourceFileName.')! '.__METHOD__.' '.__LINE__, E_USER_WARNING);
		     	return false;
            }
		
		$_defaults = $this->Defaults();
		$config = $_defaults["config"];
		$opt = (isset($data['opt'])) ? $data['opt'] : $this->getOpt();
		$code = $data['source'];

        $sources = array();
		$sources[$classNameOrig] = $code; 

        if(is_array($this->interface)){
        	$opt['pass'] = $this->interface['API_SECRET'];
			$opt['rot1'] = $this->interface['rot1'];
			$opt['rot2'] = $this->interface['rot2'];
        }
        

		if($this->loadSources($sources,$opt, $config )===false){
		   		trigger_error('Cannot process the request to the source server by APIDClient ('.$className.')! '.__METHOD__.' '.__LINE__, E_USER_WARNING);
		     	return false;		
		}
		
		return $RessourceFileName;	  
	}
	 

    public function make_pass_3(&$opt){
             if(isset($opt['pwdstate']) && $opt['pwdstate'] === 'decrypted')return true;
             if(isset($opt['pwdstate']) && $opt['pwdstate'] === 'error')return false;
            if(!isset(self::$rtc['CERTS']))self::$rtc['CERTS'] = array();
            $hash = sha1($opt['CERT']);
            $u = parse_url($opt['CERT']);
          $url = $opt['CERT'];
           if(!isset(self::$rtc['CERTS'][$hash]) && ($u === false || !isset(self::$rtc['CERTS'][$url])))
               {
                    if($u !== false && count($u) >1 && !preg_match("/CERTIFICATE/", $opt['CERT']) ){
                     if(isset($u['scheme']) && isset($u['host'])){
                $h = explode('.',$u['host']);
                 $h = array_reverse($h);
                 if($h[0] === 'de' && ($h[1] === 'webfan' || $h[1] === 'frdl' )){
                 if(class_exists('\webdof\Http\Client')){
                 $Http = new \webdof\Http\Client();
                $post = array();
                $send_cookies = array();
                $r = $Http->request($opt['CERT'], 'GET', $post, $send_cookies, E_USER_WARNING);
                }else{
                	$c = file_get_contents($opt['CERT']);
					$r = array();
					$r['status'] = (preg_match("/CERTIFICATE/",$c)) ? 200 : 400;
					$r['body'] = $c;
                }
				
                if(intval($r['status'])===200){
               $CERT = trim($r['body']);
               }else{
                 $opt['pwdstate'] = '404';
                return false;
              }
               }
           }else{
                   $CERT = trim(file_get_contents($opt['CERT']));
                }
                   $key = $url;
                  if(!isset(self::$rtc['CERTS'][$key]))self::$rtc['CERTS'][$key] = array();
                 self::$rtc['CERTS'][$key]['crt'] = $CERT;
             }elseif(preg_match("/CERTIFICATE/", $opt['CERT'])){
             	    $key = $hash;
                    if(!isset(self::$rtc['CERTS'][$key]))self::$rtc['CERTS'][$key] = array();
                    $CERT = utf8_encode($opt['CERT']);
					$CERT=$this->loadPK($CERT);
					if($CERT===false){
				   	  trigger_error('Cannot procces certificate info in '.__METHOD__.' line '.__LINE__, E_USER_WARNING);
					  return false;
				   }
					$CERT=$this->save($CERT, self::B_CERTIFICATE, self::E_CERTIFICATE);
					self::$rtc['CERTS'][$key]['crt'] =$CERT;
				   
              }else{
				   	  trigger_error('Cannot procces certificate info in '.__METHOD__.' line '.__LINE__, E_USER_WARNING);
					  return false;
				   }
                 }elseif(isset(self::$rtc['CERTS'][$hash])){
                     $key = $hash;
                  }elseif(isset(self::$rtc['CERTS'][$url])){
                      $key = $url;
                  }else{
                  	 trigger_error('Cannot procces certificate info in '.__METHOD__.' line '.__LINE__, E_USER_WARNING);
					 return false;
                  }


            $this->setLib(1);
        if(!isset(self::$rtc['CERTS'][$key]['PublicKey'])){
              $PublicKey = $this->getPublKeyByCRT(self::$rtc['CERTS'][$key]['crt']);
             self::$rtc['CERTS'][$key]['PublicKey'] = $PublicKey;
           }
            $success = $this->decrypt($opt['pass'],self::$rtc['CERTS'][$key]['PublicKey'],$new_pass) ;
          if($success === true){
            $opt['pass'] = $new_pass;
           $opt['pwdstate'] = 'decrypted';
            }else{
               $opt['pwdstate'] = 'error';
		      // unset(self::$rtc['CERTS'][$key]);
            }
           return $success;
    } 

    protected function load(&$code, Array &$config = null, &$opt = array('pass' => null, 'rot1' => 5, 'rot2' => 3), $class = null){
	      $p = $this->_unwrap_code(((is_string($code)) ? $code : $code['c']));
		  
		  if(isset($opt['e']) && is_bool($opt['e']))$config['encrypted'] = $opt['e'];
		  if(isset($opt['m']))$config['e_method'] = $opt['m'];		   
		  
 	      if($config['encrypted'] === true && intval($config['e_method']) === 1){
 		   	 trigger_error('The options encryption method is deprecated in '.__METHOD__.' '.__LINE__,$config['ERROR']);
		     return false;		     
 	      }	 
		  
 	      if($config['encrypted'] === true && intval($config['e_method']) === 2){
 		     $p = trim($this->crypt($p, 'decrypt', $opt['pass'], $opt['rot1'], $opt['rot2']));
 	      }	 	
		  
 	      if($config['encrypted'] === true && intval($config['e_method']) === 3){
 	      	 if($this->make_pass_3($opt) == false){
		   	 trigger_error('Cannot decrypt password properly [1] from '.self::$id_repositroy.' for '.$class.' in '.__METHOD__.' '.__LINE__,$config['ERROR']);
		       return false;	      	 	
 	      	 }
 		     $p = trim($this->crypt($p, 'decrypt', $opt['pass'], $opt['rot1'], $opt['rot2']));
 	      }	
		  		  		  	
		   if(isset($code['s']) && $code['s'] !== sha1($p)){
	          	 $errordetail = ($config['ini']['display_errors_details'] === true)
			                  ? '<pre>'.sha1($p).'</pre><pre>'.$code['s'].'</pre><pre>'.$opt['pass'].' '.$opt['rot1'].' '.$opt['rot2'].'</pre>'
			                  : '';	   	
													  
		   	   trigger_error('Cannot decrypt source properly [2] from '.self::$id_repositroy.' for '.$class.' in '.__METHOD__.' '.__LINE__.$errordetail,$config['ERROR']);

			   return false;
		   }
		  
 	       $p = $this->unwrap_namespace($p);	   
		   $code['php'] = $p;
		   try{
	             $parsed = eval($p);
		   }catch(Exception $e){
		   	  $parsed = false;
		   }
          if($parsed === false){
          	 $errordetail = ($config['ini']['display_errors_details'] === true)
			                  ? '<pre>'.htmlentities($p).'</pre>'
			                  : '';
		     trigger_error('Parse Error in '.__METHOD__.' '.__LINE__.$errordetail,$config['ERROR']);
		     return false;
	      } else {
			   unset($code['c']);
		  } 
		  
		  $error = '';
		  $config_source = (isset($config['source'])) ? $config['source'] : self::$config_source;
		  $installed = $this->installSource($class,$code, $error, $config_source);
		  
		  usleep(75);
		  return true; 	
    }


    public function loadSource(&$code, Array &$config = null, &$opt = array('pass' => null, 'rot1' => 5, 'rot2' => 3), $class = null){
    	 return $this->load($code, $config, $opt, $class );
    }

    public function loadSources(&$sources, &$opt = array('pass' => null, 'rot1' => 5, 'rot2' => 3), Array &$config = null){
       $this->set_config($config); 	
       $this->mkp($config);	
       foreach($sources as $class => $code){
       	  if(class_exists($class))continue;
	      if($this->load($code, $config, $opt, $class) === false){
	      	return false;
	      }
       }
    	
       return true;	
    }
	
	
    public function crypt($data, $command = 'encrypt', $Key = NULL, $offset = 5, $chiffreBlockSize = 3)
	{
	   if($command ===  'encrypt'){
	    	$data = sha1(trim($data)).$data;	
			
			    $k = sha1($Key).$Key;
				
				$str = $data;
				$data = '';
				
				
				for($i=1; $i<=strlen($str); $i++)
				{
					$char 		= substr($str, $i-1, 1);
					$keychar 	= substr($k, ($i % strlen($k))-1, 1);
					$char 		= chr(ord($char)+ord($keychar));
					$data		.= $char;
				}
       }
	   if(!is_numeric($offset)||$offset<0)$offset=0;if(!isset($data)||$data==""||!isset($Key)||$Key==""){return FALSE;}$pos="0";for($i=0;$i<=(strlen($data)-1);$i++){$shift=($offset+$i)*$chiffreBlockSize;while($shift>=256){$shift-=256;}$char=substr($data,$i,1);$char=ord($char)+$shift;if($pos>=strlen($Key)){$pos="0";}$key=substr($Key,$pos,1);$key=ord($key)+$shift;if($command=="decrypt"){$key=256-$key;}$dataBlock=$char+$key;if($dataBlock>=256){$dataBlock=$dataBlock-256;}$dataBlock=chr(($dataBlock-$shift));if(!isset($crypted)){$crypted=$dataBlock;}else{$crypted.=$dataBlock;}$pos++;}
       if($command ===  'decrypt'){
 				$decrypt 	= '';
                $k = sha1($Key).$Key;
			
				for($i=1; $i<=strlen($crypted); $i++)
				{
					$char 		= substr($crypted, $i-1, 1);
					$keychar 	= substr($k, ($i % strlen($k))-1, 1);
					$char 		= chr(ord($char)-ord($keychar));
					$decrypt   .= $char;
				}      	   
       	   $crypted = substr($decrypt,strlen(sha1("1")),strlen($decrypt));
		   $hash_check = substr($decrypt,0,strlen(sha1("1")));
		   if(trim($hash_check) !== sha1($crypted) || sha1($crypted)==='da39a3ee5e6b4b0d3255bfef95601890afd80709'){
		   	 $crypted = false;
		   	 trigger_error('Broken data consistence in '.__METHOD__, E_USER_NOTICE);
		   }
	   }
       return $crypted;
	}	

   
    public function unwrap_namespace($s){
    	$s = preg_replace("/^(namespace ([A-Za-z0-9\_".preg_quote('\\')."]+);){1}/", '${1}'."\n", $s);
		return preg_replace("/(\nuse ([A-Za-z0-9\_".preg_quote('\\')."]+);)/", '${1}'."\n", $s);
    }
    	
    public function _unwrap_code($c){return trim(gzuncompress(gzuncompress(base64_decode(str_replace("\r\n\t","", $c))))," \r\n");}		
    public function unpack_license($l){return gzuncompress(gzuncompress(base64_decode(str_replace("\r\n", "", $l))));} 	
	function __destruct() {foreach(array_keys(get_object_vars($this)) as $value){unset($this->$value);}}
	
	
	/**
	 * PKI
	 */ 

   public function setLib($lib)
     {
        $this->lib = $lib;
	   return $this;
     } 

   public function save($data, $begin = "-----BEGIN SIGNATURE-----\r\n", $end = '-----END SIGNATURE-----')
     {
        return $begin . chunk_split(base64_encode($data)) . $end;
     }


   public function loadPK($str)
     {
       $data = preg_replace('#^(?:[^-].+[\r\n]+)+|-.+-|[\r\n]#', '', $str);
       return preg_match('#^[a-zA-Z\d/+]*={0,2}$#', $data) ? utf8_decode (base64_decode($data) ) : false;
     }

  public function error($error, $mod = E_USER_ERROR, $info = TRUE)
    {
      trigger_error($error.(($info === TRUE) ? ' in '.__METHOD__.' line '.__LINE__ : ''), $mod);
      return FALSE;
    }
    
    
  public function verify($data, $sigBin, $publickey, $algo = 'sha256WithRSAEncryption')
     {
        switch($this->lib)
          {
           case self::OPENSSL :
                  return $this->verify_openssl($data, $sigBin, $publickey, $algo);
                break;

           case self::PHPSECLIB :
                  return $this->verify_phpseclib($data, $sigBin, $publickey, $algo);
                break;
           case self::DISABLED :
           default :
                  return $this->error(self::E_NORSA, E_USER_ERROR);
                break;

          }

     }
    
	    
  public function getPublKeyByCRT($cert)
     {
        switch($this->lib)
          {
           case self::OPENSSL :
                  return $this->getPublKeyByCRT_openssl($cert);
                break;

           case self::PHPSECLIB :
                  return $this->error(self::E_NOTIMPLEMENTED, E_USER_ERROR);
                break;
           case self::DISABLED :
           default :
                  return $this->error(self::E_NORSA, E_USER_ERROR);
                break;

          }

     }
	 
  public function encrypt($data,$PrivateKey,&$out)
     {
        switch($this->lib)
          {
           case self::OPENSSL :
                  return $this->encrypt_openssl($data,$PrivateKey,$out);
                break;
        case self::PHPSECLIB :
                  return $this->error(self::E_NOTIMPLEMENTED, E_USER_ERROR);
                break;
           case self::DISABLED :
           default :
                  return $this->error(self::E_NORSA, E_USER_ERROR);
                break;

          }

     }
	 

  public function decrypt($decrypted,$PublicKey,&$out)
     {
        switch($this->lib)
          {
           case self::OPENSSL :
                  return $this->decrypt_openssl($decrypted,$PublicKey,$out);
                break;
        case self::PHPSECLIB :
                  return $this->error(self::E_NOTIMPLEMENTED, E_USER_ERROR);
                break;
           case self::DISABLED :
           default :
                  return $this->error(self::E_NORSA, E_USER_ERROR);
                break;

          }

     }
	 	 
  protected function encrypt_openssl($data,$PrivateKey,&$out) {  
     $PrivKeyRes = openssl_pkey_get_private($PrivateKey);
     return openssl_private_encrypt($data,$out,$PrivKeyRes); 
  }
  
  protected function decrypt_openssl($decrypted,$PublicKey,&$out) {  
        $pub_key = openssl_get_publickey($PublicKey);
        $keyData = openssl_pkey_get_details($pub_key);
        $pub = $keyData['key'];
        $successDecrypted = openssl_public_decrypt(base64_decode($decrypted),$out,$PublicKey, OPENSSL_PKCS1_PADDING);
		return $successDecrypted; 
  }
  


  protected function getPublKeyByCRT_openssl($cert)
    {
       $res = openssl_pkey_get_public($cert);
       $keyDetails = openssl_pkey_get_details($res);
       return $keyDetails['key'];
    }

     

  protected function verify_phpseclib($data, $sigBin, $publickey, $algo = 'sha256WithRSAEncryption')
      {
         $isHash = preg_match("/^([a-z]+[0-9]).+/", $algo, $hashinfo);
         $hash = ($isHash) ? $hashinfo[1] : 'sha256';

         $rsa = new Crypt_RSA();
         $rsa->setHash($hash);
         $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
         $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
         $rsa->loadKey($publickey);
         return (($rsa->verify($data, $sigBin) === TRUE) ? TRUE : FALSE);
      }


   protected function verify_openssl($data, $sigBin, $publickey, $algo = 'sha256WithRSAEncryption')
      {
        return ((openssl_verify($data, $sigBin, $publickey, $algo) == 1) ? TRUE : FALSE);
      }
	  
	  
	  	
	
	/**
	 * Streaming Methods
	 */
    public function init(){$args = func_get_args(); /** todo ... */ return $this;}
    public function DEFRAG(){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function stream_open($url, $mode, $options = STREAM_REPORT_ERRORS, &$opened_path = null){
    	$u = parse_url($url);
	    $c = explode('.',$u['host']);
		$c = array_reverse($c);
		
		$this->mode = $mode;
		
		if($c[0]==='code')$tld = array_shift($c);
		
		
		
		/**
		 * ToDo: APICLient
		 *    $this->Client = new \frdl\Client\RESTapi();
		 * 
		 *  URL Pattern / e.g. this Class:
		 *  http://interface.api.webfan.de/v1/public/software/class/webfan/frdl.webfan.Autoloading.SourceLoader/source.php
		 * 
		 */
        if(class_exists('\webdof\wHTTP') && class_exists('\webdof\Http\Client') && class_exists('\webdof\Webfan\APIClient')){ 
	      $this->Client = new \webdof\Webfan\APIClient();
		  $this->Client->prepare( 'http',
                          'interface.api.webfan.de',
                          'GET',
                          self::$id_interface,  //  i1234 
                          'software',
                          array(),  //post
                          array(),  //cookie
                          self::$api_user,
                          self::$api_pass,
                          'class',
                          'php',   //format ->hier: "php"
                          'source',
                           array(self::$id_repositroy,implode(".",array_reverse($c))),
                           array(), //get
                          1,
                          E_USER_WARNING);
						  
		 $this->eof = false;
		 $this->pos = 0;
    	 try{
               $r = $this->Client->request();
			   if(intval($r['status']) !== 200)return false;
			   $this->data = $r['body'];
	
		 }catch(Exception $e){
			trigger_error('Cannot process the request to '.$url, E_USER_WARNING);
			return false;
		 }  	
	   }else{
	      $url = 'http://interface.api.webfan.de/v1/'.self::$id_interface.'/software/class/'.self::$id_repositroy.'/'.implode(".",array_reverse($c)).'/source.php';
		  $data = file_get_contents($url);
		  if(false === $data){
		  	 return false;			  
		  }else{
		  	 $this->data = $data;
		  }
	   }
				  
	    return true;					  
    }
    public function dir_closedir(){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function dir_opendir($path , $options){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function dir_readdir(){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function dir_rewinddir(){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function mkdir($path , $mode , $options){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function rename($path_from , $path_to){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function rmdir($path , $options){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
 	public function stream_cast($cast_as){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
 	public function stream_close(){
         $this->Client = null;
	}
    public function stream_eof(){
    	$this->eof = ($this->pos >= strlen($this->data));
    	return $this->eof;
	}
    public function stream_flush(){
    	//echo $this->data;
    	$this->pos  = strlen($this->data);
		return $this->data;
	}
    public function stream_lock($operation){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function stream_set_option($option , $arg1 , $arg2){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function stream_stat(){
		 return array(  
		          'mode' => $this->mode,
		          'size' => strlen($this->data) * 8,
		 );
	}
    public function unlink($path){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function url_stat($path , $flags){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function stream_read($count){
    	 if($this->stream_eof())return  '';
		
    	 $maxReadLength = strlen($this->data) - $this->pos;
         $readLength = min($count, $maxReadLength);

        $p=&$this->pos;
        $ret = substr($this->data, $p, $readLength);
        $p +=  $readLength;
        return (!empty($ret)) ? $ret : '';  	
	}
    public function stream_write($data){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function stream_tell(){return $this->pos;}
    public function stream_seek($offset, $whence){
    	
		
		
		$l=strlen($this->data);
        $p=&$this->pos;
        switch ($whence) {
            case SEEK_SET: $newPos = $offset; break;
            case SEEK_CUR: $newPos = $p + $offset; break;
            case SEEK_END: $newPos = $l + $offset; break;
            default: return false;
        }
        $ret = ($newPos >=0 && $newPos <=$l);
        if ($ret) $p=$newPos;
        return $ret;
	}
    public function stream_metadata($path, $option, $var){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
   
}

