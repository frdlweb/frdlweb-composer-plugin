<?php
namespace webfan\InstallShield\apc;

class i1
{
	public $vm;
	
	protected $_m = array();
	
	function __construct(&$vm = null, $m = null){
		if(null === $vm)$vm = \frdl\webfan\App::God(false)->vm();
		$this->vm = $vm;
		
     $this->_m = array(
	   'apc.admin' => array($this, '_apc_admin_0001')
	 );		
		
	 if(null !== $m && is_array($m)){
		$this->_m = array_merge($this->_m, $m);
	 }	
		
     
	}
	
	
	
	public function install( /* $op = null */){
		$args = func_get_args();
		$op = (0<count($args)) ? array_pop($args) : null;
		
		$this->oldConfig =  (0<count($args)) ? $args[0] : null;
		$this->installArgs = $args;
		
		if(isset($this->_m[$op]) ){
			call_user_func_array($this->_m[$op], array($this));
		}		
		
	}
	
	
	
	protected function _apc_admin_0001($i1 = null){
		if(null === $i1)$i1 = &$this;


		if(!isset($i1->vm->request->request['PIN']) || trim($i1->vm->request->request['PIN']) === ''){
			\frdl\webfan\App::God(false)->apcWarn('You must specify a PIN or username!');
			return;
		}

		if(!isset($i1->vm->request->request['password']) || trim($i1->vm->request->request['password']) === ''){
			\frdl\webfan\App::God(false)->apcWarn('You must specify a password!');
			return;
		}
		
		if(!isset($i1->vm->request->request['password_confirm']) || trim($i1->vm->request->request['password_confirm']) === ''){
			\frdl\webfan\App::God(false)->apcWarn('You must confirm the password!');
			return;
		}			
		
		if($i1->vm->request->request['password_confirm'] !== $i1->vm->request->request['password']){
			\frdl\webfan\App::God(false)->apcWarn('You did not confirm the password correctly!');
			return;
		}				
		
		
$_AdminUser = array();
$_AdminUser['user'] = $i1->vm->request->request['PIN']; 
$_AdminUser['pass'] = array(
          'sha1' => sha1($i1->vm->request->request['password']),
          'oauth' => false,
          'openid' => false,
);      
$_AdminUser['permissions'] = array(
          'admin',
);           
$_AdminUser['email'] = false;



 array_push($i1->vm->context->apc->config->admin, $_AdminUser);
		
		\frdl\apc\Helper::saveConfig($i1->vm, false);
		
       \frdl\webfan\App::God(false)->session('user', $_AdminUser); 
	}
}
