<?php
error_reporting(E_ALL);

$dir_lib =  realpath(__DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
$file_autoloader = $dir_lib 
         . 'frdl' . DIRECTORY_SEPARATOR . 'webfan' . DIRECTORY_SEPARATOR 
         . 'Autoloading'  . DIRECTORY_SEPARATOR . 'SourceLoader.php';
		 
require $file_autoloader;
require $dir_lib . 'frdl' . DIRECTORY_SEPARATOR . 'webfan'.DIRECTORY_SEPARATOR . 'App.php';

frdl\webfan\App::God(true, "dev-test", 'frdl\webfan\Autoloading\SourceLoader', false)
    ->init_autoloader(true)
    -> addNamespace('frdl\\', $dir_lib . 'frdl'.DIRECTORY_SEPARATOR,  false)  
    -> addNamespace('webfan\\', $dir_lib . 'webfan'.DIRECTORY_SEPARATOR,  false)
    -> addNamespace('webdof\\', $dir_lib . 'webdof'.DIRECTORY_SEPARATOR,  false)
    -> addNamespace('', $dir_lib,  true)  
 ->j()	
   -> addClass('frdl\aSQL\Engines\Terminal\Test', 'Terminal',  true )
;

$cmd = 'erstelle FS localhost @username:password `/path/to/`.`file` `FIELD`=VALUE -c --opt=val';
$Terminal = new Terminal();
$batch = $Terminal->parse($cmd);
echo 'Test command line:'."\n\n".$cmd."\n\n".'Parsed:'."\n"
     .'<pre>'
     .print_r($batch,true)
     .'</pre>';
