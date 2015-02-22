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
 *  @package    webfan://webfan.App.code
 *  @uri        /v1/public/software/class/webfan/frdl.webfan.App/source.php
 *  @version 	0.9.4.3
 *  @file       frdl\webfan\App.php
 *  @role       project/ Main Application Wrap 
 *  @copyright 	2015 Copyright (c) Till Wehowski
 *  @license 	http://look-up.webfan.de/bsd-license bsd-License 1.3.6.1.4.1.37553.8.1.8.4.9
 *  @license    http://look-up.webfan.de/webdof-license webdof-license 1.3.6.1.4.1.37553.8.1.8.4.5
 *  @link 	http://interface.api.webfan.de/v1/public/software/class/webfan/frdl.webfan.App/doc.html
 *  @OID	1.3.6.1.4.1.37553.8.1.8.8 webfan-software
 *  @requires	PHP_VERSION 5.3 >= 
 *  @requires   webfan://webfan.Autoloading.SourceLoader.code
 *  @api        http://interface.api.webfan.de/v1/public/software/get/1/
 *  @reference  http://www.webfan.de/install/
 *  @implements Singletone
 *  @implements StreamWrapper
 * 
 */
namespace frdl\webfan;
use frdl;



class App
{
	
	const VERSION = '0.9.4.2';
	
	const NS = __NAMESPACE__;
	const DS = DIRECTORY_SEPARATOR;
	
	const LOADER = 'webfan\Loader';
	
	public static $instance = null;
	
	protected $name;
	
	protected $E_CALL = E_USER_ERROR;
	protected $wrap;
	public $_; 
	
	/**
	 * Stream Properties
	 */
	public $context = array();
	protected $data;
	protected $chunk;
	public $buflen;
	protected $pos = 0;
	protected $read = 0; 	
	protected $Controller;
	
	protected $wrappers;
	protected $shortcuts;
	
	
	protected function __construct($init = false, $name = '', $LoaderClass = 'frdl\webfan\Autoloading\SourceLoader' ){
		   $this->shortcuts = array();
		   $this->addShortCut('$', array($this,'addShortCut'));
		   
		   $this->name = $name;
		   $this->_ = (function(mixed $args = null){
		   	             $args = func_get_args();
						 $method = array_shift($args); 
                          trigger_error('Not fully implmented yet '.__METHOD__.' '.__CLASS__.' '.__LINE__, E_USER_WARNING);
		   	             return call_user_func(array(self::getInstance(false),$method),$args);
		   });
           $this->wrap = array( 
		         'c' => array(
				        self::LOADER =>  array($LoaderClass, false), 
         		        'webfan\App' =>  array(__CLASS__, false),
				 ),
		         'f' => array( 
	                    'test' => (function ($test = ''){
		                       echo 'Test: '.$test."\n";
	                    }),
	                    
				 ),
		   );
		   
	  $this->wrappers = array(  
	     'webfan' => array(
		         'tld' => array(   
				        'code' => 'webfan\Loader',
 
                  ),
		  ),
	      'frdl' => array(  
		  
		  ),
	      'homepagespeicher' => array(
		  
		  ),
	      'frdlweb' => array(  
		  
		  ),
	      'outshop' => array(  
		  
		  ),
	      'startforum' => array(  
		  
		  ),	 
	      
		       	 
	      

	      'wehowski' => array(  
		  
		  ),		       
	  );	   
		   
	   if($init === true)$this->init();
	}
	
	
	
   public function init_autoloader(&$Loader = null){
	 $Loader = (class_exists('\\'.self::LOADER) ) ? call_user_func('\\'.self::LOADER.'::top') : call_user_func('\\'.$this->wrap['c'][self::LOADER][0].'::top') ;
	 return $this;
   }
	
	
   public static function God($init = false, $name = '', $LoaderClass = 'frdl\webfan\Autoloading\SourceLoader'){
        return self::getInstance($init, $name, $LoaderClass);
   }
	
	
   public static function getInstance($init = false, $name = '', $LoaderClass = 'frdl\webfan\Autoloading\SourceLoader')
     {
       if (NULL === self::$instance) {
           self::$instance = new self($init, $name, $LoaderClass);
       }
       return self::$instance;
     }
	 	
 		
	public function __toString(){
		return (string)$this->name;
	}		
	
	
	
	/**
	 *  A)
	 *
	 *   webfan\App::God()->addShortCut('$', array($this,'addShortCut'))
	 *           ->{'$'}('$.MyIdentifier', function($arg){echo $arg;} )
	 *           -> {'$.MyIdentifier'}('Hello world')
	 * ;
	 * 
	 * B)
	 *   webfan\App::God()->{'webfan.Loader.repository'}('frdl');       //OR
	 *   webfan\App::God()->{'webfan.Loader->repository'}('frdl');
	 */	
	protected function _fnCallback($name){
		
		 $name = str_replace('\\','.',$name);
		
		// A
		  if(isset($this->shortcuts[$name])){
		  	   if(is_callable($this->shortcuts[$name]))return $this->shortcuts[$name];
		  } 
		  
		  
		 //B 
		 if(strpos($name,'.')!==false || strpos($name,'->')!==false || strpos($name,'::')!==false){
		 	  
			 if(strpos($name,'->')===false && strpos($name,'::')===false){
			   $n = explode('.', $name);
			   $method =  array_pop($n);
			   $name = implode('\\', $n);		 	
			   return array($name, $method);
			 }elseif( strpos($name,'->')!==false){
			 	 $n = explode('->', $name, 2);
				 $static = false;
			 }elseif(strpos($name,'::')!==false){
			 	 $n = explode('::', $name, 2);
				 $static = true;
			 }
               $n = explode('.', $n[0]);
			   $method =  $n[1];
			   $name = implode('\\', $n);			 
			   return ($static === false) ? array($name, $method) : $name.'::'.$method;
		      		    
		 }
	} 
	 
	public function addShortCut ($short,  $long){
          $this->shortcuts[$short] = $long;
		 return $this;
	} 
	 
    public function __call($name, $arguments)
    {
    	
		if(isset($this->wrap['f'][$name])){
    	try{
    	     call_user_func_array($this->wrap['f'][$name],$arguments);
			 return $this;
		}catch(Exeption $e){
		     trigger_error($e->getMesage().' '.__METHOD__.' '.__LINE__, $this->E_CALL);
		}
		}

   
    	try{
    	      call_user_func_array($this->_fnCallback($name),$arguments);
			  return $this;
		}catch(Exeption $e){
		     trigger_error($e->getMesage().' '.__METHOD__.' '.__LINE__, $this->E_CALL);
			 return $this;
		}		
		
		
		 trigger_error($name.' not defined in '.__METHOD__.' '.__LINE__, $this->E_CALL);
		 return $this;
    }	 
	
	
	
	
	
    public static function __callStatic($name, $arguments)
    {
    	if(isset(self::God(false)->wrap['f'][$name])){
    	try{
    	       call_user_func_array(self::God(false)->wrap['f'][$name],$arguments);
			   return self::God();
		}catch(Exeption $e){
		     trigger_error($e->getMesage().' '.__METHOD__.' '.__LINE__, self::God(false)->E_CALL);
		}
		}
		
	
	    try{
    	      call_user_func_array(self::God()->_fnCallback($name),$arguments);
			  return self::God();
		}catch(Exeption $e){
		     trigger_error($e->getMesage().' '.__METHOD__.' '.__LINE__, self::God(false)->E_CALL);
			  return self::God();
		}	
		
		
		 trigger_error($name.' not defined in '.__METHOD__.' '.__LINE__, $this->E_CALL);
		 return self::God();
    }	
	
	

   public function addStreamWrapper( $protocoll, $tld, $class, $overwrite = true  ) {
          if(!isset($this->wrappers[$protocoll]))$this->wrappers[$protocoll] = array();
          if(!isset($this->wrappers[$protocoll]['tld']))$this->wrappers[$protocoll]['tld'] = array();		  
          $this->wrappers[$protocoll]['tld'][$tld] = $class; 
		  $this->_stream_wrapper_register($protocoll, $overwrite);
          return $this;
    }	
   
   
   public function addClass($Instance, $Virtual, $autoload = TRUE, &$success = null  ) {
    	$success =  class_alias( $Instance, $Virtual, $autoload);
		$this->wrap['c'][$Virtual]= array( (is_object($Instance)) ? get_class($Instance) : $Instance, $success);
        return $this;
    }
   
	public function addFunc($name, \Closure $func){
		$this->wrap['f'][$name] = $func; 
		return $this; 	
	}
	
	
	public function init_aliasing(){
		foreach($this->wrap['c'] as $v => $o){
			if($o[1]!==true)$this->addClass($o[0], $v,true);
		}		 
		return $this; 	
	}

   
   protected function _stream_wrapper_register($protocoll, $overwrite = true, &$success = null){
 		         if (in_array($protocoll, stream_get_wrappers())) {
		         	        if(true !== $overwrite)return false;
		                    stream_wrapper_unregister($protocoll);	
				 }
		        $success = stream_wrapper_register($protocoll, get_class($this));	 
		return $this; 	
   }

    public function init_stream_wrappers($overwrite = true){
 		 foreach($this->wrappers as $protocoll => $wrapper){
		       $this->_stream_wrapper_register($protocoll, $overwrite); 	
	     }
		return $this;
    }

	public function init(){
		$this
		 ->init_autoloader()
 		 ->init_aliasing()
         ->init_stream_wrappers(true)
		;
		return $this;
    }
	
	
	
	/**
	 * Streaming Methods
	 */	
   public function stream_open($url, $mode, $options = STREAM_REPORT_ERRORS, &$opened_path = null){
    	$u = parse_url($url);
	    $c = explode('.',$u['host']);
		$c = array_reverse($c);
		
		$this->Controller = null;
		
		if(isset($this->wrappers[$u['scheme']]['tld'][$c[0]])){
			try{
			  $this->Controller = new $this->wrappers[$u['scheme']]['tld'][$c[0]];
			}catch(Exception $e){
				trigger_error($e->getMessage(), E_USER_NOTICE);
				return false;
			}
		}else{
				trigger_error('Stream handler for '.$url.' not found.', E_USER_NOTICE);
				return false;	
		}
				
    	return  call_user_func(array($this->Controller, __FUNCTION__),$url, $mode, $options, $opened_path );
    }
    public function dir_closedir(){return  call_user_func(array($this->Controller, __FUNCTION__) );}
    public function dir_opendir($path , $options){return  call_user_func(array($this->Controller, __FUNCTION__), $path , $options );}
    public function dir_readdir(){return  call_user_func(array($this->Controller, __FUNCTION__) );}
    public function dir_rewinddir(){return  call_user_func(array($this->Controller, __FUNCTION__) );}
    public function mkdir($path , $mode , $options){return  call_user_func(array($this->Controller, __FUNCTION__), $path , $mode , $options );}
    public function rename($path_from , $path_to){return  call_user_func(array($this->Controller, __FUNCTION__), $path_from , $path_to );}
    public function rmdir($path , $options){return  call_user_func(array($this->Controller, __FUNCTION__), $path , $options );}
    public function stream_cast($cast_as){return  call_user_func(array($this->Controller, __FUNCTION__), $cast_as );}
    public function stream_close(){return  call_user_func(array($this->Controller, __FUNCTION__) );}
    function stream_eof(){return  call_user_func(array($this->Controller, __FUNCTION__) );}
    public function stream_flush(){return  call_user_func(array($this->Controller, __FUNCTION__) );}
    public function stream_lock($operation){return  call_user_func(array($this->Controller, __FUNCTION__), $operation );}
    public function stream_set_option($option , $arg1 , $arg2){return  call_user_func(array($this->Controller, __FUNCTION__), $option , $arg1 , $arg2 );}
    public function stream_stat(){return  call_user_func(array($this->Controller, __FUNCTION__) );}
    public function unlink($path){return  call_user_func(array($this->Controller, __FUNCTION__), $path );}
    public function url_stat($path , $flags){return  call_user_func(array($this->Controller, __FUNCTION__), $path , $flags );}
    function stream_read($count){return  call_user_func(array($this->Controller, __FUNCTION__), $count );}
    function stream_write($data){return  call_user_func(array($this->Controller, __FUNCTION__), $data) ;}
    function stream_tell(){return  call_user_func(array($this->Controller, __FUNCTION__) );}
    function stream_seek($offset, $whence){return  call_user_func(array($this->Controller, __FUNCTION__), $offset, $whence );}
    function stream_metadata($path, $option, $var){return  call_user_func(array($this->Controller, __FUNCTION__), $path, $option, $var);}
     
	
}
