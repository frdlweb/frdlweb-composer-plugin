<?php
/**
* 
*    1.3.6.1.4.1.37553.8.1.8.8.5.65::plugout
* 
*/
	 	\webfan\Loader::top() 
          -> addPsr4('frdl\ApplicationComposer\\', __DIR__ . DIRECTORY_SEPARATOR .'..' .DIRECTORY_SEPARATOR . 'ApplicationComposer' .DIRECTORY_SEPARATOR, false) 
          -> class_mapping_add(
                  '\frdl\xGlobal\webfan',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . 'webfan.fexe.php', $success)           
          ;
       
       
       
        \frdl\webfan\App::God()
               -> addClass('\frdl\ApplicationComposer\DBSchema', '\frdl\_db',true, $success)
          
          ;