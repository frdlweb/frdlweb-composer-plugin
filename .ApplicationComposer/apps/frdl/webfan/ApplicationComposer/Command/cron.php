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

class cron extends CMD
{


   protected $data;
   protected $file;
   
   protected $tasks;
   protected $time_last;
   
   protected $config;
   
   protected $count_tasks = 0;

   function __construct(){
		parent::__construct();
	}
   

   protected function Config(){
   	  $this->config = array();
   	  $this->config['flooding_protection'] = 0;
   	  $this->config['cache_prune_limit'] = time() - 24 * 60 * 60; 
   	  $this->config['tmp_prune_limit'] = time() - 5 * 60; 
   }


   protected function helper_func_prune_cache(){
    	$this->helper_func_prune_dir($this->data['config']['DIRS']['cache'], $this->config['cache_prune_limit'] );
        $this->helper_func_prune_dir($this->data['config']['DIRS']['tmp'], $this->config['cache_prune_limit'] );
   }

   protected function helper_func_prune_dir($dir, $limit){
   	$directory = new RecursiveDirectoryIterator($dir);
   	foreach (new \RecursiveIteratorIterator($directory) as $filename=>$current) {
   		 if ($current->isDot())continue;
            if($current->getMTime() <  $limit){
            	chmod($current->getFilename(), 0755);
				unlink($current->getFilename());
			}
     }
    
   }
   
   protected function getNextTasks(){
   	  /**
		 * ToDo...!
		 */
		  $this->helper_func_prune_cache();
   }


    public function process()
    {
       $args = func_get_args();


		if(true!== $this->loadConfigFromFile(true)){
                $this->result->out = 'set config ERROR: cannot readf config file';
        	 return;			
		}
		
		
		if('prune' === strtolower($this->argtoks['arguments'][0]['cmd']) && intval($this->argtoks['arguments'][0]['pos']) === 1){
			  $this->helper_func_prune_cache();
		}
			
			
		$this->getNextTasks();
        $this->result->out = 'Crontasks executed.';
    }
    

    
    public function required()
    {
       $args = func_get_args();
    }
}