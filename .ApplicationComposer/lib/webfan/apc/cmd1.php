<?php
namespace webfan\InstallShield\apc;

class cmd1 extends \frdl\aSQL\Engines\Terminal\CLI
{
	public $vm;
/**  \frdl\aSQL\Engines\Terminal\CLI
 abstract protected function boot();
 abstract protected function _exec($args);
 abstract protected function force_state($state);
 abstract public function parseQuery();  
 abstract public function validateQuery(); 
*/	


   
   public $cmd = null;
	
	function __construct(&$vm = null){
		if(null === $vm)$vm = \frdl\webfan\App::God(false)->vm();
		$this->vm = $vm;
        

	}
	
   
   

	 	
	
   public function exe($cml = ''){
 	 global $argv;
 	 
 	
 	 
	 $this->IN = ($this->mode === self::MODE_CLI) ? implode(self::DELIM, $argv) : urldecode($cml);
	 $this->parse();
 	 	/*  
 	  echo $this->HALT.' - '.$this->state.'<br />';
 	
 
  	$this->statusText = 'Parsing query...';
  	  $this->parseQuery();
 	$this->statusText = 'Validating query...';
  	  $this->validateQuery('before'); 	
  	 $this->statusText = 'Process batch...';  	
  	*/ 
  	 $this->HALT = false;
	 foreach($this->batch as $num => $args){
	 	 if(false !== $this->HALT || 'HALT' ===  $this->state)break;
  	 //    $this->statusText = 'Process command:>'.$args['command']['cmd'];  
	 	 $this->_exec($args);
	// 	  $this->statusText .= htmlentities(' :>Complete.');
	 }
	
//	 $this->statusText = 'Batch run complete';
	   
	
	 return $this;
 }
	
	
  protected function boot(){
  	  	
  }
  
  protected function _exec($args){
  	
  	    $classname = 'webfan\InstallShield\apc\CMD\\'.$args['command']['cmd'];
  	    if(class_exists($classname)){
			$this->cmd = new $classname($this);
			$CMD = &$this->cmd;
			$CMD($args);
			
			header('Content-Type: plain/text');
			
			if(false!==$CMD->getRequestOption('format')){
				header('Content-Type: '.strip_tags($CMD->getRequestOption('format')));
			}else{
				header('Content-Type: plain/text');
			}
			
			$CMD->run();
			
			$result = $CMD->result;
			
			if ($CMD->getRequestOption('format') && false !== strpos(strtolower($CMD->getRequestOption('format')), 'json') ) {
              $result = json_encode($result);
            }elseif(!$CMD->getRequestOption('format') || 'text/plain' === strtolower($CMD->getRequestOption('format'))){
				$result = implode(PHP_EOL, $result);
			}
			
			echo $result;
		}

  }
  

    
  protected function force_state($state){
  	  	
  }
  
  public function parseQuery(){
  	  	
  }  
  
  public function validateQuery(){
  	  	
  }	

 public function add_command($command, callable $callable){
  	  	
  }	
}
