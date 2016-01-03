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
 *   @vendor     frdl
 *   @package    webfan Application Composer Backend
 *   @filename   webfan.fexe.php
 *   @todo
 * 
 *   @state:     test/development
 *  
 * 
 */
namespace frdl\xGlobal; 

/* BEGIN CONFIGSECTION */
error_reporting(E_ALL);
ini_set('display_errors', 1);
/* END CONFIGSECTION */

/* BEGIN BOOTSECTION   */
if(!class_exists('\frdl\webfan\App')){
	
 if(!defined( __NAMESPACE__.'\\'.'__BOOTFILE__')) {
 	define( __NAMESPACE__.'\\'.'__BOOTFILE__', __DIR__ . DIRECTORY_SEPARATOR . '..'.DIRECTORY_SEPARATOR .'..'.DIRECTORY_SEPARATOR .'..'.DIRECTORY_SEPARATOR . 'bootstrap.php');
 }


 if(!in_array( __BOOTFILE__, get_included_files())){
 	
  if(!file_exists(  __BOOTFILE__)){
	$str = 'App '.basename(__FILE__).' is not installed cortrectly! File ' .__BOOTFILE__.' not found. 
	<br />
	Please read <a target="_blank" href="https://github.com/frdl/webfan/wiki/Installation">Installation instruction</a>!';
	trigger_error($str, E_USER_ERROR);
	echo $str; 
	die();
  }
 	
 	require  __BOOTFILE__;
 }
 
}
 
 if(!class_exists('\frdl\webfan\App')){
 	$str = 'App '.basename(__FILE__).' is not installed cortrectly! Class \frdl\webfan\App not found. 
	<br />
	Please read <a target="_blank" href="https://github.com/frdl/webfan/wiki/Installation">Installation instruction</a>!';
	trigger_error($str, E_USER_ERROR);
	echo $str; 
	die();	
 }
 
 
/* END BOOTSECTION */ 

 
   
class webfan extends fexe    
{
	 const DEL='µ';
     const CMD = 'cmd';
	 
	 const HINT_NOTINSTALLED = 'The Program frdl/webfan is not installed properly, try to install via {___$$URL_INSTALLER_HTMLPAGE$$___}!';
	 
	 const DIR_PLUGIN = '.1.3.6.1.4.1.37553.8.1.8.8.5.65';
	
	
	 protected $aSess;
	 
	 protected $debug = false;
	 
	 protected $Console;
	 
	 protected $isAdmin = false;
	 
	 protected static $_db = null;
	
	
	 public static function db(){

		return self::$_db;
	 }
	
	 public function Request(mixed $args = null){
	 	
	 }
	 
	 public function data(Array $data = null){
	  if(null === $this->data){
	  	

	   	  	
	   	  	
	   if(!isset($this->data))$this->data = array();
	   $this->data['DIR'] = getcwd() . DIRECTORY_SEPARATOR ; 
	   $this->data['CONFIGFILE'] = $this->data['DIR'].'config.frdl.php';
	   $this->data['o'] = new \stdclass;	   
	   $this->data['data_out'] = new \stdclass;
	   $this->data['config'] = array();
	   $this->data['settings'] = new \stdclass;
	   $this->data['settings']->cli = array(
	         'frdl' => array(
	             'cli.cmd.cli' => 'frdl',
	             'cli.class' => '\frdl\ApplicationComposer\Console',
	             'cli.class.required.parent' => '\frdl\aSQL\Engines\Terminal\CLI',
	         ),
	   );
	   
	   

	     
	     $this->data['tpl_data'] = array(
	          'FILE' => htmlentities(__FILE__),
	          'URI_DIR_API' => self::URI_DIR_API,
 	          'LOCATION' => 'http://'.$_SERVER['SERVER_NAME']
 	                         .implode('/', \webdof\wURI::getInstance()->getU()->dirs)
 	                         .'/'.\webdof\wURI::getInstance()->getU()->file,
 	          'URL' => '',
 	          'EXTRA_PMX_URL' => '',
 	          
	     );	   
	   
	   $h  = parse_url($this->data['tpl_data']['LOCATION']);
	   $pu = \webdof\wURI::parse_uri($h['scheme'], $h['host'], $h['path']);
	   $path = '/';
	   foreach($pu->dirs as $num => $dir){
	   	 $path.= $dir. '/';
	   }
	   $this->data['installed'] = false;
	   $this->data['index'] = 'Main Template';	
       $this->data['template_main_options'] = array(   
                'Title' =>  'Webfan - Application Composer',
	            'css' => array(
	            
	            ),
			    'js' => array(
				       //->later by assets installer!-> 'js/lib/flow.js',
				       //'http://api.webfan.de/api-d/4/js-api/library.js',
				       ((file_Exists($this->data['CONFIGFILE']) && file_exists($this->data['DIR'].'js'. DIRECTORY_SEPARATOR . 'lib'. DIRECTORY_SEPARATOR.'flow.js'))
				        ? 'js/lib/flow.js' : 'http://api.webfan.de/api-d/4/js-api/library.js'),
				       
				        'js/app.js', 
				),
				'meta' =>  array(
				     array('http-equiv' => 'content-type', 'content' => 'text/html; charset=utf-8'),	
				     array('http-equiv' => 'content-style-type', 'content' => 'text/css'),	
				     array('http-equiv' => 'content-script-type', 'content' => 'text/javascript'),		
				     array('http-equiv' => 'content-script-type', 'content' => 'text/javascript'),				 
				   /*  array('http-equiv' => 'X-UA-Compatible', 'content' => 'IE=7.5'),		  */
				     array('name' => 'mobile-web-app-capable', 'content' => 'yes'),
				     array('name' => 'apple-mobile-web-app-capable', 'content' => 'yes'),
				     array('name' => 'apple-mobile-web-app-status-bar-style', 'content' => 'lightblue'),
				     array('name' => 'HandheldFriendly', 'content' => 'true'),
				     array('name' => 'MobileOptimized', 'content' => '320'),
				     array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0, user-scalable=yes'),
				     
				 ),
				 
				
				 'link' => array(
				     /* array('rel' => 'prefetch', 'type' => 'application/l10n', 'href' => 'locale/locales.ini'), */
				      array('rel' => 'package', 'type' => 'application/package', 'href' => 'https://github.com/frdl/webfan/archive/master.zip'),
				      array('rel' => 'describedby', 'type' => 'application/xml', 'href' => 'config.xml'),
				      array('rel' => 'manifest', 'type' => 'application/manifest+json', 'href' => ltrim($path, '/ ').'manifest.webapp'),
				      
				 )
				 
	    );

	 
	 
	  
	   	   
	   if(is_array($data) || is_object($data)){
	   	 foreach($data as $k => $v){
		 	if(isset($this->data[$k]))$this->data[$k] = $v;
		 }
	   }
	
	  
	   \webfan\App::God()->{'?session_started'}(true);
       if(!isset($_SESSION[__CLASS__]))$_SESSION[__CLASS__] = array();
       $this->aSess = & $_SESSION[__CLASS__] ;
        
        
         
       $this->data['config_new'] = $this->readFile('config.json');
       $this->data['config'] = $this->data['config_new'];
       if(file_exists($this->data['CONFIGFILE'])){
	   	  require $this->data['CONFIGFILE'];
	   	  $this->data['installed'] = "1";
	   }else{
	   	$this->data['installed'] = "0";
	   }
       $this->data['config_new'] = (array)$this->data['config_new'];        
       $this->data['config'] = (array)$this->data['config'];
       $this->data['config']['INSTALLED'] = $this->data['installed'];

       

       
       
       if(base64_decode('eyRfX0xPQ0FUSU9OX19ffQ==') === $this->data['config']['URL'] ){
	   	   $this->data['config']['URL'] = $this->data['tpl_data']['LOCATION'];
	   	  if(true === $this->debug) trigger_error(self::HINT_NOTINSTALLED, E_USER_WARNING);
	   }
       $this->data['tpl_data']['URL'] = &$this->data['config']['URL'];
       $this->data['tpl_data']['URL'] = str_replace('setup.phpsetup.php', 'setup.php', $this->data['tpl_data']['URL']);
       
       
	   $this->data['tpl_data']['URI_DIR_API'] =  $this->data['tpl_data']['URL'].'api.php';	
	   $this->data['config']['URL_API_ORIGINAL'] =  $this->data['tpl_data']['URI_DIR_API'];	
	   $this->data['config']['URL_API_ORIGINAL'] = str_replace('setup.phpapi.php', 'api.php', $this->data['config']['URL_API_ORIGINAL']);
   
 	    $this->data['tpl_data']['PACKAGE'] = function(){
 	       return $this->data['config']['PACKAGE'];
 	    };
 	    $this->data['tpl_data']['VERSION'] = function(){
 	       return $this->data['config']['VERSION'];
 	    }; 
 	    $this->data['tpl_data']['INSTALLED'] = function(){
 	       return $this->data['config']['INSTALLED'];
 	    }; 
 	    $this->data['tpl_data']['REGISTERED'] = function(){
 	       return $this->data['config']['REGISTERED'];
 	    }; 
  	    $this->data['tpl_data']['UNAME'] = function(){
 	       return $this->data['config']['UNAME'];
 	    }; 
  	    $this->data['tpl_data']['UID'] =  function(){
 	       return $this->data['config']['UID'];	  
 	    };   
         	
       $this->data['INSTALLER_PHAR_AVAILABLE'] = '0';
       $this->data['tpl_data']['EXTRA_PHAR_URL'] = '';
       $this->data['tpl_data']['INSTALLER'] = '';
	   if(   function_exists('frdl_install_rewrite_function')
	      || file_exists($this->data['DIR'] . 'install.phar') 
	      || file_exists($this->data['DIR'] . 'install.php') 	    
	    ){
	   	  $this->_installFromPhar( \webdof\wURI::getInstance() );
	   }
	   $this->data['tpl_data']['INSTALLER_PHAR_AVAILABLE'] = function(){
 	       return $this->data['INSTALLER_PHAR_AVAILABLE'];
 	    };   
    	
    	
	  }else{
	  	 foreach($data as $k => $v){
		 	$this->data[$k] = $v;
		 }
	  }
	 
	
	  $this->data['config']['EXTRA'] = $this->_extra($this->data);
		 	       
	   return $this->data;	 	
	 }
	 
	 
	 
	 protected function _extra(&$data){

            $extractConfig = array();
		 	$extractConfig['extra'] = array();
		 	$extractConfig['extra']['pragmamx'] = array();
		 	$pmxf = $data['DIR'].'mainfile.php';
		 	$extractConfig['extra']['pragmamx']['main'] = (file_exists(DIRECTORY_SEPARATOR . realpath( $pmxf)) && preg_match("/pragmamx/i", file_get_contents(DIRECTORY_SEPARATOR . realpath( $pmxf)))
		 	                                                     ? true
		 	                                                     : false);
		 	$extractConfig['extra']['pragmamx']['installdirs'] = array_merge(glob($pmxf), glob("mainfile.php"), glob("/*mainfile.php"),
		 	                                                               glob("*/*/mainfile.php"),
		 	                                                               glob(getcwd(). DIRECTORY_SEPARATOR . 'mainfile.php') );	
		 	                                                               	 	                                                     
 
		 	return $extractConfig; 	
	 }
	 
	 
	 protected function _boot(){
	 	$this->default_boot() ;
		
		require  __DIR__ . DIRECTORY_SEPARATOR . self::DIR_PLUGIN . DIRECTORY_SEPARATOR . 'plugout.php';
		require  __DIR__ . DIRECTORY_SEPARATOR . self::DIR_PLUGIN . DIRECTORY_SEPARATOR . 'plugin.php';

	 }
	 
	 	
    public function run(&$Request =null){
       if('cli' === PHP_SAPI){
 	      trigger_error('This is a web app, cli support is not implemented yet completly!', E_USER_WARNING);
 	      chdir(dirname($_SERVER['argv'][0]));
       }  	
    	
    	$this->default_run($Request);
    	
    	 $this->out(); 
	   return $this;
	}
	
	
	protected function process_modul($modul){
		
		/*  if('2421fe7de922a9eb14f67e0cd28188594f6b16d2' === sha1($_SERVER['SERVER_NAME']))die('Test: ').$modul;  */
		 switch($modul){
		   	 default : 
		 	  case 'PAGE' :
		 	  case 'HTML' : 
		 	       $this->template = $this->readFile('Main Template');  
		 	    break;
		 	 case 'API' : 
		 	    $this->prepare_api();
		 	    $this->_api();
		 	    break;   
		 	 case '404' : 
		   	      $this->template = $this->prepare404();
		   	   break;
		 }
	}
	
	
    protected function route($u = null){
       $u = (null === $u) ? \webdof\wURI::getInstance() : $u;
     /*  
      $t = ini_get('display_errors'); 
       ini_set('display_errors', 0); 
       trigger_error('@ToDo in '.__METHOD__. ' '.__LINE__. ' : Fix / implement any router', E_USER_NOTICE);
       ini_set('display_errors',$t); 
       */
       $this->__todo($u);
       
       
       try{
	   	 $this->default_route($u);
      
	   }catch(\Exception $e){
	   	 die($e->getMessage());
	   }
       
     if(!is_string($this->modul) || trim($this->modul) === ''){
	 	$this->__todo($u);
	 }else{
	 	$this->process_modul($this->modul);
	 }
       
        
        
       return $this;
	}

 protected function __todo(\webdof\wURI $u = null){
       $u = (null === $u) ? \webdof\wURI::getInstance() : $u;
       $inc_files =  get_included_files();
        array_walk($inc_files, (function(&$file, $num) {
	     	           $file = basename($file);
	     	      }), $this);
         
  
 
    	if(
	         in_array( self::URI_DIR_API , $u->getU()->dirs) 
	     ||  in_array( 'api.php' , $inc_files ) 
	     ||  'api.php' === $u->getU()->file     
	     ||  'frdl.jsonp' === $u->getU()->file     
	     ||  'api' === $u->getU()->file        
	     ||  'jsonp' === $u->getU()->file_ext    
	     ||  'json' === $u->getU()->file_ext    
	     ||  'xml' === $u->getU()->file_ext     
	     ||  'dat' === $u->getU()->file_ext     
	     ||  'bin' === $u->getU()->file_ext     
	     ||  'api' === $u->getU()->file_ext     
	   ){
		 	   /* $this->prepare_api();  */
		 	    $this->_api();
	   }elseif(
	       '/' === $u->getU()->req_uri 
	       || basename(__FILE__) ===  $u->getU()->file
	       || 'install.phar' === $u->getU()->file
	       || 'install.php' === $u->getU()->file
	       || substr($u->getU()->location,0,strlen($this->data['config']['URL']))  === $this->data['config']['URL']
	   ){
	       $this->template = $this->readFile('Main Template');  
	   }/* elseif (file_exists($u->getU()->path) && is_file($u->getU()->path)){
	   	    $this->template = $this->readFile('Main Template');  
	   }*/
	    else{
	   	   $this->template = $this->prepare404();
	   }	
        
              
	   return $this;
	}			
	


	
	
	protected function _installFromPhar($u){
	   global $include;	
	   $this->data['INSTALLER_PHAR_AVAILABLE'] = '1';
	   $f = ( false !== strpos(\webdof\wURI::getInstance()->getU()->location, 'install.phar') ) ? 'install.phar' : 'install.php';
	   
	   /*
       $f2 = ( false !== file_exists($this->data['DIR'] . 'install.phar') ) ? 'install.phar/'
                   : ( false !== file_exists($this->data['DIR'] . 'install.php') ) ? 'install.php/' : '';	   
	   */
       $f2 = (  file_exists($this->data['DIR'] . 'install.phar') || 'install.phar' === basename(__FILE__) ) ? 'install.phar/'
                   : ( file_exists( $this->data['DIR'] .'install.php') ) ? 'install.php/' : '';	   	   
	   
	   $this->data['tpl_data']['URI_DIR_API'] =  $this->data['tpl_data']['URL'].$f.'/api.php';	
	   $this->data['tpl_data']['EXTRA_PMX_URL'] =  $this->data['tpl_data']['URL'].$f.'/pragmamx.php';	
	   $this->data['tpl_data']['INSTALLER'] = 'phar';
       $this->data['PHAR_INCLUDE'] = str_replace('phar://', '',$include);  	
       

       $this->data['tpl_data']['EXTRA_PHAR_URL'] = $this->data['tpl_data']['URL'].$f2.'api.php';	
       
       
       
       if('' !== $include) $this->data['INSTALLER'] = 1;
       

    	

	   return $this;
	}


    public function OutData(){
    	$p = func_get_args();
    	if(0 === count($p)){
			return $this->data['data_out'];
		}elseif(1 === count($p)){
			$this->data['data_out'] = $p[0];
		}elseif(2 === count($p) && is_string($p[0])){
			$this->data['data_out']->{$p[0]} = $p[1];
		}else{
			return trigger_error('Invalid number of arguments in '.__METHOD__.' '.__LINE__, E_USER_ERROR);
		}
		
		return $this;
	}
	
	
	
	public function login(){
    	 	if( 
    	 	
    	 	 isset($_POST['pwd']) && isset($_POST['PIN'])
    	 	&& ((
 		 	 $this->data['config']['ADMIN_PWD'] === sha1(trim($_POST['pwd'], '"\' '))
		 	&& $this->data['config']['HOST'] === $_SERVER['SERVER_NAME']
		 	&& $this->data['config']['PIN'] ===$_POST['PIN']
		 	&& (!isset($this->data['config']['DISABLE_ADMIN_PWD']) || false === $this->data['config']['DISABLE_ADMIN_PWD'])
		    	)
		 	  || (
 		 	          $this->data['config_new']['ADMIN_PWD'] === sha1(trim($_POST['pwd'], '"\' '))
		 	       && $this->data['config_new']['HOST'] === $_SERVER['SERVER_NAME']
		 	       && $this->data['config_new']['PIN'] ===$_POST['PIN']
		 	       && (!isset($this->data['config_new']['DISABLE_ADMIN_PWD']) || false === $this->data['config_new']['DISABLE_ADMIN_PWD'])
		 	))
		 	
		 	){
 		 	    $this->aSess['ADMIN_PWD'] =  sha1(trim($_POST['pwd'], '"\' '));
		 	    $this->aSess['HOST'] = $_SERVER['SERVER_NAME'];
		 	    $this->aSess['PIN'] =$_POST['PIN'];
		 	    $this->aSess['isAdmin'] = true;
			 	session_write_close();
			 	session_start();    
			 	
			 	

	 	
			 	
			} elseif(isset($_POST['pwd']) || isset($_POST['PIN'])){
				if(isset( $this->aSess['isAdmin']))unset( $this->aSess['isAdmin']);
				if(!isset($_REQUEST['test'])){
         	    	foreach($this->aSess as $k => $v){
					   unset($this->aSess[$k]);
				   }					
				}

			}  	 
				
		
		return $this->isLoggedIn();		
	}
	
	public function isLoggedIn(){
       $this->isAdmin = false;

		foreach($this->data['config']['EXTRA']['pragmamx']['installdirs'] as $mainfile){
			try{
				
			  $func = function($file){
			  	  ob_start();
			  		@include $file;
			      ob_end_clean();		
			  };
			
			 $func($mainfile);
			
			 if(MX_IS_ADMIN){
			 	  $this->aSess['isAdmin'] = true;
			 }
			 
			
			  
			}catch(\Exception $e){
				trigger_error($e->getMessage(), E_USER_WARNING);
			}
		}
		
		
		
		 	if(true !== $this->aSess['isAdmin']){
		 		$this->isAdmin = false;
		 		unset($this->aSess['ADMINDATA']);
		 		$this->aSess['ADMINDATA'] = array(
		 		   'CONFIGFILE' => $this->data['CONFIGFILE'],
		 		);
		 		if(isset( $this->aSess['isAdmin']))unset( $this->aSess['isAdmin']);
			}else{
				   $this->aSess['ADMINDATA'] = $this->data;
				   $this->isAdmin = true;
			}	 		
			
					
		$this->aSess['isAdmin']	= $this->isAdmin;	
		return $this->isAdmin;	
	}
	
    protected function _api($u = null){
		 $u = (null === $u) ? \webdof\wURI::getInstance() : $u;
		ini_set('display_errors', 0);
		 
        $this->login();	 
	  	
		 	 			 
			/*
			* ToDo:  set output formatter (defaults to jsonp)
			*/	
	  	    $this->ob_compress();
			header("Content-Type: application/x-javascript; charset=UTF-8;");
		 	ob_start(function($c){
		 		       	 $r = (isset($this->data['data_out'])) ? $this->data['data_out'] :  new \stdclass;
		 		       	 $r->type = (isset($r->type)) ? $r->type : 'print';
		 		       	 $r->out = (isset($r->out)) ? $r->out : $c;
      	                 $fnregex = "/^[A-Za-z0-9\$\.-_\({1}\){1}]+$/";
      	                 $callback = (isset($_REQUEST['callback']) && preg_match($fnregex, $_REQUEST['callback']))
		                   ? strip_tags($_REQUEST['callback'])
		                   : '';
		                   
		                   
                if($callback === ''){
         	            $o = json_encode($r);
                }  else {
                	       $r->callback = $callback;
                           $o = $callback.'(' . json_encode($r) . ')';
		                }
		        
		  
		        return $o;
		 	});
		 		
		 
		
		
	
		 
		 
		 /* BEGIN extract phar (todo build/refactor API) */
		 if(isset($_GET['EXTRA_EXTRACT_PHAR']) ){
		

		 	
		 	if(file_exists( $this->data['CONFIGFILE']) && $this->data['config_new']['PACKAGE'] !== $this->data['config']['PACKAGE'] ){
		 		\webdof\wResponse::status(409);
			    $str ='Error: Invalid installer package name';
				if(true === $this->debug)trigger_error($str, E_USER_ERROR);
				die($str);				
			}
		 	
		 	if( 1 !== intval($this->data['INSTALLER_PHAR_AVAILABLE'])
		 	  || intval( str_replace(array('"', "'"), array('',''), $this->data['INSTALLER']) ) !== 1
		 	  || !isset( $this->data['PHAR_INCLUDE'])
		 	  || !class_exists('\Extract')
		 	){
		 		\webdof\wResponse::status(400);
			    $str ='Error: Not in installer context';
				if(true === $this->debug)trigger_error($str, E_USER_ERROR);
				die($str);			
			}

		 	
		 	if(true !== $this->isLoggedIn()){
		 		\webdof\wResponse::status(401);
				die('Invalid credentials, try to install via <a href="{___$$URL_INSTALLER_HTMLPAGE$$___}">{___$$URL_INSTALLER_HTMLPAGE$$___}</a>!');
			}
		 	
		 	
		   if(isset($_REQUEST['test']) && 'response' === $_REQUEST['test']){
		   	    \webdof\wResponse::status(200);
		   	    $this->data['data_out']->code = 200;
		   	    $this->data['data_out']->extra = true;
		   	    $this->data['data_out']->out = 'OK';		   	    
		   	    $this->data['data_out']->changed = (json_encode($this->data['config']) !== json_encode($this->data['config_new']));
		   	 

                $this->data['data_out']->config =  new \stdclass;
                $this->data['data_out']->config_new = new \stdclass;                
		
                $this->data['data_out']->config->PACKAGE =  $this->data['config']['PACKAGE'];
                $this->data['data_out']->config_new->PACKAGE =  $this->data['config_new']['PACKAGE'];
                
		
                $this->data['data_out']->config->VERSION =  $this->data['config']['VERSION'];
                $this->data['data_out']->config_new->VERSION =  $this->data['config_new']['VERSION'];
                
              		
                $this->data['data_out']->config->INSTALLED =  $this->data['config']['INSTALLED'];
                $this->data['data_out']->config_new->INSTALLED =  $this->data['config_new']['INSTALLED'];
                
              		
                $this->data['data_out']->config->REGISTERED =  $this->data['config']['REGISTERED'];
                $this->data['data_out']->config_new->REGISTERED =  $this->data['config_new']['REGISTERED'];
                
              		
                $this->data['data_out']->config->UNAME =  $this->data['config']['UNAME'];
                $this->data['data_out']->config_new->UNAME =  $this->data['config_new']['UNAME'];
                
              		
                $this->data['data_out']->config->UID =  $this->data['config']['UID'];
                $this->data['data_out']->config_new->UID =  $this->data['config_new']['UID'];
                
                              		   	    	   	       	       
		   	    die('OK');
		   }
		 	
		 	$EXTRA = &$this->data['config']['EXTRA'];
		 			 	
		 	try{
				\Extract::from($this->data['PHAR_INCLUDE'])->to(  $this->data['DIR'] ,
                   function (Entry $entry) use(&$EXTRA) {
                      if (false == strpos(basename($entry->getName()), '.')
                        && (
                                 true == \webdof\valFormats::is(basename($entry->getName()), 'md5', true)
                              || true == \webdof\valFormats::is(basename($entry->getName()), 'sha1', true)    
                            )     
                      ) {
                            return; 
                      }                    
                      
                      if('config.frdl.php' == basename($entry->getName()) || 'config.php' == basename($entry->getName())){
					  	 return;
					  }
					  
					//  if(true == $EXTRA['extra']['pragmamx']['main'] ){
					  	 if('.htaccess' === basename($entry->getName())) return;
					  	 if('index.php' === basename($entry->getName())) return;
					//   }
					  
                   });
                   

                   
			}catch(\Exception $e){
				\webdof\wResponse::status(409);
				$str = $this->data['PHAR_INCLUDE'] .' -> '.$this->data['DIR'].' - ' .$e->getMessage();
				if(true === $this->debug)trigger_error($str, E_USER_ERROR);
				die($str);
			}
		 	 
		 	 if(file_exists($this->data['DIR']. 'composer.json')){
		 	 	
	
		 	 	
			 			/*
			 	   		 $.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].config.loc.api_url="'.$this->data['config']['URL_API_ORIGINAL'].'";
			 			*/
			 			$this->data['data_out']->js = trim(preg_replace("/\s+|\n/", " ", 'try{
			 			$(\'#window_\' + \'frdl-webfan\').find(\'#wd-li-frdl-webfan-installPHAR\').find(\'u\').html(\'Update\');
			 			$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig();
			 		
			 			}catch(err){console.error(err);}			 			
			 			'));
			 			
			 			if($this->data['config_new']['ADMIN_PWD'] !== $this->data['config']['ADMIN_PWD']){
							$this->data['data_out']->js.= ' 
					     		alert("Attention: Password has changed ('.trim($_POST['pwd'], '"\' ').')!");
							 ';
						}
			 			
			 			if($this->data['config_new']['PIN'] !== $this->data['config']['PIN']){
							$this->data['data_out']->js.= ' 
					     		alert("Attention: PIN has changed ('.$this->aSess['PIN'].')!");
							 ';
						}
									 		
									 		
			 			$this->data['config']['VERSION'] = $this->data['config_new']['VERSION'];
			 			$this->data['config']['DOWNLOADUPDATETIME'] = $this->data['config_new']['DOWNLOADTIME'];
			 			
			 			
			 			$this->data['config']['UPDATETIME'] = time();
			 			
			 			$this->data['config']['ADMIN_PWD'] = $this->aSess['ADMIN_PWD'];
			 			$this->data['config']['PIN'] = $this->aSess['PIN'];
			 			$this->data['config']['DIR_PACKAGE'] = $this->data['DIR'];
			 			
			 			if(0 === intval($this->data['config']['UID']) && 0 !== intval($this->data['config_new']['UID']) ){
							$this->data['config']['UID'] = $this->data['config_new']['UID'];
						}
			 			
				 			
			 			if( '' === $this->data['config']['UNAME'] && '' !== $this->data['config_new']['UNAME']){
							$this->data['config']['UNAME'] = $this->data['config_new']['UNAME'];
						}
	 			
			 			$this->data['config']['DIRS'] = array_merge((isset($this->data['config']['DIRS']) && is_array($this->data['config']['DIRS']))
			 			         ? $this->data['config']['DIRS'] : array(), array(
			 			            'apps' =>  $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'apps'. DIRECTORY_SEPARATOR,
			 			            'batch' =>  $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'batch'. DIRECTORY_SEPARATOR,
			 			            'cache' =>   $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'cache'. DIRECTORY_SEPARATOR,
			 			            'config' =>   $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'config'. DIRECTORY_SEPARATOR,
			 			            'data.norm' =>   $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'data.norm'. DIRECTORY_SEPARATOR,
			 			            'data.storage' =>   $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'data.storage'. DIRECTORY_SEPARATOR,
			 			            'packages' =>   $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'packages'. DIRECTORY_SEPARATOR,
			 			            'repositories' =>   $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'repositories'. DIRECTORY_SEPARATOR,
			 			            'servers' =>   $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'servers'. DIRECTORY_SEPARATOR,
			 			            'share' =>   $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'share'. DIRECTORY_SEPARATOR,
			 			            'tmp' =>   $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'tmp'. DIRECTORY_SEPARATOR,
			 			            'vendor' =>   $this->data['config']['DIR_PACKAGE'] . '.ApplicationComposer'. DIRECTORY_SEPARATOR .'vendor'. DIRECTORY_SEPARATOR,
			 			                 
			 			  ));
			 					
			 					
			 			
			 			$files = array();
			 			
			 			$this->data['config']['FILES'] = array_merge((isset($this->data['config']['FILES']) && is_array($this->data['config']['FILES']))
			 			         ? $this->data['config']['FILES'] : array(), array(
			 			            'composer' =>  $this->data['config']['DIR_PACKAGE'] . 'composer.json',
			 			            'config' =>    $this->data['CONFIGFILE'],
			 			            'database-schema' =>  $this->data['config']['DIRS']['config'] . 'database-schema.dat', 
			 			            'boot.batch' =>  $this->data['config']['DIRS']['batch'] . '.boot.bat',     
			 			  ));					 					
			 				
			 			
			 			$config = $this->data['config'];	
			 			if(true === $EXTRA['extra']['pragmamx']['main'] ){
                        	$config['URL'] .= 'setup.php';
                        }
                        	
			 				
			 					 			
			 			$php = "<?php
			 			/*
			 			  - Do not edit this file manually! 
			 			  Application Composer - Config
			 			  Download: {___$$URL_INSTALLER_HTMLPAGE$$___}
			 			  
			 			*/
			 			    if(isset(\$this) &&
			 			     (  get_class(\$this) === '\\".get_class($this)."' 
			 			     || is_subclass_of(\$this, '\\frdl\ApplicationComposer\Command\CMD')  
			 			     || get_class(\$this) === '".get_class($this)."' 
			 			     || is_subclass_of(\$this, 'frdl\ApplicationComposer\Command\CMD')  
			 			     )
			 			     ){
			 			     	 \$this->data['config'] = ".var_export($config, true).";		
			 			     	
                         	    ";





$php .= "


/*
* If you have PragmaMx installed, its db-config will be used instead!
*/
\$pmxconfigfile=__DIR__.DIRECTORY_SEPARATOR. 'config.php';
if(true === \$this->data['config']['EXTRA']['extra']['pragmamx']['main'] && file_exists( \$pmxconfigfile)  ){
	try{
	  ob_start();
		@include \$pmxconfigfile;
		if(isset(\$dbtype))\$this->data['config']['db-driver']=strtolower(\$dbtype);
		if(isset(\$dbhost))\$this->data['config']['db-host']=\$dbhost;
		if(isset(\$dbuname))\$this->data['config']['db-user']=\$dbuname;
		if(isset(\$dbpass))\$this->data['config']['db-pwd']=\$dbpass;
		if(isset(\$dbname))\$this->data['config']['db-dbname']=\$dbname;
		if(isset(\$prefix))\$this->data['config']['db-pfx']=\$prefix;
	  ob_end_clean();			
	}catch(\\Exception \$e){
		
	}

}



";      

                     	    
                         	    						
						
						$php .= "}";
			 			
			 			file_put_contents($this->data['CONFIGFILE'], $php);
							 			
							 			
							 			
			 		    if(isset($_REQUEST['u'])){
			 		    	unlink( $this->data['PHAR_INCLUDE']);
			 		    	$this->data['data_out']->js.= ' $.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].config.EXTRA_INSTALLER = null;	';
			 		    }
			 			
			 				 			
	                     $config['URL'] = str_replace('setup.phpsetup.php', 'setup.php', $config['URL']);

					 	   	$this->data['data_out']->js.= '
			                	$(\'#window_main_postbox-ttt-all\').wdPostbox(\'deleteMessage\', \'install-frdl-webfan-installer-'.$this->data['config_new']['VERSION'].'\',  \'update\', false);	 	   	
					 	   	
					 	   	
					 	   	alert(\'Please set up the configuration next...\');
			 		    	window.location = "'.$config['URL'].'";
			 		    	';															 		
		             
		              \webdof\wResponse::status(201);
		 	            die('Extracted');
			 }else{
				$str = 'Error extracting php archive';
				\webdof\wResponse::status(409);
				if(true === $this->debug)trigger_error($str, E_USER_ERROR);
				die($str);			 	
			 }
		 }
		 /* END extract (todo) */
		 elseif(isset($_REQUEST[self::CMD])){
		 	return $this->_api_request_cmd($_REQUEST[self::CMD], $_REQUEST);
		 }
		 
		 
		 \webdof\wResponse::status(404);
		 die('Unexpected end of api.request');
	}
     
	
	public function prepare404(){
		\webdof\wResponse::status(404);
		$this->template = $this->readFile('404');
		return $this->template.$this->route;
	}
	
	
	

	public function _api_request_cmd($cmd, $settings){

	try{
	if(null === self::$_db){
		self::$_db =\frdl\DB::_(array(
		//self::$_db =  new \frdl\DB(array(
		   'driver' => $this->data['config']['db-driver'],
		   'host' => $this->data['config']['db-host'],
		   'dbname' => $this->data['config']['db-dbname'],
		   'user' => $this->data['config']['db-user'],
		   'password' => $this->data['config']['db-pwd'],
		   'pfx' => $this->data['config']['db-pfx'],
		   
		  ), false);
	}			
	}catch(\Exception $e){
		/* no explicit need of DB connection here */
	}

	
		 
		foreach($this->data['settings']->cli as $cmdpfx => $console){
			$t = $console['cli.cmd.cli'];
		
			$l = strlen($t);
			if(substr($cmd, 0, $l) === $t){
				$cmd = substr($cmd, $l, strlen($cmd));
				if(is_subclass_of($console['cli.class'], $console['cli.class.required.parent'])
				 && is_subclass_of($console['cli.class'], '\frdl\aSQL\Engines\Terminal\CLI')){
					
				   $this->Console = new $console['cli.class'];
				   $this->Console->applyApp($this);
				  
				  
				   $this->Console->exe($cmd);
				  return  $this->_api_response( $this->Console->dump() );
			
				}else{
					\webdof\wResponse::status(501);
					trigger_error('No valid Console SubClass.', E_USER_NOTICE);
					continue;
				}
				   
			}
		}
		
		
		 \webdof\wResponse::status(404);
		 die('API cli not found');
	}
	
	
	protected function _api_response($dump){
		 $this->data['data_out']->data = $dump;
		 die($dump->statusText);
	}
	
	
} 

 
 
 
  
 $fexe = new webfan(__FILE__, __COMPILER_HALT_OFFSET__);
 $fexe->run();


__halt_compiler();µConfig%json%config.json
{
	"PACKAGE" : "{___$PACKAGE___}",
	"VERSION" : "{___$VERSION___}",
	"OID" : "{___$OID___}",
	"PIN_HASH" : "{___$PIN_HASH___}",
	"ADMIN_PWD" : "{___$adminpwd_optional_HASH___}",
	"PIN" : "{___$PIN___}",
	"HOST" : "{___$HOST___}",
	"HOST_HASH" : "{___$HOST_HASH___}",
	"URL" : "{$__LOCATION___}",
	"DOWNLOADTIME" : "{___$DOWNLOADTIME___}",
	"INSTALLTIME" : "{___$INSTALLTIME___}",
	"UID" : "{___$UID___}",
	"UNAME" : "{___$UNAME___}",
	"REGISTERED" : "{___$REGISTERED___}",
	"LICENSEKEY" : "{___$LICENSEKEY___}",
	"LICENSESERIAL" : "{___$LICENSESERIAL___}",
	"SECRET" : "{___$SECRET___}",
	"SHAREDSECRET" : "{___$SHAREDSECRET___}"
}
µxTpl%%Main Template



{___$TPL_FRDL_WEBFAN_INSTALL_&HTML___}






<script type="text/javascript">
(function($){
$(document).ready(function() {
	try{
	 frdl.wd({
	  browseContentOnInit : false,	
      modules : [
        function(){
	   $.ApplicationComposerOpen({
 	    PACKAGE : '{$___PACKAGE___}',
 	    VERSION : '{$___VERSION___}',
 	    INSTALLED : '{$___INSTALLED___}',
 	    INSTALLER : '{$___INSTALLER___}',
 	    REGISTERED : '{$___REGISTERED___}',
		EXTRA_INSTALLER : '{$___INSTALLER_PHAR_AVAILABLE___}',
 	    INSTALLER_PHAR_AVAILABLE : '{$___INSTALLER_PHAR_AVAILABLE___}',
 	    
		user : {
			uid : '{$___UNAME___}',
			uname : '{$___UID___}'
		},
 	    
		loc : {
			url : '{$___URL___}',
			api_url : '{$___URI_DIR_API___}',
			EXTRA_PMX_URL : '{$___EXTRA_PMX_URL___}',
			EXTRA_PHAR_URL : '{$___EXTRA_PHAR_URL___}'
		}
       });	
	}
  ]
});
 

	}catch(err){
		console.error(err);
	}
     	
     
});	
})(jQuery);
</script>	
µxTpl%%404
<span style="color:red;">The requested content was not found!</span>
<br />
[ <a href="/">Go to startpage</a> ]
<br />
<script type="text/javascript">
$(document).ready(function(){
$.WebfanDesktop({});	
document.title = '404 - Not found';
 uhrTimer.add("Timer_" + window.location.href + "_notification_404", function(){
	  if('function' !== typeof $.notificationcenter.newAlert)return;	
	  uhrTimer.remove("Timer_" + window.location.href + "_notification_404");
  	 $.notificationcenter.newAlert("Der angeforderte Inhalt unter " + window.location.href + " wurde nicht gefunden!", "error", true, function(notif){
		   document.title = notif.text;
	   }, 
	   Guid.newGuid()
	  );
	 $.ApplicationComposerOpen({});
  }); 
});
</script>
