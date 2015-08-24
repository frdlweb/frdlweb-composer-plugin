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
 *  @role       example/test
 * 
 *  @cmd "frdl test -b -d"
 * 
 */
namespace frdl\ApplicationComposer\Command;

class html extends CMD
{

     
     protected $item;
     protected $html;
     
     protected $loginformIsOut = false;
     
     protected $wizard_errors = 0;
     
     protected $date;

    function __construct(){
		parent::__construct();
		$this->html = '';
		 $this->loginformIsOut = false;
		 $this->wizard_errors = array();
		 $this->date = new \webdof\wDate();

	}
    
  public function m($js)
  {
   $js = preg_replace("/[\t]/", " ", $js);
   $js = preg_replace("/[\s]+/", " ", $js);
   $js = preg_replace("/[\t\r\n]{1,}/", " ", $js);
   $js = preg_replace("/\;\s/", ";", $js);
   $js = preg_replace("/\s\{/", "{", $js);
   $js = preg_replace("/\}\s/", "}", $js);
   $js = preg_replace("/\s\}/", "}", $js);
   $js = preg_replace("/\{\s/", "{", $js);
   $js = preg_replace("/([\s\t\r\n]{1,})/", " ", $js);
   return $js;
  }
     
    public function process()
    {
       $args = func_get_args();
       $this->result->out = '';
       $this->result->js = '';
       
        $this->item = $this->getRequestOption('item');
       $this->result->item = strip_tags($this->item); 

	   $this->loadConfigFromFile(false);


    try{
	      $db =  \frdl\DB::_(array(
		   'driver' => $this->data['config']['db-driver'],
		   'host' => $this->data['config']['db-host'],
		   'dbname' => $this->data['config']['db-dbname'],
		   'user' => $this->data['config']['db-user'],
		   'password' => $this->data['config']['db-pwd'],
		   'pfx' => $this->data['config']['db-pfx'],
		   
		  ), true);
		   $this->connected = $db->connected;			
		}catch(\Exception $e){
           $this->connected = false;;
		}
		

       try{
	    	if(null !== $this->item)$this->html .= $this->{'item_'.$this->result->item}();
	   }catch(\Exception $e){
	   	  $this->statusText = 'Error: '.$e->getMessage();
	   }
     
       $this->result->js = $this->m(trim($this->result->js));
       $this->result->html = base64_encode(trim($this->html));
    }
    
    
        
    public function required()
    {
       $args = func_get_args();
    }
    
       protected function item_api(){
	  //  if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']){
	     	// return (true !== $this->loginformIsOut) ? $this->item_login() : '';
	     //	 return  $this->item_login();
		//  }
	 $html = '';
	
	 $html.= '<div id="window_main_frdl-webfan-api" class="wd-tab">'   ;
	 $html.= '<h2 class="webfan-blue">API</h2>';	
        $html.= '<iframe src="http://www.webfan.de/API.html?context=popup" style="width:100%;height:450px;overflow:auto;" />';
 
     $html.= '</div>';
		   
	  return $html;
	}

	protected function item_wizard(){
     $html = '';
     
    
		
    	$tab = 'window_main_frdl-webfan-wizard';
  	    if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']){
	     	 return '<div id="'.$tab.'" class="wd-tab">'. $this->item_login() .'</div>';
		  }
	
	
     $html.= '<div id="'.$tab.'" class="wd-tab">';

        $html.= '<table style="width:100%;vertical-align:top;">';
        $html.= '<tr>';
         $html.='<td style="width:66%;">';
         
         $html.='</td>';
         

         $html.='<td>';
          $html.='<strong>Every Day Checklist</strong>:';
          $html.='<ul>';
            
            $html.='<li onclick="$(\'#window_frdl-webfan\').hide();$.WebfanDesktop.go(\'postbox\');$(\'#window_postbox\').show();  ">';
        //  $html.='<li id="show_postbox" href="#">';
             $html.='<img src="http://static.webfan.de/icons/icons-3/icon_email.gif" style="border:none;" />';
             $html.= '<u>Check your Postbox</span>';
            $html.='</li>';
            
          $html.= '</ul>';
          
          
         $html.='</td>';
                  
        $html.='</tr>';
        $html.='</table>';
        
 
      $html.= '</div>';
  
      $this->result->js .= " 
     	
     $.WebfanDesktop.resetReady('Loading...',25, 
	   	        function(){
	   	        	var r= ( Dom.isVisible('".$tab."') );
	   	        	if(true !== r) return r;
	   	        
	   	        	$.each($.WebfanDesktop.Registry.Notifs, function(k,m){
	   	        		
        	         if(null === m)return false;
        	          if('important' === m.type || 'system' === m.type || 'error' === m.type || 'warning' === m.type || 'update' === m.type || 'settings' === m.type || 'todo' === m.type){
				 	    $('#".$tab."').wdPostbox('RenderMessage', m, Dom.g('".$tab."'));
				 	    
				      }
               
        	        });
	   	        	return r;
	   	        }
	  );
	   	     
	   	     

    ";

  
      
      return $html;		
	}	

	
	protected function item_suggestions(){
    	$tab = 'window_main_frdl-webfan-pm-frdl-suggestions';
  	    if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']){
	     	 return '<div id="'.$tab.'" class="wd-tab">'. $this->item_login() .'</div>';
		  }
	 $p = new \frdl\ApplicationComposer\Pack\Man(true);
	 $p -> run('frdl suggestions', $this->argtoks, $this->data['config'], $this);
	 $p -> result($this->result);   
	 
	  return '';		
	}
	protected function item_allpackages(){
	 $html = '';
    	$tab = 'window_main_frdl-webfan-pm-all';
  	    if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']){
	     	 return '<div id="'.$tab.'" class="wd-tab">'. $this->item_login() .'</div>';
		  }
	
	 $html.= '<div id="'.$tab.'" class="wd-tab">'   ;
	 $html.= '<h2 class="webfan-blue">All known packages</h2>';	

 
     $html.= '</div>';
		   
	  return $html;		
	}	
     protected function item_packages(){
    	$tab = 'window_main_frdl-webfan-packages-main';
  	    if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']){
	     	 return '<div id="'.$tab.'" class="wd-tab">'. $this->item_login() .'</div>';
		  }
         $html .=  $this->item_suggestions();
	     $html .=  $this->item_allpackages();  	     
  	   	 $this->result->js .= "      
  var mod = $.WebfanDesktop.Registry.Programs['frdl-webfan'];
  mod.Tabs.delTabs();	  
 
    mod.Tabs.addTab('#window_main_frdl-webfan-pm-all', 'All', 'window_main_frdl-webfan-pm-all', true);  	   
    
     mod.Tabs.addTab('#window_main_frdl-webfan-pm-installs', 'Installed Packages', 'window_main_frdl-webfan-pm-installs', true);     
    	    
    mod.Tabs.addTab('#window_main_frdl-webfan-pm-frdl-suggestions', 'Suggestions', 'window_main_frdl-webfan-pm-frdl-suggestions', true);
    	   	   	    
 	

    mod.Tabs.render(); 
    mod.Tabs.openTab('window_main_frdl-webfan-pm-frdl-suggestions');
     ";
     
     /*
        mod.Tabs.addTab('#window_main_frdl-webfan-pm-projects', 'Projects', 'window_main_frdl-webfan-pm-projects', true);
        */
     
      return $html;
	}
			 

     protected function item_accounts(){
       $html .=  $this->item_login();
	   $html .=  $this->item_icontem();  	
       $html .=  $this->item_webfan();  
       $html .=  $this->item_api();  
  	   	 $this->result->js .= "      
  var mod = $.WebfanDesktop.Registry.Programs['frdl-webfan'];
  mod.Tabs.delTabs();	   	   	    
    mod.Tabs.addTab('#window_main_frdl-webfan-login', 'Admin', 'window_main_frdl-webfan-login', true);	   	   	    
    mod.Tabs.addTab('#window_main_frdl-webfan-icontem', 'Icontem', 'window_main_frdl-webfan-icontem', true);
    mod.Tabs.addTab('#window_main_frdl-webfan-webfan', 'Webfan', 'window_main_frdl-webfan-webfan', true);
    mod.Tabs.addTab('#window_main_frdl-webfan-api', 'Webfan API', 'window_main_frdl-webfan-api', true);
    mod.Tabs.render(); 
    mod.Tabs.openTab('window_main_frdl-webfan-api');


     ";
      return $html;
	}
	    
     
    protected function item_settings(){
		$html = '';
	    $html .=  $this->item_login();
	    $html .=  $this->item_wizard();
	    $html .=  $this->item_db();	
        $html .=  $this->item_repositories();  
	    $html .=  $this->item_expert();  
	     
	     
	  return $html;
	}
	
	
	public function check(&$html, $tab, $check_admin = true, $check_errors_wizard = true){
		
  	    if(true === $check_admin && (!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin'])){
	     	 $html.= '<div id="'.$tab.'" class="wd-tab">'. $this->item_login() .'</div>';
	     	 return false;
		  }
	
    	if(true === $check_errors_wizard && 0 !== count($this->wizard_errors)){
			  $html.= '<div id="'.$tab.'" class="wd-tab"><p><span class="webfan-red">Not installed properly</span>.</p>
        <ul>
        <li>
         '.implode('</li><li>', $this->wizard_errors).'
        </li>
        </ul></div>';
	       return false;
		}		
		
		return true;
	}
	
	
	protected function item_repositories(){
	 $html = '';
    	$tab = 'window_main_frdl-webfan-repositories';
    
        if(true !== $this->check($html, $tab, true, true))return $html;  
    

    	
    		
	   $S = new \frdl\_db(array(
		   'driver' => $this->data['config']['db-driver'],
		   'host' => $this->data['config']['db-host'],
		   'dbname' => $this->data['config']['db-dbname'],
		   'user' => $this->data['config']['db-user'],
		   'password' => $this->data['config']['db-pwd'],
		   'pfx' => $this->data['config']['db-pfx'],
		   
		));
	
	 $html.= '<div id="'.$tab.'" class="wd-tab">'   ;
	 $html.= '<h2 class="webfan-blue">Repositories</h2>';	
 
     $R = $S->i('Repositories');
     $rep = $R->all();
 
    // $html.= print_r($rep,true);
    $html.='<div class="data-box" style="margin:2px;padding:2px;">';
     
     foreach($rep as $num => $r){
     	$html.='<div>';
	 	$html.='<div style="margin:8px;padding:8px;">';
	 	
	 	 $html.='<p><strong>'.$r['name'].'</strong> ('.((1 === intval($r['_use'])) ? 'active' : 'deactivated').')</p>';
	 	 $html.='<p><a href="'.$r['homepage'].'" target="_blank">'.$r['host'].'</a></p>';
	 	 $html.='<p>'.$r['description'].'</p>';	 
	 	 $html.='</div>';
	 	$html.='</div>';
	 }
     
    $html.='</div>';
    
 
     $html.= '</div>';
		   
	  return $html;		
	}
	
	
    protected function item_webfan(){
	
	 $html = '';
	
	 $html.= '<div id="window_main_frdl-webfan-webfan" class="wd-tab" style="position:relative;height:100%;">'   ;
	 $html.= '<h2 class="webfan-blue">Your Webfan Accounts</h2>';	
 
        $html.= '<iframe src="http://webfan.de/auth/frame-1/" style="width:100%;height:100%;overflow:auto;" />';
 
     $html.= '</div>';
		   
	  return $html;
	}
		
    protected function item_icontem(){


	 $html = '';
	
	 $html.= '<div id="window_main_frdl-webfan-icontem" class="wd-tab">'   ;
	 $html.= '<h2 class="webfan-blue">Your Icontem Accounts</h2>';	
 
        $html.= '<iframe src="https://accounts.icontem.com/edit" style="width:100%;height:432px; overflow:auto;" />';
  
  $html.='<table style="width:100%;border:none;vertical-align:top;">';
  $html.='<tr>';
  $html.='<td style="width:50%;">';
  
   	 $html.='<div>';
	 $html.='<legend>Username (<a href="http://webfan.users.phpclasses.org" target="_blank">phpclasses.org</a>)</legend>';
	 $html.='<input type="text" id="composer-user-phpclasses" value="'.((isset($this->data['config']['composer-user-phpclasses'])) ? $this->data['config']['composer-user-phpclasses'] : '').'" />';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'composer-user-phpclasses\', Dom.g(\'composer-user-phpclasses\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);">change</button>';
	 $html.='</div>';
  
   	 $html.='<div>';
	 $html.='<legend>Composer <span>Password</span></legend>';
	 $html.='<input type="password" id="composer-pwd-phpclasses" value="'.((isset($this->data['config']['composer-pwd-phpclasses'])) ? $this->data['config']['composer-pwd-phpclasses'] : '').'" />';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'composer-pwd-phpclasses\', Dom.g(\'composer-pwd-phpclasses\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);">change</button>';
	 $html.='</div>';

     $html.='</td><td>';

   	 $html.='<div>';
	 $html.='<legend>Username (<a href="http://webfan.users.jsclasses.org" target="_blank">jshpclasses.org</a>)</legend>';
	 $html.='<input type="text" id="composer-user-jsclasses" value="'.((isset($this->data['config']['composer-user-jsclasses'])) ? $this->data['config']['composer-userjsclasses'] : '').'" />';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'composer-user-jsclasses\', Dom.g(\'composer-user-jsclasses\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);">change</button>';
	 $html.='</div>';
  
   	 $html.='<div>';
	 $html.='<legend>Composer <span>Password</span></legend>';
	 $html.='<input type="password" id="composer-pwd-jsclasses" value="'.((isset($this->data['config']['composer-pwd-jsclasses'])) ? $this->data['config']['composer-pwd-jsclasses'] : '').'" />';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'composer-pwd-jsclasses\', Dom.g(\'composer-pwd-jsclasses\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);">change</button>';
	 $html.='</div>';

  $html.='</td></tr></table>';

     $html.= '</div>';
		   
		   
	
		   
		   
	  return $html;
	}
	
	  	
    protected function item_expert(){
	 $html = '';
    	$tab = 'window_main_frdl-webfan-expert';
    	
	     if(true !== $this->check($html, $tab, true, false))return $html;  
	
	 $html.= '<div id="'.$tab.'" class="wd-tab">'   ;
	 $html.= '<h2 class="webfan-blue">Expert Editor <span class="webfan-red">(Experts only!)</span></h2>';	
	 
	 $html.='<div style="margin:10px;padding:10px;text-align:center;font-style:italic;">';
      $html.='<p><span class="webfan-red">Change these settings wisely</span></p>';
	 $html.='</div>';		 	 	
	 
    // $html.='<textarea id="wd-frdl-webfan-editor-config-json" style="width:100%;height:350px;overflow:scroll;">';
     $html.='Please edit the file:<p>'.$this->aSess['ADMINDATA']['CONFIGFILE'].'</p>';
     
     $c = $this->data['config'];
     $c['db-pwd'] = '***';
     $c['PIN_HASH'] = '***';
     $c['ADMIN_PWD'] = '***';
     $c['SECRET'] = '***';
     $c['SHAREDSECRET'] = '***';
     
     
     unset($c['callback']);
     unset($c['expect']);   
     unset($c['method']);   
       
          
        $html.= '<pre>';
      $html.= htmlentities(var_export($c, true));
      $html.='</pre>';
    // $html.='</textarea>';
	 $html.='Please edit the file:<p>'.$this->aSess['ADMINDATA']['CONFIGFILE'].'</p>';
	 
	 $html.='</div>';	
		   
	  return $html;
	}
	
	     
	public function wizard_error($msg = 'ERROR', $s = E_USER_ERROR){
		  array_push($this->wizard_errors, $s ." ".$msg);
	}     
	     
    protected function item_db(){
    	$tab = 'window_main_frdl-webfan-db';
    	
	     if(true !== $this->check($html, $tab, true, false))return $html;  
	

     if(true !== $this->connected){
     	    $this->wizard_error( '<span>No connection to database</span>', E_USER_ERROR);
     	}

		  	
	 $html = '';
	
	 $html.= '<div id="'.$tab.'" class="wd-tab">'   ;
	 
	 
	
	 
	 $html.='<table style="width:100%;vertical-align:top;">';
	 $html.='<tr>';
	 $html.='<td style="width:50%;">';
	 
	 $html.= '<h2 class="webfan-blue"><span>Database</span> <span>Settings</span></h2>';	
	 
	 $html.='<div>';
	 $html.='<legend>Driver</legend>';
	 $html.='<input type="text" id="db-driver" value="'.((isset($this->data['config']['db-driver'])) ? $this->data['config']['db-driver'] : '').'" />';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-driver\', Dom.g(\'db-driver\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].html(\'db\');">change</button> (e.g. mysql)';
	 $html.='</div>';
	 
	 
	 $html.='<div>';
	 $html.='<legend>Host</legend>';
	 $html.='<input type="text" id="db-host" value="'.((isset($this->data['config']['db-host'])) ? $this->data['config']['db-host'] : '').'" />';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-host\', Dom.g(\'db-host\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].html(\'db\');">change</button> (e.g. localhost)';
	 $html.='</div>';
	 
	 
	 
	 $html.='<div>';
	 $html.='<legend>User</legend>';
	 $html.='<input type="text" id="db-user" value="'.((isset($this->data['config']['db-user'])) ? $this->data['config']['db-user'] : '').'" />';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-user\', Dom.g(\'db-user\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].html(\'db\');">change</button>';
	 $html.='</div>';
	 	 	 
	 
	 
	 $html.='<div>';
	 $html.='<legend>Database</legend>';
	 $html.='<input type="text" id="db-dbname" value="'.((isset($this->data['config']['db-dbname'])) ? $this->data['config']['db-dbname'] : '').'" />';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-dbname\', Dom.g(\'db-dbname\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].html(\'db\');">change</button>';
	 $html.='</div>';
	 	 	 
	 
	 
	 $html.='<div>';
	 $html.='<legend>Password</legend>';
	 $html.='<input type="password" id="db-pwd" value="'.((isset($this->data['config']['db-pwd'])) ? $this->data['config']['db-pwd'] : '').'" />';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-pwd\', Dom.g(\'db-pwd\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].html(\'db\');">change</button>';
	 $html.='</div>';
	 
	 
	 $html.='<div>';
	 $html.='<legend>Table-Prefix</legend>';
	 $html.='<input type="text" id="db-pfx" value="'.((isset($this->data['config']['db-pfx'])) ? $this->data['config']['db-pfx'] : '').'" />';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-pfx\', Dom.g(\'db-pfx\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].html(\'db\');">change</button> (e.g. wd'.mt_rand(1000,9999).')';
	 $html.='</div>';
	 	 	 
	 	 
	 $html.='<div>';
	 $html.='<legend>Test Connection</legend>';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].test(\'database\'); ">Test Connection</button>';
	 $html.='</div>';	
	  	

	 	  	 
	 $html.='<div style="margin:10px;padding:10px;text-align:center;font-style:italic;">';
      $html.='<p><span class="webfan-red">Change these settings wisely</span></p>';
	 $html.='</div>';		 	 	 
	 	 	 
	 $html.='</td>';
	 $html.='<td>';
	  $html.= '<h2 class="webfan-blue"><span>Database</span> <span>Tables</span></h2>';	
	  

	  
	$tables = array();
	  if(true === $connected){
	  	
     		try{

	     	 $S = new \frdl\_db();	
	     $cfile =  $this->data['config']['FILES']['database-schema'];
	
	     $settings=array(
		   'driver' => $this->data['config']['db-driver'],
		   'host' => $this->data['config']['db-host'],
		   'dbname' => $this->data['config']['db-dbname'],
		   'user' => $this->data['config']['db-user'],
		   'password' => $this->data['config']['db-pwd'],
		   'pfx' => $this->data['config']['db-pfx'],
		   
		);
	   
		 if(file_exists($cfile)){
		 	$oldSchema = $S->load_schema(file_get_contents($cfile)); 
		 	//$oldSchema = $S->load_schema($this->read($cfile, 'rb',  null)); 
		 }else{
		 	 $oldSchema = $S->schema();
		 }
		 if(!is_object($oldSchema))$oldSchema = $S->schema();
		 	 
		
		
		 $S->check($schema, $tables,  null,  true,  false,  false,   \frdl\DB::_($settings, true), $settings, 
		              $oldSchema);
		
		}catch(\Exception $e){
	          $tablesOK = false;
	           $html.= '<span class="webfan-red">Error checking tables!</span>';
		}
       		
		$html.= '<p>Version: '.$oldSchema->version.'/'.$schema->version.'</p>';
        $newTables = false;
        $T = array();
       
       
         $_html = '';
         $_html.='<div class="data-box" style="height:260px;overflow:auto;">';
         foreach($schema->tables as $alias => $t){
			$_html.='<div>';
	         $_html.= '<p><span style="color:'.((
	                     true === $schema->tables[$alias]['exists']
	                  && true === $oldSchema->tables[$alias]['exists'] && $S->isFresh( $oldSchema->tables[$alias]['version'],  $schema->tables[$alias]['version']) )
	                      ? 'green' : 'red').';">'.$alias.' ('. $oldSchema->tables[$alias]['version'].'/'.$schema->tables[$alias]['version'].')</span></p>';					 
	         $_html.= '<p>'.$t['table'].'</p>';
		    $_html.='</div>';	
		    
		   if(true !== $t['exists'] || !$S->isFresh( $oldSchema->tables[$alias]['version'], $schema->tables[$alias]['version']))  $newTables = true;
		   $T[$t['table']] = &$t;
		}
     
       $_html.='</div>';
     
       
     
        if( true === $newTables || !$S->isFresh($oldSchema->version,  $schema->version)){
        	
         $this->wizard_error( '<span>Missing database tables! Please run the database setup!</span>', E_USER_WARNING);
        	 
      	 $html.='<div style="text-align:center;">
      	 <button style="color:green;font-weight:bold;font-size:1.2em;" onclick="if(true!==confirm(\'Please backup your data first, possible data loss while converting!\nBitte machen Sie zunaechst ein Datenbank Backup, Daten koennen durch der Konvertierung verloren gehen!\n(@ToDo: Convert data from db versions)\'))return false;$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cmd(\'frdl setup create-tables -b\', function(){$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].html(\'db\');}); ">
      	 &rArr;&rArr;&rArr;<span>Create</span> <span>Tables</span>&lArr;&lArr;&lArr;
      	 </button>
      	 </div>
      	 ';
	        	
	   	 $this->result->js .= " 
			try{
		    	$.WebfanDesktop.Registry.Programs['nachrichtendienst'].post({
		    		text : '<span>Missing database tables! Please run the database setup!</span>',
		    		type : 'error',
		    		show : true,
		    		callback : function(){\$('#window_frdl-webfan').show();$.WebfanDesktop.Registry.Programs['frdl-webfan'].html('db');},
		    		time : new Date().getTime() / 1000,
		    		newnotif : true,
		    		id : 'system-error-no-database-connection-".$this->date->day()."'
		    	});
			}catch(err){
				console.error(err);
			}	   	 
          ";	 			


      	}

          $html.= $_html;
        $html.='<br />';
        
       $html.='<p>Other tables in '. $this->data['config']['db-dbname'].':</p>';
       $html.='<div class="data-box" style="height:160px;overflow:auto;">';
        foreach($tables as $name => $table){
        	if(isset($T[$name] ))continue;
			$html.= '<p>'.$name.'</p>';
		}
       $html.='</div>';
 
 
       
       }else{
	   	 $html.= '<span class="webfan-red">Not connected.</span>';
	   }
	  
	 $html.='</td>';
	 $html.='</tr>';
	 $html.='</table>';	 
	 	 	 
	 $html.='</div>';	
		   
	
	   if(true !== $connected){
	   	
	   	 $this->result->js .= " 
			try{
		    	$.WebfanDesktop.Registry.Programs['nachrichtendienst'].post({
		    		text : '<span>No connection to database</span>. <span>Please goto the database settings!</span>',
		    		type : 'error',
		    		show : true,
		    		callback : function(){\$('#window_frdl-webfan').show();\$.WebfanDesktop.Registry.Programs['frdl-webfan'].Tabs.openTab('window_main_frdl-webfan-db');},
		    		time : new Date().getTime() / 1000,
		    		newnotif : true,
		    		id : 'system-error-no-database-connection-".$this->date->day()."'
		    	});
			}catch(err){
				console.error(err);
			}	   	 
";	   	 
	   	 
	   }
		   
	  return $html;
	}
	
       
    protected function item_login(){
		$html = '';


      $html.= '<div id="window_main_frdl-webfan-login" class="wd-tab">'   ;
      
	  if(isset($this->aSess['isAdmin']) && true === $this->aSess['isAdmin']){
	  		 $html.= '<p><span>You are logged in as Admin.</span> <button class="wd-btn-no" onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].logout();" >Logout</button></p>';
		 		
		 		 $html.= '<p style="font-size:0.8em;">';
		 		 $html.='<span>Change password</span>: ';
		 		 $html.='<input style="max-width:120px;" id="wd-login-ac-NEWPASS" type="password" name="new_pwd" />
		 		  <span>confirm</span>: <input style="max-width:120px;" id="wd-login-ac-NEWPASS_2" type="password" name="new_pwd_2" /> 
		 		  <button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'admin-pwd\', Dom.g(\'wd-login-ac-NEWPASS\').value,Dom.g(\'wd-login-ac-NEWPASS_2\').value,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig);" >Change password</button>';
		 		  $html.='</p>';
		 		  
		 		  $html.='<p>';
		 		   $html.= '<input type="checkbox" name="CHK_ENABLE_ADMIN_PWD" id="CHK_ENABLE_ADMIN_PWD" '.((!isset($this->data['config']['DISABLE_ADMIN_PWD']) || false === $this->data['config']['DISABLE_ADMIN_PWD']) 
		 		             ? ' checked onclick="
					
						 if(true !== confirm(\'Sure you want to disable the Adminlogin?\nATTENTION YOU MAY LOOSE ACCESS TO YOUR ADMINLOGIN\nIF NO OTHER LOGIN-METHOD AVAILABLE!\'))
		 		    	        { this.setAttribute(\'checked\', 1); return false;	}
					        $.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'DISABLE_ADMIN_PWD\',  \'true\', null, $.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig);  
					        $.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].html(\'login\');
						
						
						return true;
						
						"': ' onclick="
							 if(true !== confirm(\'Sure you want to enable the Adminlogin?\nATTENTION THIS MAYBE INSECURE!\nADDITIONAL LOGIN-METHODS AVAILABLE SOON!\'))
		 		    	               { this.removeAttribute(\'checked\'); return false;	}
		 		    	       $.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'DISABLE_ADMIN_PWD\', \'false\', null, $.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig); 
					        $.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].html(\'login\');      							
						
						
						return true;
						
						" ').'  /> <span>Enable the Adminpassword, allow login with it.</span>';
		 		  $html.='</p>';
		 		  

		 		  
		 		  
		 		 
	   }else{
		 	 $html.= '<p><span>Please loggin!</span></p>';
		 	 $html.=  '<strong>1. <span>PIN</span></strong>:<br />';
		 	 $html.='<input id="wd-login-ac-PIN" type="password" name="PIN" maxlength="2" style="width:32px;" />';
		 	 $html.='<br /><br />';    	 
    	 
 
		 	 $html.='<strong>2. <span>Password</span></strong>:<br />';
		 	 $html.='<input id="wd-login-ac-PASS" type="password" name="pwd" style="width:200px;" />';
		 	 $html.= '<br /><br />'; 
		 	 $html.= '<p><button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].login();" >Login...</button>';
  
	  }

	   $html.='</div>';
	   
	    $this->loginformIsOut = true;
		return $html;
	}
	
}