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

    //$this->Console->App->cmd() //webfan.fexe.php
    protected $dir;
    
    protected $s;
    protected $z;
    
    public function process()
    {
       $args = func_get_args();
       $this->result->out = '';
       $this->result->js = '';
       $this->result->args = $this->argtoks;   
 
        $this->s = new \frdl\webfan\Serialize\Binary\bin();


         //   $this->db = \frdl\xGlobal\webfan::db();
  		if(true!== $this->loadConfigFromFile(true)){
                $this->result->out = 'set config ERROR: cannot readf config file';
        	 return;			
		}          
         
           $this->dir = $this->data['config']['DIRS']['data.storage'].'.projects'.DIRECTORY_SEPARATOR;
           if(!is_dir($this->dir)){
		   	  mkdir($this->dir, 0744, true);
		   }
        
        
        if(!isset($this->aSess['project']))$this->aSess['project']=null;
        
        
        
         if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin'] ){
                $this->result->out = 'set config ERROR: You are not logged in as Admin';
  	
	     	 return;
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
    
 
    protected function _sub_create(){
    	  $id=file_get_contents('http://www.webfan.de/getid.php?pfx=wpjct-&suffix=-us'.$this->data['config']['UID']);
    	  if(false===$id){
		  	$id = uniqid('wpjct-', false);
		  }
    	  
    	
          $project = new \stdclass;
          $project->id=$id;        
          $project->generator = $this->data['config']['PACKAGE'].' '.$this->data['config']['VERSION']
                                .';'.$this->data['config']['URL']
                                .';'.$this->data['config']['URL_API_ORIGINAL']
                                ;              
          $project->created=time();                      
          $project->title=$this->getRequestOption('title');
          $project->url=$this->getRequestOption('url');
          $project->dirs =  new \stdclass;
          $project->dirs->www = $this->getRequestOption('www');
          $project->dirs->files = $this->getRequestOption('files');
          $project->dirs->widgets = rtrim($project->dirs->www, '/ ') . DIRECTORY_SEPARATOR . $this->getRequestOption('widgets');
          $project->widgetsUrl = $this->getRequestOption('widgets-url');
          $project->online = false;
          $project->deployed = false;
          
          $dir = $this->dirname($project->title);
          
          
          if(!mkdir($dir, 0744, true)){
          	$this->result->code=409;
		  	$this->result->out = 'Cannot create directory '.$dir;
		  	return $this->result;
		  }
		  $pfile = $dir.$project->id.'.project.zip';
          $f = $project->id.'.project';
	      
	     // file_put_contents($f, json_encode($project)); 
	          //file_put_contents($f, $this->s->serialize($project)); 
	          //file_put_contents($dir.'.rootproject', $project->id); 
	       try{
	            \frdl\Compress\Zip\Helper::add('.rootproject', $pfile, $project->id);    
	            \frdl\Compress\Zip\Helper::add($f, $pfile, $this->s->serialize($project));    
	      		   	
		   }catch(\Exception $e){
	          	$this->result->code=409;
		    	$this->result->out = 'Error creating project '.$pfile;
		  	    return;	   	  
		   }   
	          

	      if(!file_exists($pfile)){
          	$this->result->code=409;
		  	$this->result->out = 'Cannot create file '.$pfile;
	      	rmdir($dir);
		  	return;		  	
		  }  
		  
	 
	   $this->result->code=200;	  
	   $this->result->out = 'Project created';
	   $this->result->project = $project;
	   return;		    
	}   
   
    protected function _sub_list(){

	         
	} 
    
    
    protected function dirname($title){
		$n = preg_replace("/[^A-Za-z0-9_\-]/", "_", $title);
		$n = $this->dir.$n;
		$t=$n;
		$c=1;
		while(is_dir($t.DIRECTORY_SEPARATOR)){
			$c++;
			$t =$n.'_'.$c;
		}
		return $t.DIRECTORY_SEPARATOR;
	} 
    
    public function required()
    {
       $args = func_get_args();
    }
}