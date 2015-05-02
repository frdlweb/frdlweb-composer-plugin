<?php

/*
  class valFormats
  by T. Wehowski http://www.webfan.de
  License: Do What The Fuck You Want To Public License, some funcs
           by users from stackoverflow or php.net

  Version: 5.0.0
 
  Change Log:
    - fixed isint
    - static methods changed to non-static, fixed
    - enables to call all methods as static (Backward compatibillity)
  
  This class is a collection of the following methods:
 
  @Input - string [,options -boolean]
  
::class-Methods.
  - create @returns new instance of self
 
::validation-Methods:  => @returns MIXED :(or) FALSE
  - isFilename   ? ^[a-zA-Z0-9\.-_ ]+$ =>  true : false
  - isint ? integer => true : false
  - isurl ? url => ARRAY : false
  - ismail ? email => true : false
  - ismd5 ? md5 => true : false
  - issha1 ? sha1 => true : false
  - isUUID ? UUID => true : false
  - isUUIDVersion ? UUID => UUID version : false
  - isCSSPositionLength ? CSS positioning => true : false
  - isCSSPositionLengthColor ? CSS positioning or color => true : false
  - isCSSColor ? CSS color value => true : false
  - isCSSTextAlign ? CSS text-align value ( left|center|right|justify )  => true : false
  - isCSSVerticalAlign ? CSS vertical-align value ( top|middle|bottom|baseline|sub|super|text-top|text-bottom )  => true : false
  - isCSSPosition ? CSS position value ( static|relative|absolute|fixed )  => true : false
  - isOID ? OID (like "1.3.6.1.4.1.37553.8") => true : false
  - isbase64 ? base64 encoded => true : false
  - valAdress ?    Straßenname 123
                || Straßenname 1a
                || Straßenname 2-b
                || Müster-/GÄsse 123/b
                || 2.nd Street 99d        => true : false
  - germanNameTitle  ? /^[\w\sÄÖÜäöüß,\)\(\.\-]+$/      => true : false
  - deppenS ? name ends with "s"   =>   returns "s"  : ""
  - valVersion ? IP => false : ip version
 
::formatting-Methods
  - ip2long  =>  IP to INT/LONGINT
                ip6 : http://stackoverflow.com/questions/18276757/php-convert-ipv6-to-number
                ip4 : http://php.net/manual/de/function.ip2long.php

                list(, $ip) = unpack('l',pack('l',ip2long('200.200.200.200')));

               $ip  = 'fe80:0:0:0:202:b3ff:fe1e:8329';
               $dec = valFormats::ip2long_v6($ip);
               $ip2 = valFormats::long2ip_v6($dec);

              // $ip  = fe80:0:0:0:202:b3ff:fe1e:8329
              // $dec = 338288524927261089654163772891438416681
              // $ip2 = fe80::202:b3ff:fe1e:8329
  - long2ip_v6 => LONGINT  TO IPv6
  - fromCamelCaseToWhiteSpace => http://stackoverflow.com/questions/4519739/split-camelcase-word-into-words-with-php-preg-match-regular-expression

 * 
*/
namespace webdof;

class valFormats
{

 const MODE_VALIDATE = 'validate';
 const MODE_FORMAT = 'format';

 protected $in;
 protected $valid;
 protected $out;
 
 protected $deprecated;
 protected static $creators;

 protected $mode = null;

 function __construct(){
 	$this->deprecated = array(
        'valAdress' => '_isaddress',
        'germanNameTitle' => '_isname',        
        'valVersion' => '_isip',          
		'ip2long' => '_ip2long',      
		'ip2long_v4' => '_ip2long_v4',              
		'ip2long_v6' => '_ip2long_v6',         
		'long2ip_v6' => '_long2ip_v6',
        
        
		'fromCamelCaseToWhiteSpace' => '_camelcase2whitespace'
    );
	
	$this->creators = array('c','g','create');
	
    $this->clear();
 } 


 /**
  * Mock method, comment out when  tested !?
  */
 public function test(){
 	echo '<pre>';
 	$str = array(
	   'test.zip' ,
	   3,
	   'http://www.webfan.de',
	   'php.support@webfan.de',
	   'Till Wehowski',
	   'Wattenscheiderstraße 59',
	   '1.3.6.1.4.1.37553.8.1.8.8',
	   'ffffff9a-1e7d-5547-8ede-5aee3c939a37',
	   'd41d8cd98f00b204e9800998ecf8427e',
	   'da39a3ee5e6b4b0d3255bfef95601890afd80709',
	   '16px',
	   'blue',
	   'center',
	   'middle',
	   'fixed',
	   '127.0.0.1',
	   base64_encode(mt_rand(10000,999999)),
	   
	   '34367',
	   'dölfkltgß5   ö4ü359',
	   'z435 j4894  rk ftz',
	   
   );
   foreach($str as $num => $s){
   	   echo $s."\r\n";
   	   echo print_r($this->is($s),true)."\r\n\r\n";
   }
   
   echo '</pre>';
 } 


 public function is($in, $format = null){
 	if(is_string($format)){
 		$method = '_is'.strtolower($format);
		$r = $this->{$method}($in);
 	}elseif(is_array($format)){
 		$r = array();
		foreach($format as $pos => $f){
			$method = '_is'.strtolower($f);
			$r[$f] = $this->{$method}($in);
		}
    }elseif(null === $format){
    	$ref = new \ReflectionClass(get_class($this));
		$methods = $ref->getMethods();
 		$r = array();
		foreach($methods as $index => $m){
			if('is' === substr($m->name,1,3) ){
         		$method = '_is'.substr($m->name,4,strlen($m->name));
			    $r[$f] = $this->{$method}($in);				
			}
       }
    }

   return $r;
 }
 

 public function __get($name){
 	return (property_exists($this, $name)) ? $this->{$name} : null;
 }

 public function clear(){
 	$this->in = null;
	$this->valid = false;
	$this->out = '';
	$this->mode = null;
	$this->from = null;
	$this->to = null;
	return $this;
 }

 public static function __callStatic($name, $arguments)
 {
     if(in_array($name,self::$creators))return new self();
	 
 	 try{
    	  return call_user_func_array(array(new self,$name),$arguments);
		}catch(Exeption $e){
			 $trace = debug_backtrace();
		     trigger_error($e->getMesage().' '.$trace[0]['file'].' '.$trace[0]['line'], E_USER_ERROR);
			 return false;
		}
 }
	


 public function __call($name, $arguments)
 {
    if(in_array($name,self::$creators))return new self();
	
    $func = $name;
	$this->valid = false;
		
	//fixed old versions and deprecated method names (backwards compatibillity)
   if(isset($this->deprecated[$func])){
		trigger_error('Deprecated method call '.get_class($this).'::'.$func.', instead use: '.get_class($this).'::'.$this->deprecated[$func],  E_USER_DEPRECATED);
	    $name = $this->deprecated[$func];	
	}
    	
	if(substr($name,0,1) !== '_'){		
        $name = '_'.$name;
	}
	$name = strtolower($name);
	
	$this->in = $arguments[0];
	$this->out = '';

    $f = explode('2',$func, 2);
	$this->mode = ('is' === substr($name,1,3)) ? self::MODE_VALIDATE : ((2 === count($f)) ? self::MODE_FORMAT : null);
	if(self::MODE_FORMAT === $this->mode){
		$this->from = $f[0];	
	    $this->to = $f[1];
	}
	
	

	if(!is_callable(array($this,$name)) ){
	    $trace = debug_backtrace();
		trigger_error('Unsupported method call '.get_class($this).'::'.$name.' in '.$trace[0]['file'].' '.$trace[0]['line'], E_USER_ERROR);
		return false;
	}
	
    try{
    	 $result = call_user_func_array(array($this,$name),$arguments);
		}catch(Exeption $e){
			 $trace = debug_backtrace();
		     trigger_error($e->getMesage().' called in '.$trace[0]['file'].' '.$trace[0]['line'], E_USER_ERROR);
			 return false;
		}
 	
	 $this->out = ($this->mode === self::MODE_FORMAT && !is_bool($result) ) ? $result 
	                      : (($this->mode === self::MODE_VALIDATE && false !== $result) ? $this->in : '');	
	 $this->valid = ($this->mode === self::MODE_VALIDATE && false !== $result) ? true 
	                  : (($this->mode === self::MODE_FORMAT && false !== $result) ? $result : false);
	 	
	 return $result;	
 }
	


 public function deppenS($name)
    {
      if( strtolower(substr($name, -1, 1)) != 's')
        {
          return 's';
        }else{
              return '';
             }
    }

	

 protected function _isname($name)
   {
	  return (preg_match("/^[\w\sÄÖÜäöüß,\)\(\.\-]+$/", $name)) ? true : false;
   }


 protected function _isendingwiths($name)
    {
      if( strtolower(substr($name, -1, 1)) !== 's')
        {
          return false;
        }else{
              return true;
             }
    }



  //"^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{4})$"
  protected function _isbase64($str)
   {
     if (preg_match("/^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{4})$/", trim($str))) {
        return TRUE;
     }
     return FALSE;
   }





  protected function _isint($str)
   {
   	 return preg_match("/^[0-9]{1,}$/", $str);
   }


  protected function _isurl($str)
   {
     $c = parse_url($str);
     if(is_array($c)){return $c;}else{return FALSE;}
   }

  protected function _ismail($str)
   {
      if (preg_match("/^([a-zA-Z0-9-])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", trim($str))) {
        return TRUE;
      }
     return FALSE;
   }

 protected function _ismd5($str)
   {
      return !empty($str) && preg_match('/^[a-f0-9]{32}$/', $str);
   }


 protected function _issha1($str)
   {
      return !empty($str) && preg_match('/^[a-f0-9]{40}$/', $str);
   }


 protected function _isuuid($in){
 	 $c = $this->_isuuidversion($in, true);
 	 return (false !== $c && preg_match("/^[0-5]{1,1}$/",$c))?true:false;	 	
 }
 
 /**
 * returns UUID version or FALSE if no UUID format
 */
 protected function _isuuidversion($in, $strict = true)
   {
     if(false !== $strict)
      {
        if(!preg_match("/\-/", $in) || !preg_match("/^[0-9a-f-]+$/s",$in) )return false;
      }else{
             if(!preg_match("/\-/", $in) || !preg_match("/^[0-9a-z-]+$/s",$in) )return true;
           }

     $u = explode('-', $in);
     if(
            count($u) !== 5
        ||  strlen($u[0]) !==  8
        ||  strlen($u[1]) !==  4
        ||  strlen($u[2]) !==  4
        ||  strlen($u[3]) !==  4
        ||  strlen($u[4]) !==  12
     ){
       return false;
     }else{
             return (string)$u[2][0].'';
          }
  }



  protected function _iscsspositionlength($str)
   {
      return !empty($str) && preg_match('/^auto$|^[+-]?[0-9]+\\.?([0-9]+)?(px|em|ex|%|in|cm|mm|pt|pc)?$/', $str);
   }


  protected function _iscsspositionlengthcolor($str)
   {
      return !empty($str) && preg_match('/^auto|aqua|black|blue|fuchsia|gray|green|lime|maroon|navy|olive|orange|purple|red|silver|teal|white|yellow$|^[+-]?[A-Fa-f0-9]+\\.?([0-9]+)?(px|em|ex|%|in|cm|mm|pt|pc)?$/', $str);
   }


  protected function _iscsscolor($str)
   {
      return !empty($str) && preg_match('/^auto|aqua|black|blue|fuchsia|gray|green|lime|maroon|navy|olive|orange|purple|red|silver|teal|white|yellow$|^\#[A-Fa-f0-9]{6}$/', $str);
   }

 protected function _iscsstextalign($str)
   {
      return !empty($str) && preg_match('/^left|center|right|justify$/', $str);
   }

 protected function _iscssverticalalign($str)
   {
      return !empty($str) && preg_match('/^top|middle|bottom|baseline|sub|super|text-top|text-bottom$/', $str);
   }
   
 protected function _iscssposition($str)
   {
      return !empty($str) && preg_match('/^static|relative|absolute|fixed$/', $str);
   }



  protected function _isaddress($adress)
   {
     if(preg_match("/^[a-zA-Z0-9äöüÄÖÜß\/\-\. ]+ +[0-9]+(|[a-z\/\-\.])+$/", $adress) )
    {
     return TRUE;
    }else{
          return FALSE;
       }
   }



  protected function _isoid($oid)
   {
    if(!preg_match("/^[0-9\.]+$/s",$oid))
      {
       return FALSE;
      }else{
           return TRUE;
           }
   }



 protected function _isfilename($str){
 	return ((preg_match("/^[a-zA-Z0-9\.-_ ]+$/", $str) ) ? TRUE : FALSE); 
 } 
 
 
 protected function _isip($ip)
  {
     if(filter_var($ip, FILTER_FLAG_IPV4))return 'ipv4';
     if(filter_var($ip, FILTER_FLAG_IPV6))return 'ipv6';
     return FALSE;
  }
  
 
 /**
  * http://stackoverflow.com/questions/4519739/split-camelcase-word-into-words-with-php-preg-match-regular-expression
  * Converts camelCase string to have spaces between each.
  * @param $camelCaseString
  * @return string
  */
  protected function _camelcase2whitespace($camelCaseString){
  	    $re = '/(?<=[a-z])(?=[A-Z])/x';
        $a = preg_split($re, $camelCaseString);
        return join($a, " " );
  }
 
 
 /**
  * IP Addresses...
  *  - php.net
  */
  protected function _ip2long($ip, $getVersion = TRUE)
  {
   $version = $this->_isip($ip);
   if($getVersion === FALSE && $version === FALSE)return FALSE;
   if($getVersion === FALSE && $version === 'ipv4')return $this->_ip2long_v4($ip);
   if($getVersion === FALSE && $version === 'ipv6')return $this->_ip2long_v6($ip);

   if($getVersion === TRUE && $version === FALSE)return array('version' => FALSE, 'int' => FALSE);
   if($getVersion === TRUE && $version === 'ipv4')return array('version' => $version, 'int' => $this->_ip2long_v4($ip));
   if($getVersion === TRUE && $version === 'ipv6')return array('version' => $version, 'int' => $this->_ip2long_v6($ip));

    return trigger_error('inalid argument getVersion in ipFormat::ip2long()!', E_USER_ERROR);
  }




 protected function _ip2long_v4($ip)
  {
    list(, $result) = unpack('l',pack('l',ip2long($ip) )  );
    return $result;
  }



 protected function _ip2long_v6($ip) {
    $ip_n = inet_pton($ip);
    $bin = '';
    for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
        $bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
    }

    if (function_exists('gmp_init')) {
        return gmp_strval(gmp_init($bin, 2), 10);
    } elseif (function_exists('bcadd')) {
        $dec = '0';
        for ($i = 0; $i < strlen($bin); $i++) {
            $dec = bcmul($dec, '2', 0);
            $dec = bcadd($dec, $bin[$i], 0);
        }
        return $dec;
    } else {
        trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
    }
 }



 protected function _long2ip_v6($dec) {
    if (function_exists('gmp_init')) {
        $bin = gmp_strval(gmp_init($dec, 10), 2);
    } elseif (function_exists('bcadd')) {
        $bin = '';
        do {
            $bin = bcmod($dec, '2') . $bin;
            $dec = bcdiv($dec, '2', 0);
        } while (bccomp($dec, '0'));
    } else {
        trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
    }

    $bin = str_pad($bin, 128, '0', STR_PAD_LEFT);
    $ip = array();
    for ($bit = 0; $bit <= 7; $bit++) {
        $bin_part = substr($bin, $bit * 16, 16);
        $ip[] = dechex(bindec($bin_part));
    }
    $ip = implode(':', $ip);
    return inet_ntop(inet_pton($ip));
 }
 
 
}
