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
	
	protected function task_newpackage(){
		$form = 'form-wd-frdl-webfan-package-new'.mt_rand(1000,9999);
		$divSerp = 'wd-frdl-webfan-pm-new-serp'.mt_rand(1000,9999);

		$this->html.='<form id="'.$form.'" action="#">';
		$this->html.='<div>';
		 $this->html.='<strong>Find</strong> <input type="text" name="packagename" value="vendor/package" />';
		 
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
                 cmd += \' -bcs\';
                 $.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cmd(cmd,  function(o){
                 	  Dom.g(\''.$divSerp.'\').innerHTML = \'\';
                 	  $.each(o.searchresults, function(k,i){
                 	  	     var d = Dom.create(\'div\'), p, p2, h, a;
                 	  	     d.setAttribute(\'class\', \'data-box\');
                 	  	     h = Dom.create(\'h2\');
                 	  	     h.setAttribute(\'class\', \'webfan-blue\');
                 	  	     Dom.addText(i.name, h);
                 	  	     Dom.add(h,d);
                 	  	     
                 	  	     p = Dom.create(\'p\');
                 	  	     Dom.addText(i.description, p);
                 	  	     Dom.add(p,d);
                 	  	     
                  	  	     if(\'undefined\' !== typeof i.url){
                 	  	       p2 = Dom.create(\'p\');
                 	  	       a =  Dom.create(\'a\');
                 	  	       a.setAttribute(\'href\', i.url);
                 	  	       Dom.addText(i.url, a);
                 	  	       a.setAttribute(\'target\', \'_blank\');
                 	  	       Dom.add(a,p2);
                 	  	       Dom.add(p2,d);							 	
							 }
                	  	     
                 	  	     
                 	  	   
                 	  	     Dom.add(d, Dom.g(\''.$divSerp.'\'));  
                 	  	}); 	
                 }, true);      
                                  
		 ">';
		  $this->html.='Search';
		 $this->html.='</button>';
		 
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