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
 *  @role       Template [CLI]
 * 
 */
namespace frdl\aSQL\Engines\Terminal;

abstract class CLI
{

 const MODE_CLI = 1;
 const MODE_HTTP = 2;
 
 public $buf = null;
 protected $state;


 protected $mode;	
 protected $shell = array(
            'name' => 'frdl',
            'prompt' => '!#frdl:>',
            'commands' => array(),
            'options' => array(),
            'flags'    => array(),
            'arguments' => array(),
            'operators' => array(),
    );
		
 protected $cml;
 protected $args;
 protected $core = array('exit', 'quit', 'stop', 'help', 'pause', 'debug', 'about'); 
	
 /**
  * env
  */	
 protected $env= array( 
 /**
  * User Interfaces
  */
      'UI' => array(),
      
  /**
   * Devices
   */	   
      'DEV' => array(), 
      
	  'CACHE' => array( ),
  ); 
  
 protected $config = array( 
      
  ); 	
 
 /**
  * processing
  */	
 	
 function __construct(){
 	  $this->state = 'booting';
      $this->mode = (PHP_SAPI === 'cli')  ? self::MODE_CLI : self::MODE_HTTP;
	  $this->boot();
 }
 
 /**
  * To inherit...
  */
 /**
  * Run CLI
  */
 abstract protected function boot();
 abstract protected function _exec($args);
 abstract protected function force_state($state);
 /**
  * Build CLI
  */
 abstract public function add_command($command, callable $callable);
 abstract public function add_option_handler(mixed $settings);
 abstract public function add_flag(mixed $settings);
 abstract public function add_operator($operator, \Closure $closure);
 

 
 
 /**
 * Core
 */
  
 public function state($state = null){
 	 if($state !== null)$this->force_state($state);
	 return $state;
 }
  
 
 public function normalize($cml){
   //	return preg_replace("/[\s+]/", " ", trim($cml));
   return $cml;
 }
 
 

 public function parse($cml){
   $this->state = 'parsing';
   //$args = explode(' ', $this->normalize($cml));
   
   // from stackoverflow !!! 
$pattern = <<<REGEX
/
(?:
  " ((?:(?<=\\\\)"|[^"])*) "
|
  ' ((?:(?<=\\\\)'|[^'])*) '
|
  (\S+)
)
/x
REGEX;
   
   preg_match_all($pattern, $cml, $matches, PREG_SET_ORDER);
   $args = array();
   foreach ($matches as $match) {
    if (isset($match[3])) {
        $args[] = $match[3];
    } elseif (isset($match[2])) {
        $args[] = str_replace(array('\\\'', '\\\\'), array("'", '\\'), $match[2]);
    } else {
        $args[] = str_replace(array('\\"', '\\\\'), array('"', '\\'), $match[1]);
    }
   }   
   
   
   $args = $this->arguments( $args );
   return $args; 
 }
 
 
 public function exe($cml = ''){
 	 global $argv;
 	 $this->state = 'preparing';
 	 $this->args = ($this->mode === self::MODE_CLI) ? $this->arguments( $argv ) : $this->parse($cml);
	 $this->state = 'executing';
	 $this->_exec($this->args);
	 
	 return $this;
 }
 

 
 protected function arguments( $args = null )
 {
  if(!is_array($args) && is_array($this->args))$args = $this->args;	
	
  if($this->mode === self::MODE_CLI)array_shift( $args );
  $command = array_shift( $args );
  $endofoptions = FALSE;

  $ret = array
    (
    'commands' => array(),
    'options' => array(),
    'flags'    => array(),
    'arguments' => array(),
    );

  while( $arg = array_shift($args) )
  {

    if($endofoptions)
    {
      $ret['arguments'][] = $arg;
      continue;
    }

    if ( substr( $arg, 0, 2 ) === '--' )
    {

      if(!isset ($arg[3]))
      {
        $endofoptions = true; 
        continue;
      }

      $value = "";
      $com   = substr( $arg, 2 );

      if(strpos($com,'='))
       {
          list($com,$value) = explode("=",$com,2);
       }


     if( (substr($value, 0, 1) === '"' || substr($value, 0, 1) === "'") && substr($value, strlen($value)-1, 1) !== "'"  && substr($value, strlen($value)-1, 1) !== '"' )
      {
        while(count($args) > 0)
         {
           $v = array_shift($args);
           $value .= $v.' ';
           if(strpos($v,'"') !== FALSE || strpos($v,"'") !== FALSE)break;
         }
        $value = trim($value, ' ');
        $value = trim($value, '"');
        $value = trim($value, "'");
     }


      $value = trim($value , '"');

      $ret['options'][$com] = !empty($value) ? $value : true;
      continue;

    }

    if ( substr( $arg, 0, 1 ) === '-' && substr( $arg, 0, 2 ) != '--')
    {
      for ($i = 1; isset($arg[$i]) ; $i++)
        $ret['flags'][] = $arg[$i];
      continue;
    }

    $ret['commands'][] = $arg;
    continue;
  }


 array_unshift($ret['commands'], $command);

 if(count($ret['commands']) > 1)
 {
   foreach($ret['commands'] as $k => $com)
    {
      if($k <= 0)continue;
      $ret['arguments'][] = $com;
      unset($ret['commands'][$k]);
    }
 }

 return $ret;
 }



}
