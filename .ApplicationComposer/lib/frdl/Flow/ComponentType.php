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
 */
namespace frdl\Flow;


class ComponentType extends Element
{
	protected $name; //id/selector
	protected $data; /* = array(
	     'html' => array(
	           'attributes' => array(
	                'data-invoke-async' => '' // data-frdl-component | data-frdl-desktop-widget | ...
	                
	           )
	     )
	)
	
	*/
	function __construct(){
		$args = func_get_args();
		$this->name=$args[0];
		$this->data=$args[1];

	}
	public static function create(){
	   $_call='\\'.get_class(self).'::__construct';	
	   return call_user_func_array($_call, func_get_args());
	}	
	
	/*	
    public function __toString()
    {
       return $this->name;        
    }	
     */   
    public function &__get($name)
    {
      $retval = null;	

      if('name' === $name){
      	 return $this->name;
      }elseif('dump' === $name){
      	 $retval = $this->_dump();
      	 return $retval;
      }elseif (isset($this->data[$name])){
           $retval = $this->data[$name];
           return $retval;
	  }

        return $retval;
    }		
    
    protected function _dump(){
		return $this->data;
	}	
	
}
