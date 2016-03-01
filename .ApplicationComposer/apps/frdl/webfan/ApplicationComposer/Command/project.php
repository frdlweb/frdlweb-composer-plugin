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

class project extends CMD
{


    
    
    public function process()
    {
       $args = func_get_args();
       $this->result->out = '';
       $this->result->js = '';
       $this->result->args = $this->argtoks;   
       
           if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin'] ){
                $this->result->out = 'set config ERROR: You are not logged in as Admin';
  	
	     	 return;
		  }     
            $this->db = \frdl\xGlobal\webfan::db();
            
         
         $project=$this->getRequestOption('projectID');
         if(!empty($project) && is_numeric($project)){
         	 $p = new \frdl\ApplicationComposer\Project(array(),  \frdl\xGlobal\webfan::db()->settings(),  $this->db); 
		 	 $this->result->isProject = $p->find($project);
		 	 if(true===$this->result->isProject){
			 	$this->result->project=$p->variables;
			 }
		 }
         
         if(isset($this->argtoks['arguments'][0]) && intval($this->argtoks['arguments'][0]['pos']) === 1){
         	$sub = $this->argtoks['arguments'][0]['cmd'];
         	$method = '_sub_'.$sub;
         	if(is_callable(array($this,$method))){
				return call_user_func_array(array($this,$method), func_get_args());
			}
         }   
              
   

         
        $this->result->out = 'Unexpected end - subcommand undefined?';
        
        return $this->result;
    }
    
   
    protected function _sub_children(){

   	   $p = new \frdl\ApplicationComposer\Project(array(),  \frdl\xGlobal\webfan::db()->settings(),  $this->db); 
    	   $parent=$this->getRequestOption('projectID');
    	   if(empty($parent))$parent=0; 
    	   $this->result->projects = $p->selectAll(array('node_parent' =>$parent));
  
/*		     $this->result->js.= 'alert("test");';
*/		         
	}
    
    public function required()
    {
       $args = func_get_args();
    }
}