<?php
/*
  wURI url parsing class
  by T. Wehowski http://webfan.de
  License: Do What The Fuck You Want To Public License
  Download: http://www.phpclasses.org/package/8005-PHP-Parse-an-URL-and-extract-its-parts.html
  Version: 3.0.0 webdof edition/build

examples:
####

$URI = wURI::getInstance();
$location = $URI::getU();
if( in_array('@me', $location->dirs ) )
{
 //Account page...

}

####

$URI = wURI::getInstance();
$xUrl = $URI::parse_uri('http', 'subdomain.domain.tld', '/adirectory/?aquery=123');
$host = $xUrl->dom;   // $host = domain.tld
$q = $xUrl->query['aquery']; // $q = 123
####

$URI = wURI::getInstance();
$location = $URI::getU();
var_dump($location);

*/
namespace webdof;



class wURI
{

private static $instance = NULL;

static $u;


private function __construct()
{
self::getLocation();
}
//eof constructor


public static function getInstance() {

       if (NULL === self::$instance) {
           self::$instance = new self;
       }
       return self::$instance;
}



private function __clone() {}


static function getLocation()
{
$protocoll = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
self::$u = self::parse_uri($protocoll, $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']);
}


static function getU()
{
return self::$u;
}
//eof getU



static function parse_uri($protocoll, $SERVER_NAME, $REQUEST_URI)
{
$xw = new \stdclass;
$xw->protocoll = $protocoll;
$xw->protocoll  = trim($xw->protocoll , ' :/');
$xw->location = $xw->protocoll.'://'.$SERVER_NAME.$REQUEST_URI;
$xw->server = $SERVER_NAME;
$xw->req_uri = $REQUEST_URI;
if(substr($xw->req_uri, 0, 1) == '/' )
 {
    $xw->uri = substr($xw->req_uri,1 , strlen($xw->req_uri) );
 }else{
       $xw->uri = $xw->req_uri;
      }

$xw->dirs = explode('/', $xw->uri);
$xw->dirs_rev = array_reverse($xw->dirs);
$xw->dir = dirname( $xw->req_uri );


$xw->dots = explode('.', $xw->uri);
$xw->dots_rev = array_reverse($xw->dots);

$xw->host = explode('.',$xw->server);
$xw->host_rev = array_reverse($xw->host);
$xw->tld = @$xw->host_rev[0];
$xw->dom = @$xw->host_rev[1].'.'.$xw->host_rev[0];


$t = explode('?', $xw->req_uri);
$xw->file = basename( $t[0] );
$xw->query = array();
if(isset($t[1]))parse_str($t[1], $xw->query);

$t = explode('.', $xw->file);
$t = array_reverse($t);
$xw->file_ext = $t[0];

$xw->classic = parse_url($xw->location);


/*
http://php.net/manual/en/function.parse-url.php#106731
*/
$xw->unparse_classic = function(Array $parsed = null) use($xw) {
  if(!is_array($parsed) && isset($xw->classic) && is_array($xw->classic))$parsed = $xw->classic;	
  $scheme   = isset($parsed['scheme']) ? $parsed['scheme'] . '://' : '';
  $host     = isset($parsed['host']) ? $parsed['host'] : '';
  $port     = isset($parsed['port']) ? ':' . $parsed['port'] : '';
  $user     = isset($parsed['user']) ? $parsed['user'] : '';
  $pass     = isset($parsed['pass']) ? ':' . $parsed['pass']  : '';
  $pass     = ($user || $pass) ? "$pass@" : '';
  $path     = isset($parsed['path']) ? $parsed['path'] : '';
  $query    = isset($parsed['query']) ? '?' . $parsed['query'] : '';
  $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
  return "$scheme$user$pass$host$port$path$query$fragment";
} ;



return $xw;
}
//eof parse_uri




}
//eof