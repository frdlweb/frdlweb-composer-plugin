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
 *  @role       Skeleton [Template:CLI]
 * 
 */
namespace frdl\ApplicationComposer\Command;

class setup extends CMD
{

   protected $_db;
    
    public function process()
    {
       $args = func_get_args();
        $this->result->js = '';
        
          if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin'] ){
                $this->result->out = 'set config ERROR: You are not logged in as Admin';
  	
	     	 return;
		  }     
       
       	if(true!== $this->loadConfigFromFile(true)){
                $this->result->out = 'config ERROR: cannot readf config file';
        	 return;			
		}	
		
		
   	   if(isset($this->argtoks['flags']['d'])){
   	   	 ini_set('display_errors', 1);
		 error_reporting(E_ALL);	   	
	   }
	   		
	try{
		 $this->_db =  \frdl\xGlobal\webfan::db();		
		

	 if('create-tables' === strtolower($this->argtoks['arguments'][0]['cmd']) && intval($this->argtoks['arguments'][0]['pos']) === 1){
		return $this->create_tables();			
	 }
	
		     
	}catch(\Exception $e){
            $this->result->out = $e->getMessage();
            return $this->result;
	}
		
     //  $this->result->out = 'tewsto';
      // $this->result->args = $this->argtoks;  
    }
    
    
    protected function create_tables(){
	 
	   	if(isset($this->argtoks['flags']['d'])){
   	   	 ini_set('display_errors', 1);
		 error_reporting(E_ALL);	   	
	   }
	   
	   	 
		 if(!is_string($this->data['config']['db-pfx']) || '' === trim($this->data['config']['db-pfx'])){
		 	$str =  'Please provide a table prefix!';
		 	 $this->result->out = $str;
		 	  $this->result->js .= 'alert("'.$str.'");';
		 	return;
		 }
		 
		  $settings=array(
		   'driver' => $this->data['config']['db-driver'],
		   'host' => $this->data['config']['db-host'],
		   'dbname' => $this->data['config']['db-dbname'],
		   'user' => $this->data['config']['db-user'],
		   'password' => $this->data['config']['db-pwd'],
		   'pfx' => $this->data['config']['db-pfx'],
		   
		);	 
		

	$this->_db-> Connect($settings,  false);
		 if(true !== $this->_db->connected){
		 	$str =  'No database connection!';
		 	 $this->result->out = $str;
		 	  $this->result->js .= 'alert("'.$str.'");';
		 	return;
		 }	 
		 
		 
		 
		 
	    		try{

	     	 $S = new \frdl\_db();	
	     $cfile =  $this->data['config']['FILES']['database-schema'];
	

		 if(file_exists($cfile)){
		 	$oldSchema = $S->load_schema(file_get_contents($cfile)); 
		 	//$oldSchema = $S->load_schema($this->read($cfile, 'rb',  null)); 
		 }else{
		 	 $oldSchema = $S->schema();
		 }
		 if(!is_object($oldSchema))$oldSchema = $S->schema();
		 	 
		
		
		 $S->check($schema, $tables,  null,  true,  true,  true,   $this->_db, $settings, 
		              $oldSchema);
	
       
          $this->result->js.='
            	$(document).wdPostbox(\'deleteMessage\', \'system-error-database-missing-or-obsolete-tables-'.$oldSchema->version.'\',  \'update\', false);	 	 
            	$(document).wdPostbox(\'deleteMessage\', \'system-error-database-missing-or-obsolete-tables-'.$schema->version.'\',  \'update\', false);	 	   	
					 	   
          ';


		}catch(\Exception $e){
	          $tablesOK = false;
	            $this->result->out ='Error checking tables! '.$e->getMessage();
		
	    	return $this->result;
		}
       			 
		 
		$S->tables($tables, false);
		$report = $S->check_tables($schema, $tables, $schema);
		file_put_contents( $cfile, $S->save_schema($schema, 128));
		
		$this->data['config']['db-schema-version'] = $schema->version;
	
			 if(true !== $this->writeToConfigFile()){
			 	\webdof\wResponse::status(409);
			 	 $this->result->out = 'set config: ERROR writing config file';
			 }else{
			 	 $this->result->out = $report;
			 }
	
		
			 

		
		return $this->result;

	}
    
    
    public function required()
    {
       $args = func_get_args();
    }
}