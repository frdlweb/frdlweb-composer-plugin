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
 
 abstract class fexe
{
	const VERSION='6.0.0.0';const DEL='#';const STR_LEN="[Encoding error] String has an invalid length flag";const ARR_LEN="[Encoding error] Array has an invalid length flag";const OBJ_LEN="[Encoding error] Object has an invalid length flag";const UNKNOWN_TYPE="Don't know how to serialize/unserialize %s";const V_NULL=0x00;const V_ZERO=0x01;const V_1INT_POS=0x10;const V_1INT_NEG=0x11;const V_2INT_POS=0x12;const V_2INT_NEG=0x13;const V_4INT_POS=0x14;const V_4INT_NEG=0x15;const V_FLOAT_POS=0x20;const V_FLOAT_NEG=0x21;const V_BOOL_TRUE=0x30;const V_BOOL_FALSE=0x31;const V_ARRAY=0x40;const V_OBJECT=0x50;const V_STRING=0x60;
	
	
	protected $IO = null;
	
	/**
	* HTML  
	*/
	protected $meta;
	protected $tpl;
	protected $css;
	protected $js;
	
	protected $app;
	protected $data;
	protected $config;
	protected $lang;
	
	protected $file; 
	protected $file_offset;
	protected $files;	
		
	protected $Request;
	
	protected $func_readFiles;
	
	/**
	 * Stream Properties
	 */
	protected $host; 
	public $context = array();
    protected $raw;
	protected $chunk;
	public $buflen;
	protected $pos = 0;
	protected $read = 0; 
	protected $eof = false;
	protected $mode;
	
	
	function __construct($file = null, $file_offset = null ){
		 $this->file = $file;
		 $this->file_offset = $file_offset;
		 $this->setFuncs();
		 $this->data(null);
		 $this->_boot();
	}	
	
	
	abstract public function data();
	abstract protected function _boot();	
	abstract public function run(&$Request =null);	
	abstract protected function route();
	
	
	public function appstream(){
		return 'webfan://'.str_replace('\\', '.', get_class($this)) .'.fexe:'.$this->file_offset.'/'.$this->file;
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
	
	
	
	protected function Files(&$out){
		    $this->files = &$out;
			$this->func_readFiles = (function($token) use (&$out) {
			      if(!$out || !is_array($out))$out = array();
             	//  if(substr($token,0,1) !== self::DEL)return;
             	  $h = explode("\n", $token, 2);
             	  $t = explode('%', $h[0], 3);
               	  $file = array();
             	  $file['pos'] = count($out);
             	  $file['size'] = strlen($token) * 8;
             	 
             	  $file['type'] = (isset($t[0])) ? trim($t[0]) : null;
             	  $file['enc'] = (isset($t[1])) ? trim($t[1]) : null;
             	  $file['name'] = (isset($t[2]))  ? trim($t[2]) : null;
             	  if('RSA' === strtoupper($file['enc'])){
				  	$file['content'] = $this->unwrapData($h[1]);
				  }
             	  elseif('BIN' === strtoupper($file['enc'])){
				  	$file['content'] = $this->unserialize($h[1]);
				  }	
             	  elseif('b64' === strtolower($file['enc'])){
				  	$file['content'] = base64_decode($h[1]);
				  }				  
				  else{
				   	$file['content'] = $h[1];
				  }
             	  $k = ((isset($file['name'])) ? $file['name'] : $file['pos']);
           	      $out[$k] = $file;
       	    });	
		 $this->read($this->raw, self::DEL,  $this->func_readFiles, $out);
	}
	
	protected function readFile($file){
		return (isset($this->files[$file])) ? $this->files[$file]['content'] : false;
	}
	
	protected function FileInfo($file){
		return (isset($this->files[$file])) ? $this->files[$file] : false;
	}
		
	protected function setFuncs(){

        	
        	
        $this->func_readSections_Test = (function($token) use (&$out) {
             	$out.= trim($token).'<br />';
        	});	
        	
	}
	
	protected function default_run(&$Request =null){
    	$this->Request = (null !== $Request) ? $Request : $this->initRequest();
    	$this->route();		
	}		
			
	public function initRequest(){
       $this->Request = new \frdl\common\Request();	
	}		
			
	protected function default_boot(){
		\frdl\webfan\App::God()->addStreamWrapper( 'webfan', 'fexe', $this,  true  ) ;
	}
	
   /**
   * e.g.: 	  
            $data = $this->getFileData();
            $this->read($data, '#', (function($token) use (&$out) {
             	$out.= trim($token).'<br />';
        	}));  
        	echo $out;
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
	
	public function __call($name, $args){
		$tok = 'get';
		if(substr($name,0,strlen($tok))===$tok){
			$name = substr($name, strlen($tok), strlen($name));
			if(!isset($this->{$name}))$name = strtolower($name);
			return (isset($this->{$name})) ? $thi->{$name} : null;
		}
		
		$method = 'func_'.$name;
		if(isset($this->{$method})) {
			return call_user_func_array($this->{$method},$args);
		}
	    trigger_error('Not implemented yet: '.$name, E_USER_ERROR);	
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
        $this->raw =  stream_get_contents($this->IO);
        return $this->raw;
	}
	
	public function __destruct(){
			
		try{
			 if(is_resource($this->IO))fclose($this->IO);
			 /*
			 $fp = fopen($this->file, 'w+');
			 flock($fp,LOCK_UN);
			 fclose($fp);
			 */
		}catch(\Exception $e){
			trigger_error($e->getMessage(). ' in '.__METHOD__, E_USER_ERROR);
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
   public function unserialize(&$var){$i=0;return $this->_unserialize($var,false,$i);}		
   protected function _unserialize(&$var,$just_first=false,&$start) {
       try{ $len = strlen($var); }catch(Exception $e) {trigger_error($e->getMessage(). ' in '.__CLASS__.'::'.__METHOD__.' line '.__LINE__,E_USER_WARNING);
								  return;}
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

   public function serialize($var) {
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
    }

    protected function __toint($string,$blen=4) {
        $out  = 0;
        $n    = ($blen-1) * 8;
        for($bits=0; $bits < $blen; $bits++) {
            $out |= ord($string[$bits]) << $n;
            $n -= 8;
        }
        return $out;
    }

    protected function __fromint($int,$blen=4) {
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

    protected function __fromfloat($float) {
        $str  = $this->__fromint($float);
        $str .= $this->__fromint( round(($float-(int)$float)*1000) , 2 );
        return $str;
    }

    protected function __tofloat($string) {
        $float  = $this->__toint(substr($string,0,4));
        $float += $this->__toint(substr($string,4,2),2)/1000;
        return $float;
    }	
}
 
