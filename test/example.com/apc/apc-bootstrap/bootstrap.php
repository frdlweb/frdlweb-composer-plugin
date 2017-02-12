<?php
/* include this on top of every project or entry point! */
include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'O.php';
include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'A.php';
include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'App.php';
include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'Stream.php';
include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'Loader.php';
include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'SourceLoader.php';
include __DIR__.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'Autoloader.php';

\frdl\webfan\Autoloading\SourceLoader::repository('frdl'); 

\frdl\webfan\App::God(true, 'frdl\webfan\Autoloading\Autoloader','AC boot') 

;


  \frdl\webfan\Autoloading\SourceLoader::top()
       ->addPsr4('\\', __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'apc'.DIRECTORY_SEPARATOR.'Psr4'.DIRECTORY_SEPARATOR, false)  
       ->addPsr0('\\', __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'apc'.DIRECTORY_SEPARATOR.'Psr0'.DIRECTORY_SEPARATOR, false)  
 ;   
