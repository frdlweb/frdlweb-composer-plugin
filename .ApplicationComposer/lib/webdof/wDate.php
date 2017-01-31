<?php

/*
 wDate by Webfan.de, T. Wehowski
 License: Do What The Fuck You Want To Public License

    just another date wrapper
 
 Usage example:
 - Your you can pass a timestamp as optional parameter to the functions
 
$D = new wDate();
if(!defined('HEUTE_JAHR'))define('HEUTE_JAHR', $D->year() );
if(!defined('HEUTE_MONAT'))define('HEUTE_MONAT', $D->month() );
if(!defined('HEUTE_MONAT_NULLED'))define('HEUTE_MONAT_NULLED', $D->month0() );

if(!defined('HEUTE_TAG'))define('HEUTE_TAG', $D->day() );

if(!defined('HEUTE_WEDAY'))define('HEUTE_WEDAY', $D->weekday() );
if(!defined('HEUTE_MONTHSHORT'))define('HEUTE_MONTHSHORT', $D->monthS() );

if(!defined('JETZT_STUNDE'))define('JETZT_STUNDE', $D->hour() );
if(!defined('JETZT_MINUTE'))define('JETZT_MINUTE', $D->minute() );
if(!defined('JETZT_SEKUNDE'))define('JETZT_SEKUNDE', $D->sec() );
  
*/
namespace webdof;

class wDate
{

var $n;

function __construct($time = NULL)
{
if( !is_numeric($time) )$time = time();
$this->n = $time;
}




function year($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return date('Y', $time);
}
//eof jahr


function month($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return date('n', $time);
}
//eof


function month0($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return date('m', $time);
}
//eof

function monthS($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return date('M', $time);
}
//eof

function day($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return date('j', $time);
}
//eof

function weekday($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return date('D', $time);
}
//eof

function week($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return date('W', $time);
}
//eof



function hour($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return date('H', $time);
}
//eof


function minute($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return date('i', $time);
}
//eof


function sec($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return date('s', $time);
}
//eof


function now($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return $this->hour($time).':'.$this->minute($time).' '.$this->sec($time);
}
//eof


function date($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return $this->day($time).'.'.$this->month($time).'.'.$this->year($time);
}
//eof


function dateR($time = NULL)
{
if( !is_numeric($time) )$time = $this->n;
return $this->year($time).'-'.$this->month($time).'-'.$this->day($time);
}
//eof


}
//eof class