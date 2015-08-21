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
 */
namespace frdl\ApplicationComposer;




class Console extends \frdl\aSQL\Engines\Terminal\CLI
{

  
  public $App;
  
  public $strip_frdl = true;
  
  protected $out = array();
  protected $p; //pointer
  protected $statusText;
  
  protected $dir_cmds;
  protected $state;
  
  private $HALT = false;
  
  
   function __construct($strip_frdl = true){
   	$this->strip_frdl = $strip_frdl;
  	parent::__construct();
    $this->dir_cmds = __DIR__. DIRECTORY_SEPARATOR . 'Command' . DIRECTORY_SEPARATOR;
  }
  
  
   public function applyApp(&$app){
     	$this->App = $app;
     	return $this;
   } 
    
    
  public function dump(){
  	 $out = $this->App->OutData(); 
  	 $out->statusText = htmlentities($this->statusText);
  	 return $out;
  }
  
  protected function _list_commands(){
  	 $commands = array();
  	 
  	 $dir =  $this->dir_cmds;
  	          foreach (new \DirectoryIterator($dir) as $fileInfo) {
              if($fileInfo->isDot()) continue;
              if(preg_match("/([a-z][a-z0-9\-\_\.]+)\.php/", $fileInfo->getFilename(), $matches) ){
			  	 $commands[] = $matches[1];
			  }
             
        }
        
      foreach($this->shell['commands'] as $cmd => $p){
	  	 $commands[] = $cmd;
	  }  
        
      return $commands;    
  }
  
  protected function cmd_help(){
  	$av = '';
  	$av .= 'Available commands: '.PHP_EOL;
  	$i = 0;
  	foreach($this->_list_commands() as $num=> $cmd){
  		$i++;
		$av .= $cmd ."\t\t";
		
		if($i % 3 === 0){
			$av.=PHP_EOL;
		}
	}
   
    $r = new AjaxResult;
    $r->type = 'print';
    $r->out = $av;
    return $r;
  }
  
  public function HALT(){
  	 $this->HALT = true;
  	 $this->force_state('HALT');
  	 $this->statusText = 'STOPPED DUE TO THE HALT COMMAND!';
     $r = new AjaxResult;
     $r->type = 'print';
     $r->out = $this->statusText;
    return $r; 	 
  }
  
  protected function boot(){
  	$this->statusText = 'Booting cli...';
  	$this->out = array();
  	$this->p = NIL;
   	   
  	  $this->add_command('help', array($this, 'cmd_help'));
      $this->add_command('HALT', array($this, 'HALT'));
  	
  }
  
   public function exe($cml = ''){
 	 global $argv;
 	 if(false !== $this->strip_frdl && 'frdl' === substr($cml,0,strlen('frdl')))$cml = substr($cml, strlen('frdl'), strlen($cml));
	 $this->IN = ($this->mode === self::MODE_CLI) ? implode(self::DELIM, $argv) : urldecode($cml);
	 $this->parse();
 	 
  	/*$this->App->OutData('out', print_r($this->batch, true));*/
  	$this->statusText = 'Parsing query...';
  	  $this->parseQuery();
 	$this->statusText = 'Validating query...';
  	  $this->validateQuery('before'); 	
  	 $this->statusText = 'Process batch...';  	
  	 
  	 
	 foreach($this->batch as $num => $args){
	 	 if(false !== $this->HALT || 'HALT' ===  $this->state)break;
  	     $this->statusText = 'Process command:>'.$args['command']['cmd'];  
	 	 $this->_exec($args);
	 	  $this->statusText .= htmlentities(' :>Complete.');
	 }
	
	 $this->statusText = 'Batch run complete';
	   
	
	 return $this;
 }
 

  public function get_cmd_file($cmd){
  	$file = $this->dir_cmds . $cmd . '.php';
  	return  (file_exists($file)) ?  $file : false;
  }


  protected function _exec($args){
  	 if(false !== $this->HALT || 'HALT' ===  $this->state)return;
  	 $command = $args['command']['cmd'];
     $cmd_file = $this->get_cmd_file($command);

	 $this->statusText = 'Invoke frdl command...';  	 
     if(isset($this->shell['commands'][$command]) 
     && is_callable($this->shell['commands'][$command])){
	 	return $this->App->OutData(call_user_func($this->shell['commands'][$command], $args));
	 }elseif(false !== $cmd_file){
	 	require $cmd_file;
	 	$classname = '\frdl\ApplicationComposer\Command\\'.$command;
	 	try{
	 		if(!is_subclass_of($classname, '\frdl\ApplicationComposer\Command\CMD')){
				\webdof\wResponse::status('409');
				die('Invalid CMD class in '.__METHOD__.' '.__LINE__);
		 	}
	 	   $CMD = new $classname;
	 	   return $this->App->OutData($CMD($this, $args));			
		}catch(\Exception $e){
		  return $this->App->OutData('out', 'Error: '.$e->getMessage());
		}

	 }
	 
  
  
 	$this->statusText = 'Command not found: '.strip_tags($command);	  
  	  
  }
  
  protected function force_state($state){
  	 $this->state = (false === $this->HALT && ('HALT' !==  $this->state || 'HALT' === $state)) ? $state :  $this->state;
  	 if('HALT' === $this->state)$this->HALT = true;
  	 return $this;
  }
  
  public function parseQuery(){
  	   $this->statusText .= htmlentities(' ->Pre-Parsing batch [GLOBAL]');
  	   
  } 
  
  public function validateQuery(){
  	  $args = func_get_args();
  	   $this->statusText .=  htmlentities('->skipped(@ToDo) [GLOBAL]');
  }

  public function add_command($command, callable $callable){
  	$this->shell['commands'][$command] = $callable;
  	return $this;
  }
 
	
public function test()
{
		header("Content-Type: text/plain");
		 $cmd = 'frdl test -c --test=console --foo=bar; install 
 ';


 $batch = $this->parse($cmd);
 echo 'Test command line:'."\n\n".$cmd."\n\n".'Parsed:'."\n"
     .print_r($batch,true);
 echo "\n"; 
 echo 'Unparsed:'."\n\n";
 $u =  $this->unparse($batch);
 echo $u;
 echo "\n";
 $batch = $this->parse($u);
 echo 'Test-Re-Parsing:'."\n"
     .print_r($batch,true); 
}
	
}