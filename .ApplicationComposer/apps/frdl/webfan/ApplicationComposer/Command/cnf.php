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
 * 3. Neither the name of frdl/webfan nor the
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
 *  @role       example/test
 * 
 *  @cmd "frdl test -b -d"
 * 
 */
namespace frdl\ApplicationComposer\Command;

class cnf extends CMD
{


   protected $data;
   protected $file;

   function __construct(){
		parent::__construct();
	}
   
   
   public function applyValue($k,$v, $cbs = null){
   	 $setSessionFunc = function($k,$v){
	 	$this->data['config'][$k] = $v;
	 	return $v;
	 };
	 
   	 $setAdminPWD = function($k,$v){
	 	$this->data['config'][$k] = $v;
	 	$this->aSess['ADMIN_PWD'] = $v;
	 	return $v;
	 };	 
	 
	 $bool = function($k,$v){
	 	if('true' === $v || 1 === intval($v))$v = true;
	 	if('false' === $v || 0 === intval($v))$v = false;
	 	$this->data['config'][$k] = $v;
	 	return $v;
	 };	 
	 
   	 $cbs = (is_array($cbs)) ? $cbs : array(
   	      '______DEFAULT______' =>  $setSessionFunc,
   	       'ADMIN_PWD' =>  $setSessionFunc,
   	       'DISABLE_ADMIN_PWD' => $bool,
   	       'PUBLIC_PROXY' => $bool,
		);	
		
     if(isset($cbs[$k])){
	 	if(is_callable($cbs[$k])){
			return $cbs[$k]($k,$v);
		}
	 }else{
	 	return $cbs['______DEFAULT______']($k,$v);
	 }		
	  
	  return $v;	
   }     
   
   
   public function filterValue($k,$v, $cbs = null){
   	 $cbs = (is_array($cbs)) ? $cbs : array(
		  'ADMIN_PWD' => function($pwd){
		  	
		  	return sha1($pwd);
		  },
		 
		'DISABLE_ADMIN_PWD' =>  function($v){
	 	if('true' === $v || 1 === intval($v))$v = true;
	 	if('false' === $v || 0 === intval($v))$v = false;
	 	return $v;
	 } ,
		 
		'PUBLIC_PROXY' =>  function($v){
	 	if('true' === $v || 1 === intval($v))$v = true;
	 	if('false' === $v || 0 === intval($v))$v = false;
	 	return $v;
	 } 
		);	
		
     if(isset($cbs[$k])){
	 	if(is_callable($cbs[$k])){
			return $cbs[$k]($v);
		}elseif(is_string($cbs[$k])){
			return $cbs[$k];
		}
	 }		
	  
	  return $v;	
   }  
    
    public function getKey($k){
		$ks = array(
		  'admin-pwd' => 'ADMIN_PWD',
		);
	
		if(isset($ks[$k]))return $ks[$k];
		return $k;
	}
    
    public function process()
    {
       $args = func_get_args();
         if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin'] ){
                $this->result->out = 'set config ERROR: You are not logged in as Admin';
  	
	     	 return;
		  }

		if(true!== $this->loadConfigFromFile(true)){
                $this->result->out = 'set config ERROR: cannot readf config file';
        	 return;			
		}

		  		  
		  		  
		if(isset($this->argtoks['flags']['t']) || 'response' === $this->getRequestOption('test')){
			 $this->result->out = 'set config: TEST ONLY - aborted ';
			  $this->result->out .= 'Request: '.print_r($this->argtoks, true); 
			  return;
		} 
		
		

		
		if('get' === strtolower($this->argtoks['arguments'][0]['cmd']) && intval($this->argtoks['arguments'][0]['pos']) === 1){
			$this->result->config =$this->data['config'];
			unset($this->result->config['PIN_HASH']);
			unset($this->result->config['ADMIN_PWD']);
			unset($this->result->config['PIN']);
			unset($this->result->config['SECRET']);
			unset($this->result->config['SHAREDSECRET']);
			if('all' === $this->argtoks['arguments'][1]['cmd'] && intval($this->argtoks['arguments'][1]['pos']) === 2){
				return;
			}
			
			
		 $this->result->config = Array();	
		 foreach($this->argtoks['options'] as $num => $o){
		    $v =  $o['value'];	
		     if(isset( $this->data['config'][ $this->getKey($o['opt'])]) && !is_string($this->data['config'][ $this->getKey($o['opt'])])
		      && is_string($v)
		     ){
			 	$v = json_decode($v);
			 }
		     
	 	       if(isset( $this->data['config'][ $this->getKey($o['opt'])] )
	 	        && 'PIN_HASH' !== $this->getKey($o['opt'])
	 	        && 'ADMIN_PWD' !== $this->getKey($o['opt'])
	 	        && 'PIN_HASH' !== $this->getKey($o['opt'])
	 	        && 'SECRET' !== $this->getKey($o['opt'])
	 	        && 'SHAREDSECRET' !== $this->getKey($o['opt'])
	 	       ){
			   	 $this->result->config[$o['opt']] =$this->data['config'][$this->getKey($o['opt'])];
			   }else{
			   	 unset($this->result->config[$o['opt']]);
			   }
		    }
			return;
		}


	  if('set' === strtolower($this->argtoks['arguments'][0]['cmd']) && intval($this->argtoks['arguments'][0]['pos']) === 1){
		
	   foreach($this->argtoks['options'] as $num => $o){
		 $v =  $this->filterValue($this->getKey($o['opt']), $o['value']);	
		 if(isset($this->argtoks['flags']['w']) || isset($this->data['config'][ $this->getKey($o['opt'])])){
		 	    $this->data['config'][ $this->getKey($o['opt'])] = $v;
		 }
		 
	    }
	     
	  }
		
		
			 if(true===$this->writeToConfigFile()){
			 	 $this->result->out = 'set config: done';
			 }else{
			 	 $this->result->out = 'set config: ERROR writing config file';
			 }
			
		
        
    }
    

    
    public function required()
    {
       $args = func_get_args();
    }
}