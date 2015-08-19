<?php
/**
 * 
 *  Class \webdof\Text\diff\Compare
 *  
 *   Compares two texts, finds the diffrences and patches back to
 *   the original version using a minimal ammount of data.
 * 
 *   Version: 4.0.0
 *
 *   @Author: Till Wehowski, http://www.webfan.de 
 *   @Author: php Community, http://php.net
 *   @License: Do What The Fuck You Want To Public License
 * 
 *   http://www.phpclasses.org/package/8834-PHP-Class-to-find-differences-in-texts-and-rollback.html
 * 
 * */
namespace webdof\Text\diff;
/**
 * Example: 
$text_1 = 'Dies ist ein Test uber Hackfleisch.<br />Dies ist ein Text uber Hackfleisch.';
$text_2 = "Dies ist Text uber Kackfleisch.<br />Dies ist Test uber Kackfleisch.";

$newversion = $text_2;

$comp = new \webdof\Text\diff\Compare();
$diff = $comp->calcdiffer($text_1, $text_2);
echo $text_1.'<br />';
echo $text_2.'<br />';
echo '<pre>'.print_r($diff, true).'</pre>';

$back = '';
foreach( $diff as $step => $d)
 {
   if($d[0] === '=' || $d[0] === '-')$back.= $d[1];
 }

echo  'Patching:<br />';
echo  $back.'<br />';


echo  '<br />';
echo  'Patching R&uuml;w&auml;rts nur mit &Auml;nderungen und strlen(equals):<br />';

$diff = $comp->minimize( $diff );
$oldversion = $comp->patch( $text_2, $diff );

echo '<pre>'.print_r($diff, true).'</pre>';
echo  $oldversion.'<br />';

echo '<br /><br />Test 2<br />';
$text_1 = 'An einem Sommertag fuhr ich mit dem Fahrrad über die Straße nach Hause.';
$text_2 = 'An einem schönen Sommertag ging ich mit dem Auto über die Straße spazieren.';

echo  $text_1.'<br />';
echo  $text_2.'<br />';

$comp = new \webdof\Text\diff\Compare();
$diff = $comp->calcdiffer($text_1, $text_2);
$diff = $comp->minimize( $diff );
$oldversion =  $comp->patch( $text_2, $diff );

echo  $oldversion.'<br />';
* 
*/
 
class Compare
{

  const ADD = 0;   // = '+';
  const DEL = 1;   // = '-';
  const EQUAL = 2;  // = '=';


  protected $data = array(
	      'old' => NULL,
	      'version' => array(
	                         'new' => NULL,
	                         'diff' => NULL,
	                         ),  
  );
  

  function __construct($old = NULL, $new = NULL)
   {
      if(is_string($old) && is_string($new))return $this->diff($old, $new);
   }


  public function data($data = NULL) {
  	 if(is_array($data) && (isset($data['old']) || isset($data['version'])))$this->data = $data;
	 return $this->data;
  } 

  public function diff($old, $new, $returns = 'version'){
  	 $this->data = array(
	      'old' => $old,
	      'version' => array(
	                         'new' => $new,
	                         'diff' => $this->minimize( $this->calcdiffer($old, $new) ),
	                         ),
	 );
	 
	 switch($returns){
	 	case 'version' : 
		           $result = $this->data['version'];
			break;
        case 'minimized' :	
	 	case 'diff' : 
		           $result = $this->data['version']['diff'];
			break;	
		case 'all' :				
		case 'max' :
			       $this->data['sha1'] = array(
				      'old' => sha1($old),
				      'new' => sha1($new),
				   );
				   
			       $this->data['strlen'] = array(
				      'old' => strlen($old),
				      'new' => strlen($new),
				   );				   
				   $result = $this->data;
			break;
        case 'data' :	
        default :
		          $result = $this->data;
		    break;	  
	    		
	 }
	 
	 return $result;
  }

/*
 *  Alias for patch
 * */
 public function rollback($newversion, $diff)
  {
  	return $this->patch($newversion, $diff);
  }
  
  
  	
 public function patch($newversion, $diff)
  {
    $back = array_reverse($diff);
    $str = '';
    $r = strrev($newversion);
    $offset = 0;
    foreach($back as $step => $d)
     {
      if($d[0] === self::DEL)$d[1] = strrev($d[1]);

      if($d[0] === self::EQUAL)$str.= substr($r, $offset, $d[2]  );
      if($d[0] === self::DEL)$str.= $d[1];
      if($d[0] === self::ADD || $d[0] === self::EQUAL)$offset += $d[2];

     }
    $oldversion = strrev($str);
    return $oldversion;
  }


/*
 * 
 *  minimize 
 *  compress the diff data, we only need the the added and equal strings position and length 
 *  and the value of the deleted strings
 * 
 */
 
 public function minimize($diff)
   {
    foreach($diff as $step => $d)
     {
       if($diff[$step][0] !== self::DEL)$diff[$step][1] = NULL;
     }
    return $diff;
   }


 /*
  * Returns the differences of $a and $b
  */
 public function calcdiffer($a, $b)
 {
  $alen = strlen($a);
  $blen = strlen($b);
  $aptr = 0;
  $bptr = 0;

  $ops = array();

  while($aptr < $alen && $bptr < $blen)
  {
    $matchlen = $this->matchlen(substr($a, $aptr), substr($b, $bptr));
    if($matchlen)
    {
      $str = substr($a, $aptr, $matchlen);
      $ops[] = array(self::EQUAL, $str, strlen($str) );
      $aptr += $matchlen;
      $bptr += $matchlen;
      continue;
    }
    /* Difference found */

    $bestlen=0;
    $bestpos=array(0,0);
    for($atmp = $aptr; $atmp < $alen; $atmp++)
    {
      for($btmp = $bptr; $btmp < $blen; $btmp++)
      {
        $matchlen = $this->matchlen(substr($a, $atmp), substr($b, $btmp));
        if($matchlen>$bestlen)
        {
          $bestlen=$matchlen;
          $bestpos=array($atmp,$btmp);
        }
        if($matchlen >= $blen-$btmp)break;
      }
    }
    if(!$bestlen)break;

    $adifflen = $bestpos[0] - $aptr;
    $bdifflen = $bestpos[1] - $bptr;

    if($adifflen)
    {
      $str = substr($a, $aptr, $adifflen);
      $ops[] = array(self::DEL, $str, strlen($str) );
      $aptr += $adifflen;
    }
    if($bdifflen)
    {
      $str = substr($b, $bptr, $bdifflen);
      $ops[] = array(self::ADD, $str, strlen($str) );
      $bptr += $bdifflen;
    }
    $str = substr($a, $aptr, $bestlen);
    $ops[] = array(self::EQUAL, $str, strlen($str) );
    $aptr += $bestlen;
    $bptr += $bestlen;
  }
  if($aptr < $alen)
  {
    /* b has too much stuff */
    $str = substr($a, $aptr);
    $ops[] = array(self::DEL, $str, strlen($str) );
  }
  if($bptr < $blen)
  {
    /* a has too little stuff */
    $str = substr($b, $bptr);
    $ops[] = array(self::ADD, $str, strlen($str)  );
  }
  return $ops;
 }


 protected function matchlen(&$a, &$b)
 {
  $c=0;
  $alen = strlen($a);
  $blen = strlen($b);
  $d = min($alen, $blen);
  while($a[$c] == $b[$c] && $c < $d)
    $c++;
  return $c;
 }



}
//eof


