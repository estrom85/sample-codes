<?php


/**
 * trieda Nonce vygeneruje, registruje a kontoluje jednoznacny indikator formulara
 *
 * @author mato
 */
final class Nonce {
    //vygeneruje indikator
    public static function getNonce(){
        $rand_string=  static::randomString();  //vygeneruje nahodny textovy retazec
        $timestamp=  time();                    //ziska aktualnu casovu znamku
        
        $nonce=  sha1($rand_string.$timestamp); //vygeneruje indikator (sha1 hash)
        $_SESSION[$nonce]['string']=$rand_string;   //registruje indikator
        $_SESSION[$nonce]['time']=$timestamp;
        return $nonce;
    }
    //skontroluje indikator
    public static function checkNonce($nonce){
        
        if(empty($_SESSION[$nonce])){
            return false;
        }
        //ziska informacie o indikatore
        $string=$_SESSION[$nonce]['string'];
        $timestamp=$_SESSION[$nonce]['time'];
        unset($_SESSION[$nonce]); //odstrani indikator zo sekcie
        $diff=time()-$timestamp;
        //ak bol indikator vytvoreny pred viac ako 5 minutami, straca platnost
        if($diff>300){
            return false;
        }
         //skontoluje, ci je indikator spravny opatovnym vygenerovanim indikatora
        if(strcmp(sha1($string.$timestamp),$nonce)){ 
            return false;
        }

        return true;
    }
    //vygeneruje nahodny textovy retazec
    private static function randomString($length=20){
        $validChars="abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVXYZ0123456789+-*#&@!?";
        $validLength=  strlen($validChars);
        
        $result="";
        
        for($i=0;$i<$length;$i++){
            $index=  mt_rand(0, $validLength-1);
            
            $result .=$validChars[$index];
        }
        return $result;
    }
}

?>
