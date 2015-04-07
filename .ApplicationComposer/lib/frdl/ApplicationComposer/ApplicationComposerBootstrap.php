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
 *  @author 	Till Wehowski <software@frdl.de>
 *  @copyright 	2014 Copyright (c) Till Wehowski
 *  @version 	0.0.1
 *    
 */
namespace frdl\ApplicationComposer;
use frdl\ApplicationComposer\ApplicationMap;
use frdl;

class ApplicationComposerBootstrap
{
	protected static $_instance = null;
	protected static $baseconfig = array(); 
	
	
	protected function __construct($baseconfig = array(), $boot = true){
		$this->init($baseconfig, $boot);
	}
	
	protected function __clone(){}
	
	public static function me( $baseconfig = null, $boot = true){
		if(null===self::$_instance){
			 self::$_instance = new self($baseconfig, $boot);
		}else{
			if(null !== $baseconfig)$this->init($baseconfig, $boot);
		}
		
	   return self::$_instance;
	}
	
	/**
	 * set working directory
	 */
	public function wd($dir = null, $code = E_USER_WARNING){
		if(null !== $dir && is_dir($dir)){
			$this->wd = $dir;
			if(chdir($dir) === false){
			    throw new Exception('Cannot change to directory '.$dir.' in '.__METHOD__, $code);
				return $this;
			}
		}else{
			$dir = $this->wd;
		}
		return $this;
	}

	public function __get($name){
		return (isset(self::$baseconfig['name'])) ? self::$baseconfig['name'] : null;
	}
	
	public function __set($name, $value){
		if(array_key_exists($name, self::$baseconfig)) self::$baseconfig[$name] = $value;
		return $this;
	}
	
	public function boot(){
		
		try{
			require self::$baseconfig['file_loader'];
			require self::$baseconfig['file_app'];
	
	
	//$init = false, $LoaderClass = self::LOADER, $name = '', $branch = 'dev-master', 
	//   $version = 'v1.0.2-beta.1', $meta = array()
	\frdl\webfan\App::God(self::$baseconfig['initOnBoot'],  self::$baseconfig['psr8_initial'], self::$baseconfig['name'],
   self::$baseconfig['branch'],   self::$baseconfig['version'],   self::$baseconfig['meta'])
   	     ->{'$'}('L!', '\webfan\Loader::top')
	  //    ->{'L!'}()
		->Autoloader(true)
             -> addPsr4('frdl\\', self::$baseconfig['dir_lib'] . 'frdl'.DIRECTORY_SEPARATOR,  false)  
             -> addPsr4('webfan\\', self::$baseconfig['dir_lib'] . 'webfan'.DIRECTORY_SEPARATOR,  false)
             -> addPsr4('webdof\\', self::$baseconfig['dir_lib'] . 'webdof'.DIRECTORY_SEPARATOR,  false)
             -> addPsr4('', self::$baseconfig['dir_lib'],  false)  
		 
		
		
         //  -> addClass('frdl\aSQL\Engines\Terminal\aSQLCommand', 'Terminal',  true )
         

       ;
			
			/**
			 *    ---- > load ApplicationMap...
			 *    $this->loadCoreConfig( CoreConfiguration $CoreConfiguration  )
			 */  
			
		}catch(Exception $e){
			echo $e->getMessage();
		}
		
		return $this;
	}
	

	protected function defaultConfig($dir_lib = null){
		$c =  array(  
		   'name' => 'Setup',
		   'branch' => 'dev-master',
		   'version' => 'v1.0.2-beta.1',
		   
		   'package' => 'frdl/webfan#setup',
		   	      
		   'meta' => array(),
		   
		   'config' => array(),
		   
		   'cmd' => 'setup',
		   
		   'initOnBoot' => true,		   
		   'psr8_initial' => 'frdl\webfan\Autoloading\SourceLoader',
	   
		   'dir_lib' => (null!==$dir_lib && is_dir($dir_lib)) ? $dir_lib : realpath(__DIR__ . DIRECTORY_SEPARATOR .'..' .DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR). DIRECTORY_SEPARATOR,
	       'wd' => getcwd(),
  	   
		   'UI' => PHP_SAPI,
		   
		   
		);
		
		$c['file_loader'] = $c['dir_lib']. 'frdl'.DIRECTORY_SEPARATOR.'webfan'.DIRECTORY_SEPARATOR.'Autoloading'.DIRECTORY_SEPARATOR.'SourceLoader.php';
		$c['file_app'] = $c['dir_lib']. 'frdl'.DIRECTORY_SEPARATOR.'webfan'.DIRECTORY_SEPARATOR.'App.php';
		
		return $c;
	}
	
	protected function init($baseconfig = array(), $boot = true){
		self::$baseconfig = (is_array($baseconfig) && count($baseconfig) > 0) ? array_merge($this->defaultConfig(null), $baseconfig) : $this->defaultConfig(null);
	    
		if($boot === true)$this->boot();
	    return $this;
	}
	
	
	
	
}
