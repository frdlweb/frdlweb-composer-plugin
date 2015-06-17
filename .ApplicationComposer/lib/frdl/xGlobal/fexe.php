<?php
/**
 * @license BSD style
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
	
	function __construct(){
		 $this->data(null);
	}	
	
	
	/**
	* 
	*  getter 
	* 
	* @return
	*/
	abstract public function data();
	
	
		
	public function read($data, $delimiters = '#', \closure $func){
		$ti = new \frdl\common\TokenIterator($data, $delimiters);
		foreach ($ti as $count => $token) {
            $func($token);
        }
	}

	public function __call($name, $args){
		
	}
	
   
   /*
   http://php.net/strtok
    Split string/data by tokens 
   */
   public function tokenize($str, $token_symbols, $token_reset = true) {
      $word = strtok($str, $token_symbols);
      while (false !== $word) {
        // do something here...
         $word = strtok($token_symbols);
     }

       if($token_reset)
        strtok('', '');
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
     
     
    public function getFileData(){
		$this->IO = fopen(__FILE__, 'r');
        fseek($this->IO, __COMPILER_HALT_OFFSET__);
        return stream_get_contents($this->IO);
	}
	
	public function __destruct(){
		try{
			if(null !== $this->IO)fclose($this->IO);
		}catch(\Exception $e){
			trigger_error($e->getMessage(). ' in '.__METHOD__, E_USER_ERROR);
		}
	}

}
 
