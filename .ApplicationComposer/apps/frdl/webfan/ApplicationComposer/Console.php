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
  
  protected $out = array();
  protected $p; //pointer
  protected $statusText;
  
  protected $dir_cmds;
  
  
   function __construct(){
  	parent::__construct();
    $this->dir_cmds = __DIR__. DIRECTORY_SEPARATOR . 'Command' . DIRECTORY_SEPARATOR;
  }
  
  
   public function applyApp(&$app){
     	$this->App = $app;
     	return $this;
   } 
    
    
  public function dump(){
  	 $out = $this->App->OutData(); 
  	 $out->statusText = $this->statusText;
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
   
    $r = new \stdclass;
    $r->type = 'print';
    $r->out = $av;
    return $r;
  }
  
  protected function boot(){
  	$this->statusText = 'Booting cli...';
  	$this->out = array();
  	$this->p = NIL;
   	   
  	  $this->add_command('help', array($this, 'cmd_help'));
  	
  }
  
   public function exe($cml = ''){
 	 global $argv;
	 $this->IN = ($this->mode === self::MODE_CLI) ? implode(self::DELIM, $argv) : urldecode($cml);
	 $this->parse();
 	 
  	/*$this->App->OutData('out', print_r($this->batch, true));*/
  	
	 foreach($this->batch as $num => $args){
	 	 $this->_exec($args);
	 }
	
	 return $this;
 }
 

  public function get_cmd_file($cmd){
  	$file = $this->dir_cmds . $cmd . '.php';
  	return  (file_exists($file)) ?  $file : false;
  }


  protected function _exec($args){
  	
  	$this->statusText = 'Executing command...';
 	$this->statusText = 'Parsing query...';
  	  $this->parseQuery();
 	$this->statusText = 'Validating query...';
  	  $this->validateQuery('before');
  	 
  	 $command = $args['command']['cmd'];
  	 

	   	 
     if(isset($this->shell['commands'][$command]) 
     && is_callable($this->shell['commands'][$command])){
	 	return $this->App->OutData(call_user_func($this->shell['commands'][$command], $args));
	 }elseif(false !== $this->get_cmd_file($command)){
	 	require $this->get_cmd_file($command);
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
  
  protected function force_state($state){}
  public function parseQuery(){} 
  public function validateQuery(){
  	  $args = func_get_args();
  	
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