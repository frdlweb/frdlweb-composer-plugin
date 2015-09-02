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
   protected $scmd = null;
   
   protected $F; //fetcher


   function __construct(){
		parent::__construct();
	}
   
   
   protected function wrongArgumentCount(){
           $this->result->out = 'Invalid arguments count';
     return;	 	
   }
   
    public function process()
    {
       $args = func_get_args();
         if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin'] ){
                $this->result->out = 'set config ERROR: You are not logged in as Admin';
  	
	     	 return;
		  }

		if(true!== $this->loadConfigFromFile(true)){
                $this->result->out = 'set config ERROR: cannot read config file';
        	 return;			
		}

	 if(count($this->argtoks['arguments']) < 1 ){
                $this->wrongArgumentCount();
        	 return;						
	 }		
     
     if('find' === strtolower($this->argtoks['arguments'][0]['cmd']) && intval($this->argtoks['arguments'][0]['pos']) === 1){
		 return $this->find();			
	 }
	 
     if('select' === strtolower($this->argtoks['arguments'][0]['cmd']) && intval($this->argtoks['arguments'][0]['pos']) === 1){
		 return $this->select();			
	 }	       
	       
	      \webdof\wResponse::status(404);
	         $this->result->out = '(Sub-)Command not found.'; 
    }
    
    
    public function select(){
		$start = $this->getRequestOption('start');
		$limit = $this->getRequestOption('limit');
		if(null === $start)$start = 0;
		if(null === $limit)$limit = 25;
		$p = new \frdl\ApplicationComposer\Package(array(),  \frdl\xGlobal\webfan::db()->settings(),  \frdl\xGlobal\webfan::db()); 
	    $packages = $p->select( $start, $limit, array('vendor' => 'ASC', 'package' => 'ASC'));
	    $this->result->packages = $packages;
	    $this->result->packages = array_unique($this->result->packages);
	    $this->result->out = 'OK';
	}
    
    public function find(){
    	
      	if(!isset($this->argtoks['arguments'][1]) ||  intval($this->argtoks['arguments'][1]['pos']) !== 2)return $this->wrongArgumentCount();
    	$this->packagefullname = str_replace(array('"', "'"), array('',''), $this->argtoks['arguments'][1]['cmd']);
    	
    	$o = array();
    	if(!isset($this->argtoks['flags']['c']))$o['cache_time'] = 0;
    	$o['debug'] = (isset($this->argtoks['flags']['d']));
    	
    	if(true === $o['debug']){
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
		}
    	$o = array_merge($this->data['config'], $o);
    
    	
    	try{
    	$this->F = new \frdl\ApplicationComposer\Repos\Fetch($o);
    	$this->result->searchresults = $this->F->search($this->packagefullname);
    	$this->result->searchresults = array_unique($this->result->searchresults);
    	
    			
		}catch(\Exception $e){
		  \webdof\wResponse::status(409);
			$this->result->out = $e->getMessage();
		}

        $p = new \frdl\ApplicationComposer\Package(array(),  \frdl\xGlobal\webfan::db()->settings(),  \frdl\xGlobal\webfan::db()); 
    	if(isset($this->argtoks['flags']['s'])){
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
		}
    	
    	$this->result->out = 'OK';
	}
    

    
    public function required()
    {
       $args = func_get_args();
    }
}