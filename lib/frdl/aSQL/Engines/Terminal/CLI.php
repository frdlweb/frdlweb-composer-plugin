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
/** 
 const REGEX_CML = <<<REGEX
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

 * */
 const REGEX_CML = <<<REGEX
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
 
 
 const REGEX_DELIM = '/^;|\|$/';
 
 
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
		
 protected $IN;
 protected $tokens;
 protected $batch;
 
	
 
 /**
  * processing
  */	
 	
 function __construct(){
 	  $this->IN = '';
	  $this->batch = array();
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
 abstract public function parseQuery();  
 abstract public function validateQuery(); 
 /**
  * Build CLI
  */
 abstract public function add_command(mixed $settings);
 abstract public function add_option(mixed $settings);
 abstract public function add_flag(mixed $settings);
 abstract public function add_argument(mixed $settings);
 

 
 
 /**
 * Core
 */
  
 public function state($state = null){
 	 if($state !== null)$this->force_state($state);
	 return $this->state;
 }
  
 
 public function normalize($cml){
   //	return preg_replace("/[\s+]/", " ", trim($cml));
   return $cml;
 }
 
 

 public function parse($cml = null){
   $cml = (null === $cml) ? $this->IN : $cml;
   
   $cml = rtrim($cml, ';| ');
   $cml .= ';';
   
   $batch = array();
   $p = 0; 
   $batch[$p] = array();
     
   preg_match_all(self::REGEX_CML, $cml, $matches, PREG_SET_ORDER);
   
   $args = array();

   foreach ($matches as $pos => $match) {
    if (isset($match[3])) {
        $a = $match[3];
    } elseif (isset($match[2])) {
        $a = str_replace(array('\\\'', '\\\\'), array("'", '\\'), $match[2]);
    } else {
        $a = str_replace(array('\\"', '\\\\'), array('"', '\\'), $match[1]);
    }
	
      if(preg_match(self::REGEX_DELIM, $a)){
	  	 $batch[$p] = $this->arguments( $args );
		 $args = array();
	     $p++;
	  }elseif(substr($a,-1)===';' || substr($a,-1)==='|'){
     	 $a = rtrim($a, ';|');
		 $args[] = $a;
	  	 $batch[$p] = $this->arguments( $args );
		 $args = array();
	     $p++;
	  }else{	  	
	 	  $args[] = $a;
	  }
	  
   }   
   
   
   $this->batch = $batch;
   return $batch; 
 }
 
 
 public function exe($cml = ''){
 	 global $argv;
	 
	 $this->IN = $cml;
	 if($this->mode === self::MODE_CLI){
	 	$this->batch = array();
		$this->batch[] =  $this->arguments( $argv );
	 }else{
	 	$this->parse();
	 }
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
