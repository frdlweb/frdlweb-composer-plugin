<?php
 
 /**
  * Boot Sections
  */

  /**
   * Test / ignore
   */
error_reporting(E_ALL);


 /**
  * boot.inc
  */
$file_autoloader = __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR 
         . 'frdl' . DIRECTORY_SEPARATOR . 'webfan' . DIRECTORY_SEPARATOR . 'Autoloading'  . DIRECTORY_SEPARATOR . 'SourceLoader.php';
$dir_lib =  __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR ;

require $file_autoloader;
require $dir_lib . 'frdl' . DIRECTORY_SEPARATOR . 'webfan'.DIRECTORY_SEPARATOR . 'App.php';



/**
 * init.autoloading
 * init.app
 * load.Psr 
 * 
 * psr-8
 * psr-8 load.__vendorplugin
 */
 $SourceLoader = new frdl\webfan\Autoloading\SourceLoader(); 
 $App = frdl\webfan\App::getInstance(true, "dev-test");
 $SourceLoader->autoload_register() 
    -> addNamespace('\frdl\\', $dir_lib . 'frdl'.DIRECTORY_SEPARATOR,  true)  
    -> addNamespace('\webfan\\', $dir_lib . 'webfan'.DIRECTORY_SEPARATOR,  true)
    -> addNamespace('\webdof\\', $dir_lib . 'webdof'.DIRECTORY_SEPARATOR,  true)
    -> addNamespace('\Psr\\', $dir_lib . 'Psr'.DIRECTORY_SEPARATOR,  true)
    -> addNamespace('\JsonRPC\\', $dir_lib . '__vendorplugin'.DIRECTORY_SEPARATOR.'fguillot'.DIRECTORY_SEPARATOR.'JsonRPC'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR,  true);

 /** 
 * Alias class mapping
 */
 frdl\webfan\App::God() -> addClass('frdl\aSQL\Engines\Terminal\WebConsole', 'frdl\Terminal',  true );
			 

 $cmd = '$install --host=localhost --dir=/var/www/dir/lib/ --package="frdl/webfan" -w --update --db="LIQ:php;db=alias.ApplicationComposer" + # > log.default; ';

 $Terminal = new frdl\Terminal();
 echo '<pre>
 BEGIN
 '.$cmd.'
 '.print_r($Terminal->parse($cmd),true).'</pre>';
 
 
 echo '<pre>
 >Script end.
 END;
 </pre>';
