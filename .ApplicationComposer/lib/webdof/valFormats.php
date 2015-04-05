<?php
/*
  class valFormats
  by T. Wehowski http://webfan.de
  License: Do What The Fuck You Want To Public License, some funcs
           by users from stackoverflow or php.net

  Version: 4.0.2

  This class is a collection of static methods:
  Input -string
  Methods:
  - isint ? integer => true or false
  - isurl ? url => ARRAY or false
  - ismail ? email => true or false
  - ismd5 ? md5 => true or false
  - issha1 ? sha1 => true or false
  - isUUIDVersion ? UUID => uuid version or false
  - isCSSPositionLength ? CSS positioning => true or false
  - isCSSPositionLengthColor ? CSS positioning or color => true or false
  - isCSSColor ? CSS color value => true or false
  - isCSSTextAlign ? CSS text-align value ( left|center|right|justify )  => true or false
  - isCSSVerticalAlign ? CSS vertical-align value ( top|middle|bottom|baseline|sub|super|text-top|text-bottom )  => true or false
  - isCSSPosition ? CSS position value ( static|relative|absolute|fixed )  => true or false
  - isOID ? OID (like "1.3.6.1.4.1.37553.8") => true or false
  - isbase64 ? base64 encoded => true or false
  - valAdress ?    Stra�enname 123
                || Stra�enname 1a
                || Stra�enname 2-b
                || M�ster-/G�sse 123/b
                || 2.nd Street 99d        => true or false
  - germanNameTitle  ? /^[\w\s�������,\)\(\.\-]+$/      => true or false
  - deppenS ? name ends with "s"   =>   returns "s"  or ""
  - valVersion ? IP => false or ip version
  - ip2long  =>  IP to INT/LONGINT
                ip6 : http://stackoverflow.com/questions/18276757/php-convert-ipv6-to-number
                ip4 : http://php.net/manual/de/function.ip2long.php

                list(, $ip) = unpack('l',pack('l',ip2long('200.200.200.200')));

               $ip  = 'fe80:0:0:0:202:b3ff:fe1e:8329';
               $dec = self::ip2long_v6($ip);
               $ip2 = self::long2ip_v6($dec);

              // $ip  = fe80:0:0:0:202:b3ff:fe1e:8329
              // $dec = 338288524927261089654163772891438416681
              // $ip2 = fe80::202:b3ff:fe1e:8329
  - long2ip_v6 => LONGINT  TO IPv6
  - fromCamelCaseToWhiteSpace => http://stackoverflow.com/questions/4519739/split-camelcase-word-into-words-with-php-preg-match-regular-expression
  - isFilename   =>  ^[a-zA-Z0-9\.-_ ]+$       ?  true   : false
*/
namespace webdof;



class valFormats
{

 public static function isFilename($str){
 	return ((preg_match("/^[a-zA-Z0-9\.-_ ]+$/", $str) ) ? TRUE : FALSE); 
 } 


 public static function ip2long($ip, $getVersion = TRUE)
  {
   $version = self::valVersion($ip);
   if($getVersion === FALSE && $version === FALSE)return FALSE;
   if($getVersion === FALSE && $version === 'ipv4')return self::ip2long_v4($ip);
   if($getVersion === FALSE && $version === 'ipv6')return self::ip2long_v6($ip);

   if($getVersion === TRUE && $version === FALSE)return array('version' => FALSE, 'int' => FALSE);
   if($getVersion === TRUE && $version === 'ipv4')return array('version' => $version, 'int' => self::ip2long_v4($ip));
   if($getVersion === TRUE && $version === 'ipv6')return array('version' => $version, 'int' => self::ip2long_v6($ip));

    return trigger_error('inalid argument getVersion in ipFormat::ip2long()!', E_USER_ERROR);
  }
  //eof ip2long


 public static function valVersion($ip)
  {
     if(filter_var($ip, FILTER_FLAG_IPV4))return 'ipv4';
     if(filter_var($ip, FILTER_FLAG_IPV6))return 'ipv6';
     return FALSE;
  }
  //eof valVersion


 public static function ip2long_v4($ip)
  {
    list(, $result) = unpack('l',pack('l',ip2long($ip) )  );
    return $result;
  }
  //eof ip2long_v4


 public static function ip2long_v6($ip) {
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
 //eof ip2long_v6


 public static function long2ip_v6($dec) {
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
 //eof long2ip_v6




  public static function germanNameTitle($name)
   {
     return (preg_match("/^[\w\s�������,\)\(\.\-]+$/", $name)) ? TRUE : FALSE;

   }


  public static function deppenS($name)
    {
      if( strtolower(substr($name, -1, 1)) != 's')
        {
          return 's';
        }else{
              return '';
             }
    }

  //"^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{4})$"
  public static function isbase64($str)
   {
     if (preg_match("/^(?:[A-Za-z0-9+/]{4})*(?:[A-Za-z0-9+/]{2}==|[A-Za-z0-9+/]{3}=|[A-Za-z0-9+/]{4})$/", trim($str))) {
        return TRUE;
     }
     return FALSE;
   }
//eof isbase64




  public static function isint($str)
   {
     if(is_numeric($str)){return TRUE;}else{return FALSE;}
   }

/*
returns parse_url($str) or FALSE if no URL format
*/
  public static function isurl($str)
   {
     $c = parse_url($str);
     if(is_array($c)){return $c;}else{return FALSE;}
   }

  public static function ismail($str)
   {
      if (preg_match("/^([a-zA-Z0-9-])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", trim($str))) {
        return TRUE;
      }
     return FALSE;
   }

  public static function ismd5($str)
   {
      return !empty($str) && preg_match('/^[a-f0-9]{32}$/', $str);
   }


  public static function issha1($str)
   {
      return !empty($str) && preg_match('/^[a-f0-9]{40}$/', $str);
   }


/*
returns UUID version or FALSE if no UUID format
*/
  public static function isUUIDVersion($in, $strict = TRUE)
   {
     if($strict != FALSE)
      {
        if(!preg_match("/\-/", $in) || !preg_match("/^[0-9a-f-]+$/s",$in) )return FALSE;
      }else{
             if(!preg_match("/\-/", $in) || !preg_match("/^[0-9a-z-]+$/s",$in) )return FALSE;
           }

     $u = explode('-', $in);
     if(
            count($u) != 5
        ||  strlen($u[0]) !=  8
        ||  strlen($u[1]) !=  4
        ||  strlen($u[2]) !=  4
        ||  strlen($u[3]) !=  4
        ||  strlen($u[4]) !=  12
     ){
       return FALSE;
     }else{
             return (string)$u[2][0].'';
          }
  }


  public static function isCSSPositionLength($str)
   {
      return !empty($str) && preg_match('/^auto$|^[+-]?[0-9]+\\.?([0-9]+)?(px|em|ex|%|in|cm|mm|pt|pc)?$/', $str);
   }



  public static function isCSSPositionLengthColor($str)
   {
      return !empty($str) && preg_match('/^auto|aqua|black|blue|fuchsia|gray|green|lime|maroon|navy|olive|orange|purple|red|silver|teal|white|yellow$|^[+-]?[A-Fa-f0-9]+\\.?([0-9]+)?(px|em|ex|%|in|cm|mm|pt|pc)?$/', $str);
   }


  public static function isCSSColor($str)
   {
      return !empty($str) && preg_match('/^auto|aqua|black|blue|fuchsia|gray|green|lime|maroon|navy|olive|orange|purple|red|silver|teal|white|yellow$|^\#[A-Fa-f0-9]{6}$/', $str);
   }

 public static function isCSSTextAlign($str)
   {
      return !empty($str) && preg_match('/^left|center|right|justify$/', $str);
   }

 public static function isCSSVerticalAlign($str)
   {
      return !empty($str) && preg_match('/^top|middle|bottom|baseline|sub|super|text-top|text-bottom$/', $str);
   }

 public static function isCSSPosition($str)
   {
      return !empty($str) && preg_match('/^static|relative|absolute|fixed$/', $str);
   }

  public static function valAdress($adress)
   {
    if(preg_match("/^[a-zA-Z0-9�������\/\-\. ]+ +[0-9]+(|[a-z\/\-\.])+$/", $adress) )
    {
     return TRUE;
    }else{
          return FALSE;
       }
   }
//eof valAdress





  public static function isOID($oid)
   {
    if(!preg_match("/^[0-9\.]+$/",$oid))
      {
       return FALSE;
      }else{
           return TRUE;
           }
   }

  /*
  * http://stackoverflow.com/questions/4519739/split-camelcase-word-into-words-with-php-preg-match-regular-expression
  * Converts camelCase string to have spaces between each.
  * @param $camelCaseString
  * @return string
  */
  public static function fromCamelCaseToWhiteSpace($camelCaseString) {
        $re = '/(?<=[a-z])(?=[A-Z])/x';
        $a = preg_split($re, $camelCaseString);
        return join($a, " " );
   }

}
//eof class valFormats