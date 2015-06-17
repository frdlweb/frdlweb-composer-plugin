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
 */
 namespace frdl\xGlobal; 
 
 abstract class fexe
{
	
	protected $IO = null;
	
	protected $data;
	
	protected $file; 
	protected $file_offset;
	
	protected $raw = null;
	
	protected $Request;
	
	function __construct($file, $file_offset ){
		 $this->file = $file;
		 $this->file_offset = $file_offset;
		 $this->data(null);
		 $this->_boot();
	}	
	
	
	abstract public function data();
	abstract protected function _boot();	
	abstract public function run(&$Request =null);	
	abstract protected function route();
			
	protected function default_run(){
    	$this->Request = (null !== $Request) ? $Request : $this->getRequest();
    	$this->route();		
	}		
			
	public function getRequest(){
		
	}		
			
	protected function default_boot(){
		\frdl\webfan\App::God()->addStreamWrapper( 'webfan', 'fexe', get_class($this),  true  ) ;
	}
	
		
	public function read($data, $delimiters = '#', \closure $func){
		$ti = new \frdl\common\TokenIterator($data, $delimiters);
		foreach ($ti as $count => $token) {
            $func($token);
        }
	}
	
	public function __call($name, $args){
		$tok = 'get';
		if(substr($name,0,strlen($tok))===$tok){
			$name = substr($name, strlen($tok), strlen($name));
			if(!isset($this->{$name}))$name = strtolower($name);
			return (isset($this->{$name})) ? $thi->{$name} : null;
		}
		
	    trigger_error('Not implemented yet: '.__METHOD__, E_USER_ERROR);	
	}
	
   
   
   public function wrapData($data, $subject)
     {
     	$begin = "-----BEGIN $subject-----\r\n";
     	$end = "-----END $subject-----";
        return $begin . chunk_split(base64_encode($data)) . $end;
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
			if(null !== $this->IO)fclose($this->IO);
		}catch(\Exception $e){
			trigger_error($e->getMessage(). ' in '.__METHOD__, E_USER_ERROR);
		}
	}
}
 
