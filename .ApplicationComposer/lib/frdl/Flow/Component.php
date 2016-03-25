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
 * 3. Neither the name of frdl/webfan nor the
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
 */
namespace frdl\Flow;

/**
* 
*  getTempFilename - HelperFunction
* 
* @param string $url
* @param string $NS
* 
* @return
*/
function getTempFilename($url, $NS = '09c8637d-64f7-5eed-a80a-07a59059c47c'/* 1.3.6.1.4.1.37553.8.1.8.8.5.65.1 */){
	//1.3.6.1.4.1.37553.8.1.8.8.5.65.1
  return 
   sys_get_temp_dir().DIRECTORY_SEPARATOR
   .'download.zip.'.mt_rand(1000,9999)
   .'.'
   .UUID::v3($NS, $url)
   .'.'
   .UUID::v5($NS, $url)
   .'.'    
   .UUID::v4()
   .'.'    
   .'zip' 
   ; 	
	
}



class Component
{
 const URL_DEFAULT = 'widget://example.com/';/* example.com will invoke from webfan API */
 protected $FrdlWebfanAppController = null;
 protected $components_dir=null; 
 protected $components_url=null; 
 protected $shops = null;
 protected $opts = array();
 
 protected $Types = array();

 public function __construct(&$FrdlWebfanAppController, $defaults=true, &$shop=null){
 	if(!is_subclass_of($FrdlWebfanAppController, 'frdl\xGlobal\fexe')){
		trigger_error(__METHOD__.' '.__LINE__.' : first parameter must be an instance inherited from frdl\xGlobal\fexe', E_USER_ERROR);
		return;
	}
     $this->FrdlWebfanAppController=$FrdlWebfanAppController;
     $data=$this->FrdlWebfanAppController->data();
     $this->FrdlWebfanAppController->loadConfigFromFile(false);
     $data=$this->FrdlWebfanAppController->data();     
     $this->components_dir=(isset($data['config']['DIRS']['components'])) ? $data['config']['DIRS']['components'] : $data['DIR'] .'components'.DIRECTORY_SEPARATOR;
     $this->components_url=$data['config']['URL'].'/components/';
     if(true===$defaults){
     	$this->options($this->defaultOptions());
     	$this->Types=$this->FlowComponentTypes();
	 	$shop=$this->shop($shop, 'webfan/marketplace', 'http://interface.api.webfan.de/v1/public/software/marketplace/components.json');
	 }
 }

  public function commands(){
  	return array('hooks', 'q', 'types', 'extract');
  }
  
  public function hooks(){
  	return array('create', 'html', 'schema', 'install', 'edit', 'delete', 'apply', 'shop', 'options', 'mime',
  	      'format',
  	      'copy',
  	      'config', 
  	      'help',
  	      'register',
  	      'publish',
  	      'post',
  	      
  	);
  }

  public function q($method, $Type=null){
  	//todo : load type definition from lazy
  	$types=$this->types();
  	if(isset($types[$Type]['ComponentMethods'][$method])){
	  return $types[$Type]['ComponentMethods'][$method];
	}
    return false;
  }

  //  hookmethod(create($compent, $Type=null, $options =array())
  public function __call($name, $args){
  	$hooks = $this->hooks();
  	if(isset($hooks[$name])){
  		$component = $args[0];
  		$Type =$args[1];
  		$options=$args[2];
  		$get = $this->q($name, $Type);
 	  if(is_callable($get))return call_user_func_array($get, array($component, $options) );

	}
  	 return false; 
  }



 
 public function FlowComponentTypes(){
     $Types = array();
     
  $WidgetTypeData = array(
         'ComponentMethods' => array(
    'html' => function($component, $options){

          if(true===$options['preferLocal'] && is_dir($options['components_dir'].str_replace('/', DIRECTORY_SEPARATOR, $component))){
            $url=$options['components_url'];
            $url= str_replace('widget://', 'http://', $url).$component;

          }else{
             $url=$options['URL_DEFAULT'].$component;
          }
          
          $JSInvocation = (false===$options['forceLibraryJSInvocation'])
?''
:<<<SCRIPTtag

<script>(function(){if('undefined'===typeof frdl && 0===document.querySelectorAll('script[src*="api.webfan.de\/api-d\/4\/js-api\/library.js"]').length && 0===document.querySelectorAll('script[src*="flow.js"]').length){var h=document.getElementsByTagName("head",document)[0];var s=document.createElement('script');s.setAttribute('async','true');s.setAttribute('src','http://api.webfan.de/api-d/4/js-api/library.js');h.insertBefore(s,h.firstChild);}}());</script>          

SCRIPTtag;



          



              $html=<<<WEBAPP

<div data-frdl-component="$url"></div>$JSInvocation

WEBAPP;









          		   return $html; 	
			 },
         ),
     );
     $WidgetTypeData['ComponentType']=new ComponentType('Widget', $WidgetTypeData);
     $Types['Widget']=$WidgetTypeData;
     /*
     get:
       $types=$this->types();
       $componentTypeData= $types['Widget'];
       $getHtml = $componentTypeData['ComponentMethods']['html'];
       $html=$getHtml($component, $preferLocal, $components_dir, $components_url);
     */
     
     
     
  $DesktopWidgetTypeData = array(
         'ComponentMethods' => array(
    'html' => function($component, $options){

          if(true===$options['preferLocal'] && is_dir($options['components_dir'].str_replace('/', DIRECTORY_SEPARATOR, $component))){
            $url=$options['components_url'];
            $url= str_replace('widget://', 'http://', $url).$component;

          }else{
             $url=$options['URL_DEFAULT'].$component;
          }
          
          $JSInvocation = (false===$options['forceLibraryJSInvocation'])
?''
:<<<SCRIPTtag

<script>(function(){if('undefined'===typeof frdl && 0===document.querySelectorAll('script[src*="api.webfan.de\/api-d\/4\/js-api\/library.js"]').length && 0===document.querySelectorAll('script[src*="flow.js"]').length){var h=document.getElementsByTagName("head",document)[0];var s=document.createElement('script');s.setAttribute('async','true');s.setAttribute('src','http://api.webfan.de/api-d/4/js-api/library.js');h.insertBefore(s,h.firstChild);}}());</script>          

SCRIPTtag;



          



              $html=<<<WEBAPP

<div data-frdl-desktop-widget="$url"></div>$JSInvocation

WEBAPP;


          			
          			
          		   return $html; 	
			 },
         ),
     );
     $DesktopWidgetTypeData['ComponentType']=new ComponentType('DesktopWidget', $DesktopWidgetTypeData);
     $Types['DesktopWidget']=$DesktopWidgetTypeData;     
     
     
     
     
      
  $ManifestWebAppTypeData = array(
         'ComponentMethods' => array(
    'html' => function($component, $options){
                 return false; 	
			 },
			 
    'create' => function($component, $options = array(
                     'data' => null,
                     'destination' => null,
               )){
                 return false; 	
			 },
			 			 
         ),
     );
     $ManifestWebAppTypeData['ComponentType']=new ComponentType('ManifestWebApp', $ManifestWebAppTypeData);
     $Types['ManifestWebApp']=$ManifestWebAppTypeData;     
     
         
     
     
     return $Types;
 }	
     
 /*
 * public methods following
 */
 
 /**
 * 
 * @param Array $options
 * 
 * @return
 */
 public function types($Types=null){
 	if(is_array($Types)){
 		foreach($Types as $k => $o){
			$this->Types[$k]=$o;
		}
	}
 	
 	return $this->Types;
 }
  
 
 /**
 * 
 * @param Array $options
 * 
 * @return
 */
 public function options($options=null){
 	if(is_array($options)){
 		foreach($options as $k => $o){
			$this->opts[$k]=$o;
		}
	}
 	
 	return $this->opts;
 }
 
 
    
 /**
 * 
 * @param Array &$shop : result reference
 * @param string|null $name : Shopname
 * @param string|null $url : Shopurl (expecting JSON)
 * 
 * @return $this : methodchain
 */   
 public function shop(&$shop =null, $name=null/* 'My µ.Flow Components Marketplace'*/, $url = null /* 'http://interface.api.webfan.de/v1/public/software/marketplace/components.json'*/){
	if(!is_string($name) && !is_int($name) && !is_string($url)){
		$shop = $this->shops;

	}elseif((is_string($name) || is_int($name) ) && !is_string($url)){
		$shop = (isset($this->shops[$name]) ) ? $this->shops[$name] : null;

	}elseif((is_string($name) || is_int($name) ) && is_string($url)){
		try{
			$this->shops[$name]=$this->getShopFromUrl($url);
			$shop=$this->shops[$name];
		}catch(\Exception $e){
			$shop=null;
		}

	}elseif(!is_string($name) && !is_int($name) && is_string($url)){
		try{
    		$shop=$this->getShopFromUrl($url);
			if(!in_array($shop, $this->shops)){
              $this->shops[]=$shop;
			}
		}catch(\Exception $e){
			$shop=null;
		}

	}else{
		throw new \Exception('Cannot catch that logical exception error in '.__METHOD__.' '.__LINE__);
	}
	
	return $this;
 } 


/**  <div data-frdl-desktop-widget="widget://example.com/webfan/marketplace"></div>
* 
* @param string $html : result reference
* @param string $component (example: 'vendor/component')
* @param boolean $preferLocal : if true search for the widget installed locally, if false use 
* 
* @return $this : methodchain
*/
 public function html($component, $Type = null, $options = array(
       'preferLocal'=>true,
       'forceLibraryJSInvocation' => true,
       'URL_DEFAULT' => null,
       'components_dir' => null,
       'components_url' => null,
       
 )){
 	$types=$this->types();
 	
 	if(!is_string($options['components_dir']) || !is_dir($options['components_dir']))$options['components_dir']=$this->components_dir;
 	if(!is_string($options['components_url']) )$options['components_url']=$this->components_url; 	
 	if(!is_string($options['URL_DEFAULT']) )$options['URL_DEFAULT']=self::URL_DEFAULT; 	 
 	if(true!==$options['preferLocal'] && false !== $options['preferLocal']){
		$u= \webdof\wURI::getInstance();
		$options['preferLocal']=('install.phar' === $u->getU()->file || 'install.php' === $u->getU()->file) ? false : true;
	}
 	

 	$get = $this->q('html', $Type);
 	if(is_callable($get))return call_user_func_array($get, array($component, $options) );
 	return false;
 	
       $preferLocal=(bool)$preferLocal;
          
          if(true===$options['preferLocal'] && is_dir($options['components_dir'].str_replace('/', DIRECTORY_SEPARATOR, $component))){
            $url=$options['components_url'];
            $url= str_replace('widget://', 'http://', $url).$component;

          }else{
             $url=$options['URL_DEFAULT'].$component;
          }
          
          $JSInvocation = (false===$options['forceLibraryJSInvocation'])
?''
:<<<SCRIPTtag

<script>(function(){if('undefined'===typeof frdl && 0===document.querySelectorAll('script[src*="api.webfan.de\/api-d\/4\/js-api\/library.js"]').length && 0===document.querySelectorAll('script[src*="flow.js"]').length){var h=document.getElementsByTagName("head",document)[0];var s=document.createElement('script');s.setAttribute('async','true');s.setAttribute('src','http://api.webfan.de/api-d/4/js-api/library.js');h.insertBefore(s,h.firstChild);}}());</script>          

SCRIPTtag;



          


              $html=<<<WEBAPP

<div data-frdl-component="$url"></div>$JSInvocation

WEBAPP;


          
   return $html;       
 }	
 
 /**
 * 
 * @param srting $component : Components name you wish to install
 * @param bool $success : result reference
 * 
 * @return $this : methodchain
 */
 public function install($component, $shopName=null, &$success = false, &$log=array()){
 	$widgeturl=null;
 	foreach($this->shop() as $shopname => $shop){
		if(null!==$widgeturl && (is_string($shopName) || is_int($shopName)) && $shopname!==$shopName)continue;
		foreach($shop as $ix => $c){
			if($component===$c->name){
				if(isset($c->download)){
					$widgeturl=$c->download;
				}else{
					$widgeturl='http://webfan.de/cdn/frdl/flow/components/'.$c->name;
				}
				
				array_push($log, 'Component '.$component.' found in shop #'.$shopname);
			}
		}
	}
	if(null===$widgeturl){
		$success=false;
		array_push($log, 'Component '.$component.' not found in the applied shops');
	}else{
	  $this->extract($widgeturl,$this->components_dir.str_replace('/', DIRECTORY_SEPARATOR, $component), $success);	
	}
	
 	
 	return $this;
 }
 
 
 public function extract($zipFileUrl,$toDir, &$success=false){
 	$success = $this->_extract($zipFileUrl,$toDir);
 	return $this;
 }
 
 /*
 * private methods following
 */
 
 
 protected function defaultOptions(){
   	  return array(
   	        'cache_time' => 60 * 60,
   	        'save' => false,
   	        'debug' => false,
   	        'cachekey' => '~components.'.sha1(get_class($this)),
   	  );
 }	
      
      
 protected function _extract($zipFileUrl,$toDir){
    $success=false;
 	$zipcontents = file_get_contents($zipFileUrl);
 	if(false===$zipcontents){
	  $success=false;
	  return $success;
	}
	$zipfilename =getTempFilename($zipFileUrl);
	file_put_contents( $zipfilename, $zipcontents); 	
 	
 	if(!is_dir($toDir)){
		mkdir($toDir, 0755, true);
	}
 	if(!is_dir($toDir)){
	  $success=false;
	  return $success;
	} 	
	try{
 	  $Zip = new \frdl\webfan\Compress\zip\wUnzip($zipfilename, $toDir);
 	  $r = $Zip->unzip();
      unlink($zipfilename);			
	}catch(\Exception $e){
	  $success=false;
	  return $success;		
	}

 	
 	$success = (0===count($r['errors'])) ? true : false;
	return $success;		
 }
  
 protected function cachefile($sub){
     $data=$this->FrdlWebfanAppController->data();
   	 $dir = (isset($data['DIRS']['cache']) && '' !== $data['DIRS']['cache']) ? $data['DIRS']['cache'] : '.ApplicationComposer/cache/';
   	 return $dir . $this->opts['cachekey'].'.'.sha1($sub).'.'.strlen($sub).'.php';
   }
   
 protected function cache($sub, $value = null){
 	 $data=$this->FrdlWebfanAppController->data();
   	  $file = $this->cachefile($sub);
   	  if(null === $value && (!file_exists($file) || filemtime($file) < time() -  $this->opts['cache_time']))return null;
   	  if(null === $value){
   	  	try{
   	  	    require $file;
   	  	    if($time < time() -  $this->opts['cache_time'])return null;
   	  	    return $value;			
		}catch(\Exception $e){
			trigger_error($e->getMessage(), E_USER_ERROR);
		}

   	  	}
   	  	
   	  $code = "<?php
  \$time = ".time().";
  \$expires = ".(time() + intval($this->opts['cache_time'])).";
  \$value = ".str_replace("stdClass::__set_state", "(object)", var_export($value, true)).";
             	  
";  
   if(file_exists($file)){
   	   file_put_contents($file, $code);
   	   chmod($file, 0644);   	
   }

 }
 
 protected function getShopFromUrl($url){
     	$k = __METHOD__.$url.date('w', time());
     	$cache = $this->cache($k, null);
     	if(null!==$cache){
		  return $cache;	
		}
		try{
    		$r=json_decode(file_get_contents($url));
    		if(!is_array($r) && isset($r->data) && is_array($r->data)){
				$r=$r->data;
			}elseif(!is_array($r) && isset($r->result)  && isset($r->result->data) && is_array($r->result->data)){
				$r=$r->result->data;
			}
		}catch(\Exception $e){
			$r=null;
		}		
		$this->cache($k, $r);
		return $r;
 }

 
}