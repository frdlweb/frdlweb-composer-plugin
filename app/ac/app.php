<?php

			 			    if(!isset($this) ||
			 			      ( 
			 			         !is_subclass_of($this, 'frdl\xGlobal\fexe')  
			 			      )){
			 			      	 $str = 'Invalid context in '.__FILE__.' '.__LINE__;
			 			      	 trigger_error($str, E_USER_WARNING);
			 			      	 die();
			 			      	}
			 			      	
$this->template = file_get_contents(__DIR__. DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'main.html');	

$this->data['template_main_options']['js'][]='http://webfan.de/cdn/frdl/flow/components/frdl/intent/webintents.js';		 			      	