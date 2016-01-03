<?php
/**
* 
*    1.3.6.1.4.1.37553.8.1.8.8.5.65::plugin
* 
*/
 	\webfan\Loader::top() 



       
       /* geraintluff/jsv4  */
          -> class_mapping_add(
                  'Jsv4',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'geraintluff'
                      .DIRECTORY_SEPARATOR . 'jsv4-php' 
                      .DIRECTORY_SEPARATOR . 'master' 
                      .DIRECTORY_SEPARATOR . 'jsv4-php-master' 
                      .DIRECTORY_SEPARATOR . 'jsv4-php-master' 
                     . DIRECTORY_SEPARATOR .'jsv4.php', $success) 
          
            -> class_mapping_add(
                  'Jsv4Error',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'geraintluff'
                      .DIRECTORY_SEPARATOR . 'jsv4-php' 
                      .DIRECTORY_SEPARATOR . 'master' 
                      .DIRECTORY_SEPARATOR . 'jsv4-php-master' 
                      .DIRECTORY_SEPARATOR . 'jsv4-php-master' 
                     . DIRECTORY_SEPARATOR .'jsv4.php', $success)         
          
            -> class_mapping_add(
                  'SchemaStore',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'geraintluff'
                      .DIRECTORY_SEPARATOR . 'jsv4-php' 
                      .DIRECTORY_SEPARATOR . 'master' 
                      .DIRECTORY_SEPARATOR . 'jsv4-php-master' 
                      .DIRECTORY_SEPARATOR . 'jsv4-php-master' 
                     . DIRECTORY_SEPARATOR .'schema-store.php', $success)         
          
          ;
          


 	\webfan\Loader::top() 

       
       /* outlandish/sync  */
          -> class_mapping_add(
                  'Outlandish\AbstractSync',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'outlandishideas'
                      .DIRECTORY_SEPARATOR . 'sync' 
                      .DIRECTORY_SEPARATOR . 'master' 
                      .DIRECTORY_SEPARATOR . 'AbstractSync.php', $success) 
          
            -> class_mapping_add(
                  'Outlandish\Client',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'outlandishideas'
                      .DIRECTORY_SEPARATOR . 'sync' 
                      .DIRECTORY_SEPARATOR . 'master' 
                      .DIRECTORY_SEPARATOR . 'Client.php', $success)      
          
            -> class_mapping_add(
                  'Outlandish\Server',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'outlandishideas'
                      .DIRECTORY_SEPARATOR . 'sync' 
                      .DIRECTORY_SEPARATOR . 'master' 
                      .DIRECTORY_SEPARATOR . 'Server.php', $success)     
          
          ;
          
