<?php
/**
 * 
 * Copyright  (c) 2016, Till Wehowski
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
 *  @author 	Till Wehowski <software@frdl.de>
 *  @copyright 	2016 Copyright (c) Till Wehowski
 *  @version 	2.0
 *    
 */
 //error_reporting(E_ALL);
 //ini_set('display_errors',1);
 
call_user_func((function(){ 



if(true===version_compare(PHP_VERSION, '5.5', '>=')) {
$fF = function(){
yield (__DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'frdl' . DIRECTORY_SEPARATOR . 'webfan' . DIRECTORY_SEPARATOR .  'App.php');
yield (__DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'frdl' . DIRECTORY_SEPARATOR . 'common'. DIRECTORY_SEPARATOR . 'Stream.php');
yield (__DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'frdl' . DIRECTORY_SEPARATOR . 'webfan'. DIRECTORY_SEPARATOR . 'Autoloading' . DIRECTORY_SEPARATOR .  'Loader.php');
yield (__DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'frdl' . DIRECTORY_SEPARATOR . 'webfan'. DIRECTORY_SEPARATOR . 'Autoloading' . DIRECTORY_SEPARATOR . 'SourceLoader.php');
yield (__DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'frdl' . DIRECTORY_SEPARATOR . 'webfan'. DIRECTORY_SEPARATOR . 'Autoloading' . DIRECTORY_SEPARATOR . 'Autoloader.php');
}; 	
}else{
$fF = function(){
return array(	
 __DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'frdl' . DIRECTORY_SEPARATOR . 'webfan' . DIRECTORY_SEPARATOR .  'App.php',
 __DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'frdl' . DIRECTORY_SEPARATOR . 'common'. DIRECTORY_SEPARATOR . 'Stream.php',
 __DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'frdl' . DIRECTORY_SEPARATOR . 'webfan'. DIRECTORY_SEPARATOR . 'Autoloading' . DIRECTORY_SEPARATOR .  'Loader.php',
 __DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'frdl' . DIRECTORY_SEPARATOR . 'webfan'. DIRECTORY_SEPARATOR . 'Autoloading' . DIRECTORY_SEPARATOR . 'SourceLoader.php',
 __DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'frdl' . DIRECTORY_SEPARATOR . 'webfan'. DIRECTORY_SEPARATOR . 'Autoloading' . DIRECTORY_SEPARATOR . 'Autoloader.php',
);
}; 	
}

foreach($fF() as $file) {
  require $file;
}
 
 


\frdl\webfan\Autoloading\SourceLoader::top() 
  -> addPsr4('frdl\\', __DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'frdl' .DIRECTORY_SEPARATOR, true) 
  -> addPsr4('webfan\\', __DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'webfan' .DIRECTORY_SEPARATOR, true) 
  -> addPsr4('webdof\\', __DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . 'webdof' .DIRECTORY_SEPARATOR, true)  
  
   -> addPsr4('\\', __DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . '' .DIRECTORY_SEPARATOR, false) 
  ;
  
\frdl\webfan\App::God(true, 'frdl\webfan\Autoloading\Autoloader','AC boot') 

;

if(defined('FRDL_WEBFAN_DIR_MAIN'))define('FRDL_WEBFAN_DIR_MAIN', __DIR__ . DIRECTORY_SEPARATOR);

\frdl\webfan\App::God()-> addClass('\frdl\webfan\Serialize\Binary\bin', '\frdl\bs',true, $success);
\frdl\webfan\App::God()-> addClass('\webdof\Http\Client', '\frdl\Broxy',true, $success);

\frdl\webfan\Autoloading\SourceLoader::repository('webfan');



call_user_func(($bootfiles=function($src){
	$dir = opendir($src);
      while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
               $bootfiles($src . '/' . $file);
            }
            else {
                include $src . '/' . $file;
            }
        }
      }
      closedir($dir);
}), __DIR__ . DIRECTORY_SEPARATOR . 'lib' .DIRECTORY_SEPARATOR . '.inc' . DIRECTORY_SEPARATOR);



}));

