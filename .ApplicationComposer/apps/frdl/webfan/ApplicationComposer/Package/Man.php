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
namespace frdl\ApplicationComposer\Package;



class Man implements \frdl\ApplicationComposer\OutputInterface
{
	protected $html = '';
	protected $js = '';
	
	protected $prepend = true;

    protected $argtoks;
    protected $config;
    protected $CMD; //in
    
    protected $Console;
    
    protected $task;
    
    protected $db = null;
	
	function __construct($prepend = true){
	  $this->prepend = $prepend;	
	  $this->html = '';
	  $this->js = '';
	  $this->assignDB($this->db);
		  	
	}
	
	
	public function run($task, $argtoks, $config, &$CMD = null){
		$this->argtoks = $argtoks;
		$this->config = $config;
		$this->CMD = $CMD;
		$this->task = $task;
	/*	
	    $this->Console = new \frdl\ApplicationComposer\Console;
	    $this->Console->applyApp($this->CMD);	
	  */  
	    	
	    $method = 'task_'.$this->task;
	    if(is_callable(array($this,$method))){
			return call_user_func_array(array($this,$method), func_get_args() );
		}
	}
	
	
	public function assignDB(&$db = null){
		$db =  \frdl\xGlobal\webfan::db();
		return $this;
	}
	
	
	protected function task_package(){
		
		
		$divSerp = 'wd-frdl-webfan-pm-packages-package'.mt_rand(1000,9999);	
	 //    $p = new \frdl\ApplicationComposer\Package(); 
	  //	$packages = $p->all();
	
	   // sort($packages);
	   


			
			
		$this->html.='<div id="'.$divSerp.'">';
		
         $this->html.='package...';
		 
		 
		$this->html.='</div>';	
		
	
	}
		
	
	protected function task_suggestions(){
	             
		$divSerp = 'wd-frdl-webfan-pm-packages-suggestions'.mt_rand(1000,9999);	
	 //    $p = new \frdl\ApplicationComposer\Package(); 
	  //	$packages = $p->all();
	
	   // sort($packages);
	   
	       
	   $groups['CMS'] = array(  
	          'title' => 'Content Management',
	          'packages' => array( ),
	   );	   
	   
	   
	   $groups['PROJECT'] = array(  
	          'title' => 'Project Management',
	          'packages' => array( ),
	   );	  
	   
	   
	   $groups['SDK'] = array(  
	          'title' => 'API SDK',
	          'packages' => array( ),
	   );		

	   
	   $groups['LOCALE'] = array(  
	          'title' => 'Localization &amp; Translation',
	          'packages' => array( ),
	   );		   

  
	   
	    /*
	    
		   $groups['CMS']['packages'][] = array( 
	                'vendor' => 'TerraProject',
	                'package' => 'pragmamx',
	                'description' => 'Just another CMS...',
	                'url' => 'http://www.pragmamx.org',
	                'img' => 'http://www.pragmamx.org/favicon.ico',
	              );
		
		
		
			$groups['PROJECT']['packages'][] = array( 
	                'vendor' => 'frdl',
	                'package' => 'webfan',
	                'description' => 'PHP Package Manager - Application Composer',
	                'url' => 'https://github.com/frdl/webfan',
	                'img' => 'http://static.webfan.de/icons/icons-3/icon_package_get.gif',
	              );		
		*/	
			
			
			
			$groups['SDK']['packages'][] = array( 
	                'vendor' => 'phpclasses',
	                'package' => 'oauth-api',
	                'description' => 'OAuth API SDK',
	                'url' => 'http://www.phpclasses.org/package/7700-PHP-Authorize-and-access-APIs-using-OAuth.html',
	                'img' => 'http://www.phpclasses.org/favicon.ico',
	              );		
			
					
			$groups['LOCALE']['packages'][] = array( 
	                'vendor' => 'gettext',
	                'package' => 'gettext',
	                'description' => 'gettext Compliant',
	                'url' => 'https://github.com/oscarotero/Gettext',
	                'img' => 'https://camo.githubusercontent.com/2a794b2cf192bfdbf5b64ad332f24b5f29f9711b/68747470733a2f2f696e73696768742e73656e73696f6c6162732e636f6d2f70726f6a656374732f34393664633261362d343362652d343034362d613238332d6638333730323339646434372f6269672e706e67',
	              );		
						
			
			
		$this->html.='<div id="'.$divSerp.'">';
		
	 foreach($groups as $n => $group){	
	  $this->html.='<div>';
	   $this->html.='<h2>'.$group['title'].'</h1>';
	   
	    $packs = $group['packages'];
		ksort($packs);
		 foreach($packs as $num => $package){
		 	$this->html.='<div class="data-box">';
		 	
	/*
							      frdl.wd().resetReady(\'Loading page window.......\',45, 
                      		   function(){
                      		   	    var r = (\'undefined\' !== typeof PackageTask.WIN && \'function\' === typeof PackageTask.WIN.set);
                      		   	    if(true !== r)return r;
                      		   	    PackageTask.WIN.set(\'img\', \''.$package['img'].'\');
                      		     return true;
                               }
	   	                    );	
	   	                    */	 	
		 	$this->html.='<h2 class="webfan-blue" onclick="var App = frdl.wd().Registry.Programs[\'frdl-webfan\'];var p = this.getAttribute(\'data-package\'); 
                 	  	     	var e = explode(\'/\', p);
							    var PackageTask = $(App).package(\'c\', e[0], e[1]);
							     
   	    
	   	                    	     
							"
					  data-package="'.$package['vendor'].'/'.$package['package'].'" style="text-decoration:underline;">';
			$this->html.='<img src="'.$package['img'].'" style="border:none;" />';	  
		 	$this->html.= $package['vendor'].'/'.$package['package'];
		 	$this->html.='</h2>';
		 	if(isset($package['description'])){
				$this->html.='<p>'.$package['description'].'</p>';
			}
		 	if(isset($package['url'])){
				$this->html.='<p><a href="'.$package['url'].'" style="text-decoration:underline;" target="_blank">'.$package['url'].'</a></p>';
			}
			
			
		 	$this->html.='</div>';
		 }
	   $this->html.='</div>'; 
	}	 
		 
		 
		$this->html.='</div>';	
		
	
	}
	
	
	protected function task_packages(){
		
		
		
		$divSerp = 'wd-frdl-webfan-pm-packages-main'.mt_rand(1000,9999);	
	    $p = new \frdl\ApplicationComposer\Package(); 
	  //	$packages = $p->all();
	   $num = 25;
	   $packages = $p->select( 0, $num, array('vendor' => 'ASC', 'package' => 'ASC'));
	   // sort($packages);
			
		$this->html.='<div id="'.$divSerp.'">';
		
		
		 foreach($packages as $num => $package){
		 	$this->html.='<div class="data-box">';
		 	
		 	
		 	$this->html.='<h2 class="webfan-blue" onclick="var p = this.getAttribute(\'data-package\'); 
                 	  	     	var e = explode(\'/\', p);
							   	$(frdl.wd()).pack(\'c\', e[0], e[1]);"
					  data-package="'.$package['vendor'].'/'.$package['package'].'" style="text-decoration:underline;">';
		 	$this->html.= $package['vendor'].'/'.$package['package'];
		 	$this->html.='</h2>';
		 	if(isset($package['description'])){
				$this->html.='<p>'.$package['description'].'</p>';
			}
		 	if(isset($package['url'])){
				$this->html.='<p><a href="'.$package['url'].'" style="text-decoration:underline;" target="_blank">'.$package['url'].'</a></p>';
			}
			
			
		 	$this->html.='</div>';
		 }
		 
		 
		$this->html.='</div>';	
		
		
		$this->html.='
		  <button onclick="var App = frdl.wd().Registry.Programs[\'frdl-webfan\'];
		  frdl.wd().wdFrdlWebfanHtmlPackagesOffset =  frdl.wd().wdFrdlWebfanHtmlPackagesOffset + '.intval($num).';
		    Dom.g(\''.$divSerp.'\').innerHTML += \'<img src=\' + base64_decode(\'Ig==\') + \'http://images.webfan.de/ajax-loader_2.gif\' + base64_decode(\'Ig==\') + \' alt=\' + base64_decode(\'Ig==\') + \'lade...\' + base64_decode(\'Ig==\') + \' style=\' + base64_decode(\'Ig==\') + \'border:none;\' + base64_decode(\'Ig==\') + \' class=\' + base64_decode(\'Ig==\') + \'img-ajax-loader\' + base64_decode(\'Ig==\') + \' />\';
	        frdl.wd().Registry.Programs[\'frdl-webfan\'].cmd(
	            \'frdl pm select --start=\' + frdl.wd().wdFrdlWebfanHtmlPackagesOffset + \' --limit='.$num.' -b\',  function(o){
	             	  $.each(o.packages, function(_k,i){
                 	  	     i.name = i.vendor + \'/\' + i.package;
                 	  	     var d = Dom.create(\'div\'), p, p2, h, a;
                 	  	     d.setAttribute(\'class\', \'data-box\');
                 	  	     h = Dom.create(\'h2\');
                 	  	     h.setAttribute(\'class\', \'webfan-blue\');
                 	  	     h.setAttribute(\'data-package\', i.name);
                 	  	     h.style.textDecoration=\'underline\';
                 	  	     Dom.addText(i.name, h);
                 	  	     h.onclick=function(ev){
                 	  	     	var p = this.getAttribute(\'data-package\'); 
                 	  	     	var e = explode(\'/\', p);
							   	$(App).pack(\'c\', e[0], e[1], true);
							 };
                 	  	     Dom.add(h,d);
                 	  	    
                 	  	     if(\'undefined\' !== typeof i.description){ 
                 	  	       p = Dom.create(\'p\');
                 	  	       Dom.addText(i.description, p);
                 	  	       Dom.add(p,d);
                 	  	     }
                 	  	     
                  	  	     if(\'undefined\' !== typeof i.url){
                 	  	       p2 = Dom.create(\'p\');
                 	  	       a =  Dom.create(\'a\');
                 	  	       a.setAttribute(\'href\', i.url);
                 	  	       a.style.textDecoration=\'underline\';
                 	  	       Dom.addText(i.url, a);
                 	  	       a.setAttribute(\'target\', \'_blank\');
                 	  	       Dom.add(a,p2);
                 	  	       Dom.add(p2,d);							 	
							 }
                	  	     
                 	  	     
                 	  	   
                 	  	     Dom.add(d, Dom.g(\''.$divSerp.'\'));  
                 	  	}); 		            
              	     $(\'.img-ajax-loader\').hide();    	
	        }, true);	    
		  "><span>More</span>...</button>
		';
		
		
		$this->js.= " 
		frdl.wd().wdFrdlWebfanHtmlPackagesOffset = ".$num.";

		";
		
	}
	
	protected function task_newpackage(){
		$form = 'form-wd-frdl-webfan-package-new'.mt_rand(1000,9999);
		$divSerp = 'wd-frdl-webfan-pm-new-serp'.mt_rand(1000,9999);

		$this->html.='<form id="'.$form.'">';
		$this->html.='<div>';
		 $this->html.='<strong>Package</strong> 
		
	';
		 $this->html.='<form id="'.$form.'">&nbsp;	 <input type="text" name="packagename" value="vendor/package"
		  onclick="if(\'vendor/package\' === this.value)this.value=\'\';" 
		  />';
		 $this->html.='<input type="checkbox" name="save" />';
		 $this->html.=' <span>Save found packages</span>';
		 
		$this->html.='</div>';
		
		$this->html.='<div id="'.$divSerp.'">';
		
		$this->html.='</div>';
		
		
		
		$this->html.='</form>';
				 
		 /**
		 * 
		 * flags -bcs  bounce,use cache, save(not in use yet)
		 * 
		 */
		 $this->html.=' <button onclick="var fd =  $(\'#'.$form.'\').serializeArray().reduce(function(obj, item) {
                                             obj[item.name] = item.value;
                                             return obj;
                                        }, {});		 
                 	  	     	var e = explode(\'/\', fd.packagename), App = frdl.wd().Registry.Programs[\'frdl-webfan\'];
							   	$(App).package(\'c\', e[0], e[1], true);">create</button>
		 
		 <button onclick="
			   var fd =  $(\'#'.$form.'\').serializeArray().reduce(function(obj, item) {
                                             obj[item.name] = item.value;
                                             return obj;
                                        }, {}), 
                                             App = frdl.wd().Registry.Programs[\'frdl-webfan\'];		 
                 var cmd = \'frdl pm find \' + base64_decode(\'Ig==\') + fd.packagename + base64_decode(\'Ig==\') ;
                 cmd += \' -bc\';
                 try{
				  cmd += (\'on\' === fd.save) ? \'s\' : \'\';	
				 }catch(err){
				 	console.warn(err);
				 }
                
                 cmd += (true === frdl.wd().o.debug) ? \'d\' : \'\';
                 Dom.g(\''.$divSerp.'\').innerHTML = \'<img src=\' + base64_decode(\'Ig==\') + \'http://images.webfan.de/ajax-loader_2.gif\' + base64_decode(\'Ig==\') + \' alt=\' + base64_decode(\'Ig==\') + \'lade...\' + base64_decode(\'Ig==\') + \' style=\' + base64_decode(\'Ig==\') + \'border:none;\' + base64_decode(\'Ig==\') + \' class=\' + base64_decode(\'Ig==\') + \'img-ajax-loader\' + base64_decode(\'Ig==\') + \' />\';
                 frdl.wd().Registry.Programs[\'frdl-webfan\'].cmd(cmd,  function(o){
                 	  $.each(o.searchresults, function(k,_i){
                 	  	  $.each(_i, function(_k,i){
                 	  	     var d = Dom.create(\'div\'), p, p2, h, a;
                 	  	     d.setAttribute(\'class\', \'data-box\');
                 	  	     h = Dom.create(\'h2\');
                 	  	     h.setAttribute(\'class\', \'webfan-blue\');
                 	  	     h.setAttribute(\'data-package\', i.name);
                 	  	     h.style.textDecoration=\'underline\';
                 	  	     Dom.addText(i.name, h);
                 	  	     h.onclick=function(ev){
                 	  	     	var p = this.getAttribute(\'data-package\'); 
                 	  	     	var e = explode(\'/\', p);
							   	$(App).package(\'c\', e[0], e[1], true);
							 };
                 	  	     Dom.add(h,d);
                 	  	    
                 	  	     if(\'undefined\' !== typeof i.description){ 
                 	  	       p = Dom.create(\'p\');
                 	  	       Dom.addText(i.description, p);
                 	  	       Dom.add(p,d);
                 	  	     }
                 	  	     
                  	  	     if(\'undefined\' !== typeof i.url){
                 	  	       p2 = Dom.create(\'p\');
                 	  	       a =  Dom.create(\'a\');
                 	  	       a.setAttribute(\'href\', i.url);
                 	  	       a.style.textDecoration=\'underline\';
                 	  	       Dom.addText(i.url, a);
                 	  	       a.setAttribute(\'target\', \'_blank\');
                 	  	       Dom.add(a,p2);
                 	  	       Dom.add(p2,d);							 	
							 }
                	  	     
                 	  	     
                 	  	   
                 	  	     Dom.add(d, Dom.g(\''.$divSerp.'\'));  
                 	  	     });
                 	  	      
                 	  	}); 	
                 	  	     $(\'.img-ajax-loader\').hide();
                 }, true);      
                                  
		 ">';
		  $this->html.='Search';
		 $this->html.='</button>';
		 

		$this->js.= " 
		
		$('#".$form."').on('submit', function(ev){
			   ev.stopPropagation();
			   ev.preventdefault();
			   return false;
			});
		

		
		";
	}
	
	public function js(){
	   return $this->js;	
	}
	
	public function html(){
		return $this->html;
	}
	
	public function result(\frdl\ApplicationComposer\AjaxResult &$result = null){
		if(true === $this->prepend){
		  $result->js .= $this->js();
		  $result->html .= $this->html();			
		}else{
			  $result->js = $this->js();
		     $result->html = $this->html();				
		}

	}
		
	
}