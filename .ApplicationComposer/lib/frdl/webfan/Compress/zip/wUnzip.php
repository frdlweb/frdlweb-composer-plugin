<?php
namespace frdl\webfan\Compress\zip;


class wUnzip
{

 var $errors;
 var $file;
 var $destination;
 var $filesOut;

 function __construct($file, $destination = '' )
  {
   $this->errors = array();
   $this->file = $file;
   $this->destination = $destination;
   $this->filesOut = array();
  }


 function load($file, $destination = '')
  {
   $this->errors = array();
   $this->file = $file;
   $this->destination = $destination;
   $this->filesOut = array();
  }
  //eof load


 function unzip( ){
    @set_time_limit(ini_get('max_execution_time'));
    $zip=zip_open($this->file);
    if(!$zip)
       {
          $te = "Unable to proccess file '{$file}'\n";
          $this->errors[] = $te;
          return array('html' => $te, 'errors' => $this->errors);
       }

    $e='';
    $k = -1;

    while($zip_entry=zip_read($zip)) {
       $k++;
    	 @set_time_limit(ini_get('max_execution_time'));
		 
	   if($k > 65535)	
		trigger_error('Zip archive '.$this->file.' contains to many entries in '.__CLASS__.' '.__METHOD__.' '.__LINE__, E_USER_ERROR);
		

       $zdir= dirname(zip_entry_name($zip_entry));
       $zname= $this->destination.zip_entry_name($zip_entry);
       $this->filesOut[$k] = array('file' => $zname, 'success' => FALSE);

       $zip_fs=zip_entry_filesize($zip_entry);
       if(!zip_entry_open($zip,$zip_entry,"rb"))
            {
                $te = "Unable to proccess file '{$zname}'\n";
                $this->errors[] = $te;
                $e.= $te;
                continue;
            }
       if(!is_dir($this->destination.$zdir)) mkdir($this->destination.$zdir,0775, TRUE);
	   chmod($this->destination.$zdir, 0755);

       $e.= "{$zdir} | {$zname} \n";

       
       if(!$zip_fs || $zip_fs === 0)
          {
             $te = "File empty '{$zname}'\n";
             $this->errors[] = $te;
             $e.= $te;
              continue;
          }

       $data=zip_entry_read($zip_entry,$zip_fs);

	   
       $fp=fopen($zname,"wb+");
       fwrite($fp,$data);
       fclose($fp);

	   zip_entry_close($zip_entry);
 
       if(file_exists($zname))$this->filesOut[$k]['success'] = TRUE;
    }
    zip_close($zip);

    return array('html' => $e, 'errors' => $this->errors);
 }
 //eof unzip






}
//eof clas