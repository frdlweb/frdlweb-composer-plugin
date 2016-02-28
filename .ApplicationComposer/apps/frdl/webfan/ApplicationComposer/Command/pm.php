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

class pm extends CMD
{


   protected $data;
   protected $file;
   
   protected $packagefullname = null;
   protected $vendor = null;
   protected $package = null;
   protected $scmd = null;
   
   protected $F; //fetcher

   protected $db;

   function __construct(){
		parent::__construct();
	}
   
   
   protected function isErrorDB(&$errorInfo, $out = true){
   	 $errorInfo = \frdl\xGlobal\webfan::db()->errorInfo(); 		
   	 if(0 !== intval($errorInfo[0])){
   	 	if(true === $out)$this->result->erroinfo = $errorInfo;
   	 	$this->result->out = 'Database error';
   	 	\webdof\wResponse::status(500);
	 	return true;
	 }
   	 return false;
   }
   
   protected function wrongArgumentCount(){
           $this->result->out = 'Invalid arguments count';
     return;	 	
   }
   
    public function process()
    {
       $args = func_get_args();
       
   		if(true!== $this->loadConfigFromFile(true)){
                $this->result->out = 'set config ERROR: cannot read config file';
        	 return;			
		}    
       
       /*
         if((!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']) && true !== $this->data['config']['PUBLIC_PROXY'] ){
                $this->result->out = 'set config ERROR: You are not logged in as Admin';
  	
	     	 return;
		  }
*/


     $this->db = \frdl\xGlobal\webfan::db();

	 if(count($this->argtoks['arguments']) < 1 ){
                $this->wrongArgumentCount();
        	 return;						
	 }		
   
   
     	$o = array();
    	if(!isset($this->argtoks['flags']['c']))$o['cache_time'] = 0;
    	$o['debug'] = (isset($this->argtoks['flags']['d']));
    	
    	if(true === $o['debug']){
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		}
    	$o = array_merge($this->data['config'], $o);
    	
    	  
     
     if('find' === strtolower($this->argtoks['arguments'][0]['cmd']) && intval($this->argtoks['arguments'][0]['pos']) === 1){
		 return $this->find($o);			
	 }
	 
     if('select' === strtolower($this->argtoks['arguments'][0]['cmd']) && intval($this->argtoks['arguments'][0]['pos']) === 1){
		 return $this->select($o);			
	 }	       
	 
     if('get' === strtolower($this->argtoks['arguments'][0]['cmd']) && intval($this->argtoks['arguments'][0]['pos']) === 1){
		 return $this->package($o);			
	 }	        
	 
     if('stats' === strtolower($this->argtoks['arguments'][0]['cmd']) && intval($this->argtoks['arguments'][0]['pos']) === 1){
		 return $this->stats($o);			
	 }	       
	 
     if('download' === strtolower($this->argtoks['arguments'][0]['cmd']) && intval($this->argtoks['arguments'][0]['pos']) === 1){
		 return $this->stats($o);			
	 }	      
	 	       	       
	      \webdof\wResponse::status(404);
	         $this->result->out = '(Sub-)Command not found.'; 
    }
    
    
    
     public function download($o){
       	if(true !== $this->aSess['isAdmin']){
     		$this->result->stats[0] = '<span class="webfan-red">You are not logged in</span>';
			$this->result->out = 'OK';
			return;
		}   	
     	
	 	$this->packagefullname = $this->getRequestOption('package');
	 	$url = $this->getRequestOption('url');
	 	
	 	$version = $this->getRequestOption('version');
	 	$checksum = $this->getRequestOption('checksum');
	 	
            $e = explode('/', $this->packagefullname);
              if(2 !== count($e)){
                 $this->result->out = 'Invalid packagename';
                return;	 				
	     	  }
	     	  
		    $this->vendor = $e[0];
		    $this->package = $e[1];		
		    
		if(!is_array(($u = parse_url($url)))){
                 $this->result->out = 'Invalid url';
                return;	 				
   		}    	     
       
        $this->result->response = new \stdclass; 	
        $this->result->response->errors = array();	
   		
  		    $p = new \frdl\ApplicationComposer\Package(array(),  \frdl\xGlobal\webfan::db()->settings(),  $this->db); 
		    if(true !== $p->find( $this->vendor,  $this->package )){ 	
					   $p->vendor = $this->vendor;
					   $p->package = $this->package;
					   $p->time_last_fetch_info = time();
					   $p->create();	    
		    }	
   		  if($this->isErrorDB($errorInfo, true)){
   		  	$this->result->response->errors[] = $errorInfo;
   		  }
   		
     	  $R = new \frdl\ApplicationComposer\Repository( array(), $this->db->settings(), $this->db );
          $this->repos  = $R->search(array('_use' => 1, 'host' => $u['host']));
     	  $this->result->response->repos = $this->repos;	
 
           
          $D = new \frdl\ApplicationComposer\Download( array(), $this->db->settings(), $this->db ); 
          $D->host = $u['host'];  
     	  $D->repos = $this->repos['name'];
     	  $D->success = 0;
     	  $D->unpacked = 0;
     	  $D->lid = 0;
     	  $D->protocoll = $u['scheme'];	
     	  $D->vendor 	= $this->vendor;
     	  $D->package   = $this->package;
     	  $D->version = $version;
     	  $D->checksum = $checksum;
     	  $D->time = time();
     	  
     	  $e = explode('/', $u['path']);
     	  $filename = $e[(count($e)-1)];
     	  $D->dir = $this->data['config']['DIRS']['packages'] . $this->vendor . DIRECTORY_SEPARATOR . $this->package . DIRECTORY_SEPARATOR
     	            . $this->repos['name'] . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . 'downloads' .DIRECTORY_SEPARATOR
     	            ;
     	  	  
     	            
     	  $D->file = $D->dir . $filename;
     	  
     	  $D->create();
     	  
     	  
     	  	
   		  $this->result->out = 'OK';	  	 	
	 }
	 
	 
    
     public function stats($o){
     	/*
     	if(true !== $this->aSess['isAdmin']){
     		$this->result->stats[0] = '<span class="webfan-red">You are not logged in</span>';
			$this->result->out = 'OK';
			return;
		}
     	*/
     	$this->result->stats = array();
        if(0 === count($this->argtoks['options'])){
			
		}elseif(null !== ($package = $this->getRequestOption('package'))){
			$this->packagefullname = str_replace(array('"', "'"), array('',''), $package);
            $e = explode('/', $this->packagefullname);
              if(2 !== count($e)){
                 $this->result->out = 'Invalid packagename';
                return;	 				
	     	  }
		    $this->vendor = $e[0];
		    $this->package = $e[1];			
			$this->result->stats[$this->packagefullname] = new \stdclass;
			$this->result->stats[$this->packagefullname]->name = $this->packagefullname;
			
		    $p = new \frdl\ApplicationComposer\Package(array(),  \frdl\xGlobal\webfan::db()->settings(),  $this->db); 
		    if(true === $p->find( $this->vendor,  $this->package )){
		    	$data = $p->variables;
		    	$data['infotime'] = $data['time_last_fetch_info'];
		    	unset($data['time_last_fetch_info']);
				//$this->result->stats[$this->packagefullname] = array_merge($this->result->stats[$this->packagefullname], $data);
				//$this->result->stats[$this->packagefullname]['info'] = $data;
				foreach($data as $name => $value){
					$this->result->stats[$this->packagefullname]->{$name} = $value; 
				}
				
				
			}
			if($this->isErrorDB($errorInfo, true))return;
			

			
		}
	    $this->result->out = 'OK';
	}
	
	   
    public function select($o){
		$start = $this->getRequestOption('start');
		$limit = $this->getRequestOption('limit');
		if(null === $start)$start = 0;
		if(null === $limit)$limit = 25;
		$p = new \frdl\ApplicationComposer\Package(array(),  \frdl\xGlobal\webfan::db()->settings(),  $this->db); 
	    $packages = $p->select( $start, $limit, array('vendor' => 'ASC', 'package' => 'ASC'));
	    $this->result->packages = $packages;
	    $this->result->packages = array_unique($this->result->packages);
	    $this->result->out = 'OK';
	}
    
    
    public function package($o){
      	if(!isset($this->argtoks['arguments'][1]) ||  intval($this->argtoks['arguments'][1]['pos']) !== 2)return $this->wrongArgumentCount();
      	
    	$this->packagefullname = str_replace(array('"', "'"), array('',''), $this->argtoks['arguments'][1]['cmd']);
    	
        $e = explode('/', $this->packagefullname);
        if(2 !== count($e)){
              $this->result->out = 'Invalid packagename';
             return;	 				
		}
		$this->vendor = $e[0];
		$this->package = $e[1];
		
    	try{
    	$this->F = new \frdl\ApplicationComposer\Repos\Fetch($o);
    	$this->result->searchresults = $this->F->package($this->vendor, $this->package );
    	//$this->result->searchresults = array_unique($this->result->searchresults);
    	
    			
		}catch(\Exception $e){
		  \webdof\wResponse::status(409);
			$this->result->out = $e->getMessage();
			return;
		}
		
				   	 
		$this->result->out = 'OK';		   	   			
	}
    
    public function find($o){
    	
      	if(!isset($this->argtoks['arguments'][1]) ||  intval($this->argtoks['arguments'][1]['pos']) !== 2)return $this->wrongArgumentCount();
    	$this->packagefullname = str_replace(array('"', "'"), array('',''), $this->argtoks['arguments'][1]['cmd']);
    	

    
    	
    	try{
    	$this->F = new \frdl\ApplicationComposer\Repos\Fetch($o);
    	$this->result->searchresults = $this->F->search($this->packagefullname);
    	$this->result->searchresults = array_unique($this->result->searchresults);
    	
    			
		}catch(\Exception $e){
		  \webdof\wResponse::status(409);
			$this->result->out = $e->getMessage();
			return;
		}

      
    	if(isset($this->argtoks['flags']['s']) && true === $this->aSess['isAdmin']){
           $p = new \frdl\ApplicationComposer\Package(array(),  \frdl\xGlobal\webfan::db()->settings(),  $this->db); 
           $p->db()->begin();  		
    		
			foreach($this->result->searchresults as $num => $s){
				if(is_array($s)){
					foreach($s as $num2 => $s2){
			     	$v = explode('/', $s2->name);
				    if(2 === count($v) && !$p->find($v[0], $v[1])){
					   $p->vendor = $v[0];
					   $p->package = $v[1];
					   $p->url = $s2->url;
					   $p->description = $s2->description;
					   $p->time_last_fetch_info = time();
					   $p->create();
				   }						
				   }
				}else{
				$v = explode('/', $s->name);
				if(2 === count($v) && !$p->find($v[0], $v[1])){
					$p->vendor = $v[0];
					$p->package = $v[1];
					$p->url = $s->url;
					$p->description = $s->description;
				    $p->time_last_fetch_info = time();
					$p->create();
				}					
				}

			}
		  $p->db()->commit();
		}
    	
        
        
    	$this->result->out = 'OK';
	}
    

    
    public function required()
    {
       $args = func_get_args();
    }
}