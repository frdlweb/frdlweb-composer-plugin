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
namespace frdl\aSQL\Engines\Terminal;

class ACConsole extends CLI
{
	
  protected function boot(){}
  protected function _exec($args){}
  protected function force_state($state){}
  public function parseQuery(){} 
  public function validateQuery(){}
 /**
  * Build CLI
  */
  public function add_command($command, callable $callable){}
  public function add_option(mixed $settings){}
  public function add_flag(mixed $settings){}
  public function add_argument(mixed $settings){}
	
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
