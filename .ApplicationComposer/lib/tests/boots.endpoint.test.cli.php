<?php
 
 /**
  * Boot Sections
  */
error_reporting(E_ALL);


 /**
  * boot.inc
  */
$dir_lib =  __DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR. 'lib'  . DIRECTORY_SEPARATOR; 

$file_autoloader = $dir_lib.'frdl' .DIRECTORY_SEPARATOR.'webfan'.DIRECTORY_SEPARATOR. 'Autoloading'.DIRECTORY_SEPARATOR.'SourceLoader.php';

require $file_autoloader;
require $dir_lib . 'frdl' . DIRECTORY_SEPARATOR . 'webfan'.DIRECTORY_SEPARATOR . 'App.php';

/**
 *    ->autoload_register()
 *  registers the following autoloaders: 
 * 
 *  
 *   	public function autoload_register(){
 *         $this->addLoader(array($this,'loadClass'), true, true);		
 * 	   $this->addLoader(array($this,'classMapping'), true, false);	
 *         $this->addLoader(array($this,'patch_autoload_function'), true, false);	
 *         $this->addLoader(array($this,'autoloadClassFromServer'), true, false);	
 *         return $this;
 * 	} 
 */

/**
 * init.autoloading
 * init.app
 * load.Psr 
 * 
 * psr-4
 * psr-4 load.__vendorplugin
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
 frdl\webfan\App::God() -> addClass('frdl\aSQL\Engines\Terminal\Test', 'Terminal',  true );
			 
 /**
  * class mapping autoloading example
  */ 
 $SourceLoader->class_mapping_add('\MyImportedDummyClass', 
    $dir_lib . '__vendorplugin'
    .DIRECTORY_SEPARATOR.'{VENDOR}'
    .DIRECTORY_SEPARATOR.'{PACKAGEDIRECTORY}'
    .DIRECTORY_SEPARATOR.'src'
    .DIRECTORY_SEPARATOR.'MyClass.class.php',
  $success);

 $Terminal = new Terminal();
 $Terminal->test();



