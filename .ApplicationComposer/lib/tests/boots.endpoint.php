<?php



$file_autoloader = __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR 
         . 'frdl' . DIRECTORY_SEPARATOR . 'webfan' . DIRECTORY_SEPARATOR . 'Autoloading'  . DIRECTORY_SEPARATOR . 'SourceLoader.php';

$dir_lib =  __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR ;


require $file_autoloader;
require $dir_lib . 'frdl' . DIRECTORY_SEPARATOR . 'webfan'.DIRECTORY_SEPARATOR . 'App.php';

 $SourceLoader = new frdl\webfan\Autoloading\SourceLoader(); 
 
 $App = frdl\webfan\App::getInstance(true, "dev-test");
 
 $SourceLoader->autoload_register();
 
 $SourceLoader->addNamespace('\frdl\\', $dir_lib . 'frdl'.DIRECTORY_SEPARATOR,  true);
 $SourceLoader->addNamespace('\webfan\\', $dir_lib . 'webfan'.DIRECTORY_SEPARATOR,  true);
 $SourceLoader->addNamespace('\webdof\\', $dir_lib . 'webdof'.DIRECTORY_SEPARATOR,  true);
 $SourceLoader->addNamespace('\Psr\\', $dir_lib . 'Psr'.DIRECTORY_SEPARATOR,  true);
 
 
 $cli = '$php myscript.php arg1 -arg2=val2 --arg3="arg3 testquotes" --testf="dies ist teststring" -arg4 --arg5-arg6=false';
 
 
 $Terminal = new frdl\aSQL\Engines\Terminal\WebConsole();
  
  

 echo $cli.'<pre>'.print_r( $Terminal->exe($cli)->test_query(),true).'</pre>';
 
 
 