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
          

   	\webfan\Loader::top()     
       /* phpclasses/sasl  */
          -> class_mapping_add(
                  'basic_sasl_client_class',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'phpclasses'
                      .DIRECTORY_SEPARATOR . 'sasl' 
                      .DIRECTORY_SEPARATOR . 'basic_sasl_client.php', $success) 
          
       
            -> class_mapping_add(
                  'cram_md5_sasl_client_class',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'phpclasses'
                      .DIRECTORY_SEPARATOR . 'sasl' 
                      .DIRECTORY_SEPARATOR . 'cram_md5_sasl_client.php', $success) 
       
            -> class_mapping_add(
                  'digest_sasl_client_class',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'phpclasses'
                      .DIRECTORY_SEPARATOR . 'sasl' 
                      .DIRECTORY_SEPARATOR . 'digest_sasl_client.php', $success) 
       
            -> class_mapping_add(
                  'login_sasl_client_class',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'phpclasses'
                      .DIRECTORY_SEPARATOR . 'sasl' 
                      .DIRECTORY_SEPARATOR . 'login_sasl_client.php', $success) 
                      
       
            -> class_mapping_add(
                  'ntlm_sasl_client_class',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'phpclasses'
                      .DIRECTORY_SEPARATOR . 'sasl' 
                      .DIRECTORY_SEPARATOR . 'ntlm_sasl_client.php', $success) 
       
            -> class_mapping_add(
                  'plain_sasl_client_class',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'phpclasses'
                      .DIRECTORY_SEPARATOR . 'sasl' 
                      .DIRECTORY_SEPARATOR . 'plain_sasl_client.php', $success) 
       
            -> class_mapping_add(
                  'sasl_interact_class',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'phpclasses'
                      .DIRECTORY_SEPARATOR . 'sasl' 
                      .DIRECTORY_SEPARATOR . 'sasl.php', $success) 
                      
            -> class_mapping_add(
                  'sasl_client_class',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'phpclasses'
                      .DIRECTORY_SEPARATOR . 'sasl' 
                      .DIRECTORY_SEPARATOR . 'sasl.php', $success) 
                      
                      
                      
                              
          ;
          
          
     	\webfan\Loader::top() 



       
       /* phpclasses/httpclient  */
          -> class_mapping_add(
                  'http_class',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'phpclasses'
                      .DIRECTORY_SEPARATOR . 'httpclient' 
                      .DIRECTORY_SEPARATOR . 'http.php', $success) 
  
                      
                              
          ;
          
 	\webfan\Loader::top() 



       
       /* phpclasses/oauth-api  */
          -> class_mapping_add(
                  'oauth_session_value_class',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'phpclasses'
                      .DIRECTORY_SEPARATOR . 'oauth-api' 
                      .DIRECTORY_SEPARATOR . 'oauth_client.php', $success) 
  
            -> class_mapping_add(
                  'oauth_client_class',
                       __DIR__ . DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' .DIRECTORY_SEPARATOR . '..' 
                      .DIRECTORY_SEPARATOR . 'packages' 
                      .DIRECTORY_SEPARATOR . 'phpclasses'
                      .DIRECTORY_SEPARATOR . 'oauth-api' 
                      .DIRECTORY_SEPARATOR . 'oauth_client.php', $success) 
  
                      
                                          
                              
          ;      
          
