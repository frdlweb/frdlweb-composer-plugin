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
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the frdl/webfan.
 * 4. Neither the name of frdl/webfan nor the
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
 *     
 *   @vendor     frdl
 *   @package    webfan Application Composer Backend
 *   @filename   webfan.fexe.php
 *   @todo
 * 
 *   @state:     test/development
 *  
 * 
 */
namespace frdl\xGlobal; 

/* BEGIN CONFIGSECTION */
error_reporting(E_ALL);
ini_set('display_errors', 1);

chdir(__DIR__. DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
/* END CONFIGSECTION */

/* BEGIN BOOTSECTION   */
if(!class_exists('\frdl\webfan\App')){
	
 if(!defined( __NAMESPACE__.'\\'.'__BOOTFILE__')) {
 	define( __NAMESPACE__.'\\'.'__BOOTFILE__', __DIR__ . DIRECTORY_SEPARATOR . '..'.DIRECTORY_SEPARATOR . '.ApplicationComposer'.DIRECTORY_SEPARATOR .'bootstrap.php');
 }


 if(!in_array( __BOOTFILE__, get_included_files())){
 	
  if(!file_exists(  __BOOTFILE__)){
	$str = 'App '.basename(__FILE__).' is not installed cortrectly! File ' .__BOOTFILE__.' not found. 
	<br />
	Please read <a target="_blank" href="https://github.com/frdl/webfan/wiki/Installation">Installation instruction</a>!';
	trigger_error($str, E_USER_ERROR);
	echo $str; 
	die();
  }
 	
 	require  __BOOTFILE__;
 }
 
}
 
 if(!class_exists('\frdl\webfan\App')){
 	$str = 'App '.basename(__FILE__).' is not installed cortrectly! Class \frdl\webfan\App not found. 
	<br />
	Please read <a target="_blank" href="https://github.com/frdl/webfan/wiki/Installation">Installation instruction</a>!';
	trigger_error($str, E_USER_ERROR);
	echo $str; 
	die();	
 }
 
 
/* END BOOTSECTION */


require __DIR__ . DIRECTORY_SEPARATOR . 'server.php';

 
		try{
			 ini_set('display_errors', 0);
			 $fexe = new webfanApp(__FILE__, __COMPILER_HALT_OFFSET__);
             $fexe->run();
		}catch(\Exception $e){
			trigger_error($e->getMessage(), E_USER_WARNING);
		}  
