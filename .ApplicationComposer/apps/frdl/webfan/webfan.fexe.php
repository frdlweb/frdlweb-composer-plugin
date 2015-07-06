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
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
/* END CONFIGSECTION */

/* BEGIN BOOTSECTION */
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
	 const URI_DIR_API = 'api';
	 
	 const HINT_NOTINSTALLED = 'The Program frdl/webfan is not installed properly, try to install via {___$$URL_INSTALLER_HTMLPAGE$$___}!';
	
	 public function Request(mixed $args = null){
	 	
	 }
	 
	 public function data($data = null){
	  if(null === $this->data){
	  	

	   	  	
	   $this->data = array();
	   $this->data['config'] = array();
	   $this->data['installed'] = false;
	   $this->data['index'] = 'Main Template';	
       $this->data['template_main_options'] = array(   
                'Title' =>  'Webfan - Application Composer',
	            'css' => array(
	            
	            ),
			    'js' => array(
				        'http://api.webfan.de/api-d/4/js-api/library.js',
				),
				'meta' =>  array(
				     array('http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-8'),				 
				     array('name' => 'apple-mobile-web-app-capable', 'content' => 'yes'),
				     array('name' => 'apple-mobile-web-app-status-bar-style', 'content' => 'lightblue'),
				     array('name' => 'HandheldFriendly', 'content' => 'true'),
				     array('name' => 'MobileOptimized', 'content' => '320'),
				     array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0, user-scalable=yes'),
				     
				 )
				 
	    );
	   
	     
	     $this->data['tpl_data'] = array(
	          'FILE' => htmlentities(__FILE__),
	          'URI_DIR_API' => self::URI_DIR_API,
 	          'LOCATION' => 'http://'.$_SERVER['SERVER_NAME']
 	                         .implode('/', \webdof\wURI::getInstance()->getU()->dirs)
 	                         .'/'.\webdof\wURI::getInstance()->getU()->file,
 	          'URL' => '',
 	          'EXTRA_PMX_URL' => '""',
 	          
	     );
	 }
	 
	 
	  
	   	   
	   if(is_array($data) || is_object($data)){
	   	 foreach($data as $k => $v){
		 	if(isset($this->data[$k]))$this->data[$k] = $v;
		 }
	   }
	   
	   return $this->data;	 	
	 }
	 
	 protected function _boot(){
	 	$this->default_boot() ;
		
	 	\frdl\webfan\Autoloading\SourceLoader::top() 
          -> addPsr4('frdl\ApplicationComposer\\', __DIR__ . DIRECTORY_SEPARATOR . 'frdl' .DIRECTORY_SEPARATOR . 'webfan' .DIRECTORY_SEPARATOR, false) ;
	 }
	 
	 	
    public function run(&$Request =null){
       if('cli' === PHP_SAPI){
 	      trigger_error('This is a web app, cli support is not implemented yet completly!', E_USER_WARNING);
 	      chdir(dirname($_SERVER['argv'][0]));
       }  	
    	
    	$this->default_run($Request);
    	
    	 $this->out(); 
	}
	
    protected function route($u = null){
       $u = (null === $u) ? \webdof\wURI::getInstance() : $u;
       
       $this->data['config'] = $this->readFile('config.json');
       $this->data['config'] = (array)$this->data['config'];
       
       if(!is_array($this->data['config']) || count($this->data['config']) < 3){
       	    $this->data['installed'] = "0";
	   	    trigger_error(self::HINT_NOTINSTALLED, E_USER_WARNING);
	   }else{
	   	    $this->data['installed'] = "1";
	   }
        $this->data['tpl_data']['INSTALLED'] = $this->data['installed'];

       
       
       if(base64_decode('eyRfX0xPQ0FUSU9OX19ffQ==') === $this->data['config']['URL'] ){
	   	   $this->data['config']['URL'] = $this->data['tpl_data']['LOCATION'];
	   	   trigger_error(self::HINT_NOTINSTALLED, E_USER_WARNING);
	   }
       $this->data['tpl_data']['URL'] = $this->data['config']['URL'];
	   $this->data['tpl_data']['URI_DIR_API'] =  $this->data['tpl_data']['URL'].'api/';	
   
 	    $this->data['tpl_data']['PACKAGE'] = $this->data['config']['PACKAGE'];
 	    $this->data['tpl_data']['VERSION'] = $this->data['config']['VERSION'];
 	    $this->data['tpl_data']['INSTALLED'] = $this->data['config']['INSTALLED'];
 	    $this->data['tpl_data']['REGISTERED'] = $this->data['config']['REGISTERED'];
 	    
         	
       $this->data['INSTALLER_PHAR_AVAILABLE'] = 0;
   
       $this->data['tpl_data']['INSTALLER'] = '';
	   if(function_exists('frdl_install_rewrite_function')){
	   	  $this->_installFromPhar($u);
	   }
	   $this->data['tpl_data']['INSTALLER_PHAR_AVAILABLE'] =   $this->data['INSTALLER'] ;
    	 
    	 
	   
	   if(
	       '/' === $u->getU()->req_uri 
	       || basename(__FILE__) ===  $u->getU()->file
	       || $u->getU()->file === $u->getU()->file_ext
	       || 'install.phar' === $u->getU()->file
	       || 'install.php' === $u->getU()->file
	   ){
	        $this->template = $this->readFile('Main Template');
	   } elseif(
	          self::URI_DIR_API === $u->getU()->dirs[0]
	     ||  'api.php' === $u->getU()->file     
	     ||  'api' === $u->getU()->file     
	   ){
	   	  return $this->_api($u);
	   }
	   
	   else{
	   	  $this->template = $this->prepare404();
	   }	
        
      
	}
	
	protected function _installFromPhar($u){
	   global $include;	
	   $f = ( false !== strpos(\webdof\wURI::getInstance()->getU()->location, 'install.phar') ) ? 'install.phar' : 'install.php';
	   $this->data['tpl_data']['URI_DIR_API'] =  $this->data['tpl_data']['URL'].$f.'/api.php';	
	   $this->data['tpl_data']['EXTRA_PMX_URL'] =  $this->data['tpl_data']['URL'].$f.'/pragmamx.php';	
	   $this->data['tpl_data']['INSTALLER'] = 'phar';
       $this->data['PHAR_INCLUDE'] = str_replace('phar://', '',$include);  	
       if('' !== $include) $this->data['INSTALLER'] = 1;
	}

    protected function _api($u = null){
		 $u = (null === $u) ? \webdof\wURI::getInstance() : $u;
		 
		 /* BEGIN extract (todo) */
		 if(isset($_GET['EXTRA_EXTRACT_PHAR']) ){
		 	
		 	if(intval( str_replace(array('"', "'"), array('',''), $this->data['INSTALLER']) ) !== 1){
			    $str ='Error: Not in installer context';
				trigger_error($str, E_USER_ERROR);
				die($str);			
			}

		 	
		 	if( $this->data['config']['ADMIN_PWD'] !== sha1($_REQUEST['pwd'])
		 	|| $this->data['config']['HOST'] !== $_SERVER['SERVER_NAME']
		 	|| $this->data['config']['PIN'] !==$_REQUEST['PIN']
		 	){
				die('Invalid credentials, try to install via {___$$URL_INSTALLER_HTMLPAGE$$___}!');
			}
		 	
		 	try{
				\Extract::from($this->data['PHAR_INCLUDE'])->to(getcwd ());
			}catch(\Exception $e){
				$str = $this->data['PHAR_INCLUDE'] .' -> '.getcwd().' - ' .$e->getMessage();
				trigger_error($str, E_USER_ERROR);
				die($str);
			}
		 	 
		 	 if(file_exists(getcwd (). DIRECTORY_SEPARATOR . 'composer.json')){
			 			unlink( $this->data['PHAR_INCLUDE']);
		 	            die('Extracted');
			 }else{
				$str = 'Error extracting php archive';
				trigger_error($str, E_USER_ERROR);
				die($str);			 	
			 }
		 }
		 /* END extract (todo) */
		 
		 die('API:callback: ToDo...');
	}
     
	
	public function prepare404(){
		\webdof\wResponse::status(404);
		$this->template = $this->readFile('404');
		return $this->template;
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
<h1 style="color:#6495ED;">frdl/webfan - Application Composer</h1>
<a href="javascript:;" onclick="$.WebfanDesktop({});" style="color:#6495ED;">!desktop</a>
<script type="text/javascript">
$(document).ready(function() {
(function($){
	try{
	 $.WebfanDesktop({
      modules : [
            
    ]
  });
 
 $.ApplicationComposerOpen({
 	    PACKAGE : '{$___PACKAGE___}',
 	    VERSION : '{$___VERSION___}',
 	    INSTALLED : '{$___INSTALLED___}',
 	    INSTALLER : '{$___INSTALLER___}',
 	    REGISTERED : '{$___REGISTERED___}',
		EXTRA_INSTALLER : '{$___INSTALLER_PHAR_AVAILABLE___}',
 	    
		loc : {
			url : '{$___URL___}',
			api_url : '{$___URI_DIR_API___}',
			EXTRA_PMX_URL : '{$___EXTRA_PMX_URL___}'
		}
 });	
	}catch(err){
		console.error(err);
	}

     	
     	
})(jQuery);

});
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
	
  }); 


});
</script>

