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

class html extends CMD
{

     
     protected $item;
     protected $html;

    function __construct(){
		parent::__construct();
		$this->html = '';
	}
    
    
    public function process()
    {
       $args = func_get_args();
       $this->result->out = '';

        $this->item = $this->getRequestOption('item');
       $this->result->item = strip_tags($this->item); 

       try{
	    	if(null !== $this->item)$this->html .= $this->{'item_'.$this->result->item}();
	   }catch(\Exception $e){
	   	  $this->statusText = 'Error: '.$e->getMessage();
	   }
     
       $this->result->html = base64_encode($this->html);
    }
    
        
    public function required()
    {
       $args = func_get_args();
    }
    
    protected function item_settings(){
	 
		
	   $html .=  $this->item_login();
	   if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']){
	     	 return $html;
		  }
		  
	  return $html;
	}
    
    protected function item_login(){
		$html = '';

	  if(isset($this->aSess['isAdmin']) && true === $this->aSess['isAdmin']){
		 		 $html.= '<p><span>You are logged in as Admin.</span> <button class="wd-btn-no" onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].logout();" >Logout</button></p>';
		 		
		 		 $html.= '<p style="font-size:0.8em;">';
		 		 $html.='<span>Change password</span>: ';
		 		 $html.='<input style="max-width:120px;" id="wd-login-ac-NEWPASS" type="password" name="new_pwd" />
		 		  <span>confirm</span>: <input style="max-width:120px;" id="wd-login-ac-NEWPASS_2" type="password" name="new_pwd_2" /> 
		 		  <button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'admin-pwd\', Dom.g(\'wd-login-ac-NEWPASS\').value,Dom.g(\'wd-login-ac-NEWPASS_2\').value,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig);" >Change password</button>';
		 		  $html.='</p>';
		 		 
	   }else{
		 	 $html.= '<p><span>Please loggin!</span></p>';
		 	 $html.=  '<strong>1. <span>PIN</span></strong>:<br />';
		 	 $html.='<input id="wd-login-ac-PIN" type="password" name="PIN" maxlength="2" style="width:32px;" />';
		 	 $html.='<br /><br />';    	 
    	 
 
		 	 $html.='<strong>2. <span>Password</span></strong>:<br />';
		 	 $html.='<input id="wd-login-ac-PASS" type="password" name="pwd" style="width:200px;" />';
		 	 $html.= '<br /><br />'; 
		 	 $html.= '<p><button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].login();" >Login...</button>';
  
	  }
		return $html;
	}
	
}