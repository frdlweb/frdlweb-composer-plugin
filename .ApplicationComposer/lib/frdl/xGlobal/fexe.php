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
 * @includes
 * @component
 * bin
 * original class bserialize:
 * Copyright (c) 2009, PHPServer
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Cesar Rodas nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY CESAR RODAS ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL CESAR RODAS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
 namespace frdl\xGlobal; 
 
 abstract class fexe extends \frdl\A 
{
	const VERSION='6.0.0.0';const DEL='µ';const STR_LEN="[Encoding error] String has an invalid length flag";const ARR_LEN="[Encoding error] Array has an invalid length flag";const OBJ_LEN="[Encoding error] Object has an invalid length flag";const UNKNOWN_TYPE="Don't know how to serialize/unserialize %s";const V_NULL=0x00;const V_ZERO=0x01;const V_1INT_POS=0x10;const V_1INT_NEG=0x11;const V_2INT_POS=0x12;const V_2INT_NEG=0x13;const V_4INT_POS=0x14;const V_4INT_NEG=0x15;const V_FLOAT_POS=0x20;const V_FLOAT_NEG=0x21;const V_BOOL_TRUE=0x30;const V_BOOL_FALSE=0x31;const V_ARRAY=0x40;const V_OBJECT=0x50;const V_STRING=0x60;
    const URI_DIR_API = 'api';
    


    
	protected $IO = null;
	
	/**
	* HTML  
	*/
	protected $meta;
	protected $tpl;
	protected $css;
	protected $js;
	protected $template;
	
	protected $app;
	protected $data = null;
	protected $config;
	protected $lang;
	
	/**
	*   $SEMR ^= Server-Endpoint-Modul-Router 
	*/
	protected $_s = null; 
	protected $_SEMR  = null; 
	protected $format = null; 
	protected $mime = null;
	
	protected $file; 
	protected $file_offset;
	protected $files = null;	
		
	protected $Request;
	
	protected $func_readFiles;
	
	/**
	 * Stream Properties
	 */
	protected $host; 
	public $context = array();
    protected $raw = null;
	protected $chunk;
	public $buflen;
	protected $pos = 0;
	protected $read = 0; 
	protected $eof = false;
	protected $mode;
	
	protected $e_level = E_USER_ERROR;
	
	function __construct($file = null, $file_offset = null, $e_level = E_USER_ERROR){
	  $this->_s = array('route' => self::SERVER_DEFAULT, 'cmd' => 'SERVER', 'responseformat' => 'html', 'modul' => 'PAGE', 'responsebuffers' => array('buffered'), 
	     'u' => \webdof\wURI::getInstance()->getU()
	 ); 
	  $this->_SEMR = &$this->_s;
	  $this->format = &$this->_s['responseformat'];
		 $this->e_level = $e_level;
		 $this->compatibility();
		 $this->file = $file;
		 $this->file_offset = $file_offset;
		 $this->setFuncs();
		 $this->data(null);
		 $this->_boot();
	   return $this;
	}	
	
   	  /*
   	   .abstract
   	   */	
	abstract public function data();
	abstract protected function _boot();	
	abstract public function run(&$Request =null);	
	abstract protected function route();
	abstract protected function process_modul($modul);

    final protected function compatibility(){
    	$arguments = func_get_args();
    	if($this->e_level <= 0)return 0;
		/**
		* todo check properties if no conflict with $this->_s  ...
		*/
		
		//return 0;  //not checked/unknown
		//return -5; //unusable
		//return -3; //incompatible
		//return -1; //possible problem
		return 1; // ok
		return 2; //better
		return 9; //optimum environment, suggestions applyed
	}
	
   public function ob_compress() {
    if( empty($_SERVER['HTTP_ACCEPT_ENCODING']) ) { return false; }
    if (( ini_get('zlib.output_compression') === 'On'
    	|| ini_get('zlib.output_compression_level') > 0 )
    	|| ini_get('output_handler') === 'ob_gzhandler'
     )
     {
    	return false;
     }
     if ( extension_loaded( 'zlib' ) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) ) {
    	ob_start('ob_gzhandler');
      }

   }
	
	
	protected function prepare_api(){
	  $this->mime = \webfan\App::God()->{'µ.sem.get->mime'}(null, \webdof\wURI::getInstance()->getU()->file, true, 'jsonp');
	  $ob =  \webfan\App::God()-> {'µ.sem->getFomatterMethod'}($this->format);
	  if(is_string($ob)){
	  	 ob_start(array( \webfan\App::God(), $ob));
	  }
	}
		
		
    public static function __callStatic($name, $arguments)
    {
	    trigger_error('Not fully implemented yet: '.get_class(self).'::'.$name,  $this->e_level);	
	    return self;
    }	
    
	public function __call($name, $arguments){
		
	   if(  '_' === strtolower($name) ||  '$' === strtolower($name) || 'semr' === strtolower($name) || 's' === strtolower($name)){
			if(count($arguments) === 0){
				return $this->_s;
			}
			
		 $SEMR = &$this->{'_s'};
		 $THIS = &$this;
		 $f = array(
		 'add' => (function() use($SEMR, $THIS) {
		 	
		   }), 
		 'remove' => (function() use($SEMR, $THIS) {
		 	
		   }), 
		 'parse' => (function($route = null) use($SEMR, $THIS) {
		 	
		   }), 
		 'unparse' => (function() use($SEMR, $THIS) {
		 	
		 })
		 );	
			

		 	if(1 === count($arguments) && isset($this->{'_s'}[$arguments[0]])){
				return $this->{$arguments[0]};
			}elseif(2 === count($arguments) && isset($this->{'_s'}[$arguments[0]]) && !isset($f[$arguments[0]]) ){
				$field = $arguments[0];
				$value = $arguments[1];
				$this->{$field} = $value;
	            return $this;
			}elseif(isset($f[$arguments[0]])){
				$function = array_shift($arguments);
				return call_user_func_array($f[$function], $arguments);
			}else{
		         trigger_error('Not fully implemented yet (the $ _ _s SEMR handler): '.get_class($this).'->'.$name.'_('.strip_tags(var_export($arguments, true)).')',  $this->e_level);	
	             return $this;			
			}
		 	

			
	      return $this;
		}
		
		
		
		
	    trigger_error('Not fully implemented yet: '.get_class($this).'->'.$name,  $this->e_level);	
	    return $this;
	}
		
    public function __set($name, $value)
    {
      if (isset($this->{'_s'}[$name])){
      	if(is_array($this->{'_s'}[$name]) && is_array($value)){
		 	$value = array_unique(array_merge($this->_s[$name], $value));
		 }elseif(is_array($this->{'_s'}[$name]) && is_string($value)){
		 	$value = array_unique(array_merge($this->_s[$name], explode(',', $value)));
		 }
          $this->_s[$name] = $value;
         return $this; 
      }
      
         $proxy =  $this->{$name} = $value;
         
         if('route' === $name){
               \webfan\App::God()  
                              -> {'µ.sem.unparse'}($this->{'_s'},$value );
		 }else{
		      $this->_s['route'] =  \webfan\App::God()  
                              -> {'µ.sem.parse'}($this->{'_s'});
		 }
         
         return $this; 
    }	
 		
    public function &__get($name)
    {
      $retval = null;	
      if (isset($this->_s[$name])){
        $retval = $this->_s[$name];
      }elseif('_' !== substr($name,0,1)){
	  	  $retval = $this->{$name};
	  }else{
	  	     trigger_error('Not fully implemented yet or unaccesable property: '.get_class($this).'->'.$name,  $this->e_level);	
	  }
        return $retval;
    }	   

	
    public function __invoke()
    {
       return call_user_func_array(array($this, 'run'), func_get_args());
    }
    	
	public function appstream(){
		return 'webfan://'.str_replace('\\', '.', get_class($this)) .'.fexe:'.$this->file_offset.'/'.$this->file;
	}
	
   
   public function HTML_wrap_head_options($opts){
   	$head = '';
		    foreach($opts['meta'] as $pos => $meta){
		            if(is_array($meta) && count($meta) === 2){
		            	if(isset($meta['name'])){
						  $head.='<meta name="'.$meta['name'].'" content="'.$meta['content'].'" />'.PHP_EOL;
						}elseif(isset($meta['http-equiv'])){
						  $head.='<meta http-equiv="'.$meta['http-equiv'].'" content="'.$meta['content'].'" />'.PHP_EOL;
						}
		               	
		            }	
		    }
		 
		    foreach($opts['css'] as $pos => $css) {
		    	$ccheck = parse_url($css);
		    	  if($ccheck === false /*||  !isset($ccheck['host']) */){
		    	  	$head.='<style type="text/css">'.preg_replace("/\s+/", '', $css).'</style>'.PHP_EOL;
		    	  }else{
                    $head.='<link rel="stylesheet" type="text/css" href="'.$css.'" />'.PHP_EOL;		    	  	
		    	  }
		    }
			
		    foreach($opts['js'] as $pos => $js) {
		    		$ccheck = parse_url($js);
		    	    if($ccheck === false /* || !isset($ccheck['host']) */){
		    	  	$head.='<script type="text/javascript">'.preg_replace("/\s+/", '', $js).'</script>'.PHP_EOL;
		    	  }else{
                     $head.='<script type="text/javascript" src="'.$js.'"></script>'.PHP_EOL;		    	  	
		    	  }
		    }  
		 
		 foreach($opts['link'] as $pos => $l) {
		    		$head.='<link rel="'.$l['rel'].'" type="'.$l['type'].'" href="'.$l['href'].'">'.PHP_EOL;
		    }  
		 	 
		    
		return $head;     	
   }
   
   
   public function parse_template($template, $data){
   	  /*
   	   ToDo...
   	   */
   	   foreach($data as $placeholder => $replacer){
   	   	  $template = (is_callable($replacer)) 
   	   	       ? preg_replace_callback('/\{\$\_\_\_('.preg_quote($placeholder).')\_\_\_\}/',(function ($ph) use ($replacer) {
   	   	           	return (is_array($replacer)) 
   	   	           	   ? call_user_func($replacer,$ph)
   	   	           	   : $replacer($ph);
   	   	       	}), $template )
               : str_replace('{$___'.$placeholder.'___}',$replacer,$template);
	   }
   	   
   	  return $template;
   }
   
   
   public function HTML_wrap_head(Array $opts = array('Title' => 'Document.Title',
	            'css' => array(), 'js' => array(), 'meta' =>  array())){
	            	
    	 if(!$opts['Title'] || !is_string($opts['Title']))$opts['Title']='webfan:// '.$_SERVER['REQUEST_URI'];  //'webfan://'.$className.'.code'
       	 $head = '';
		 $head.='<!DOCTYPE html>'.PHP_EOL;
		 $head.='<html>'.PHP_EOL;
		 $head.='<head>'.PHP_EOL;
		 $head.='<title data-l10n-id="app_title">'.$opts['Title'].'</title>'.PHP_EOL;
	
			$head .= $this->HTML_wrap_head_options($opts);
			
		 $head.='</head>'.PHP_EOL;		 
		 $head.='<body>'.PHP_EOL;	
		 return $head;   	
    }
   
   
   public function HTML_wrap_foot(){
         $foot = ''.PHP_EOL;
		 $foot.='</body>'.PHP_EOL;
		 $foot.='</html>';	  	
	  return $foot;
   }
   
   public function out(){
		 $html = '';
		 $html.= $this->HTML_wrap_head($this->data['template_main_options']);
		 $html.= $this->parse_template($this->template, $this->data['tpl_data']);
		 $html.= $this->HTML_wrap_foot();
		echo trim($html);
	   return $this;
   }	 
   
   	
	
	
	/**
	* webfan://namespace.vendor.applicationname.fexe:__COMPILER_HALT_OFFSET__/__FILE__
	* @param undefined $url
	* @param undefined $mode
	* @param undefined $options
	* @param undefined $opened_path
	* 
	* @return
	*/
    public function stream_open($url, $mode, $options = STREAM_REPORT_ERRORS, &$opened_path = null){
    	$u = parse_url($url);
    	$this->file = str_replace('/', DIRECTORY_SEPARATOR, $u['path']);
    	$this->file_offset = $u['port'];
    	$this->host = $u['host'];
    	$this->mode = $mode;
    	
        $this->IO = fopen($this->file, $this->mode);
          if(!$this->IO)return false;	
  		
  		fseek($this->IO, $this->file_offset);
  		
  		 
		 $this->pos = $this->stream_tell();      
		 $this->eof = $this->stream_eof();  
          			  
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
       fclose($this->IO);
	}
    public function stream_eof(){
    	return feof($this->IO);
	}
    public function stream_flush(){
		return fflush($this->IO);
	}
    public function stream_lock($operation){return flock($this->IO, $operation);}
    public function stream_set_option($option , $arg1 , $arg2){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function stream_stat(){
		 return array(  
		          'mode' => $this->mode,
		          'size' => filesize($this->file),
		 );
	}
    public function unlink($path){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function url_stat($path , $flags){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
    public function stream_read($count){
        return fread($this->IO, $count);
	}
    public function stream_write($data){
    	return fwrite($this->IO, $data);
    }
    public function stream_tell(){
     	return ftell($this->IO);
    }
    public function stream_seek($offset, $whence){
 		return fseek($this->IO);
	}
    public function stream_metadata($path, $option, $var){trigger_error('Not implemented yet: '.get_class($this).' '.__METHOD__, E_USER_ERROR);}
	







	
	protected function Files($refresh = false){
		    if(false === $refresh && is_array($this->files))return $this->files;
		    $out = array();
    	    if(!is_string($this->raw))$this->getFileData();
        	$this->read($this->raw, self::DEL,  (function($token) use (&$out) {
				   if(!$out || !is_array($out))$out = array();

               	    $h = explode("\n", $token, 2);
             	    $t = explode('%', $h[0], 3);
             	    
               	    $file = array();
             	    $file['pos'] = count($out);
             	    $file['size'] = strlen($token) * 8;
             	 
             	    $file['type'] = (isset($t[0])) ? trim($t[0]) : null;
             	    $file['enc'] = (isset($t[1])) ? trim($t[1]) : null;
             	    $file['name'] = (isset($t[2]))  ? trim($t[2]) : null;
             	    
             	  $enc = explode('+', $file['enc']);
             	   
             	  if(isset($enc[1]) && 'b64' === strtolower($enc[1])){
				  	 $h[1] = base64_decode($h[1]);
				  } 
             	   
             	  if('RSA' === strtoupper($enc[0])){
				  	$file['content'] = $this->unwrapData($h[1]);
				  }
             	  elseif('BIN' === strtoupper($enc[0])){
				  	$file['content'] = $this->unserialize($h[1]);
				  }	
             	  elseif('b64' === strtolower($enc[0])){
				  	$file['content'] = base64_decode($h[1]);
				  }	
             	  elseif('json' === strtolower($enc[0])){
				  	$file['content'] =  json_decode(trim($h[1]));
				  }				  
				  else{
				   	$file['content'] = $h[1];
				  }
             	    $k = ((isset($file['name'])) ? $file['name'] : $file['pos']);
           	        $out[$k] = $file;
       	    }), $out);
        	$this->files = $out;
        	return $this->files;
    }
	
	protected function readFile($file){
		try{
			if(!is_array($this->files))$this->Files();
		
		}catch(\Exception $e){
			trigger_error($e->getMessage(), E_USER_WARNING);
		}
       return (isset($this->files[$file])) ? $this->files[$file]['content'] : false;		
	}
	
	protected function FileInfo($file){
		try{
				if(!is_array($this->files))$this->Files();
		
		}catch(\Exception $e){
			trigger_error($e->getMessage(), E_USER_WARNING);
		}
  		return (isset($this->files[$file])) ? $this->files[$file] : false;
	}
		
	protected function setFuncs(){
       $this->apply_fm_flow();   
        	
       return $this;
	}
	
	
	

	
	final protected function default_route(\webdof\wURI $u = null){
       $u = (null === $u) ? \webdof\wURI::getInstance() : $u;
       $inc_files =  get_included_files();
        array_walk($inc_files, (function(&$file, $num) {
	     	           $file = basename($file);
	     	      }), $this);
         
         $_file = ((isset($this->data['config']['DIR_PACKAGE']))?$this->data['config']['DIR_PACKAGE']:getcwd().DIRECTORY_SEPARATOR).$u->getU()->file;
 
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
	   	   $this->route = self::SERVER_API;
	   }elseif(
	       '/' === $u->getU()->req_uri 
	       || basename(__FILE__) ===  $u->getU()->file
	       || 'install.phar' === $u->getU()->file
	       || 'install.php' === $u->getU()->file
	       || substr($u->getU()->location,0,strlen($this->data['config']['URL']))  === $this->data['config']['URL']
	   ){
	       $this->route = self::SERVER_PAGE;
	   } elseif (file_exists($_file) && is_file($_file)){
	   	    $this->route = self::SERVER_HTML;
	   }
	    else{
	   	   $this->route = self::SERVER_404;
	   }	
        
              
	   return $this;
	}		
		

	   
	   
	   
	   
	final protected function default_run(&$Request =null){
    	$this->Request = (null !== $Request) ? $Request : $this->initRequest();
    	if(!is_string($this->raw))$this->getFileData();
        if(!is_array($this->files))$this->Files();
    	$this->route();		
       return $this;
	}		
			
	public function initRequest(){
       $this->Request = new \frdl\common\Request();	
       return $this;
	}		
			
	final protected function default_boot(){
		\webfan\App::God()->addStreamWrapper( 'webfan', 'fexe', $this,  true  ) ;
       return $this;
	}
	
   /**
   * e.g.: 	  
   *         $data = $this->getFileData();
   *          $this->read($data, '#', (function($token) use (&$out) {
   *           	$out.= trim($token).'<br />';
   *     	}));  
   *    	echo $out;
   * @param undefined $data
   * @param undefined $delimiters
   * @param undefined $func
   * 
   * @return
   */		
	public function read(&$data, $delimiters = '#', \closure $func, &$out = null){
		$ti = new \frdl\common\TokenIterator($data, $delimiters);
		foreach ($ti as $count => $token) {
            $func($token);
        }
        return $this;
	}
   
   
   public function wrapData($data, $subject)
     {
     	$begin = "-----BEGIN $subject-----\r\n";
     	$end = "-----END $subject-----";
        return $begin . chunk_split(base64_encode($data)) . $end;
     }
   public function unwrapRSAData($str)
     {
       $data = preg_replace('#^(?:[^-].+[\r\n]+)+|-.+-|[\r\n]#', '', $str);
       return preg_match('#^[a-zA-Z\d/+]*={0,2}$#', $data) ? base64_decode($data) : false;
     }
   public function unwrapData($str)
     {
       $data = preg_replace('#^(?:[^-].+[\r\n]+)+|-.+-|[\r\n]#', '', $str);
       return preg_match('#^[a-zA-Z\d/+]*={0,2}$#', $data) ? base64_decode($data) : false;
     }
          
     
     
    /**
	* read file from offset
	* 
	* @param undefined $file     __FILE__
	* @param undefined $offset   __COMPILER_HALT_OFFSET__
	* 
	* @return string
	*/ 
    public function getFileData($file = null, $offset = null){
    	if(null === $file)$file = &$this->file;
    	if(null === $offset)$offset = $this->file_offset;
		$this->IO = fopen($file, 'r');
        fseek($this->IO, $offset);
        try{
			$this->raw =  stream_get_contents($this->IO);
		}catch(\Exception $e){
			$this->raw = '';
			trigger_error($e->getMessage(),  $this->e_level);
		}
        
        return $this->raw;
	}
	
	final public function __destruct(){
			
		try{
			 if(is_resource($this->IO))fclose($this->IO);
			 /*
			 $fp = fopen($this->file, 'w+');
			 flock($fp,LOCK_UN);
			 fclose($fp);
			 */
		}catch(\Exception $e){
			trigger_error($e->getMessage(). ' in '.__METHOD__,  $this->e_level);
		}
	}
	

	
	
	
/**
 * @component
 * bin
 * original class bserialize:
 * Copyright (c) 2009, PHPServer
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Cesar Rodas nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY CESAR RODAS ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL CESAR RODAS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */	
   final public function unserialize(&$var){
   	try{
			$i=0;return $this->_unserialize($var,false,$i);
	}catch(Exception $e) {
       	trigger_error($e->getMessage(). ' in '.__CLASS__.'::'.__METHOD__.' line '.__LINE__,E_USER_WARNING);
								  return;
	}
   
   	}		
   protected function _unserialize(&$var,$just_first=false,&$start) {
       try{ $len = strlen($var); }catch(Exception $e) {
       	trigger_error($e->getMessage(). ' in '.__CLASS__.'::'.__METHOD__.' line '.__LINE__,E_USER_WARNING);
								  return;
								  }
        $out = null;  for($i = &$start; $i < $len; $i++) {
            $type = ord($var[$i++]);
            switch ($type) {
                case self::V_ZERO:
                    $out = 0;
                    break;
                case self::V_1INT_POS:
                case self::V_1INT_NEG:
                    $out = ord($var[$i]);
                    if ($type==self::V_1INT_NEG) $out *= -1;
                    $i++;
                    break;
                case self::V_2INT_POS:
                case self::V_2INT_NEG:
                    $out = $this->__toint(substr($var,$i,2),2);
                    if ($type == self::V_2INT_NEG) $out *= -1;
                    $i += 2;
                    break;
                case self::V_4INT_POS:
                case self::V_4INT_NEG:
                    $out = $this->__toint(substr($var,$i,4),4);
                    if ($type == self::V_4INT_NEG) $out *= -1;
                    $i += 4;
                    break;
                case self::V_FLOAT_POS:
                case self::V_FLOAT_NEG:
                    $out = $this->__tofloat(substr($var,$i,6));
                    if ($type == self::V_FLOAT_NEG) $out *= -1;
                    $i += 6;
                    break;
                case self::V_BOOL_TRUE:
                    $out = true;
                    break;
                case self::V_BOOL_FALSE:
                    $out = false;
                    break;
                case self::V_STRING:
                    $xlen = $this->_unserialize($var,true,$i);
                    if (!is_numeric($xlen)) {
                        trigger_error(self::STR_LEN . ' '.__CLASS__.'::'.__METHOD__.' line '.__LINE__);
                        return;
                    }
                    $out = substr($var,$i,$xlen);
                    $i += $xlen;
                    break;
                case self::V_ARRAY:
                    $xlen = $this->_unserialize($var,true,$i);
                    if (!is_numeric($xlen)) {
                        trigger_error(self::ARR_LEN. ' '.__CLASS__.'::'.__METHOD__.' line '.__LINE__);
                        return;
                    }
                    $out = array();
                    $tmp = substr($var,$i,$xlen);
                    $itmp = 0;
                    while ($itmp < $xlen) {
                        $key    = $this->_unserialize($tmp,true,$itmp);
                        $value  = $this->_unserialize($tmp,true,$itmp);
                        $out[$key] = $value;
                    }
                    $i += $xlen;
                    break;
                case self::V_OBJECT:
                    $class_name = $this->_unserialize($var,true,$i);
                    $xlen = $this->_unserialize($var,true,$i);
                    if (!is_numeric($xlen)) {
                        trigger_error(self::OBJ_LEN. ' '.__CLASS__.'::'.__METHOD__.' line '.__LINE__);
                        return;
                    }
                    
                    $class_name = class_exists($class_name) ? $class_name : stdClass;
                    $out = new $class_name;
                   
                    $tmp = substr($var,$i,$xlen);
                    $itmp = 0;
                    while ($itmp < $xlen) {
                        $key    = $this->_unserialize($tmp,true,$itmp);
                        $value  = $this->_unserialize($tmp,true,$itmp);
                        $out->$key = $value;
                    }
                    $i += $xlen;
                    break;
					
				case self::V_NULL:	
                default:
                    trigger_error(self::UNKNOWN_TYPE. ' '.__CLASS__.'::'.__METHOD__.' line '.__LINE__,E_USER_WARNING);
                   $out = null;
            }
            if (!is_null($out)) {
                break;
            }
        }
        return $out;
    }
   final public function serialize($var) {
   	
   	
   	try{
	
	     $str = "";
        if (is_integer($var) && $var==0) {
            return chr(self::V_ZERO);
        }
        switch( ($type=gettype($var)) ) {
            case "string":
                $str .= chr(self::V_STRING);
                $str .= $this->serialize((int)strlen($var));
                $str .= $var;
                break;
            case "float":
            case "double":
                $str .= chr($var > 0 ? self::V_FLOAT_POS : self::V_FLOAT_NEG);
                $str .= $this->__fromfloat($var);
                break;
            case "integer":
            case "numeric":
                $t = abs($var);
                if ($t < 255) {
                    $str .= chr($var > 0 ? self::V_1INT_POS : self::V_1INT_NEG);
                    $str .= chr($t);
                } else if ($t < 65536) {
                    $str .= chr($var > 0 ? self::V_2INT_POS : self::V_2INT_NEG);
                    $str .= $this->__fromint($var,2);
                } else {
                    $str .= chr($var > 0 ? self::V_4INT_POS : self::V_4INT_NEG);
                    $str .= $this->__fromint($var);
                }
                break;
            case "boolean":
                $str .= chr($var ? self::V_BOOL_TRUE : self::V_BOOL_FALSE);
                break;
            case "array":
                $str .= chr(self::V_ARRAY);
                $tmp = "";
                foreach($var as $key => $value) {
                    $tmp .= $this->serialize($key);
                    $tmp .= $this->serialize($value);
                }
                $str .= $this->serialize(strlen($tmp));
                $str .= $tmp;
                break;
            case "object":
                $str .= chr(self::V_OBJECT);
                $str .= $this->serialize(get_class($var));
                $tmp = "";
                foreach(get_object_vars($var) as $key => $value) {
                    $tmp .= $this->serialize($key);
                    $tmp .= $this->serialize($value);
                }
                $str .= $this->serialize(strlen($tmp));
                $str .= $tmp;
                break;
			case "null" :
            default:
				$str .= chr(self::V_NULL);  
                  trigger_error(self::UNKNOWN_TYPE. ' '.__CLASS__.'::'.__METHOD__.' line '.__LINE__,E_USER_WARNING);
                break;
        }
        return $str;
	   }catch(Exception $e) {
       	trigger_error($e->getMessage(). ' in '.__CLASS__.'::'.__METHOD__.' line '.__LINE__,E_USER_WARNING);
								  return;
	   }
    }
    final protected function __toint($string,$blen=4) {
        $out  = 0;
        $n    = ($blen-1) * 8;
        for($bits=0; $bits < $blen; $bits++) {
            $out |= ord($string[$bits]) << $n;
            $n -= 8;
        }
        return $out;
    }
   final protected function __fromint($int,$blen=4) {
        $int = (int)($int < 0) ? (-1*$int) : $int;
        $bytes=str_repeat(" ",$blen);
        $n    = ($blen-1) * 8;
        for($bits=0; $bits < $blen; $bits++) {
            $bytes[$bits] = chr($int  >> $n);
            $int -= $bytes[$bits] << $n;
            $n -= 8;
        }
        return $bytes;
    }
  final protected function __fromfloat($float) {
        $str  = $this->__fromint($float);
        $str .= $this->__fromint( round(($float-(int)$float)*1000) , 2 );
        return $str;
    }
  final protected function __tofloat($string) {
        $float  = $this->__toint(substr($string,0,4));
        $float += $this->__toint(substr($string,4,2),2)/1000;
        return $float;
    }	
}
 
