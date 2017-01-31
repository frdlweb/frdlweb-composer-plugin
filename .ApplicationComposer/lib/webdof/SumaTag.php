<?php

/*
 * SumaTag mainclass
 *
 * @package SumaTag
 * @author Till Wehowski, http://webfan.de
 * @version 1.0.0
 * @License Do What The Fuck You Want To Public License
 * @Requires class wUri http://www.phpclasses.org/package/8005-PHP-Parse-an-URL-and-extract-its-parts.html
 */
namespace webdof;
  
/*
 Usage: 
 
   $st = new SumaTag();
   $keyword = $st->detect();
   var_dump($keyword);
   
*/

class SumaTag
{

 private $se;
 
 /* pointer to wURI */
 private $u;

 function __construct($defaultSetting = TRUE)
  {
     $this->u = & wURI::getInstance();
     $this->se = array();
     if($defaultSetting === TRUE)$this->loadDefaults();
  }


 /*
   Example settings:
               'google', 'q',
               'yahoo',  'p',
               'live',  'q',
               'aolsvc', 'q',
               'bing', 'q',
               'suche.webfan.de', 'q',
               //Test:
               'localhost', 'q',
 */               
 public function loadDefaults()
  {
    $this->addSe('*.google.*', 'q');
    $this->addSe('*.yahoo.*', 'p');
    $this->addSe('*.live.*', 'q');
    $this->addSe('*.bing.*', 'q');
    $this->addSe('*.aolsvc.*', 'q');
    $this->addSe('suche.webfan.de', 'q');
  }

  /*
    addSe - add search engine
    @param string $host E.g.: 'sub.google.com' OR '*.google.com' OR 'google.*' OR '*.*'
    @param string  $kpn - keywordParameterName - The query parameter of the referer url where the search-keyword is found
                                                 e.g. for google it is 'q'
                                                 e.g. for yahoo it is 'p'
  */
  public function addSe($host, $kpn)
   {
     $host = trim($host);
     
     $regex = str_replace('.', '\\.', $host);      
     if(substr($host, 0, 1) !== '*')$regex = '^'.$regex;
     if(substr($host, -1, 1) !== '*')$regex.= '$';
     $regex = str_replace('*', '', $regex);    
     
     $this->se[$host] = array(
         'name' => $host,
         'regex' => $regex,
         'kpn' => $kpn,
      );
   }
   

  /*
    removeSe - remove search engine
    @param string $host  
  */
  public function removeSe($host)
   {
     unset($this->se[$host]);
   }
   
   
  
  public function detect()
   {
      $u = $this->u->getU();
      return $this->check($_SERVER['HTTP_REFERER'], $u->location);
   } 

  /*
    check - check for keyword in referer !Returns only the first match!
    @param string $ref - referer url to check
    @param string  $loc - url found on your page - OPTIONAL
    
    @return FALSE || array(host, keyword, url) 
  */  
  public function check($ref = NULL, $loc = NULL)
   {
    if($loc === NULL || parse_url($loc) === FALSE)
     {
       $u = $this->u->getU();
       $loc = $u->location;
     }  
    if($ref === NULL)$ref = strip_tags($_SERVER['HTTP_REFERER']);
    $ru = parse_url($ref);
    if($ref === '' || $ru === FALSE)return FALSE;
    $r = $this->u->parse_uri($ru['scheme'], $ru['host'], $ru['path'].'?'.$ru['query']);
    
     foreach($this->se as $host => $h)
      {
         $regex = "/".$h['regex']."/";
         if(!preg_match($regex, $r->server) )continue;
         if(!isset($r->query[$h['kpn']]) )continue;
         return array(
           'host' => $h['name'],
           'keyword' => trim(urldecode($r->query[$h['kpn']])),
           'url' => $loc,
         );
      }
    
    return FALSE;
   }


}
//EOF