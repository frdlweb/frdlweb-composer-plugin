<?php
/**
 * Copyright  (c) 2015, Till Wehowski
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the frdl/webfan.
 * 4. Neither the name of frdl/webfan nor the
 *    names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY frdl/webfan ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL frdl/webfan BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * 
 *  @component abstract frdl\A
 * 
 */
 namespace frdl;
 
 abstract class A{
 	
  const FN_ASPECTS = 'aspects';	
     /**
    *  default $SEMR´s
	*  const  SERVER_ROUTER = {$cmd=SERVER} . {$format} . {$modul} . {$outputbuffers = explode(',')} 
	*/
	const TPL_SERVER_ROUTE = '{$cmd}.{$responseformat}.{$modul}.{$responsebuffers}';
    const SERVER_PAGE = 'SERVER.html.PAGE.buffered';
    const SERVER_HTML = 'SERVER.html.HTML.buffered';
    const SERVER_API = 'SERVER.?.API.format';
    const SERVER_404 = 'SERVER.html.404.buffered';
    const SERVER_JS = 'SERVER.js.JS.compressed,pragma';
    const SERVER_CSS = 'SERVER.css.CSS.compressed,pragma';
    const SERVER_IMG = 'SERVER.img.IMG.compressed,pragma';
	
    const SERVER_DEFAULT = self::SERVER_PAGE;
    	
  protected $ns_pfx = array('?' => array('frdl' => true),
              '$'=> array('frdl' => true), 
              '$'=> array('frdl' => true),
              '!'=> array('frdl' => true), 
              '#'=> array('frdl' => true), 
              '-'=> array('frdl' => true),
              '.'=> array('frdl' => true), 
              '+'=> array('frdl' => false), 
              ',' => array('frdl' => true)
          );	
  protected $wrappers;
  protected $shortcuts;
 	
  
 
  public function addShortCut ($short,  $long, $label = null){

		 
  	 array_walk($this->ns_pfx,function(&$v){
  	 	  if(!isset($v[\frdl\A::FN_ASPECTS])) $v[\frdl\A::FN_ASPECTS] = array(); 	 	
  	 });
  	 
  	    $ns = substr($short, 0, 1);
  	     if(!is_array($this->shortcuts))$this->shortcuts = array();
        $this->shortcuts[$short] = $long;
          
          if(isset($this->ns_pfx[$ns])){
		  	 if(!isset($this->ns_pfx[$ns][self::FN_ASPECTS]) || !is_array($this->ns_pfx[$ns][self::FN_ASPECTS])) $this->ns_pfx[$ns][self::FN_ASPECTS] = array(); 	
		  	 $aspect = array(
		  	   'label' => (is_string($label)) ? $label : $short,
		  	   'short' => $short,
		  	   'long' => $long
		  	 );
		  	$this->ns_pfx[$ns][self::FN_ASPECTS][$short] = $aspect;
		  }
		  
		 return $this;
  } 
	 
	
 /**
 * todo...
 * 
 */	
  protected function apply_fm_flow(){
  	 $args  = func_get_args();
     $THIS = &$this;
     $SELF = &$this;
         	
   \webfan\App::God() 	
      -> {'$'}('?session_started', (function($startIf = true) use ($THIS, $SELF) {
       	$r = false; 
        if ( php_sapi_name() !== 'cli' ) {
        if ( version_compare(phpversion(), '5.4.0', '>=') ) {
            $r =  session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
          } else {
             $r =  '' === session_id()  ? FALSE : TRUE;
          }
        }
        
        if(true === $startIf && false === $r){
          if(!session_start()){	
            if(isset($THIS) && isset($THIS->debug) && true === $THIS->debug) trigger_error('Cannot start session in '.basename(__FILE__).' '.__LINE__, E_USER_WARNING);
          }
		}
        
        
       return $r ;
        }) );
     

     $func_jsonP = (function($str) use ($THIS, $SELF) {
		 		       	 $r = (isset($THIS) && isset($THIS->data['data_out'])) ? $THIS->data['data_out'] : new \stdclass;
		 		       	 $r->type = 'print';
		 		       	 $r->out = $str;
      	                 $fnregex = "/^[A-Za-z0-9\$\.-_\({1}\){1}]+$/";
      	                 $callback = (isset($_REQUEST['callback']) && preg_match($fnregex, $_REQUEST['callback']))
		                   ? strip_tags($_REQUEST['callback'])
		                   : '';
		                   
		                   
                if($callback === ''){
         	            $o = json_encode($r);
                }  else {
                	       $r->callback = $callback;
                           $o = $callback.'(' . json_encode($r) . ')';
		                }
		                
		        return $o;
		 	});
		 	
		 	
   /**
   * http://php.net/manual/en/function.apache-request-headers.php#116645
   */      	
   \webfan\App::God() 	
      -> {'$'}('?request_headers', function() {
      	     if( function_exists('apache_request_headers') )return apache_request_headers();
                  foreach($_SERVER as $K=>$V){$a=explode('_' ,$K);
                        if(array_shift($a)==='HTTP'){
                           array_walk($a,function(&$v){$v=ucfirst(strtolower($v));});
                           $retval[join('-',$a)]=$V;}
                  } 
             return $retval;
          }
      );
        	
        	
	     \webfan\App::God() 
            -> {'$'}('$.sem.parse', function($sem) use ($THIS, $SELF) {
            	    $str = $SELF::TPL_SERVER_ROUTE;
            	    foreach($sem as $k => $v){
						$s = (is_array($v)) ? implode(',', $v) : $v;
						$str = str_replace('{$'.$k.'}', $s, $str);
					}
            	    return $str;
            	})
            	// '{$cmd}.{$responseformat}.{$modul}.{$responsebuffers}'; 	
            -> {'$'}('$.sem.unparse', function(&$sem, $route) use ($THIS, $SELF) {
            	    $seg = explode('.', $route);
            	    $sem['cmd'] =  array_shift($seg);
            	    $sem['responseformat'] =  array_shift($seg);
            	    $sem['modul'] =   array_shift($seg);
            	    $sem['responsebuffers'] = explode(',',array_shift($seg));
            	    $sem['.nodes'] =$seg;
                    return $THIS;
            	})
            	
            	
            -> {'$'}('$.sem->getFomatterMethod', (function($format){
            	 if('jsonp' !== $format && 'json' !== $format)return false;
                     return '$.sem.format->'.$format;
            	}))	
            -> {'$'}('$.sem.format->json', $func_jsonP )
            -> {'$'}('$.sem.format->jsonp', $func_jsonP)  
            /**
			* todo   css,txt,php,bin,dat,js,img,....
			*/
            -> {'$'}('$.sem.get->mime', (function($format = null, $file = null, $apply = true, $default = '') use ($THIS, $SELF) {
            $file = ((null===$file || !is_string($file)) ? \webdof\wURI::getInstance()->getU()->file : $file); 	
            if(true === $apply)$THIS->format = $default;
            
   	        $mime_types = array(
            '' =>array( 'text/html',),
            'frdl' =>array( 'application/frdl-bin',),
            'jpg' => array('image/jpeg', ),
            'jpeg' => array('image/jpeg',),
            'jpe' => array('image/jpeg',),
            'gif' => array('image/gif',),
            'png' => array('image/png',),
            'bmp' =>array( 'image/bmp',),
            'flv' => array('video/x-flv',),
            'js' => array('application/x-javascript',),
            'json' =>array( 'application/json',),
            'jsonp' =>array( 'application/x-javascript',),
            'tiff' => array('image/tiff',),
            'css' =>array( 'text/css',),
            'xml' => array('application/xml',),
            'doc' => array('application/msword',),
            'docx' => array('application/msword',),
            'xls' =>array( 'application/vnd.ms-excel',),
            'xlm' => array('application/vnd.ms-excel',),
            'xld' => array('application/vnd.ms-excel',),
            'xla' => array('application/vnd.ms-excel',),
            'xlc' => array('application/vnd.ms-excel',),
            'xlw' => array('application/vnd.ms-excel',),
            'xll' => array('application/vnd.ms-excel',),
            'ppt' => array('application/vnd.ms-powerpoint',),
            'pps' => array('application/vnd.ms-powerpoint',),
            'rtf' => array('application/rtf',),
            'pdf' => array('application/pdf',),
            'html' =>array( 'text/html',),
            'htm' => array('text/html',),
            'php' => array('text/html',),
            'txt' => array('text/plain',),
            'mpeg' => array('video/mpeg',),
            'mpg' => array('video/mpeg',),
            'mpe' => array('video/mpeg',),
            'mp3' =>array( 'audio/mpeg3',),
            'wav' => array('audio/wav',),
            'aiff' =>array('audio/aiff',),
            'aif' =>array( 'audio/aiff',),
            'avi' => array('video/msvideo',),
            'wmv' => array('video/x-ms-wmv',),
            'mov' => array('video/quicktime',),
            'zip' =>array( 'application/zip',),
            'tar' => array('application/x-tar',),
            'swf' => array('application/x-shockwave-flash',),
            'odt' => array('application/vnd.oasis.opendocument.text',),
            'ott' => array('application/vnd.oasis.opendocument.text-template',),
            'oth' =>array( 'application/vnd.oasis.opendocument.text-web',),
            'odm' => array('application/vnd.oasis.opendocument.text-master',),
            'odg' => array('application/vnd.oasis.opendocument.graphics',),
            'otg' => array('application/vnd.oasis.opendocument.graphics-template',),
            'odp' =>array( 'application/vnd.oasis.opendocument.presentation',),
            'otp' => array('application/vnd.oasis.opendocument.presentation-template',),
            'ods' => array('application/vnd.oasis.opendocument.spreadsheet',),
            'ots' => array('application/vnd.oasis.opendocument.spreadsheet-template',),
            'odc' => array('application/vnd.oasis.opendocument.chart',),
            'odf' => array('application/vnd.oasis.opendocument.formula',),
            'odb' => array('application/vnd.oasis.opendocument.database',),
            'odi' => array('application/vnd.oasis.opendocument.image',),
            'oxt' => array('application/vnd.openofficeorg.extension',),
            'docx' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document',),
            'docm' => array('application/vnd.ms-word.document.macroEnabled.12',),
            'dotx' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.template',),
            'dotm' => array('application/vnd.ms-word.template.macroEnabled.12',),
            'xlsx' => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',),
            'xlsm' => array('application/vnd.ms-excel.sheet.macroEnabled.12',),
            'xltx' => array('application/vnd.openxmlformats-officedocument.spreadsheetml.template',),
            'xltm' => array('application/vnd.ms-excel.template.macroEnabled.12',),
            'xlsb' => array('application/vnd.ms-excel.sheet.binary.macroEnabled.12',),
            'xlam' => array('application/vnd.ms-excel.addin.macroEnabled.12',),
            'pptx' => array('application/vnd.openxmlformats-officedocument.presentationml.presentation',),
            'pptm' => array('application/vnd.ms-powerpoint.presentation.macroEnabled.12',),
            'ppsx' =>array( 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',),
            'ppsm' => array('application/vnd.ms-powerpoint.slideshow.macroEnabled.12',),
            'potx' => array('application/vnd.openxmlformats-officedocument.presentationml.template',),
            'potm' => array('application/vnd.ms-powerpoint.template.macroEnabled.12',),
            'ppam' => array('application/vnd.ms-powerpoint.addin.macroEnabled.12',),
            'sldx' => array('application/vnd.openxmlformats-officedocument.presentationml.slide',),
            'sldm' => array('application/vnd.ms-powerpoint.slide.macroEnabled.12',),
            'thmx' => array('application/vnd.ms-officetheme',),
            'onetoc' => array('application/onenote',),
            'onetoc2' =>array( 'application/onenote',),
            'onetmp' =>array( 'application/onenote',),
            'onepkg' => array('application/onenote',),
            
            'po' => array( 
			         "Content-Type: text/plain; charset=UTF-8;", "Content-Transfer-Encoding: 8bit\n",
			   ),
			//http://pki-tutorial.readthedocs.org/en/latest/mime.html
            'key' => array('application/pkcs8',), 
            'crt' => array('application/x-x509-ca-cert',), //VIRTUAL !!!!
           // 'crt' => array('application/x-x509-user-cert',),
      
            'cer' => array('pkix-cert',), 
           // 'pkicrt' => array('application/x-x509-user-cert',),
            'crl' => array('application/x-pkcs7-crl',),
			'pfx' => array('application/x-pkcs12',),
                        
			'bin' => array( 
			         "Content-Type: application/octet-stream", "Content-Transfer-Encoding: binary\n",
			   ),
			'dat' => array( 
			         "Content-Type: application/octet-stream", "Content-Transfer-Encoding: binary\n",
			         'Content-Disposition:attachment; filename="' . $file. '"',
			   ),
        );            
            
             
        $fnFromatFromHeaders = function() use($mime_types){
        	/**
			* 
			* @todo
			* 
			*/
		    return false;
		    
			  $headers = \webfan\App::God()-> {'?request_headers'}();
            	  if(isset($headers['Accept'])){
					$accepts = explode(',', $headers['Accept']);
					if(count($accepts) === 1){
						$_ = explode('/', $accepts[0]);
						$_ = explode(';', $_[1]);
						$_ = explode('+', $_[0]);
						if('*' !== $_s[0]){
							return ((isset($mime_types[$_s[0]])) ? $_s[0] : false) ;
						}
						
					}				  	
				  }
		    return false;		  
		};
		    
            
           if(null === $format || false === $format || !isset($mime_types[$format])){
           	
           	$fromHeaders = $fnFromatFromHeaders();
           	
		    $_e = explode('.', $file);
            $_e = array_reverse($_e);
            $extension = (count($_e) > 1) ? $_e[0] : '';
            if('?' === $format){
            	$format = $extension;
            	if( !isset($mime_types[$format]) && false !== $fromHeaders){
            	  $format = $fromHeaders;
            	}
            }elseif('?:extension' === $format){
            	$format = $extension;
            }elseif('?:headers' === $format){
            	$format = $fromHeaders;
            }

		   } 
		
		

		if(null !== $format && false !== $format){
			if(true === $apply)$THIS->format = $format;
			return ((isset($mime_types[$format])) ? $mime_types[$format] : false);
		}else{
			return $mime_types;
	    }
     }))
     
     ;
        
         
    
   
        	
       return $this;
	}
 	
 } 