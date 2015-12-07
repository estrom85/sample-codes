<?php

/**
 * Zakadna trieda pre ovladanie jednotlivych modulov
 *
 * @author mato
 */
class ProgramManager {
    /*
     * Parametre triedy
     */
    private static $programs;
    private static $id;
    private static $includePaths;
    private static $home="";
    //zabranenie vytvorenie instancie triedy
    private function __construct() {
    }
    //nastavenie domovskeho adresara
    public static function setHomeDir($path){
        static::$home=$path;
    }
    //prida program
    public static function addProgram($id,$program,$includePath){
        static::$programs[$id]=$program;
        static::$id[$program]=$id;
        static::$includePaths[$id]=$includePath;
    }
    //ziska id programu
    public static function getId($program){
        if(isset(static::$programs[$program]))
            return $program;
        if(isset(static::$id[$program]))
            return static::$id[$program];
        return false;
    }
    //ziska nazov programu
    public static function getProgramName($program){
        if(isset(static::$programs[$program]))
            return static::$programs[$program];
        if(isset(static::$id[$program]))
            return $program;
        return false;
    }
    //ziska meno programu na zaklade id
    public static function getProgramNameById($id){
        if(isset(static::$programs[$id]))
            return static::$programs[$id];
        return false;
    }
    //nacita zdrojovy kod vybraneho programu
    public static function includeProgram($program){
        $home="./";
        if(strcmp(static::$home,""))
            $home=static::$home."/";
        
        if(isset(static::$programs[$program])){
            require_once $home.static::$includePaths[$program];
            return true;
        }
        if(isset(static::$id[$program])){
            require_once $home.static::$includePaths[static::$id[$program]];
            return true;
        }
            
        return false;
    }
    //vrati domovsky adresar
    public static function getHomeDir(){
        return static::$home;
    }
}


?>
