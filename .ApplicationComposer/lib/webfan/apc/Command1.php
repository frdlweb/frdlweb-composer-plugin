<?php
namespace webfan\InstallShield\apc;



abstract class Command1
{

	public $cli;
	
    protected $argtoks;
    protected $_result = array();
 	
	
    abstract public function run(); 
     	
	function __construct(&$cli){
		$this->cli = $cli;
	}

    
  public function __get($name)
    {
       // echo "Getting '$name'\n";
      //  if (array_key_exists($name, $this->data)) {
      //      return $this->data[$name];
      //  }
     switch($name){
     	case 'result' :
     	    return $this->_result;
     	    break;
	
	 	
	 	default:
         return null;	 	
	 	break;
	 }

         $trace = debug_backtrace();
         trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
            
            
         return null;
    }
    
    	
   public function getRequestOption($opt){
   	 foreach($this->argtoks['options'] as $num => $o){
	 	if($opt === $o['opt']){
			return $o['value'];
		}
	 }
	 return null;
   }
   
  final public function isArg($argument){
   	  return ($this->argpos($argument) > -1);
   }  
   
  final public function argpos($argument){
   	 foreach($this->argtoks['arguments'] as $num => $arg){
	 	if($argument === $arg['cmd']){
			return $arg['pos'];
		}
	 }
	 return -1;
   }
      
  final public function updateRequestOption($opt, $v){
   	 foreach($this->argtoks['options'] as $num => &$o){
	 	if($opt === $o['opt']){
			$o['value'] = $v;
			return true;
		}
	 }
	 return false;
   }	
	
	public function __invoke($args){
   	   $this->argtoks = $args;	
   	   return $this;	
	}	

	
	
		
}
