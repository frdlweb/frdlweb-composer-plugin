<?php
/*################################################################################*
#
#   (c)Copyright Till Wehowski, http://Webfan.de 
#   All rights reserved - Alle Rechte vorbehalten
#
#
#                 WEBFAN.de Software License
#
#                  * webdof license *
#                    http://look-up.webfan.de/1.3.6.1.4.1.37553.8.1.8.4.5
#
#                 Version 1.0.0
#
#  DIESE LIZENZ IST NUR G�LTIG IN VERBINDUNG MIT EINEM G�LTIGEN
#  WEBFAN SOFTWAREZERTIFIKAT.
#
#  THIS LICENSE REQUIRES A VALID WEBFAN SOFTWARE CERTIFICATE. 
#
#  WEBFAN SOFTWARE DARF NICHT AUF EINEM NICHT
#  AUTHORISIERTEN SERVER ZUM DOWNLOAD ANGEBOTEN WERDEN!
#
#  YOU ARE NOT ALLOWED TO REDISTRIBUTE THIS SOFTWARE ON A NOT AUTHORIZED SERVER.
#
#  Diese Software wird dem Endbenutzer zur Benutzung im Rahmen der Zweckbestimmung
#  und im Rahmen der per Kaufvertrag oder Nutzungsvereinbarungen vereinbarten 
#  Leistungen/Funktionen zur Verf�gung gestellt.
#  Quellcodekommentare sowie sichtbare bzw. klickbare Links d�rfen nicht entfernt
#  werden.
#  Eine Nutzung �ber die Rahmenvereinbarungen hinaus ist nicht erlaubt, die 
#  Lizenz kann durch erg�nzende Lizenzen bzw. lizensierte Funktionen erweitert
#  werden. Teile der Software k�nnen erweitert oder abweichend lizensiert sein,
#  entsprechende Lizenzen sind in diesem Falle beigef�gt.
#
#  Im Falle der Modifikation der Software durch den Endbenutzer k�nnen vorgesehene
#  Funktionalit�ten oder Updatedienste unter Umst�nden nicht mehr gew�hrleistet werden.
#
#  Die Benutzung der Software erfolgt auf eigene Gefahr, jegliche Haftung ist
#  ausgeschlossen insofern Vorsatz, grobe Fahrl��igkeit oder sonstige
#  gesetzliche Haftungsverpflichtungen nicht in Betracht kommen.
#
#  (c)Webfan Software http://domainundhomepagespeicher.webfan.de/software-center/
#
*################################################################################*/
namespace webdof;

class wResponse
{


  public static function header($name, $value)
    {
       return header($name.': '.$value);
    }



  public static function status($code = 200)
    {
       if((int)$code == 200)return header('HTTP/1.1 200 Ok');
       if((int)$code == 201)return header('HTTP/1.1 201 Created');
       if((int)$code == 304)return header('HTTP/1.1 304 Not Modified');
       if((int)$code == 400)return header("HTTP/1.1 400 Bad Request");
       if((int)$code == 401)return header('HTTP/1.1 401 Unauthorized');
       if((int)$code == 403)return header("HTTP/1.1 403 Forbidden");
       if((int)$code == 404)return header("HTTP/1.1 404 Not Found");
       if((int)$code == 409)return header('HTTP/1.1 409 Conflict');

       if((int)$code == 455)return header('HTTP/1.1 455 Blocked Due To Misbehavior');

       if((int)$code == 500)return header("HTTP/1.1 500 Internal Server Error");
       if((int)$code == 501)return header('HTTP/1.1 501 Not Implemented');
	   if(defined('mxMainFileLoaded') && version_compare(PMX_VERSION, '2.1.2', '>=') === TRUE )\pmxHeader::Status($code);
       trigger_error('status code '.intval($code).' not implemented in \'' . get_class($this) . '\'   ' . __METHOD__. ' '.__LINE__, E_USER_ERROR);
    }




}
//EOF