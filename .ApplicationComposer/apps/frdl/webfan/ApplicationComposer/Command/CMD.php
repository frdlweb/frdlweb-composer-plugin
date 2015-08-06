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
 * 
 */
namespace frdl\ApplicationComposer\Command;

abstract class CMD
{
   protected $aSess;
   
   protected $Console;	
   protected $argtoks;
   protected $result;
   abstract public function process();
   abstract public function required();
   
   function __construct(){
	    $this->aSess = & $_SESSION['frdl\xGlobal\webfan'] ;
   }
   
   public function Console(){
      return $this->Console;
   }   
   
   public function help(){
   	   $required = $this->required;
   	   
   }
   
   final public function getRequestOption($opt){
   	 foreach($this->argtoks['options'] as $num => $o){
	 	if($opt === $o['opt']){
			return $o['value'];
		}
	 }
	 return null;
   }
   
   final public function invoke(&$Console = null, $argtoks){
   	   $this->Console = $Console;
   	   $this->argtoks = $argtoks;
   	   $this->result =   new \frdl\o; 
   	   $this->result->type = 'print';
   	   $this->result->out = '';
	   call_user_func_array(array($this, 'process'), func_get_args());
	   return $this->result;
   }   
   
   final public function __invoke(){
		return call_user_func_array(array($this, 'invoke'), func_get_args());
	}
	   
   final public function getName(){
		$n = explode('\\', get_class($this));
		return $n[count($n)-1];
	}
	
}