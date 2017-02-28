<?php
namespace webfan\InstallShield\apc\CMD;



class unzip extends \webfan\InstallShield\apc\Command1
{


	
	
  public function run(){
		if(2>count($this->argtoks['arguments'])){
			\webdof\wResponse::status(400);	
			array_push($this->_result, 'The unzip command expects two arguments e.g.: unzip fromfile todir');
			return;
		}	
		
		if(!file_exists($this->argtoks['arguments'][0]['cmd'])){
			\webdof\wResponse::status(409);	
			array_push($this->_result, 'The file '.$this->argtoks['arguments'][0]['cmd'].' does not exist');
			return;			
		}
		
		if(!is_dir($this->argtoks['arguments'][1]['cmd'])){
			mkdir($this->argtoks['arguments'][1]['cmd'], 0755, true);
		}
		@chmod($this->argtoks['arguments'][1]['cmd'], 0755);				
		
		
		if(!is_writable($this->argtoks['arguments'][1]['cmd'])){
			\webdof\wResponse::status(409);	
			array_push($this->_result, 'The diectory '.$this->argtoks['arguments'][1]['cmd'].' is not writable');
			return;					
			
		}
		
		
	try{
		$wUnzip = new \frdl\webfan\Compress\zip\wUnzip($this->argtoks['arguments'][0]['cmd'], $this->argtoks['arguments'][1]['cmd']);
		$wUnzip->load($this->argtoks['arguments'][0]['cmd'], $this->argtoks['arguments'][1]['cmd']);
		$r=$wUnzip->unzip();	
		
		array_push($this->_result, count($wUnzip->filesOut).' files extracted'. ((0===count($r['errors']))?'':', errors: '.count($r['errors']) ));		
	}	catch(\Exception $e){
				\webdof\wResponse::status(409);	
			array_push($this->_result, $e->getMessage());
			return;		
	}

			
 }	
  
  
}
