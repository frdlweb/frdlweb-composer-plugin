<?php
error_reporting(E_ALL);

$dir_lib =  realpath(__DIR__. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

require $dir_lib . 'frdl' . DIRECTORY_SEPARATOR . 'webfan' . DIRECTORY_SEPARATOR . 'Autoloading'  . DIRECTORY_SEPARATOR . 'SourceLoader.php';
require $dir_lib . 'frdl' . DIRECTORY_SEPARATOR . 'webfan'.DIRECTORY_SEPARATOR . 'App.php';


$Server_IP = frdl\webfan\App::God(true, "Demo of the extensible style functionallity 'Lambada crank'", 'frdl\webfan\Autoloading\SourceLoader', false)
      ->init_autoloader(true)
	    -> addNamespace('', $dir_lib ,  true)  
        -> addNamespace('webdof\\', $dir_lib . 'webdof'.DIRECTORY_SEPARATOR,  false)
        -> addNamespace('webfan\\', $dir_lib . 'webfan'.DIRECTORY_SEPARATOR,  false)
        -> addNamespace('frdl\\', $dir_lib . 'frdl'.DIRECTORY_SEPARATOR,  false)
   ->j()
	->{'$'}('MyIdentifier', (function($arg){$a = func_get_args(); echo $a[0];}) )
	-> {'MyIdentifier'}('Hello world')
    ->{'$'}('-i!', array( new \webfan\Install(),'run'))	
	->{'-i!'}() 
	    ->addClass('frdl\aSQL\Engines\Terminal\aSQLCommand', 'webfan\Terminal',  true )
	    ->addFunc('getServerIp',(function ($all = true){
		                       $i = gethostbynamel($_SERVER['SERVER_NAME']);
		                       if($all === false)return ((isset($i['ips'][0])) ? $i['ips'][0] : '0.0.0.0');
							   return $i;
	                    }))->getServerIp()
	;

echo ' '.$Server_IP;
