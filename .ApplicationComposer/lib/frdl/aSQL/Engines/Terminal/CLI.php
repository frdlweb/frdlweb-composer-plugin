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
 *  @author 	Till Wehowski <software@frdl.de>
 *  @package    webfan://frdl.aSQL.Engines.Terminal.CLI.code
 *  @uri        /v1/public/software/class/webfan/frdl.aSQL.Engines.Terminal.CLI/source.php
 *  @version 	1.0.0.5
 *  @file       frdl\aSQL\Engines\Terminal\CLI.code.php
 *  @role       Command Line Parser
 *  @copyright 	2015 Copyright (c) Till Wehowski
 *  @license 	http://look-up.webfan.de/bsd-license bsd-License 1.3.6.1.4.1.37553.8.1.8.4.9
 *  @link 	    http://interface.api.webfan.de/v1/public/software/class/frdl/frdl.aSQL.Engines.Terminal.CLI/doc.html
 *  @OID        1.3.6.1.4.1.37553.8.1.8.8 webfan-software
 *  @requires	PHP_VERSION 5.3 >= 
 *  @requires   webfan://frdl.webfan.Autoloading.SourceLoader.code
 *  @api        http://api.webfan.de
 *  @reference  http://www.webfan.de/install/
 * 
 */
namespace frdl\aSQL\Engines\Terminal;

abstract class CLI
{

 const MODE_CLI = 1;
 const MODE_HTTP = 2;

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
 const DELIM = ';';
 const SPACE = ' ';
 
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
 abstract public function add_command($command, callable $callable);
 /*
 abstract public function add_option(\mixed $settings);
 abstract public function add_flag(\mixed $settings);
 abstract public function add_argument(\mixed $settings);
 */

 
 
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
 
 

 public function unparse_args($args){
 	$tokens = array();

 	$tokens[$args['command']['pos']] = $args['command']['cmd'];

	
    foreach($args['options'] as $k => $opt){
 		$tokens[$opt['pos']] = '--'.$opt['opt'].'='.$opt['quotes'].$opt['value'].$opt['quotes'];
 		// $tokens[$opt['pos']] = '--'.$opt['opt'].'="'.$opt['value'].'"';
 	}
	
    foreach($args['flags'] as $k => $f){
    	 if(!isset($tokens[$f['pos']]))$tokens[$f['pos']] = '-'; 
 		 $tokens[$f['pos']] .= $f['flag'];
 	}		
	
   foreach($args['arguments'] as $k => $c){
 		 $tokens[$c['pos']] = $c['cmd'];
 	}	
	
	
	ksort($tokens);
	return implode(self::SPACE, $tokens);
 } 
 

 public function unparse($batch){
 	$s = array();
 	foreach($batch as $pos => $args){
 		$s[] = $this->unparse_args($args);
 	}
	return implode(self::DELIM."\r\n", $s);
 } 
 
 
 public function parse($cml = null){
   $cml = (null === $cml) ? $this->IN : $cml;
   
   $cml = rtrim($cml, ';|'.self::SPACE);
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
	 $this->IN = ($this->mode === self::MODE_CLI) ? implode(self::DELIM, $argv) : $cml;
	 $this->parse();
	 $this->_exec($this->batch);
	 return $this;
 }
 

 
 protected function arguments( $args = null )
 {
  if(!is_array($args) && is_array($this->args))$args = $this->args;	
	
  if($this->mode === self::MODE_CLI)array_shift( $args );
  $escape = false;

  $ret = array
    (
    'command' => array(),
    'commands' => array(),
    'options' => array(),
    'flags'    => array(),
    'arguments' => array(),
    );
 
   $k = -1;

  while( $arg = array_shift($args) )
  {
   $k++;

    if ( substr( $arg, 0, 2 ) === '--' )
    {

      $value = "";
      $com   = substr( $arg, 2 );

      if(strpos($com,'='))
       {
          list($com,$value) = explode("=",$com,2);
       }

     $quotes = ''; 
	 
     if( (substr($value, 0, 1) === '"' || substr($value, 0, 1) === "'") && substr($value, strlen($value)-1, 1) !== "'"  && substr($value, strlen($value)-1, 1) !== '"' )
      {
        $quotes = substr($value, 0, 1);
        while(count($args) > 0)
         {
           $v = array_shift($args);
		   if(strpos($v,'\\') !== FALSE)$escape = true;
           $value .= $v.self::SPACE;  
           
           if($escape === false && ((strpos($v,'"') !== FALSE && $quotes === '"') || (strpos($v,"'") !== FALSE && $quotes === "'")))break;
		   if($escape === true && ((strpos($v,'"') !== FALSE && $quotes === '"') || (strpos($v,"'") !== FALSE && $quotes === "'")))$escape = false;
         }
		 
        $value = trim($value, self::SPACE);
        $value = trim($value, '"');
        $value = trim($value, "'");
     }


      $value = trim($value , '"\'');
	  if(empty($value))$value = null;

      $ret['options'][] = array(
	        'opt' => $com,
	        'value' => $value,
	        'pos' => $k,
	        'quotes'=>$quotes,
	   );
      continue;

    }

    if ( substr( $arg, 0, 1 ) === '-' && substr( $arg, 0, 2 ) != '--')
    {
      for ($i = 1; isset($arg[$i]) ; $i++)
        $ret['flags'][$arg[$i]] = array(
               'flag' =>  $arg[$i],
               'pos' => $k,
	  
	     );
      continue;
    }

    $ret['commands'][] =array( 'cmd' => $arg, 'pos' => $k,);
    continue;
  }

 $ret['arguments'] = $ret['commands'];
 $ret['command'] = array_shift($ret['arguments']);
 unset($ret['commands']);


 return $ret;
 }



}
