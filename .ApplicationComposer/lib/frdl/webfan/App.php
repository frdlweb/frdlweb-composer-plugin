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

if(!class_exists('\frdl\A') && file_exists(__DIR__ . DIRECTORY_SEPARATOR . '..'.DIRECTORY_SEPARATOR .'A.php')){
	 require __DIR__ . DIRECTORY_SEPARATOR . '..'.DIRECTORY_SEPARATOR .'A.php';
}


class App extends \frdl\A
{
		
	const NS = __NAMESPACE__;
	const DS = DIRECTORY_SEPARATOR;
	
	const LOADER = 'webfan\Loader';
	
	protected static $instance = null;
	
	protected $app;
	
	protected $E_CALL = E_USER_ERROR;
	protected $wrap;
	/**
	* 
	* @public _ - current shortcut [mixed]
	* 
	*/
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
	

	
	protected $LoaderClass =null;
	
	protected $public_properties_read =  array('app', 'wrap', 'wrappers', 'shortcuts' ,'LoaderClass');
	
	

	
	protected function __construct($init = false, $LoaderClass = self::LOADER, $name = '', $branch = 'dev-master', 
	   $version = 'v1.0.2-beta.1', $meta = array())
	 {
	    $this->app = new \stdclass;	
            $this->app->name = $name;
	    $this->app->branch = $branch;
	    $this->app->version = $version;
	    $this->app->meta = $meta;
	    $this->wrap = array();
	    $this->shortcuts = array();
            $this->setAutoloader($LoaderClass);
	    if($init === true)$this->init();
	}
	
	
    public function &__get($name)
    {
    	
      $retval = null;	
      if (in_array($name, $this->public_properties_read )){
           $retval = $this->{$name};
           return $retval;
	  }
      
        trigger_error('Not fully implemented yet or unaccesable property: '.get_class($this).'->'.$name,  $this->E_CALL);	

        return $retval;
    }		 


    public static function God($init = false, $LoaderClass = self::LOADER, $name = '', $branch = 'dev-master', 
	   $version = 'v1.0.2-beta.1', $meta = array()){
        return self::getInstance( $init, $LoaderClass, $name, $branch ,   $version, $meta );
   }
	 

  	public function init(){
	 $this->addShortCut('$', array($this,'addShortCut'))
	   
	  ;		
	  
	$this->_ = (function(){
			     return call_user_func_array(array($this,'$'), func_get_args());
		   });
	
     $this->wrap = array( 
		         'c' => array(
				        self::LOADER=>  array($this->LoaderClass, null), 
         		        'webfan\App' =>  array(__CLASS__, null),
				 ),
		         'f' => array( ),
		);

      $this ->applyAliasMap(true)
            ->mapWrappers(null)
			->init_stream_wrappers(true) 
			->Autoloader(true) 
		       ->autoload_register() 
		       -> j()
	        ;
                /**
                 * ToDo: Load Application Config and Components...
                 * */
                 
                 
		return $this;
    }
	
	  
	public function setAlias($component, $alias, $default, $abstract_parent, $interfaces = array()){
		$this->wrap['aliasing']['schema'][$component] = array(
		   'alias' => $alias, 'default' => $default, 'abstract_parent' =>$abstract_parent, 
		   'interfaces' => $interfaces
		 );
		return $this;
	}
	
	//todo : compinent registry
	public function setAliasMap($aliasing = null){
		$this->wrap['aliasing'] = (is_array($aliasing)) ? $aliasing
		 : array( 
				      'schema' => array(
					      '1.3.6.1.4.1.37553.8.1.8.8.5.65.8.1.1' => array('name' => 'Autoloader', 'alias' => self::LOADER, 'default' => &$this->LoaderClass,
					                           'abstract_parent' => 'frdl\webfan\Autoloading\SourceLoader', 
					                           'interfaces' => array() ),
					      '1.3.6.1.4.1.37553.8.1.8.8.5.65.8.1.2' => array('name' => 'Application Main Controller', 'alias' => 'webfan\App','default' => 'frdl\webfan\App',
					                           'abstract_parent' => 'frdl\webfan\App', 
					                           'interfaces' => array() ),
					      '1.3.6.1.4.1.37553.8.1.8.8.5.65.8.1.3' => array('name' => 'cmd parser', 'alias' => 'webfan\Terminal','default' =>'frdl\aSQL\Engines\Terminal\Test',
					                           'abstract_parent' => 'frdl\aSQL\Engines\Terminal\CLI', 
					                           'interfaces' => array() ),
					      '1.3.6.1.4.1.37553.8.1.8.8.5.65.8.1.4' => array('name' => 'BootLoader', 'alias' => 'frdl\AC','default' => 'frdl\ApplicationComposer\ApplicationComposerBootstrap',
					                           'abstract_parent' => 'frdl\ApplicationComposer\ApplicationComposerBootstrap', 
					                           'interfaces' => array() ),
						  '1.3.6.1.4.1.37553.8.1.8.8.5.65.8.1.5' => array('name' => 'API REST CLient', 'alias' => 'frdl\Client\RESTapi', 'default' => 'webdof\Webfan\APIClient',
					                           'abstract' => null, 
					                           'interfaces' => array() ), 
					  ),
				 );
				 
		return $this;		 
	}


    public function mapWrappers($wrappers  = null){
    	$this->wrappers = (is_array($wrappers)) ? $wrappers
    	  : array(  
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
	      'till' => array(  
		  
		  ),		        
	  );
		
		return $this;		 
   }
	

   public function setAutoloader($LoaderClass = self::LOADER, &$success = false){
      $this->LoaderClass = $LoaderClass;
	  return $this;
   }



    public function init_stream_wrappers($overwrite = true){
 		 foreach($this->wrappers as $protocoll => $wrapper){
		       $this->_stream_wrapper_register($protocoll, $overwrite); 	
	     }
		return $this;
    }
	
		
	public function mapAliasing($apply = false){
		foreach($this->wrap['aliasing']['schema'] as $OID => $map){
			$this->wrap['c'][$map['alias']] = array($map['default'],null, $OID);
			if(true===$apply){
				$this->addClass($map['default'], $map['alias'],TRUE, $success );
			}
		}
		return $this; 	
	}
	
	
   public function Autoloader($expose = false){
     $component = '1.3.6.1.4.1.37553.8.1.8.8.5.65.8.1.1';
	 
	 if(null===$this->LoaderClass){
	  foreach($this->wrap['c'] as $alias => $info){
	 	if($component !== $info[2] || true !== $info[1] )continue;
             $this->LoaderClass = $info[0];
		 break;
	  }
	 }
	$Loader = (class_exists('\\'.$this->LoaderClass) ) ? call_user_func('\\'.$this->LoaderClass.'::top') 
		          : call_user_func('\\'.$this->wrap['aliasing']['schema'][$component]['default'].'::top') ;
				 
	 return (true === $expose) ? $Loader : $this;
   }
	
		
	public function applyAliasMap($retry = false){
    	foreach($this->wrap['c'] as $v => $o){
			if(null === $o[1] || (true === $retry && false === $o[1]))$this->addClass($o[0], $v,true, $success);
		}		 
		return $this; 	
	}

	
	 		
	public function __toString(){
		return (string)$this->app->name;
	}		
	
	
	
   public static function getInstance($init = false, $LoaderClass = self::LOADER, $name = '', $branch = 'dev-master', 
	   $version = 'v1.0.2-beta.1', $meta = array())
     {
       if (NULL === self::$instance) {
           self::$instance = new self($init, $LoaderClass, $name, $branch, $version , $meta);
       }
       return self::$instance;
     }
	 	
		
		
   protected function _fnCallback($name){
		// A
		  if(isset($this->shortcuts[$name])){
		  	   if(is_callable($this->shortcuts[$name]))return $this->shortcuts[$name];
		  } 
			  
			  
			  
		 //B 	  
		  	
		 $name = str_replace('\\','.',$name);

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
             
			   $method =  array_pop($n);
			   $n = explode('.', $n[0]);
			   $name = implode('\\', $n);			 
			   return ($static === false) ? array($name, $method) : $name.'::'.$method;
		      		    
		 }
	} 
	 
    public function __call($name, $arguments)
    {
    	
		if(isset($this->wrap['f'][$name])){
    	try{
    	     return call_user_func_array($this->wrap['f'][$name],$arguments);
		}catch(Exeption $e){
		     trigger_error($e->getMesage().' '.__METHOD__.' '.__LINE__, $this->E_CALL);
		}
		}

   
    	try{
    		  $c = $this->_fnCallback($name);
    	      if(is_callable($c))call_user_func_array($c,$arguments);
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
    	       return call_user_func_array(self::God(false)->wrap['f'][$name],$arguments);
		}catch(Exeption $e){
		     trigger_error($e->getMesage().' '.__METHOD__.' '.__LINE__, self::God(false)->E_CALL);
		}
		}
		
	
	    try{
	    	  $c = self::God()->_fnCallback($name);
    	      if(is_callable($c))call_user_func_array($c,$arguments);
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
    	$success =  ($Instance !== $Virtual) ? class_alias( $Instance, $Virtual, $autoload) : true;
		$this->wrap['c'][$Virtual]= array( (is_object($Instance)) ? get_class($Instance) : $Instance, $success);
        return $this;
    }
   
	public function addFunc($name, \Closure $func){
		$this->wrap['f'][$name] = $func; 
		return $this; 	
	}
	
   
   protected function _stream_wrapper_register($protocoll, $overwrite = true, &$success = null){
   		         if (in_array($protocoll, stream_get_wrappers())) {
		         	        if(true !== $overwrite){
                                $success = false;
								return $this;
						    }		         	        		
		         	        stream_wrapper_unregister($protocoll);	
				 }
		        $success = stream_wrapper_register($protocoll, get_class($this));	 
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
		$cN = (isset(self::God()->wrappers[$u['scheme']]['tld'][$c[0]]))
		          ?self::God()->wrappers[$u['scheme']]['tld'][$c[0]]
				  :false;
		
		if(false!==$cN){
			try{
			  $this->Controller = new $cN;
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
