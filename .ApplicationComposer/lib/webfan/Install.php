<?php
/**
 * This file belongs to php software provided by Webfan.de.
 * (c) Copyright by Till Wehowski, http://www.webfan.de
 * (c) Urheberrecht Till Wehowski, http://www.webfan.de
 * Alle Rechte vorbehalten - All rights reserved
 * 
 * License/Lizenz: webdof license
 * You can read the terms and conditions of the license online at: 
 * http://look-up.webfan.de/1.3.6.1.4.1.37553.8.1.8.4.5
 * Die Lizenzbedingungen sind hier einsehbar:
 * http://look-up.webfan.de/1.3.6.1.4.1.37553.8.1.8.4.5
 * 
 *  @author 	Till Wehowski <php.support@webfan.de>
 *  @copyright 	2014 Copyright (c) Till Wehowski
 *  @version 	1.0.4   
 *  @link 		http://www.webfan.de/Downloads.html Webfan - die Internetseite
 *  @license 	http://look-up.webfan.de/webdof-license webdof license 3.0.0 1.3.6.1.4.1.37553.8.1.8.4.5
 *  @package    webfan/Install Webfan Installshield Class.
 *  @OID		1.3.6.1.4.1.37553.8.1.8.8 Webfan Software
 *  @requires	const=PHP_VERSION 5.3 >= 
 *  @requires   <webserver>?
 */
namespace webfan;

class Install
{
	const GROUP_CURRENT = '';
    const GROUP_APPS = 'apps';
	const GROUP_LIBS = 'packages';		
	const GROUP_SYS = 'sys';
	const GROUP_CREDENTIALS = 'credentials';
		
	const LIB_WEBDOF = '1.3.6.1.4.1.37553.8.1.8.8.5.16.1';
	const LIB_WEBFAN = '1.3.6.1.4.1.37553.8.1.8.8.5';

		
	const APP_PMX = '1.3.6.1.4.1.37553.8.5.8.1';
	const APP_FRDL = '1.3.6.1.4.1.37553.8.1.8.8.5.65'; 
	const APP_WEBDOF = '1.3.6.1.4.1.37553.8.1.8.8.5.5.11';

	const FRDL_APPS = '1.3.6.1.4.1.37553.8.1.8.8.5.65.3';
	const FRDL_PACKAGE = '1.3.6.1.4.1.37553.8.1.8.8.5.65.2';
	const FRDL_SERVER = '1.3.6.1.4.1.37553.8.1.8.8.5.65.4';
	const C_APID_INTERFACES = 'interfaces';
		
    const SESSKEY = __CLASS__;		
		
	public $detect;
	
	public $lang;
	protected $str;
	
	protected $Installer;
	
	public $beta = false;
	public $op;
	
	public $forms;

    protected $katalog;
	
	public $dir_install_temp;
	
	protected $lnbr;
		
	function __construct($op = null){
		$this->op = $op;
        $this->lnbr = $this->getOSVar('lnbr',PHP_OS);
		
		$this->beta = (sha1($_SERVER["SERVER_NAME"]) === "d66185da066bb74916a24bb33d134c34e8cf1073") ? true : false;
		
		
		
		$this->clear_detect();
		
		\frdl\webfan\App::God(false)->addFunc('getOSName', (function(){
			return  PHP_OS;
		}));
		 
		
		\frdl\webfan\App::God(false)->addFunc('getServerIp',(function ($all = true){
		                       $i = gethostbynamel($_SERVER['SERVER_NAME']);
		                       if($all === false)return ((isset($i['ips'][0])) ? $i['ips'][0] : '0.0.0.0');
							   return $i;
	                    }));
		
		\frdl\webfan\App::God(false)->addFunc('getBaseDir',(function (){
		                         $open_basedir = ini_get('open_basedir');
		                         if(!is_string($open_basedir) || trim($open_basedir) === ''){
	    	                     return realpath($_SERVER['DOCUMENT_ROOT'].\frdl\webfan\App::DS.'..'.\frdl\webfan\App::DS).\frdl\webfan\App::DS;
		               }else{
		                     	 $basedir = explode(':', $open_basedir);
			                     $basedir = trim($basedir[0]);
			                     return $basedir;
		                    }	
	                    }));
						
		
		\frdl\webfan\App::God(false)->addFunc('removeEvalFromFilename', (function(){
			$args = func_get_args();
			return  str_replace(' : eval()\'d code', '',preg_replace("/(\([0-9]+\))/","",$args[0][0]));
		}));		 	
					
					
					
												
	    $this->str = array();
		$this->str['en'] = array( 
		  'English',
		  array(
		  '__MAINTENANCE__' => '<span class="webfan-red">Our Install-Server is in maintenance mode.</span> Please try again later: <a href="http://www.webfan.de/install/">http://www.webfan.de/install/</a>',
		  '__APPS__' => 'Applications',
		  '__INSTALLATIONS__' => 'Installations',
		  '__SETTINGS__' => 'Settings',
		  '__INSTALL__' => 'install',
		  '__INSTALL_NOW__' => 'install now',
		  '__INSTALL_HERE__' => 'Install HERE!',
		  '__IS_NOT_WRITABLE__' => 'is not writable',
		  '__DOWNLOAD_FAILED__' => 'Download failed',
		  '__CANNOT_WRITE_FILE__' => 'Cannot write file',
		  '__CANNOT_UNZIP_FILE__' => 'Cannot unzip file',
		  '__ACCEPT_LICENSE__' => 'Accept license',	  
		  '__LANGUAGE__' => 'Language', 
		  '__VERSION_UNAVAILABLE__' => 'The selected version is not available',
		  '__ERROR__' => 'Error',
		  '__HINT_DEL_FILE__' => 'You should delete this file when done!\nTo do so click the red button at bottom!',
		  '__RECOMMENDED__' => 'recommended',
		  '__THIS_FILE__' => 'This file',
		  '__TEST_ONLY__' => 'For testing only',
		  '__INCOMPLETE_OR_BROKEN__' => 'Incomplete or broken',
		  '__SURE_BACKUP__' => 'sure/backup',
		  '__CHECK_SURE__' => 'not confirmed',
		  '__REQUIRED_PHP_VERSION_FAILED_1__' => 'Your php version does not apply to the requirements of the requested software. You need <strong>PHP Version</strong> '.htmlentities('=>').' ',
		  
		  
		  '__INSTALL_TEMP_DIR__' => 'Temp-Dir',
		  '__NO_PRAGMAMX_FOUND_IND_DIR__' => 'No PragmaMx installation found in the given directory',
		  '__CHECKSUM_INVALID__' => 'Invalid checksum',
		  '__REQUIRED_BY_WEBFAN_PMX_MODULES__' => 'required by the Webfan PragmaMx Modules',
		  
		  ),
		  
		  );
		$this->str['de'] =  array( 
		  'Deutsch',
		  array(
		  '__MAINTENANCE__' => '<span class="webfan-red">Der Install-Server wird derzeit gewartet.</span> Bitte etwas sp&auml;ter nochmal probieren: <a href="http://www.webfan.de/install/">http://www.webfan.de/install/</a>',
		  '__APPS__' => 'Anwendungen',
		  '__INSTALLATIONS__' => 'Installationen',
		  '__SETTINGS__' => 'Einstellungen',
		  '__INSTALL__' => 'installieren',
		  '__INSTALL_NOW__' => 'jetzt installieren',
		  '__INSTALL_HERE__' => 'HIER installieren',
		  '__IS_NOT_WRITABLE__' => 'ist nicht beschreibbar',
		  '__DOWNLOAD_FAILED__' => 'Download fehlgeschlagen',
		  '__CANNOT_WRITE_FILE__' => 'Kann Datei nicht schreiben',
		  '__CANNOT_UNZIP_FILE__' => 'Kann Datei nicht entpacken',
		  '__ACCEPT_LICENSE__' => 'Lizenz akzeptieren',	  
		  '__LANGUAGE__' => 'Sprache', 
		  '__VERSION_UNAVAILABLE__' => 'Die ausgew&auml;hlte Version ist nicht verf&uuml;gbar',
		  '__ERROR__' => 'Fehler',
		  '__HINT_DEL_FILE__' => 'Sie sollten diese Datei löschen wenn Sie fertig sind!\nKlicken Sie hierzu auf den roten Button ganz unten!',
		  '__RECOMMENDED__' => 'empfohlen',
		  '__THIS_FILE__' => 'Diese Datei',
		  '__TEST_ONLY__' => 'Nur f&uuml;r Testzwecke',
		  '__INCOMPLETE_OR_BROKEN__' => 'Unvollst&auml;ndig oder defekt',
		  '__SURE_BACKUP__' => 'sicher/Backup',
		  '__CHECK_SURE__' => 'nicht best&auml;tigt',
		  '__REQUIRED_PHP_VERSION_FAILED_1__' => 'Ihre php Version gen&uuml;gt nicht den Anspr&uuml;chen der angeforderten Software. Sie ben&ouml;tigen <strong>PHP Version</strong> '.htmlentities('=>').' ',
		  
		  
		  '__INSTALL_TEMP_DIR__' => 'Temp-Dir',
		  '__NO_PRAGMAMX_FOUND_IND_DIR__' => 'Keine PragmaMx Installation im angegebenen Verzeichnis gefunden',
		  '__CHECKSUM_INVALID__' => 'Ung&uuml;ltige Pr&uuml;fsumme',
		  '__REQUIRED_BY_WEBFAN_PMX_MODULES__' => 'wird zur Installation und Betrieb der Webfan Module f&uuml;r das PragmaMx CMS ben&ouml;tigt',
		  
		  ),
		  
		  );
		
		$this->str['fr'] =  array( 
	  'French',
      array(
      '__MAINTENANCE__' => '<span class="webfan-red">Notre serveur d\'installation est en cours de maintenance.</span> Merci de réessayer plus tard: <a href="http://www.webfan.de/install/">http://www.webfan.de/install/</a>',
      '__APPS__' => 'Applications',
      '__INSTALLATIONS__' => 'Installations',
      '__SETTINGS__' => 'Paramètres',
      '__INSTALL__' => 'installer',
      '__INSTALL_NOW__' => 'installer maintenant',
      '__INSTALL_HERE__' => 'Installer ici !',
      '__IS_NOT_WRITABLE__' => 'n\'est pas inscriptible',
      '__DOWNLOAD_FAILED__' => 'Téléchargement échoué',
      '__CANNOT_WRITE_FILE__' => 'Impossible d\'écrire le fichier',
      '__CANNOT_UNZIP_FILE__' => 'Impossible de décompresser le fichier',
      '__ACCEPT_LICENSE__' => 'Accepter la license',  
      '__LANGUAGE__' => 'Langage',
      '__VERSION_UNAVAILABLE__' => 'La version choise n\'est pas disponible',
      '__ERROR__' => 'Erreur',
      '__HINT_DEL_FILE__' => 'Vous devriez supprimer ce fichier un fois terminé !\nPour le faire cliquer sur le bouton rouge au dessous!',
      '__RECOMMENDED__' => 'recommendé',
      '__THIS_FILE__' => 'Ce fichier',
      '__TEST_ONLY__' => 'Seulement pour des test',
      '__INCOMPLETE_OR_BROKEN__' => 'Incomplet ou cassé',
      '__SURE_BACKUP__' => 'sauver/sauvegarder',
      '__CHECK_SURE__' => 'non confirmé',
      '__REQUIRED_PHP_VERSION_FAILED_1__' => 'Votre version de php ne correspond pas à celle requise par le logiciel. Vous avez besoin de la <strong>Version de PHP</strong> '.htmlentities('=>').' ',
		  
		  
	    '__INSTALL_TEMP_DIR__' => 'Dossier temporaire',
      '__NO_PRAGMAMX_FOUND_IND_DIR__' => 'Aucune installation de PragmaMx n\'a été trouvé de le répertoire spécifié',
      '__CHECKSUM_INVALID__' => 'Checksum invalide',
      '__REQUIRED_BY_WEBFAN_PMX_MODULES__' => 'Requis pour les modules PragmaMx WEBFAN',
		  
		  
		),  
		  );	

		$this->forms = array();
		$this->forms[self::GROUP_CURRENT] =null;
		$this->forms[self::GROUP_APPS] = array(
		
		  /**
		      self::APP_FRDL => array( 
			       'render_func' => 'form_app_frdl_application_composer',
                   'flushed' => false,			  
			  ),
			  
			
			  self::FRDL_PACKAGE => array( 
			       'render_func' => 'form_packages',
                   'flushed' => false,
			  ),	

			  self::FRDL_APPS => array( 
			       'render_func' => 'form_frdl_apps',
                   'flushed' => false,
			  ),	

			  self::FRDL_SERVER => array( 
			       'render_func' => 'form_frdl_server',
                   'flushed' => false,
			  ),
			  	
			   * 
			   */	  
			  
			  
			  
		      self::APP_PMX => array( 
			       'render_func' => 'form_pmx',
                   'flushed' => false,
	     	  ),
			  self::APP_WEBDOF => array( 
			       'render_func' => 'form_app_webdof',
                   'flushed' => false,
			  ),
		);

		
	
		if(!isset($_SESSION[self::SESSKEY]))$_SESSION[self::SESSKEY] = array();
		
		if(isset($_SESSION[self::SESSKEY]['op'])){
			$this->op = &$_SESSION[self::SESSKEY]['op'];
		}else{
			$_SESSION[self::SESSKEY]['op'] = &$this->op;
	    }

		
		
		
		$_SESSION[self::SESSKEY]['lang'] = (isset($_SESSION[self::SESSKEY]['lang']))	? $_SESSION[self::SESSKEY]['lang'] : 'en';	
		if(isset($_REQUEST['lang']) && isset($this->str[$_REQUEST['lang']] ))$_SESSION[self::SESSKEY]['lang'] = strip_tags($_REQUEST['lang']);		 
        $this->lang = &$_SESSION[self::SESSKEY]['lang']; 
		
		
		
		$this->dir_install_temp = (isset($_SESSION[self::SESSKEY]['dir_install_temp'])) ? $_SESSION[self::SESSKEY]['dir_install_temp'] : __DIR__.\frdl\webfan\App::DS;
		
		if(!isset($_SESSION[self::SESSKEY]['repositories'])){
			Loader::repository( 'frdl' );
		}
		
		
		$_SESSION[self::SESSKEY]['repositories'] = (isset($_SESSION[self::SESSKEY]['repositories']))	? $_SESSION[self::SESSKEY]['repositories'] 
		           : array(  
				       'webfan' => array('name' => 'Webfan Installer (webdof)',),
				       'frdl' => array('name' => 'frdl (Application Composer)',),
				   );	
				
		$this->katalog();
	
	}
	

	
	
	
  protected function katalog(){
			if(!isset($_SESSION[self::SESSKEY]['KATALOG']))$_SESSION[self::SESSKEY]['KATALOG'] = array();
			if(!isset($_SESSION[self::SESSKEY]['KATALOG']['pragmamx']))$_SESSION[self::SESSKEY]['KATALOG']['pragmamx'] = array();			
			
			$sk = &$_SESSION[self::SESSKEY]['KATALOG']['pragmamx'];
			if(!isset($sk['d']) || !isset($sk['t']) || $sk['t'] < time() - 12 * 60 * 60){
				$sk['t'] = time() - 12 * 60 * 60 - 60;
				$http = new \webdof\Http\Client();
				$url = 'http://www.webfan.de/dl/katalog.pragmamx.php';
				$r = $http->request($url, 'GET', array(), array(), E_USER_WARNING);
				if(intval($r['status']) !== 200){
					trigger_error('Cannot download products metadata! ('.__METHOD__.')', E_USER_WARNING);
					return false;
				}
				$bs = new \frdl\webfan\Serialize\Binary\bin();
				$sk['d'] = $bs->unserialize($r['body']);
				$sk['t'] = time();
			}
			
			
			$this->katalog = &$_SESSION[self::SESSKEY]['KATALOG'];
	}

	
	
	public function clear_detect(){
		$this->detect = array();
		
	
		$this->detect[self::GROUP_SYS] = array(
		                 'files_dir' => null,
		);
		
		$this->detect[self::GROUP_LIBS] = array(
	
		         self::FRDL_PACKAGE => array(),
		
		);			
	
		$this->detect[self::GROUP_APPS] = array(
		        self::APP_PMX => array(),
		        self::APP_FRDL => array(),
		        self::APP_WEBDOF => array(),
		);		
					
		$this->detect[self::GROUP_CREDENTIALS] = array(
	
		        self::C_APID_INTERFACES => array(),
		);
		 
	}
	
			
	public function run(){
		return $this->run_unwrapped();
	}
	
	public function lang($key){
		return $this->str[$this->lang][1][$key];
	} 
	
	
	public function e($str){
		return '<span style="color:red;">'.$str.'</span>'.$this->lnbr;
	}
	
	public function run_unwrapped(){
        
 
		 $this->detect();
		 
   		 echo '<style type="text/css">';
		 echo'.frdl-app-table {
		 	width:100%;text-align:left;vertical-align:top;border:none;height:32px;
		 }
		 
		 .frdl-app-headline{
		 	font-size:24px;font-weight:bold;display:inline;cursor:pointer;
		 }
		 ';
		 echo'</style>';
		         
		 if(isset($_POST['INSTALL_TEMP_DIR']) && is_dir($_POST['INSTALL_TEMP_DIR']) && is_writable($_POST['INSTALL_TEMP_DIR'])){
		 	$_SESSION[self::SESSKEY]['dir_install_temp'] = $_POST['INSTALL_TEMP_DIR'];
		 }  
           
		 if(isset($_REQUEST['id_repository']) && isset($_SESSION[self::SESSKEY]['repositories'][$_REQUEST['id_repository']]) ){
		 	Loader::repository( $_REQUEST['id_repository'] );
		 }  
		 		   
		 if(isset($_REQUEST['BTN_install']) && isset($_REQUEST['OID']) ){
		 	 if(!isset($_REQUEST['accept_license'])){
		 	 	echo '<pre class="console-screen">'.$this->e($this->lang('__ERROR__').': '.$this->lang('__ACCEPT_LICENSE__')).'</pre>';
		 	 }else{
		 	 	    if(!isset($_REQUEST['sure_backup'])){
		 	 	          echo '<pre class="console-screen">'.$this->e($this->lang('__ERROR__').': '.$this->lang('__CHECK_SURE__')).'</pre>';
		 	      }else{
				           echo $this->install($_REQUEST['OID']);
	                       $this->detect();
			            }
	               }
		 }  

		 echo '<div id="DIV_SETTINGS" style="position:absolute;top:2px;right:3px;min-height:64px;max-width:350px;display:inline;padding:2px;" class="webfanWikiBoxouter">';	
		 echo '<div id="DIV_SETTINGS_DRAGAREA">';
		 echo '<h1 class="webfan-blue" style="display:inline;">'.$this->lang('__SETTINGS__').'</h1>';
		 echo '</div><br />';	
		
		 echo '<span class="webfanWikiBoxinnerWerbung" style="min-width: 300px;min-height:25px;width:100%;">';	
		 		 		 
		 echo $this->lang('__LANGUAGE__').': ';
		 echo '<form id="FORM_LANG" style="display:inline;" method="get" action="'.$_SERVER['REQUEST_URI'].'">';
		 echo '<select onchange="$(\'#FORM_LANG\').submit();" name="lang" style="height:16px;font-size:9px;width:100px;">';
		 echo '<option disabled>- select language-</option>';
		  foreach($this->str as $lang => $str){
		  	echo '<option value="'.$lang.'" '.(($_SESSION[self::SESSKEY]['lang']===$lang) ? ' selected ' : '').'>'.$str[0].'</option>';
		  }
		 echo '</select>';
		 echo '</form>';

		 
		 echo '<br />';
		 echo '<br />';
		 		 		 
		 echo 'Repository: ';
		 echo '<form id="FORM_REPOS" style="display:inline;" method="get" action="'.$_SERVER['REQUEST_URI'].'">';
		 echo '<select onchange="$(\'#FORM_REPOS\').submit();" name="id_repository" style="height:16px;font-size:9px;width:100px;">';
		 echo '<option disabled>- select Repository-</option>';
		  foreach($_SESSION[self::SESSKEY]['repositories'] as $id => $repos){
		  	echo '<option value="'.$id.'" '.((\webfan\Loader::$id_repositroy ==$id) ? ' selected ' : '').'>'.$repos['name'].'</option>';
		  }
		 echo '</select>';
		 echo '</form>';
		 
		 		 
		 echo '</span>';
		 
		 echo '<br />';
		 echo '<br />';
		 
		 
		 echo '<span class="webfanWikiBoxinnerWerbung" style="min-width: 300px;min-height:25px;width:100%;">';	
		 
		 $tmpdirs = array( 
		   
		       $this->detect[self::GROUP_SYS]['temp_dir'],
		       __DIR__.\frdl\webfan\App::DS,
		 );		 		 
		 echo $this->lang('__INSTALL_TEMP_DIR__').': ';
		 echo '<form id="FORM_INSTALL_TEMP_DIR" style="display:inline;" method="post" action="'.$_SERVER['REQUEST_URI'].'">';
		 echo '<select onchange="$(\'#FORM_INSTALL_TEMP_DIR\').submit();" name="INSTALL_TEMP_DIR" style="height:16px;font-size:9px;width:100px;">';

		  foreach($tmpdirs as $num => $dir){
		  	echo '<option value="'.$dir.'" '.((isset($_SESSION[self::SESSKEY]['dir_install_temp']) && $dir===$_SESSION[self::SESSKEY]['dir_install_temp']) ? ' selected ' : '').'>'.$dir.'</option>';
		  }
		 echo '</select>';
		 echo '</form>';
		 
		 echo '</span>';
		 
		 		 
		 
		 echo '</div>';
		 	
			
			
			
			
		 echo '<script type="text/javascript">';
		 echo 'Dom.draggable(\'DIV_SETTINGS\', \'DIV_SETTINGS_DRAGAREA\', false, false);';
		 echo '</script>'; 
		
		 echo '<div class="webfanWikiBoxinner">';
	//	 echo '<h1 class="webfan-blue">'.$this->lang('__APPS__').' / Packages</h1>';	
		 
		 
		 
		 
	    $this->forms[self::GROUP_CURRENT] = &$this->forms[self::GROUP_LIBS][self::FRDL_PACKAGE];
		 
		 if($this->op === 'pragmamx-online-installer'){
		 	 $this->forms[self::GROUP_CURRENT] = &$this->forms[self::GROUP_APPS][self::APP_PMX];
		 }
		 
		 if($this->op === 'install'){
		 	 $this->forms[self::GROUP_CURRENT] = &$this->forms[self::GROUP_APPS][self::APP_FRDL];
		 }		 
		 
		 
		 
		 
		 if(isset($this->forms[self::GROUP_CURRENT]) && is_array($this->forms[self::GROUP_CURRENT])){
		 		if((!isset($this->forms[self::GROUP_CURRENT]['flushed']) || $this->forms[self::GROUP_CURRENT]['flushed'] !== true) 
		 		 && isset($this->forms[self::GROUP_CURRENT]['render_func']) && is_callable(array($this,$this->forms[self::GROUP_CURRENT]['render_func']))){
		 			echo call_user_func(array($this,$this->forms[self::GROUP_CURRENT]['render_func']),true);
					$this->forms[self::GROUP_CURRENT]['flushed'] = true;
		 		}		 	
		 }
		 
		 foreach($this->forms as $group => &$forms){
		 	if(!is_array($forms))continue; 
		 	foreach($forms as $key => &$form){
		 		if((!isset($form['flushed']) || $form['flushed'] !== true) && isset($form['render_func']) && is_callable(array($this,$form['render_func']))){
		 			echo call_user_func(array($this,$form['render_func']), false);
					$form['flushed'] = true;
		 		}
		 	}
		 }	
		// echo $this->form_pmx();
		
	
		 
		 echo '</div>';
		 //eo apps

		 
		 
		 $__HINT_DEL_FILE__ = '__HINT_DEL_FILE__'.sha1($_SERVER['SERVER_NAME']);
		 if(!isset($_SESSION[self::SESSKEY][$__HINT_DEL_FILE__][$this->lang]) || intval($_SESSION[self::SESSKEY][$__HINT_DEL_FILE__][$this->lang]) < time() - 60 * 60){
		 	echo '<script type="text/javascript">';
		//	echo 'alert("'.$this->lang('__HINT_DEL_FILE__').'\n'.$this->lang('__THIS_FILE__').':\n'.str_replace(' : eval()\'d code', '',preg_replace("/(\([0-9]+\))/","",__FILE__)).'");';
			echo 'alert("'.$this->lang('__HINT_DEL_FILE__').$this->lnbr.$this->lang('__THIS_FILE__').':'.$this->lnbr.\frdl\webfan\App::God()->removeEvalFromFilename(__FILE__).'");';
			echo '</script>';
			if(!isset($_SESSION[self::SESSKEY][$__HINT_DEL_FILE__]) || !is_array($_SESSION[self::SESSKEY][$__HINT_DEL_FILE__]))$_SESSION[self::SESSKEY][$__HINT_DEL_FILE__]=array();
			$_SESSION[self::SESSKEY][$__HINT_DEL_FILE__][$this->lang] = time();
		 }
		 
		 
		// if($this->beta ===true)echo '<br />'.ini_get('open_basedir').'<br />'.__FILE__;
		 
		 //footer schneidet sonst inhalt ab:
		 echo '<br /><br /><br /><br />';
		 
		   if($this->beta === true) echo '<pre>'.print_r($this->detect,true).'</pre>';
		 
       //  die();		
	}



    protected function js_func_onclick_app_img($div, $url){
    	return '$(\'#'.$div.'\').css(\'display\',\'block\');this.setAttribute(\'href\',\''.$url.'\');this.setAttribute(\'target\',\'_blank\');this.onclick=function(){};return false;';
    } 

    protected function render_app_head_table($title, $anker, $div, $url, $img, $click = false ){
        $html = '';
		
        $html .=  '<table class="frdl-app-table">
            <tr>	
             <td style="vertical-align:top;"> 
             <h1 id="'.$div.'_HEADLINE" onclick="var url = new webfanURLParser(\'\');url.query=\'\';url.setHash(\''
                       .$anker.'\');window.location.href= url.urlMakeNew();$(\'#'.$div.'\').css(\'display\',\'block\');" onmouseout="this.style.textDecoration=\'none\';" onmouseover="this.style.textDecoration=\'underline\';" class="webfan-blue" class="frdl-app-headline">'.$title.'</h1> 
             </td>	
              <td style="text-align:right;background:transparent;">	
                	  	  <a href="javascript:;" id="'.$div.'_LINK_LOGOCLICK" style="text-decoration:none;" onclick="'.$this->js_func_onclick_app_img($div, $url).'">	  	
                	  	         	        <img src="'.$img.'" style="border:none;" />        	 
                	  	         	         	       	       </a>      
             </td></tr></table>';
	
			if($click ===TRUE && ((isset($_POST['BTN_START_SETUP'])) || (count($_POST) < 3))){
			$html.= '<script type="text/javascript">uhrTimer.add("FRDL_OPEN_APP_FROM_OP", function(){ if(typeof Dom.get("'.$div.'") !== "object")return; try{var url_'.$div.' = new webfanURLParser(\'\');url_'.$div.'.query=\'\';url_'.$div.'.setHash(\''.$anker.'\');window.location.href= url_'.$div.'.urlMakeNew();  Dom.get("'.$div.'").style.display="block";Dom.get("'.$div.'_LINK_LOGOCLICK").onclick();}catch(err){console.warn(err);}  uhrTimer.remove("FRDL_OPEN_APP_FROM_OP");     });	</script>';
		}		
		
	  return $html;
    } 



    protected function render_app_close_x( $div, $url){
        $html = '';
		   $html.= '<div style="bottom:1px;text-align:right;">';
            $html.= '<img onclick="$(\'#'.$div.'\').css(\'display\',\'none\');Dom.get(\''.$div.'_LINK_LOGOCLICK\').onclick=function(){'.$this->js_func_onclick_app_img($div, $url).'};Dom.get(\''.$div.'_LINK_LOGOCLICK\').setAttribute(\'href\',\'javscript:;\');" src="http://static.webfan.de/icons/cancel2.png" style="border:none;cursor:pointer;" />';
		   $html.='</div>';		
       	
	  return $html;
    } 
	


   //http://static.webfan.de/logos/bg-light-1.png
   protected function form_app_webdof($click = false){
   	    $args = func_get_args();
		if(isset($args[0]))$click = $args[0];
		
		
		
        $div = 'APP_WEBDOF';
		$title = 'Webfan PragmaMx Module Setup';
		$homepage = 'http://www.webfan.de/Downloads-lid-webdof-php-library-63.html';
		$appKey = 'apps_webdof';
		
		
     	$html = '';
		$html.= '<a name="'.$appKey.'"></a>';  

		 $html.= '<div class="webfanWikiBoxouter" style="background-size: contain; background-attachment: hidden;  background: url(\'http://static.webfan.de/logos/bg-light-1.png\') #E8F9FF;vertical-align:top;">';		
	    $html.= $this->render_app_head_table($title, $appKey, $div, $homepage, 
	                'http://webfan.de/bilder/domainundhomepagespeicher/produkte/kurzbeschreibung/24.247.247THP.produktbild_artikelbeschreibung.jpg',
	                $click);
		 $html.= '<div id="'.$div.'" style="display:none;">';	  		
	     $html.= '[ <b><a title="webdof php library" href="'.$homepage.'" target="_blank" style="color:black;">Homepage</a></b> ]';
	
		 $html.= '<span class="webfan-blue" style="font-size:14px;font-weight:bold;">webdof php library</span>, '.$this->lang('__REQUIRED_BY_WEBFAN_PMX_MODULES__').'. <br />Version/update check, InstallShield, API Modul / API-D Interfaces, Cronjobverwaltung, Languagestrings-Manager, Plugin-System, Fast-Cache...<br />';
		
	
			 
			
			 $Installer = null; 
			 $li = false;
			 $pmx = false;
			 if(count($this->detect[self::GROUP_APPS][self::APP_PMX]) === 0){
			 	$html.= '<a href="#apps_pmx" style="color:red;">'.$this->e('PragmaMx required.').'</a><br />';
				$pmx = false;
			 }else{
			 	$li = true;
				$pmx = true;
			 }
			 
		 if(count($this->detect[self::GROUP_APPS][self::APP_WEBDOF]) > 0)$li = true;	 

         if($li === true){
         	$Installer = new \webfan\InstallShield\PragmaMx\WebfanModulesSetup($this);
			
			$html.= $Installer->render_form_install();
			 
            foreach($this->detect[self::GROUP_APPS][self::APP_WEBDOF] as $dir => $app){
         	
            }
		 			 
		 }
		 

		        $html.= $this->render_app_close_x( $div, $homepage);
		        $html.= '</div>';	
			$html.= '</div>';	
			
	
			
		return $html;	
   }



	
    protected function form_pmx($click = false){
    	$args = func_get_args();
		if(isset($args[0]))$click = $args[0];
		   	
    	$current_version = '2.1.2';
		$homepage = 'http://www.pragmamx.org';
    	$div = 'APP_PMX';
		$title = 'PragmaMx CMS';
		$appKey = 'apps_pmx';
		
    	$html = '';
		 $html.= '<a name="apps_pmx"></a>';
		 
		 
		 $html.= '<script type="text/javascript">';
	     $html.= 'function webfan_install_setup_pmxurl(){	Dom.get(\'SETUP_URL\').value=\'http://'.$_SERVER['SERVER_NAME'].'\' + str_replace(\''.$this->detect[self::GROUP_SYS]['SERVER_VARS']['DOCUMENT_ROOT'].'\', \'\',Dom.get(\'INSTALL_DIR_PMX\').value); }';
		 $html.= '</script>';
		 
		 
		 
		 $html.= '<div class="webfanWikiBoxouter" style="background: url(\'http://static.webfan.de/logos/pragmamx4.png\') #E8F9FF;vertical-align:top;">';		 
	
	 
		 $html.= $this->render_app_head_table($title, $appKey, $div, $homepage, 
		                                      'http://static.webfan.de/logos/pragmamx2.png',
	                                          $click);
		 
		 $html.= '<div id="'.$div.'" style="display:none;">';	  
		 $html.= '[ <b><a title="www.pragmamx.org" href="http://www.pragmamx.org/" target="_blank" style="color:black;">Homepage</a></b> | <a title="'.$this->lang('__INSTALL_HERE__').'" style="font-size:16px;font-weight:bold;color:#6495ED;" onclick="Dom.get(\'STR_INSTALL_UPGRADE_PMX\').innerHTML=\'Install or update PragmaMx to: \';Dom.get(\'INSTALL_DIR_PMX\').value=base64_decode(urldecode(\''
		 .urlencode(base64_encode(realpath(dirname(__FILE__)).\frdl\webfan\App::DS)).'\'));webfan_install_setup_pmxurl();" href="javascript:;">'.$this->lang('__INSTALL_HERE__').' <span style="font-size:11px;color:black;">(http://'.$_SERVER['SERVER_NAME'].(dirname($_SERVER['REQUEST_URI'])).'/)</span></a> ]';
				 
		 $html.= '<form id="FORM_INSTALL_PMX" method="post" action="'.$_SERVER['REQUEST_URI'].'#PMX">';
		 
		 $html.= '<table style="width:100%;border:none;">';
		 $html.= '<tr>';
		 $html.= '<td style="width:250px;">';
		  $html.= '<span id="STR_INSTALL_UPGRADE_PMX">Install PragmaMx to:</span>';
		
	    $html.= '</td>';
		  
		 $html.= '<td colspan="4">';
		  $html.= '<input onkeyup="webfan_install_setup_pmxurl();" onchange="webfan_install_setup_pmxurl();" style="width:99%;" type="text" id="INSTALL_DIR_PMX" name="INSTALL_DIR_PMX" value="'.realpath(dirname(__FILE__)).\webfan\App::DS.'" />';
	     $html.= '</td>';
		  
		  $html.= '</tr><tr>';
		  
		 $html.= '<td>';  
		  $html.= 'Install-URL:';
		 $html.= '</td>';  
		 
		 $html.= '<td colspan="4">';
		  $html.= '<input style="width:99%;" type="text" id="SETUP_URL" name="SETUP_URL" value="http://'.$_SERVER['SERVER_NAME'].(dirname($_SERVER['REQUEST_URI'])).'/" /><br />';
		  $html.= '<input type="hidden" name="OID" value="'.self::APP_PMX.'" />';
		  $html.= '</td>'; 
		  
		  
		  $html.= '</tr><tr>';
		    $html.= '<td>'; 
	        $html.= '<input type="checkbox" name="accept_license" />';
		    $html.= ' <a href="http://domainundhomepagespeicher.webfan.de/software-center/api/license/1.3.6.1.4.1.37553.8.1.8.4.7/licensetext/" target="_blank">'.$this->lang('__ACCEPT_LICENSE__').' (GNU)</a>';
	       $html.= '</td>'; 
		     
	    $html.='<td>';
		 $html.= '&copy; <a href="http://www.pragmamx.org/Impressum-op-copyright.html#systeminfo" target="_blank">PragmaMx.org</a>';
		$html.='</td>';		 
			 
		 $html.='<td>';
		 $html.= '<input type="checkbox" name="sure_backup" /> ';
		  $html.= $this->lang('__SURE_BACKUP__');
		  $html.='?';
		 $html.='</td>';	 
			 
	     $html.= '<td style="width:250px;">'; 
		  $html.= '<select id="PMX_VERSION" name="version" style="color:green;width:100%;">';
	  /*
	      $html.= '<option value="2.2.2" style="color:green;" onclick="Dom.get(\'PMX_VERSION\').style.color=\'green\';"> PragmaMx 2.2.2</option>';		
		//  $html.= '<option value="2.1.2 download from pragmamx.org" style="color:green;" onclick="Dom.get(\'PMX_VERSION\').style.color=\'green\';"> PragmaMx 2.1.2 download from pragmamx.org</option>';		
		  $html.= '<option value="2.1.2" style="color:green;" onclick="Dom.get(\'PMX_VERSION\').style.color=\'green\';"> PragmaMx 2.1.2 ('.$this->lang('__RECOMMENDED__').') download from webfan.de</option>';
		  $html.= '<option value="2.2.1" style="color:red;" onclick="Dom.get(\'PMX_VERSION\').style.color=\'red\';"> PragmaMx 2.2.1 BETA ('.$this->lang('__TEST_ONLY__').'!!!)</option>';
		  $html.= '<option value="2.2.0" style="color:red;" onclick="Dom.get(\'PMX_VERSION\').style.color=\'red\';"> PragmaMx 2.2.0 BETA ('.$this->lang('__TEST_ONLY__').'!!!)</option>';
	      $html.= '<option value="2.2.3" style="color:red;" onclick="Dom.get(\'PMX_VERSION\').style.color=\'red\';"> PragmaMx 2.2.3 BETA ('.$this->lang('__TEST_ONLY__').'!!!)</option>';
	    */
	      $html.= '<option value="2.2.4" style="color:green;" onclick="Dom.get(\'PMX_VERSION\').style.color=\'green\';" selected> PragmaMx 2.2.4</option>';	
		  $html.= '</select> ';
		  
		 $html.= '</td>';
		 
		 $html.= '<td>';   
		  $html.= '<input type="submit" name="BTN_install" value="'.$this->lang('__INSTALL_NOW__').'..." style="width:100%;font-weight:bold;" />';
		 $html.= '</td>';
		 
		 $html.= '</tr>';
		 $html.= '</table>'; 
     
	 
	 	 $html.= '</form>';
		 
		 $html.= '<h3 class="webfan-blue">PragmaMx '.$this->lang('__INSTALLATIONS__').':</h3>';
		 $html.= '<ul>';		 
		 $ID = 0;
		  foreach($this->detect[self::GROUP_APPS][self::APP_PMX] as $dir => $app){
		  	  $ID++;
		  	  if(version_compare($app['version'], $current_version, '>=') !==true){
		  	  	    $update_available = true;
					$color = 'red';
		  	  }else{
		     	  	$update_available = false;
					$color = 'lightgrey';
		  	  }
			
		  	  $html.= '<a name="apps_pmx_'.sha1($dir).'"></a><li>';
			   $html.= '<span style="font-size:16px;font-weight:bold;">PragmaMx Installation #'.$ID.' Version '.$app['version'].'</span> : '.$dir;
			   $html.= '<br />[ <a style="color:'.$color.';" href="javascript:;" onclick="Dom.get(\'STR_INSTALL_UPGRADE_PMX\').innerHTML=\'Install or update PragmaMx to: \';Dom.get(\'INSTALL_DIR_PMX\').value=base64_decode(urldecode(\''.urlencode(base64_encode($dir)).'\'));webfan_install_setup_pmxurl();">Update</a>';
			
			   if(!file_exists($dir.'config.php')){
			   	$html.= ' | '.$this->e($this->lang('__INCOMPLETE_OR_BROKEN__'));				
		       }

               $indexfile = $dir.'setup'.\frdl\webfan\App::DS.'index.php';
			   $htaccessfile = $dir.'setup'.\frdl\webfan\App::DS.'.htaccess';
			   if(!file_exists($indexfile)
			    || (
			            file_exists($htaccessfile) 
						&& 
				       preg_match("/".preg_quote('deny from all')."/", file_get_contents($htaccessfile))
				    )  
			   ){
			   	    				
		       }else{
		       	   $url = basename($_SERVER['REQUEST_URI']);
				   $url = explode('?',$url);
				   $url = $url[0];
				   $url .= '?apps=pmx&amp;installation='.sha1($dir).'#apps_pmx_'.sha1($dir);
		       	   $html .= ' | <a href="'.$url.'" class="webfan-red">Setup</a>';
		       }
			   
			   
			  $html.= ' ]';
			  
			  if(isset($_REQUEST['apps']) && $_REQUEST['apps'] === 'pmx'
			    && isset($_REQUEST['installation']) && $_REQUEST['installation'] === sha1($dir)
			  ){
			  	  $setupurl = 'http://'.$_SERVER['SERVER_NAME'].rtrim(str_replace($this->detect[self::GROUP_SYS]['SERVER_VARS']['DOCUMENT_ROOT'], '',$dir), \frdl\webfan\App::DS.' /\  ').'/setup/index.php';
			      
			      $html.= '<div class="console-sreen" style="padding:2px;">';
			      $html.='<iframe src="'.$setupurl.'" style="border:none;width:100%;height:850px;">Ihr Browser kann keine iframes!</iframe>';
				  $html.='</div>';
			  }
			  
			  $html.= '</li>';
         }
		 $html.= '</ul>';  
		 
		 $html.= $this->render_app_close_x( $div, $homepage);
		
		
		 $html.='</div>';
		 
		 
		 $html.= '</div>';
		 //eo pmx		
		
		return $html;
    }


    protected function form_packages($click = false){
    	$args = func_get_args();
		if(isset($args[0]))$click = $args[0];
		
    	 $html = '';
         $div = 'APP_PACKAGES';
		$title = 'Packages';
		$homepage = 'http://look-up.webfan.de/frdl-pluggable-package';
		$appKey = 'apps_packages';
		
		
     	$html = '';
		$html.= '<a name="'.$appKey.'"></a>';  

		 $html.= '<div class="webfanWikiBoxouter" style="background-size: contain; background-attachment: hidden;  background: url(\'http://static.webfan.de/logos/bg-light-1.png\') #E8F9FF;vertical-align:top;">';		
	    $html.= $this->render_app_head_table($title, $appKey, $div, $homepage, 'http://static.webfan.de/logos/php.medium.png',
	                $click);
		 $html.= '<div id="'.$div.'" style="display:none;">';
	     $html.= '[ <b><a title="'.$title.'" href="'.$homepage.'" target="_blank" style="color:black;">Homepage</a></b> ]';
	
		 $html.= '<span class="webfan-blue" style="font-size:14px;font-weight:bold;">'.$title.'</span>';
		 
	       $html.= '<br /><i>...coming soon...</i>';
	
		 

		  $html.= $this->render_app_close_x( $div, $homepage);
		   $html.= '</div>';	
		$html.= '</div>';	
		 return $html;
    }
	


    protected function form_frdl_apps($click = false){
    	$args = func_get_args();
		if(isset($args[0]))$click = $args[0];
		
    	 $html = '';
         $div = 'FRDL_APPS';
		$title = 'Applications';
		$homepage = 'http://look-up.webfan.de/frdl-composed-application';
		$appKey = 'frdl_apps';
		
		
     	$html = '';
		$html.= '<a name="'.$appKey.'"></a>';  

		 $html.= '<div class="webfanWikiBoxouter" style="background-size: contain; background-attachment: hidden;  background: url(\'http://static.webfan.de/logos/bg-light-1.png\') #E8F9FF;vertical-align:top;">';		
	    $html.= $this->render_app_head_table($title, $appKey, $div, $homepage, 'http://webfan.de/bilder/domainundhomepagespeicher/produkte/kurzbeschreibung/24.251.251THP.produktbild_artikelbeschreibung.jpg',
	                $click);
		 $html.= '<div id="'.$div.'" style="display:none;">';	  		
	     $html.= '[ <b><a title="'.$title.'" href="'.$homepage.'" target="_blank" style="color:black;">Homepage</a></b> ]';
	
		 $html.= '<span class="webfan-blue" style="font-size:14px;font-weight:bold;">'.$title.'</span>';
		 
	       $html.= '<br /><i>...coming soon...</i>';
	
		 

		  $html.= $this->render_app_close_x( $div, $homepage);
		   $html.= '</div>';	
		$html.= '</div>';	
		 return $html;
    }


    protected function form_frdl_server($click = false){
    	$args = func_get_args();
		if(isset($args[0]))$click = $args[0];
		
    	 $html = '';
         $div = 'FRDL_SERVER';
		$title = 'Servers';
		$homepage = 'http://look-up.webfan.de/frdl-server';
		$appKey = 'frdl_server';
		
		
     	$html = '';
		$html.= '<a name="'.$appKey.'"></a>';  

		 $html.= '<div class="webfanWikiBoxouter" style="background-size: contain; background-attachment: hidden;  background: url(\'http://static.webfan.de/logos/bg-light-1.png\') #E8F9FF;vertical-align:top;">';		
	    $html.= $this->render_app_head_table($title, $appKey, $div, $homepage, 'http://static.webfan.de/logos/php.medium.png',  $click);
		 $html.= '<div id="'.$div.'" style="display:none;">';	  		
	     $html.= '[ <b><a title="'.$title.'" href="'.$homepage.'" target="_blank" style="color:black;">Homepage</a></b> ]';
	
		 $html.= '<span class="webfan-blue" style="font-size:14px;font-weight:bold;">'.$title.'</span>';
		 
	       $html.= '<br /><i>...coming soon...</i>';
	
		 

		  $html.= $this->render_app_close_x( $div, $homepage);
		   $html.= '</div>';	
		$html.= '</div>';	
		 return $html;
    }
				
	
	protected function installform_frdl_application_composer(){
		$html = '';
		
		$INSTALL_DIR_APP = dirname($this->detect['sys']['SERVER_VARS']['SCRIPT_FILENAME']).\webfan\App::DS.'frdl-ac'.\webfan\App::DS;
		$u = parse_url('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
		$path = str_replace(\webfan\App::DS, '/',dirname($u['path'])).'/'.'frdl-ac'.'/';
		$SETUP_URL = 'http://'.$_SERVER['SERVER_NAME'].$path;
		$INSTALL_DIR = $this->detect['sys']['files_dir'].'frdl-application-composer'.\webfan\App::DS;
		

        $html.= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		
		$html.= '<table style="width:100%;border:none;">';
		 $html.= '<tr>';
	     $html.='<td style="width:250px";>';
	      $html.='<b>Installation directory</b> (it is recommended to chose a directory out of the http directory!)';
		 $html.='</td>';
		 $html.='<td colspan="2">';
	     $html.= '<input style="width:99%;" type="text" id="FRDL_INSTALL_DIR" name="INSTALL_DIR" value="'.$INSTALL_DIR.'" />';
		 $html.='</td>';		 
		 $html.= '</tr>';		 
		 

		 $html.= '<tr>';
	     $html.='<td>';
	      $html.='<b>Backend directory</b> (accessible via http and the Backend URL)';
		 $html.='</td>';
		 $html.='<td colspan="2">';
	     $html.= '<input style="width:99%;" type="text" id="FRDL_BACKEND_DIR" name="INSTALL_DIR_APP" value="'.$INSTALL_DIR_APP.'" />';
		 $html.='</td>';		 
		 $html.= '</tr>';		 
		 

		 $html.= '<tr>';
	     $html.='<td>';
	      $html.='<b>Backend URL</b> (corresponding to the application directory above!)';
		 $html.='</td>';
		 $html.='<td colspan="2">';
	  	 $html.= '<input style="width:99%;" type="text" id="FRDL_BACKEND_URL" name="SETUP_URL" value="'.$SETUP_URL.'" /><br />';
	     $html.='</td>';		 
		 $html.= '</tr>';		 		 


		 

		 $html.= '<tr>';
	     $html.='<td>';
	      $html.='<b>Adminname</b> (used for adminlogin)';
		 $html.='</td>';
		 $html.='<td colspan="2">';
	  	 $html.= '<input style="width:160px;" type="text" id="FRDL_ADMIN_USER" name="ADMIN_USER" value="" /><br />';
	     $html.='</td>';		 
		 $html.= '</tr>';		 		 

		 

		 $html.= '<tr>';
	     $html.='<td>';
	      $html.='<b>Adminpassword</b> (used for adminlogin)';
		 $html.='</td>';
		 $html.='<td colspan="2">';
	  	 $html.= '<input style="width:160px;" type="password" id="FRDL_ADMIN_PASSWORD" name="ADMIN_PASS" value="" /><br />';
	     $html.='</td>';		 
		 $html.= '</tr>';		 		 






		 $html.= '<tr>';
		 
	     $html.='<td>';
	        $html.= '<input type="checkbox" name="accept_license" />';
		    $html.= ' <a href="http://domainundhomepagespeicher.webfan.de/software-center/api/license/1.3.6.1.4.1.37553.8.1.8.4.9/licensetext/" target="_blank">'.$this->lang('__ACCEPT_LICENSE__').' (BSD)</a>';	      
		 $html.='</td>';	
		 
			 
		 $html.='<td>';
		 $html.= '<input type="checkbox" name="sure_backup" /> ';
		  $html.= $this->lang('__SURE_BACKUP__');
		  $html.='?';
		 $html.='</td>';		 
		 	 
	     $html.='<td>';			
	  	 $html.= '<input type="hidden" name="OID" value="'.self::APP_FRDL.'" />';
	     $html.= '<input type="submit" name="BTN_install" value="'.$this->lang('__INSTALL_NOW__').'..." style="width:100%;font-weight:bold;" />';
	     $html.='</td>';		 
		 $html.= '</tr>';				
		
		$html.='</table>';
		$html.='</form>';
		return $html;
	}
	
	protected function form_app_frdl_application_composer($click = false){
    	$args = func_get_args();
		if(isset($args[0]))$click = $args[0];
	
	
		
	     $html = '';
         $div = 'APP_FRDL';
		$title = 'Application Composer';
		$homepage = 'http://frdl.github.io/webfan/';
		$appKey = 'apps_frdl';
		
		
     	$html = '';
		$html.= '<a name="'.$appKey.'"></a>';  

		 $html.= '<div class="webfanWikiBoxouter" style="background-size: contain; background-attachment: hidden;  background: url(\'http://static.webfan.de/logos/bg-light-1.png\') #E8F9FF;vertical-align:top;">';		
	    $html.= $this->render_app_head_table($title, $appKey, $div, $homepage, 
	                       'http://webfan.de/bilder/domainundhomepagespeicher/produkte/kurzbeschreibung/24.251.251THP.produktbild_artikelbeschreibung.jpg',
	                       $click);
		 $html.= '<div id="'.$div.'" style="display:none;">';	  		
	     $html.= '[ <b><a title="'.$title.'" href="'.$homepage.'" target="_blank" style="color:black;">Homepage</a></b> ]';
	
		 $html.= '<span class="webfan-blue" style="font-size:14px;font-weight:bold;">'.$title.'</span>';
	
	
		 $html.= '<br /><span class="webfan-red">This is still development in progress, possible errors!</span>';		 
	    
	     if(count($this->detect[self::GROUP_APPS][self::APP_FRDL]) === 0){
	     	  $html.=$this->installform_frdl_application_composer();
	     }
		 
          
		  
		  if($this->beta === true){
		  	 $html.= '<pre>'.print_r($this->detect,true).'</pre>';
		  }
		  
		  $html.= $this->render_app_close_x( $div, $homepage);
		   $html.= '</div>';	
		$html.= '</div>';	
  	 
    	 
		 return $html;	
	}
	
	
    protected function install($OID){
    	$html = '';
		 $html.= '<pre class="console-screen">';			
		 $html.= '#!> frdl install '.$OID.$this->lnbr;
		 
		  if($OID === self::APP_PMX)$html.= $this->install_pmx($_REQUEST['INSTALL_DIR_PMX']);
		  if($OID === self::APP_WEBDOF)$html.= $this->install_webdof($_REQUEST['dir_pmx_installation']);
		  if($OID === self::APP_FRDL)$html.= $this->install_frdl($_REQUEST);
		  		 
		 $html.='</pre>';
		return $html;
    }
	
	protected function install_frdl(Array $r = null){
		$this->op = 'install';
	    $txt = "";
		$txt.="Loading installer...".$this->lnbr;
		$txt.=  $this->lang('__MAINTENANCE__').$this->lnbr;
		$txt.= htmlentities("> ").$this->lnbr;
		
		
		return $txt;
	}
	
	protected function install_webdof($dir){
	  try{	
		$this->Installer = new \webfan\InstallShield\PragmaMx\WebfanModulesSetup($this);
		return $this->Installer->install($dir);
	  }catch(\Exception $e){
	  	 return $this->e($e->getMessage());
	  }
	}	
	
	protected function install_pmx($dir){
	  try{	
		$this->Installer = new \webfan\InstallShield\PragmaMx\Installer($this);
		return $this->Installer->install($dir);
	  }catch(\Exception $e){
	  	 return $this->e($e->getMessage());
	  }
	}
	

   public function detect_app_frdl(){
   	 
 		$this->detect[self::GROUP_APPS][self::APP_FRDL] = array();
		$sdir = \frdl\webfan\App::God()->getBaseDir();
				
		$files = $this->findFiles($sdir,
		array (
                 '1.3.6.1.4.1.37553.8.1.8.8.5.65.1.4.php',
                 
               )
		 );	
		 
		 foreach($files as $fileName => $_files){
		 	 foreach($_files as $index => $file){
		 	 	   $dir = realpath(dirname($file)).\frdl\webfan\App::DS;
				   if(!isset( $this->detect[self::GROUP_APPS][self::APP_FRDL][$dir] )){
				   	  $this->detect[self::GROUP_APPS][self::APP_FRDL][$dir] = array(
					  
					  );
				   }
				 	
				   if(strtolower(substr($file,-4,4))==='frdl'){
				   	  $this->detect[self::GROUP_LIBS][self::APP_FRDL][$dir]['file_bin'] = $file;
				   }elseif(strtolower(substr($file,-3,3))==='php'){
				   	  $this->detect[self::GROUP_LIBS][self::APP_FRDL][$dir]['file_php'] = $file;
				   }elseif(strtolower(substr($file,-3,3))==='json'){
				   	  $this->detect[self::GROUP_LIBS][self::APP_FRDL][$dir]['file_json'] = $file;
				   }


		 	
			 }
		 }  	   
   }



	public function detect_packages(){
		$this->detect[self::GROUP_LIBS][self::FRDL_PACKAGE] = array();
		
		$sdir = \frdl\webfan\App::God()->getBaseDir();
				 
		$files = $this->findFiles($sdir,
		array (
                 'composer.json',
                 '1.3.6.1.4.1.37553.8.1.8.8.5.65.2.php', 
               
               )
		 );	
		 
		 foreach($files as $fileName => $_files){
		 	 foreach($_files as $index => $file){
		 	 	   $dir = realpath(dirname($file)).\frdl\webfan\App::DS;
				   if(!isset( $this->detect[self::GROUP_LIBS][self::FRDL_PACKAGE][$dir] )){
				   	  $this->detect[self::GROUP_LIBS][self::FRDL_PACKAGE][$dir] = array(
					  
					  );
				   }
				 	
				   if(strtolower(basename($file))==='composer.json'){
				   	  $this->detect[self::GROUP_LIBS][self::FRDL_PACKAGE][$dir]['file_composer'] = $file;
				   }else{
				   	  $this->detect[self::GROUP_LIBS][self::FRDL_PACKAGE][$dir]['file_package'] = $file;
				   }

		 	
			 }
		 }
		 
	}
	
	
	


	
   public function detect_app_webdof(){
      
		$this->detect[self::GROUP_APPS][self::APP_WEBDOF] = array();
		
		$files = $this->findFiles(\frdl\webfan\App::God()->getBaseDir(),
		array (
            
                'webdof.php'
               )
		 );
		
		foreach($files as $fileName => $_files){
		 foreach($_files as $index => $file){	
		  $fcont = file_get_contents($file);	
		  $valid = preg_match("/".preg_quote("const VERSION = '")
                                 ."([0-9\.]+)".preg_quote("'")."/", $fcont, $matches);
          if(!$valid)continue;								 
          $version = $matches[1];
		  $dir = realpath(dirname($file)).\frdl\webfan\App::DS;
		  if(!file_exists($dir.'..'.\frdl\webfan\App::DS.'setup.pmx.php'))continue;
		  if(basename(dirname(dirname($dir))) !== 'modules')continue;
		  $this->detect[self::GROUP_APPS][self::APP_WEBDOF][$dir] = array(
		                                                'file' => realpath($file),
                                                        'version' => $version,
                                                        'valid' => ($valid) ? true : FALSE,
                                                        'dir' => $dir,
                                                        'crc32b' => hash_file('crc32b', $file, FALSE),
                                                        'sha1_file' => sha1_file($file), 
												); 
		 }
		}
		
	}	

    
	public function detect_app_pmx(){
		$this->detect[self::GROUP_APPS][self::APP_PMX] = array();
		
		
		$files = $this->findFiles(\frdl\webfan\App::God()->getBaseDir(),
		array (
                //  "dat",
                'mainfile.php'
               )
		 );
		
		foreach($files as $fileName => $_files){
		 foreach($_files as $index => $file){	
		  $fcont = file_get_contents($file);	
          $valid = preg_match("/".preg_quote("define('PMX_VERSION', '")
                                 ."([0-9\.]+)?".preg_quote("');")."/", $fcont, $matches);
          if(!$valid)continue;								 
          $version = $matches[1];
		  $dir = realpath(dirname($file)).\frdl\webfan\App::DS;
		  $this->detect[self::GROUP_APPS][self::APP_PMX][$dir] = array(
		                                                'file' => realpath($file),
                                                        'version' => $version,
                                                        'valid' => ($valid) ? true : FALSE,
                                                        'dir' => $dir,
                                                        'crc32b' => hash_file('crc32b', $file, FALSE),
                                                        'sha1_file' => sha1_file($file), 
												); 
		 }
		}
				
	}
	

	public function detect_accounts(){
        $this->detect_interfaces();  
	}

	public function detect_apps(){
        $this->detect_app_pmx(); 
		$this->detect_app_webdof();   
		$this->detect_app_frdl();   
  	}				
		

	
	public function detect_interfaces(){
 
	}				
			
	public function detect(){
		$this->clear_detect();
        $this->detect_sys();  
	    $this->detect_apps();			
        $this->detect_packages(); 
	    $this->detect_accounts();
		
	}
	
	
	
	
	public function getOSVar($vn, $os = PHP_OS)
		{
			if(!is_string($os))$os = strtolower(\frdl\webfan\App::God(false)->getOSName());
			
			$vn = strtolower($vn);
			
			
			switch(strtolower($os))
			{
	
				case 'darwin' :	
				case 'netbsd' :  		
				case 'freebsd' : 
            	case 'solaris' : 
		
				case 'sunos' : 

					
					switch($vn)
					{
						case 'conf' :
							$var = '/sbin/ifconfig';
							break;
						case 'mac' :
							$var = 'ether';
							break;
						case 'ip' :
							$var = 'inet ';
							break;
						case 'lnbr' : 
							$var = "\n";
							break;	
					}
					break;
		
				case 'linux' : 
				
					switch($vn)
					{
						case 'conf' :
							$var = '/sbin/ifconfig';
							break;
						case 'mac' :
							$var = 'HWaddr';
							break;
						case 'ip' :
							$var = 'inet addr:';
							break;
						case 'lnbr' : 
							$var = "\n";
							break;	
					}
					break;
					
				default :
					switch($vn)
					{
						case 'conf' :
							$var = '/sbin/ifconfig';
							break;
						case 'mac' :
							$var = 'HWaddr';
							break;
						case 'ip' :
							$var = 'inet addr:';
							break;
						case 'lnbr' : 
							$var = "\r\n";
							break;	
					}					
					break;
									
			}
			return $var;
		}


	public function getServerConfigFile($OS = PHP_OS)
		{

            if(!is_string($OS))$OS = strtolower(\frdl\webfan\App::God(false)->getOSName());

			if(ini_get('safe_mode'))
			{
				return 'SAFE_MODE';
			}

			
			if(substr($OS, 0, 3)=='win')
			{

				@exec('ipconfig/all', $lines);

				if(count($lines) == 0) return false;

				$conf = implode($this->getOSVar('LINEBREAK', $OS), $lines);
			}
			else
			{
	
				$os_file = $this->getOSVar('conf', $OS);
			
				$fp = @popen($os_file, "rb");
		
				if (!$fp) return false;
			
				$conf = @fread($fp, 4096);
				@pclose($fp);
			}
			return $conf;
    }
		

	public function getMAC($s = PHP_OS)
		{
		    if(!is_string($s))$s = strtolower(\frdl\webfan\App::God(false)->getOSName());
			$conf = $this->getServerConfigFile();
			
		
			
			if(substr($s, 0, 3)=='win')
			{
		
				$l = explode($this->getOSVar('LINEBREAK', $s), $conf);
		        $e = error_reporting();    
				
				foreach ($l as $key=>$ln)
				{
					
					if(preg_match("/([0-9a-f][0-9a-f][-:]){5}([0-9a-f][0-9a-f])/i", $ln)) 
					{
						$t = trim($ln);
					
						return trim(substr($t, strrpos($t, " ")));
					}
				}
			
			}
			else
			{
				
				$m = $this->getOSVar('mac', $s);
				
			
				$pos = strpos($conf, $m);
				if($pos)
				{
				
					$str1 = trim(substr($conf, ($pos+strlen($m))));
					return trim(substr($str1, 0, strpos($str1, "\n")));
				}
			}
		
			return false; 
	}



	public function detect_sys(){
		
		$this->detect[self::GROUP_SYS]['OS'] = \frdl\webfan\App::God(false)->getOSName();
    	$this->detect[self::GROUP_SYS]['extensions'] = array_flip (get_loaded_extensions());
		ksort($this->detect[self::GROUP_SYS]['extensions']);
		
		$IPs = \frdl\webfan\App::God(false)->getServerIp(true);
		$this->detect[self::GROUP_SYS]['IPs'] = $IPs;
		unset($IPs);
		
		$this->detect[self::GROUP_SYS]['MAC'] = $this->getMAC($this->detect[self::GROUP_SYS]['OS']);
		
		
		$this->detect[self::GROUP_SYS]['SERVER_VARS'] = &$_SERVER;
		$this->detect[self::GROUP_SYS]['temp_dir'] = \frdl\webfan\App::DS.trim(sys_get_temp_dir(), \frdl\webfan\App::DS.' ').\frdl\webfan\App::DS;
		

		$callback = (function($source, &$obj){
		$writable =  is_writable($source);
			$is_dir = is_dir($source);
			
			if($writable && $is_dir && $source !== $obj->detect[\webfan\Install::GROUP_SYS]['SERVER_VARS']['DOCUMENT_ROOT']
			   && (!isset($obj->detect[\webfan\Install::GROUP_SYS]['files_dir']) || !preg_match("/files/",$obj->detect[\webfan\Install::GROUP_SYS]['files_dir']))
			   && substr(basename($source),0,1) !== '.'
			   && !preg_match("/tmp/",basename($source))
			   && !preg_match("/restore/",basename($source))
			   && !preg_match("/backup/",basename($source))
			   && !preg_match("/log/",basename($source))
			   && !preg_match("/bin/",basename($source))
			   && !preg_match("/usr/",basename($source))
			   && !preg_match("/dev/",basename($source))
			   && !preg_match("/etc/",basename($source))
			   && !preg_match("/atd/",basename($source))
			   && !preg_match("/html/",basename($source))
			 ){
				$obj->detect[\webfan\Install::GROUP_SYS]['files_dir'] = rtrim(realpath($source), \frdl\webfan\App::DS.' ').\frdl\webfan\App::DS;
			}
			
			
			$obj->detect[\webfan\Install::GROUP_SYS]['FS'][realpath($source).\frdl\webfan\App::DS] = array(
			  'writable' => $writable,
			  'is_dir' => $is_dir,
			);
        });
		
	    $this->detect[\webfan\Install::GROUP_SYS]['FS']	= array();
		
		$source = \frdl\webfan\App::God()->getBaseDir();
	    $obj = &$this;		
		$callback($source,$obj);
		$maxDepth  = 0;
		$this->readDir($source, $callback, $obj, $maxDepth);
		
		ksort($this->detect[\webfan\Install::GROUP_SYS]['FS']);

	}

	

    protected function readDir($source, \Closure &$callback = null, &$obj = null, &$maxDepth = INF,  $depth = -1){
		  $depth++;
		  if(($maxDepth !== INF && $depth > $maxDepth) || !is_dir($source))return;
		    $dirHandle=opendir($source);
            while($file=readdir($dirHandle))
            {
            	@set_time_limit(ini_get('max_execution_time'));
				
                if($file!="." && $file!="..")
                {
                	  if(is_callable($callback)){
                         	call_user_func($callback, $source.\frdl\webfan\App::DS.$file, $obj);
                      }
					  
                     if(!is_dir($source.\frdl\webfan\App::DS.$file)) {
                      
                    } else {
                       $this->readDir($source.\frdl\webfan\App::DS.$file.\frdl\webfan\App::DS, $callback , $obj, $maxDepth,  $depth);
                    }
                 
                }
            }
            closedir($dirHandle);		
	}
	
	

		
		
		
	protected function glob_recursive($directory, &$directories = array()) {
        foreach(glob($directory, GLOB_ONLYDIR | GLOB_NOSORT) as $folder) {
            $directories[] = $folder;
            $this->glob_recursive("{$folder}".\frdl\webfan\App::DS."*", $directories);
        }
    }
	
	public function findFiles($directory, $extensions = array()) {
    $this->glob_recursive($directory, $directories);
    $files = array ();
	if(is_array($directories)){
    foreach($directories as $directory) {
        foreach($extensions as $extension) {
            foreach(glob("{$directory}".\frdl\webfan\App::DS."*.{$extension}") as $file) {
                $files[$extension][] = $file;
            }
			
			if(strpos($extension,'.')){
	            foreach(glob("{$directory}".\frdl\webfan\App::DS."{$extension}") as $file) {
                   $files[$extension][] = $file;
               }			
			}
        }
    }
	}
    return $files;
    }	
	
	
	
	public function webfan_downloads_download($lid, &$r, $post = array(), $send_cookies = array()){
	  $html= '';	
  	  $html.= 'Download package...'."\n";		
      $C = new \webdof\Http\Client();
	  @set_time_limit(ini_get('max_execution_time'));
	  $idfile = time().mt_rand(1000,9999);	 
	  $r = $C->request('http://www.webfan.de/dl/get.php?lid='.$lid.'&idfile='.$idfile, 'POST', $post, $send_cookies, E_USER_WARNING);
      @set_time_limit(ini_get('max_execution_time'));
	  
	  if(intval($r['status'])!==200){
	  	$html.= $this->e($this->lang('__DOWNLOAD_FAILED__'));
		return $html;	
	  }
	 

	   $check = sha1($r['body']);
	   $url_checksum = 'http://www.webfan.de/dl/get.php?lid='.$lid.'&ressource=checksum&';
	   $url_checksum .= 'sha1='.$check.'&idfile='.$idfile;
	   $checkfile = $C->SimpleGet( $url_checksum );
	   $checksum_server = trim($checkfile);
       if($check !== $checksum_server)
             {
             	$r['status'] = 409; 
	            $html.= $this->e($this->lang('__CHECKSUM_INVALID__'));
	        	return $html;
             }	  
		
		
	   return $html;		 
	}

		
	
}

