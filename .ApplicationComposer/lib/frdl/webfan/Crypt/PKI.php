<?php
/**
 * 
 * Copyright  (c) 2015, Till Wehowski
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *   must display the following acknowledgement:
 *    This product includes software developed by the frdl/webfan.
 * 4. Neither the name of frdl/webfan nor the
 *    names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY frdl/webfan ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL frdl/webfan BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 */
namespace frdl\webfan\Crypt;


class PKI
{
    const DISABLED = 0;
    const OPENSSL = 1;
    const PHPSECLIB = 2;

    const E_NORSA = 'No RSA library selected or supported';
    const E_NOTIMPLEMENTED = 'Sorry thisd is not implemented yet';    
    

    const B_SIGNATURE = "-----BEGIN SIGNATURE-----\r\n";
    const E_SIGNATURE = "-----END SIGNATURE-----";

    const B_CERTIFICATE = "-----BEGIN CERTIFICATE-----\r\n";
    const E_CERTIFICATE = "-----END CERTIFICATE-----";

    const B_PUBLIC_KEY = "-----BEGIN PUBLIC KEY-----\r\n";
    const E_PUBLIC_KEY = "-----END PUBLIC KEY-----";

    const B_RSA_PRIVATE_KEY = "-----BEGIN RSA PRIVATE KEY-----\r\n";
    const E_RSA_PRIVATE_KEY = "-----END RSA PRIVATE KEY-----";

    const B_KEY = "-----BEGIN KEY-----\r\n";
    const E_KEY = "-----END KEY-----";
 
    const B_LICENSEKEY = "-----BEGIN LICENSEKEY-----\r\n";
    const E_LICENSEKEY = "-----END LICENSEKEY-----";



    protected $lib;


   function __construct($lib = 0)
     {
        $this->setLib($lib);
     }


   public function setLib($lib)
     {
        $this->lib = $lib;
     } 

   public function save($data, $begin = "-----BEGIN SIGNATURE-----\r\n", $end = '-----END SIGNATURE-----')
     {
        return $begin . chunk_split(base64_encode($data)) . $end;
     }


   public function load($str)
     {
       $data = preg_replace('#^(?:[^-].+[\r\n]+)+|-.+-|[\r\n]#', '', $str);
       return preg_match('#^[a-zA-Z\d/+]*={0,2}$#', $data) ? utf8_decode (base64_decode($data) ) : false;
     }

  public function error($error, $mod = E_USER_ERROR, $info = TRUE)
    {
      trigger_error($error.(($info === TRUE) ? ' in '.__METHOD__.' line '.__LINE__ : ''), $mod);
      return FALSE;
    }
    
    
  public function verify($data, $sigBin, $publickey, $algo = 'sha256WithRSAEncryption')
     {
        switch($this->lib)
          {
           case self::OPENSSL :
                  return $this->verify_openssl($data, $sigBin, $publickey, $algo);
                break;

           case self::PHPSECLIB :
                  return $this->verify_phpseclib($data, $sigBin, $publickey, $algo);
                break;
           case self::DISABLED :
           default :
                  return $this->error(self::E_NORSA, E_USER_ERROR);
                break;

          }

     }
    
	    
  public function getPublKeyByCRT($cert)
     {
        switch($this->lib)
          {
           case self::OPENSSL :
                  return $this->getPublKeyByCRT_openssl($cert);
                break;

           case self::PHPSECLIB :
                  return $this->error(self::E_NOTIMPLEMENTED, E_USER_ERROR);
                break;
           case self::DISABLED :
           default :
                  return $this->error(self::E_NORSA, E_USER_ERROR);
                break;

          }

     }
	 
  public function encrypt($data,$PrivateKey,&$out)
     {
        switch($this->lib)
          {
           case self::OPENSSL :
                  return $this->encrypt_openssl($data,$PrivateKey,$out);
                break;
        case self::PHPSECLIB :
                  return $this->error(self::E_NOTIMPLEMENTED, E_USER_ERROR);
                break;
           case self::DISABLED :
           default :
                  return $this->error(self::E_NORSA, E_USER_ERROR);
                break;

          }

     }
	 

  public function decrypt($decrypted,$PublicKey,&$out)
     {
        switch($this->lib)
          {
           case self::OPENSSL :
                  return $this->decrypt_openssl($decrypted,$PublicKey,$out);
                break;
        case self::PHPSECLIB :
                  return $this->error(self::E_NOTIMPLEMENTED, E_USER_ERROR);
                break;
           case self::DISABLED :
           default :
                  return $this->error(self::E_NORSA, E_USER_ERROR);
                break;

          }

     }
	 	 
  protected function encrypt_openssl($data,$PrivateKey,&$out) {  
     $PrivKeyRes = openssl_pkey_get_private($PrivateKey);
     return openssl_private_encrypt($data,$out,$PrivKeyRes); 
  }
  
  protected function decrypt_openssl($decrypted,$PublicKey,&$out) {  
        $pub_key = openssl_get_publickey($PublicKey);
        $keyData = openssl_pkey_get_details($pub_key);
        $pub = $keyData['key'];
        $successDecrypted = openssl_public_decrypt(base64_decode($decrypted),$out,$PublicKey, OPENSSL_PKCS1_PADDING);
		return $successDecrypted; 
  }
  


  protected function getPublKeyByCRT_openssl($cert)
    {
       $res = openssl_pkey_get_public($cert);
       $keyDetails = openssl_pkey_get_details($res);
       return $keyDetails['key'];
    }

     

  protected function verify_phpseclib($data, $sigBin, $publickey, $algo = 'sha256WithRSAEncryption')
      {
         $isHash = preg_match("/^([a-z]+[0-9]).+/", $algo, $hashinfo);
         $hash = ($isHash) ? $hashinfo[1] : 'sha256';

         $rsa = new Crypt_RSA();
         $rsa->setHash($hash);
         $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
         $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
         $rsa->loadKey($publickey);
         return (($rsa->verify($data, $sigBin) === TRUE) ? TRUE : FALSE);
      }


   protected function verify_openssl($data, $sigBin, $publickey, $algo = 'sha256WithRSAEncryption')
      {
        return ((openssl_verify($data, $sigBin, $publickey, $algo) == 1) ? TRUE : FALSE);
      }
}
