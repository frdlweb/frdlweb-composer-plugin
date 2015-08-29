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
	
	function __construct($prepend = true){
	  $this->prepend = $prepend;	
	  $this->html = '';
	  $this->js = '';
	  
		  	
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
	
	
	protected function task_packages(){
		$divSerp = 'wd-frdl-webfan-pm-packages-main'.mt_rand(1000,9999);	
	    $p = new \frdl\ApplicationComposer\Package(array(),  \frdl\xGlobal\webfan::db()->settings(),  \frdl\xGlobal\webfan::db()); 
	  //	$packages = $p->all();
	   $num = 25;
	   $packages = $p->select( 0, $num, array('vendor' => 'ASC', 'package' => 'ASC'));
	   // sort($packages);
			
		$this->html.='<div id="'.$divSerp.'">';
		
		
		 foreach($packages as $num => $package){
		 	$this->html.='<div class="data-box">';
		 	
		 	
		 	$this->html.='<h2 class="webfan-blue" onclick="var p = this.getAttribute(\'data-package\'); 
                 	  	     	var e = explode(\'/\', p);
							   	$(this).package(\'c\', e[0], e[1]);"
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
		  <button onclick="
		   $.WebfanDesktop.wdFrdlWebfanHtmlPackagesOffset =  $.WebfanDesktop.wdFrdlWebfanHtmlPackagesOffset + '.intval($num).';
	        $.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cmd(
	            \'frdl pm select --start=\' + $.WebfanDesktop.wdFrdlWebfanHtmlPackagesOffset + \' --limit='.$num.' -b\',  function(o){
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
							   	$(this).package(\'c\', e[0], e[1]);
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
		  "><span>More</span>...</button>
		';
		
		
		$this->js.= " 
		$.WebfanDesktop.wdFrdlWebfanHtmlPackagesOffset = ".$num.";

		";
		
	}
	
	protected function task_newpackage(){
		$form = 'form-wd-frdl-webfan-package-new'.mt_rand(1000,9999);
		$divSerp = 'wd-frdl-webfan-pm-new-serp'.mt_rand(1000,9999);

		$this->html.='<form id="'.$form.'" action="#" method="post">';
		$this->html.='<div>';
		 $this->html.='<strong>Find</strong> <input type="text" name="packagename" value="vendor/package"
		  onclick="if(\'vendor/package\' === this.value)this.value=\'\';" 
		  />';
		 
		 /**
		 * 
		 * flags -bcs  bounce,use cache, save(not in use yet)
		 * 
		 */
		 $this->html.='<button onclick="
			   var fd =  $(\'#'.$form.'\').serializeArray().reduce(function(obj, item) {
                                             obj[item.name] = item.value;
                                             return obj;
                                        }, {});		 
                 var cmd = \'frdl pm find \' + base64_decode(\'Ig==\') + fd.packagename + base64_decode(\'Ig==\') ;
                 cmd += \' -bc\';
                 try{
				  cmd += (\'on\' === fd.save) ? \'s\' : \'\';	
				 }catch(err){
				 	console.warn(err);
				 }
                
                 cmd += (true === $.WebfanDesktop.o.debug) ? \'d\' : \'\';
                 Dom.g(\''.$divSerp.'\').innerHTML = \'<img src=\' + base64_decode(\'Ig==\') + \'http://images.webfan.de/ajax-loader_2.gif\' + base64_decode(\'Ig==\') + \' alt=\' + base64_decode(\'Ig==\') + \'lade...\' + base64_decode(\'Ig==\') + \' style=\' + base64_decode(\'Ig==\') + \'border:none;\' + base64_decode(\'Ig==\') + \' class=\' + base64_decode(\'Ig==\') + \'img-ajax-loader\' + base64_decode(\'Ig==\') + \' />\';
                 $.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cmd(cmd,  function(o){
                 	  Dom.g(\''.$divSerp.'\').innerHTML = \'\';
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
							   	$(this).package(\'c\', e[0], e[1]);
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
                 }, true);      
                                  
		 ">';
		  $this->html.='Search';
		 $this->html.='</button>';
		 
		 $this->html.='&nbsp;';
		 $this->html.='<input type="checkbox" name="save" />';
		 $this->html.=' <span>Save found packages</span>';
		 
		$this->html.='</div>';
		
		$this->html.='<div id="'.$divSerp.'">';
		
		$this->html.='</div>';
		
		
		
		$this->html.='</form>';
		
		$this->js.= " 
		
		$('#'.$form.'').on('submit', function(ev){
			   ev.stopPropagation();
			   e.preventdefault();
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