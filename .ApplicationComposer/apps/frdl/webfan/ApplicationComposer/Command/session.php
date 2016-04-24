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

class session extends CMD
{


    
    
    public function process()
    {
       $args = func_get_args();
       $this->result->out = '';
       $this->result->js = '';
       $this->result->args = $this->argtoks;   
       
       $this->result->isAdmin = $this->aSess['isAdmin'];
       
           if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin'] ){
                $this->result->out = 'set config ERROR: You are not logged in as Admin';
  	       
	     	 return $this->result;
		  }     
         
   	if(true!== $this->loadConfigFromFile(true)){
                $this->result->out = 'set config ERROR: cannot readf config file';
        	 return;			
		}         
                
       	  
       $this->result->project = (isset($this->aSess['project'])) ? $this->aSess['project'] : null;
       $this->result->url = $this->data['config']['URL'];
       $this->result->dirs = $this->data['config']['DIRS'];
       $this->result->dirs['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
       $this->result->dirs['HOME'] = (is_dir($tDIR = call_user_func(function() {
                $home = getenv('HOME');
                 if (!empty($home)) {
                  $home = rtrim($home, '/');
                 }
                elseif (!empty($_SERVER['HOMEDRIVE']) && !empty($_SERVER['HOMEPATH'])) {
                 $home = $_SERVER['HOMEDRIVE'] . $_SERVER['HOMEPATH'];
                 $home = rtrim($home, '\\/');
                }
                 return empty($home) ? NULL : $home;
        }). DIRECTORY_SEPARATOR .'files'. DIRECTORY_SEPARATOR))
            ? $tDIR : $this->data['config']['DIRS']['files'];
        
        
       
       
         return $this->result;
    }
    
   
    protected function _sub_children(){
	         
	}
    
    public function required()
    {
       $args = func_get_args();
    }
}