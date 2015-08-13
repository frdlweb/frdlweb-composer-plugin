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

    function __construct(){
		parent::__construct();
		$this->html = '';
		 $this->loginformIsOut = false;
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
	    	if(null !== $this->item)$this->html .= $this->{'item_'.$this->result->item}();
	   }catch(\Exception $e){
	   	  $this->statusText = 'Error: '.$e->getMessage();
	   }
     
       $this->result->html = base64_encode($this->html);
    }
    
        
    public function required()
    {
       $args = func_get_args();
    }
    
    
    /*
         THIS.Tabs.addTab('#window_main_frdl-webfan-login', 'Login', 'window_main_frdl-webfan-login', false);
     THIS.Tabs.addTab('#window_main_frdl-webfan-db', 'Database', 'window_main_frdl-webfan-db', true);
     THIS.Tabs.addTab('#window_main_frdl-webfan-icontem', 'Icontem', 'window_main_frdl-webfan-icontem', true);
     THIS.Tabs.addTab('#window_main_frdl-webfan-webfan', 'Webfan', 'window_main_frdl-webfan-webfan', true);
     THIS.Tabs.addTab('#window_main_frdl-webfan-expert', 'Expert', 'window_main_frdl-webfan-expert', true);
     */
    protected function item_settings(){
	 
	
		
	   $html .=  $this->item_login();
       $html .=  $this->item_icontem();  	
       $html .=  $this->item_webfan();  
	   if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']){
	     	 return $html;
		  }
		
		
		 $html .=  $this->item_db();	 
	     $html .=  $this->item_expert();  
	     
	     
	  return $html;
	}
	
    protected function item_webfan(){
	  //  if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']){
	     	// return (true !== $this->loginformIsOut) ? $this->item_login() : '';
	     //	 return  $this->item_login();
		//  }
	 $html = '';
	
	 $html.= '<div id="window_main_frdl-webfan-webfan" class="wd-tab">'   ;
	 $html.= '<h2 class="webfan-blue">Your Webfan Accounts</h2>';	
 
        $html.= '<iframe src="http://webfan.de/auth/frame-1/" style="width:100%;height:450px;overflow:auto;" />';
 
     $html.= '</div>';
		   
	  return $html;
	}
		
    protected function item_icontem(){
	  //  if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']){
	     	// return (true !== $this->loginformIsOut) ? $this->item_login() : '';
	     //	 return  $this->item_login();
		//  }
	 $html = '';
	
	 $html.= '<div id="window_main_frdl-webfan-icontem" class="wd-tab">'   ;
	 $html.= '<h2 class="webfan-blue">Your Icontem Accounts</h2>';	
 
        $html.= '<iframe src="https://accounts.icontem.com/edit" style="width:100%;height:432px; overflow:auto;" />';
  
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


     $html.= '</div>';
		   
		   
		/* $this->result->js = 'alert(\'DB settings\');';    */
		   
		   
	  return $html;
	}
	
	  	
    protected function item_expert(){
	    if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']){
	     	// return (true !== $this->loginformIsOut) ? $this->item_login() : '';
	     	 return '<div id="window_main_frdl-webfan-expert" class="wd-tab">'. $this->item_login() .'</div>';
		  }
	 $html = '';
	
	 $html.= '<div id="window_main_frdl-webfan-expert" class="wd-tab">'   ;
	 $html.= '<h2 class="webfan-blue">Expert Editor <span class="webfan-red">(Experts only!)</span></h2>';	
	 $html.='<p><span class="webfan-red">Only use if you know what you are doing!!! - Possible site effects, DO NOT USE ON PRODUCTION SITE</span></p>';
	 
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
	
	     
    protected function item_db(){
	    if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin']){
	     	// return (true !== $this->loginformIsOut) ? $this->item_login() : '';
	     	 return '<div id="window_main_frdl-webfan-db" class="wd-tab">'. $this->item_login() .'</div>';
		  }
	
		ini_set('display_errors', 0);
		/*
	try{

		$db = new \frdl\DB(array(
		   'driver' => $this->data['config']['db-driver'],
		   'host' => $this->data['config']['db-host'],
		   'dbname' => $this->data['config']['db-dbname'],
		   'user' => $this->data['config']['db-user'],
		   'password' => $this->data['config']['db-pwd'],
		   
		));			
		$connected = true;
		}catch(\Exception $e){
	          $connected = false;
		}
	*/
		try{
				   $Console = new \frdl\ApplicationComposer\Console;
				   $Console->applyApp($this);
 			       $Console->exe('utest database');
				   $r =  $Console->dump();
				   $connected = (intval($r->code) === 200 );
		}catch(\Exception $e){
	          $connected = false;
		}
						  	
	 $html = '';
	
	 $html.= '<div id="window_main_frdl-webfan-db" class="wd-tab">'   ;
	 
	 $html.='<table style="width:100%;vertical-align:top;">';
	 $html.='<tr>';
	 $html.='<td style="width:50%;">';
	 
	 $html.= '<h2 class="webfan-blue"><span>Database</span> <span>Settings</span></h2>';	
	 
	 $html.='<div>';
	 $html.='<legend>Driver</legend>';
	 $html.='<input type="text" id="db-driver" value="'.((isset($this->data['config']['db-driver'])) ? $this->data['config']['db-driver'] : '').'" />';
	 $html.='<button onclick="if(true !== confirm(\'Are you sure to change the config? This can have dirty side effects if you do not know what you are doing!\'))return;$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-driver\', Dom.g(\'db-driver\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);">change</button> (e.g. mysql)';
	 $html.='</div>';
	 
	 
	 $html.='<div>';
	 $html.='<legend>Host</legend>';
	 $html.='<input type="text" id="db-host" value="'.((isset($this->data['config']['db-host'])) ? $this->data['config']['db-host'] : '').'" />';
	 $html.='<button onclick="if(true !== confirm(\'Are you sure to change the config? This can have dirty side effects if you do not know what you are doing!\'))return;$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-host\', Dom.g(\'db-host\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);">change</button> (e.g. localhost)';
	 $html.='</div>';
	 
	 
	 
	 $html.='<div>';
	 $html.='<legend>User</legend>';
	 $html.='<input type="text" id="db-user" value="'.((isset($this->data['config']['db-user'])) ? $this->data['config']['db-user'] : '').'" />';
	 $html.='<button onclick="if(true !== confirm(\'Are you sure to change the config? This can have dirty side effects if you do not know what you are doing!\'))return;$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-user\', Dom.g(\'db-user\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);">change</button>';
	 $html.='</div>';
	 	 	 
	 
	 
	 $html.='<div>';
	 $html.='<legend>Database</legend>';
	 $html.='<input type="text" id="db-dbname" value="'.((isset($this->data['config']['db-dbname'])) ? $this->data['config']['db-dbname'] : '').'" />';
	 $html.='<button onclick="if(true !== confirm(\'Are you sure to change the config? This can have dirty side effects if you do not know what you are doing!\'))return;$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-dbname\', Dom.g(\'db-dbname\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);">change</button>';
	 $html.='</div>';
	 	 	 
	 
	 
	 $html.='<div>';
	 $html.='<legend>Password</legend>';
	 $html.='<input type="password" id="db-pwd" value="'.((isset($this->data['config']['db-pwd'])) ? $this->data['config']['db-pwd'] : '').'" />';
	 $html.='<button onclick="if(true !== confirm(\'Are you sure to change the config? This can have dirty side effects if you do not know what you are doing!\'))return;$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-pwd\', Dom.g(\'db-pwd\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);">change</button>';
	 $html.='</div>';
	 
	 
	 $html.='<div>';
	 $html.='<legend>Table-Prefix</legend>';
	 $html.='<input type="text" id="db-pfx" value="'.((isset($this->data['config']['db-pfx'])) ? $this->data['config']['db-pfx'] : '').'" />';
	 $html.='<button onclick="if(true !== confirm(\'Are you sure to change the config? This can have dirty side effects if you do not know what you are doing!\'))return;$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].cnf(\'db-pfx\', Dom.g(\'db-pfx\').value,null,$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].formConfig, true);">change</button> (e.g. wd'.mt_rand(1000,9999).')';
	 $html.='</div>';
	 	 	 
	 	 
	 $html.='<div>';
	 $html.='<legend>Test Connection</legend>';
	 $html.='<button onclick="$.WebfanDesktop.Registry.Programs[\'frdl-webfan\'].test(\'database\'); ">Test Connection</button>';
	 $html.='</div>';	 	 	 
	 	 	 
	 	 	 
	 $html.='</td>';
	 $html.='<td>';
	  $html.= '<h2 class="webfan-blue"><span>Database</span> <span>Tables</span></h2>';	
	  
	
	  if(true !== $connected){

       
       }
	  
	 $html.='</td>';
	 $html.='</tr>';
	 $html.='</table>';	 
	 	 	 
	 $html.='</div>';	
		   
		if(!isset($this->result->js)) $this->result->js = '';  
	   if(true !== $connected){
	   	 $this->result->js .= " 
			try{
		    	$.WebfanDesktop.Registry.Programs['nachrichtendienst'].post({
		    		text : '<span>The database is not connected. Please goto the database settings!</span>',
		    		type : 'error',
		    		show : true,
		    		callback : function(){\$(\'#window_frdl-webfan\').show();\$.WebfanDesktop.Registry.Programs['frdl-webfan'].Tabs.openTab('window_main_frdl-webfan-db');},
		    		time : new Date().getTime() / 1000,
		    		newnotif : true,
		    		id : 'system-error-no-database-connection'
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