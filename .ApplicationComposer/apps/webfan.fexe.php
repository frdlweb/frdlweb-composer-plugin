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
 *   @requires   frdl\xGlobal\fexe
 * 
 */
namespace frdl\xGlobal; 

/* BEGIN CONFIGSECTION */
error_reporting(E_ALL);
ini_set('display_errors', 1);
/* END CONFIGSECTION */

/* BEGIN BOOTSECTION */
if(!class_exists('\frdl\webfan\App')){
	
 if(!defined( __NAMESPACE__.'\\'.'__BOOTFILE__')) {
 	define( __NAMESPACE__.'\\'.'__BOOTFILE__', __DIR__ . DIRECTORY_SEPARATOR . '..'.DIRECTORY_SEPARATOR . 'bootstrap.php');
 }


 if(!in_array( __BOOTFILE__, get_included_files())){
 	
  if(!file_exists(  __BOOTFILE__)){
	echo 'App '.basename(__FILE__).' is not installed cortrectly! File ' .__BOOTFILE__.' not found. 
	<br />
	Please read <a target="_blank" href="https://github.com/frdl/webfan/wiki/Installation">Installation instruction</a>!';
	die();
  }
 	
 	require  __BOOTFILE__;
 }
 
}
 
 if(!class_exists('\frdl\webfan\App')){
 	echo 'App '.basename(__FILE__).' is not installed cortrectly! Class \frdl\webfan\App not found. 
	<br />
	Please read <a target="_blank" href="https://github.com/frdl/webfan/wiki/Installation">Installation instruction</a>!';
	die();	
 }
/* END BOOTSECTION */ 
 
 
 
   
class webfan extends fexe    
{
	 const DEL='µ';
	 const URI_DIR_API = 'api';
	
	 public function Request(mixed $args = null){
	 	
	 }
	 
	 public function data($data = null){
	  if(null === $this->data){
	  	
	   $this->data = array();
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
	 }
	 
	 	
    public function run(&$Request =null){
    	$this->default_run($Request);
    	
    	 $this->out(); 
	}
	
    protected function route($u = null){
        
	   $u = (null === $u) ? \webdof\wURI::getInstance() : $u;
	   
	   if('/' === $u->getU()->req_uri ){
	      $this->template = $this->readFile('Main Template');
	   } elseif(self::URI_DIR_API === $u->getU()->dirs[0]){
	   	  return $this->_api($u);
	   }
	   
	   else{
	   	  $this->template = $this->prepare404();
	   }	
        
      
	}

    protected function _api($u = null){
		 $u = (null === $u) ? \webdof\wURI::getInstance() : $u;
		 
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

__halt_compiler();µxTpl%%Main Template
<h1 style="color:#6495ED;">frdl/webfan - Application Composer</h1>
<a href="javascript:;" onclick="$.WebfanDesktop({});" style="color:#6495ED;">!desktop</a>
<script type="text/javascript">
$(document).ready(function() {
(function($){
	
	 $.WebfanDesktop({
     	      modules : [
 {
	mid : 'frdl-webfan',
	title : 'Application Composer',
	description : 'Plugin for Webfan Desktop to provide a AC frontend.',
	init : function(){
	        $('#window_frdl-webfan').show();
    },
	cb_open : function(){
        
    },
	icons : [
	    {
	    	mid : 'frdl-webfan',
		    img :  'http://static.webfan.de/icons/icons-3/icon_package_get.gif',
		
			  cb_open : function(ev){
		                     
	                 }
		}
	],
    windows : [ 
    {
     	mid : 'frdl-webfan',
		 	img : 'http://static.webfan.de/icons/icons-3/icon_package_get.gif',
		 	title : 'Application Composer at ' + window.location.href,	
	        
	        html_aside : "<ul><li>Server</li><li>Projects</li><li>Packages</li><li>Apps</li><li>Repositories</li></ul>",
            
            html_main : "...",        
	
	        html_bottom : 'Testmodus - {$___FILE___}',
	        
	        exec : function(ev){
		                   
	                 }
	
		 }
    ],
    menulinks : [
    
    ],
    canvas : [
  /*
       {
	   	  type : 'html',
	   	  device : '#desktop',
	   	  prepend : true,
	   	  html : '<span style="color:red;font-size:06.em;">Testmodus - Development Version</span>'
	   	  
	   	   	   	
	   }	
	   */
    ],
    
    config : {
		location : {
			url : window.location.href,
			api_url : window.location.href + '{$___URI_DIR_API___}'
		}
	},
	
	  /*  ydn schema    */
    shema : null
    
	
     }    	      
    ]
 });
     	
     	
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

