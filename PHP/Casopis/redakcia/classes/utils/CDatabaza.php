<?php

/**
 * Trieda CDatabaza - trieda zodpovedna za pripojenie k databaze. Trieda je implementovana
 * ako singleton, cize existuje len jedina instancia tejto triedy v programe. Ostatne casti programu
 * vyuzivaju len odkaz na tento objekt. Trieda sluzi na manipulaciu s databazou.
 *
 * @author Martin Macaj
 */

class CDatabaza {
    /*
     * Parametre triedy
     */
    private $database;      //reprezentuje pripojenie na databazu - mysqli objekt
    private $db_name;       //nazov databazy
    private $login;         //prihlasovacie meno k databazovemu serveru
    private $password;      //heslo na prihlasenie k databazovemu serveru
    private $server;        //adresa mysql servera
    private $connected;     //indikator pripojenia - true->pripojeny, false->nepripojeny
    
    private static $default_database;
    /*
     * Konstruktor
     */
    private function __construct($server, $login, $password, $database) {
        $this->server=$server;
        $this->login=$login;
        $this->password=$password;
        $this->db_name=$database;
        
        $this->database=new mysqli($server,$login,$password, $database);
        if($this->database->connect_errno){
            $this->connected=false;
            return;
        }
        $this->database->close();
        $this->connected=true;
        
    }
    /*
     * Destruktor
     */
    public function __destruct() {
        //ak sa uzivatel triedy neodhlasi z databazy, tato metoda zabezpeci
        //ze pri zruseni instancie triedy aplikacia uzavrie spojenie s databazou
        
        if($this->connected)
            $this->database->close();
    }


    /*
     * STATICKE METODY:
     */
    
    /*
     * Inicializacia databazy-zo suboru casopis.conf
     */
    private static function initDB(){
        //otvori subor casopis.conf
        $file=fopen("casopis.conf", "r",true);
        if(!$file)
            die ("Nemozem otvorit subor");
        
        $temp="";
        $id="";
        
        $server="";
        $login="";
        $password="";
        $database="";
    
        //nacitanie konfiguracneho suboru
        
        while(!feof($file)){
            $temp=  fgets($file);
            
            //odstranenie bielych znakov zo zaciatku a konca riadku
            $temp=trim($temp);
            //odignoruje prazdne riadky a riadky obsahujuce znak #
            if(strstr($temp,"#"))
                    continue;
            if(!strcmp($temp, ""))
                    continue; 
            
            list($id,$temp)=explode(": ", $temp);
            $id=trim($id);
            $temp=trim($temp);
            
            //nastavi premenne server,login, password a database podla hodnot konfiguracneho suboru
            if(!strcmp($id, "server")){
                $server=$temp;
                continue;
            }
            if(!strcmp($id,"login")){
                $login=$temp;
                continue;
            }
            if(!strcmp($id, "psswd")){
                $password=$temp;
                continue;
            }
            if(!strcmp($id,"dbase")){
                $database=$temp;
                continue;
            }
            
        }
        fclose($file);
         
        //vytvori instanciu triedy.
        static::$default_database=new CDatabaza($server, $login, $password, $database);
        if(!static::$default_database->connected)
            static::$default_database=null;
        else
            static::$default_database->connected=false;
         
    }
    /*
     * Vrati instanciu triedy, ak nie je inicializovana, inicializuje zo suboru
     */
    public static function getInstance(){
        if(empty(static::$default_database))
            static::initDB();
        
        return static::$default_database;
    }
    
    /*
     * Vytvorí prázdnu databázu a vytvorí účet admin
     */
    public static function createDB($database="casopis",$login="casopis",$password="redakcia",$server="localhost"){
  
        //pripojenie na server
        $db=new mysqli($server,$login,$password);
        if($db->connect_errno){
            
            die("Spojenie so serverom sa nepodarilo, skúste neskôr prosím");
        }
        
        //kontrola, či databáza s daným menom už existuje
        if($db->select_db($database)){
            $db->close();
            die ("databaza s nazvom '$database' uz existuje, zadajte iny nazov");
        }
        
        //vytvorenie prazdnej databazu
        if(!$db->query("CREATE DATABASE $database CHARACTER SET utf8 COLLATE utf8_slovak_ci")){
            echo $db->error."</br>";
            $db->close();
            die("Nepodarilo sa vytvoriť databázu");
        }
        echo "Databáza $database úspešne vytvorená </br>";
        
        //napojenie na vytvorenu databazu
        if(!$db->select_db($database)){
            $db->close();
            die("Nepodarilo sa pripojit na databazu");
        }
        echo "Spojenie s databázou úspešné</br>";
        
        /*
         * Tabulky na ukladanie dat
         */
        //Vytvorenie tabulky Uzivatel
        $sql="CREATE TABLE Uzivatel (
            uzivatel_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            prihlasovacie_meno VARCHAR(50) NOT NULL UNIQUE KEY,
            heslo VARCHAR(50) NOT NULL,
            pristupove_prava SET('EDIT_USERS','EDIT_ALL','ADD','REMOVE','RELEASE','DROP','EDIT_ENUMS')
            );";
        if(!$db->query($sql)){
            echo $db->error."</br>"; //zobrazi error
            //vymaze databazu
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close(); //uzavrie databazu
            die ("Nepodarilo sa vytvorit tabulku Uzivatel.");
        }
        echo "Tabulka uzivatel uspesne vytvorena. </br>";
        
        //vytvorenie uzivatela admin
        $sql="INSERT INTO Uzivatel(prihlasovacie_meno,heslo,pristupove_prava) 
            VALUES('admin','".md5("admin")."',
                'EDIT_USERS,EDIT_ALL,ADD,REMOVE,RELEASE,DROP,ASSIGN,EDIT_ENUMS');";
        
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa pridat uzivatela admin.");
        }
        echo "Ucet admin pridany </br>";

        //Vytvorenie tabulky Uzivatel_info
        $sql="CREATE TABLE Uzivatel_info(
            uzivatel_id INT NOT NULL PRIMARY KEY,
            meno VARCHAR(100) NOT NULL ,
            priezvisko VARCHAR(100) NOT NULL,
            trieda VARCHAR(5)
            )";
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa vytvorit tabulku Uzivatel_info.");
        }
        echo "Tabulka Uzivatel_info uspesne vytvorena. </br>";
        
        //vytvorenie uzivatela admin
        $sql="INSERT INTO Uzivatel_info(uzivatel_id,meno,priezvisko) 
            VALUES(1,' ',' ')";
        
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa pridat uzivatela admin.");
        }
        echo "Ucet admin_info pridany </br>";
        
        //Vytvorenie tabulky Clanok
        $sql="CREATE TABLE Clanok(
            clanok_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            nazov_clanku VARCHAR(100) NOT NULL UNIQUE KEY,
            typ_clanku_id INT NOT NULL,
            rubrika_id INT NOT NULL,
            hodnotenie_pocet INT NOT NULL,
            hodnotenie INT NOT NULL,
            zobrazit BOOLEAN NOT NULL,
            diskusia BOOLEAN NOT NULL,
            casova_znamka INT NOT NULL
            )";
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa vytvorit tabulku Clanok.");
        }
        echo "Tabulka Clanok uspesne vytvorena. </br>";
        
        //Vytvorenie tabulky Prispevok
        $sql="CREATE TABLE Prispevok(
            prispevok_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            nazov_prispevku VARCHAR(50) NOT NULL,
            kategoria_id INT NOT NULL,
            prispevok TEXT NOT NULL,
            uzivatel_id INT NOT NULL,
            clanok_id INT NOT NULL,
            zobrazit BOOLEAN NOT NULL,
            casova_znamka INT NOT NULL
            )";
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa vytvorit tabulku Prispevok.");
        }
        echo "Tabulka Prispevok uspesne vytvorena. </br>";
        
        //Vytvorenie tabulky Diskusia
        $sql="CREATE TABLE Diskusia(
            diskusia_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            meno VARCHAR(50) NOT NULL,
            prispevok TEXT NOT NULL,
            clanok_id INT NOT NULL,
            casova_zmanka INT NOT NULL
            )";
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa vytvorit tabulku Diskusia.");
        }
        echo "Tabulka Diskusia uspesne vytvorena. </br>";
        /*
         * Tabulky enumeracii
         */
        //Vytvorenie tabulky typ_clanku
        $sql="CREATE TABLE Typ_clanku(
            typ_clanku_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            nazov VARCHAR(50) NOT NULL
            )";
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa vytvorit tabulku Typ_clanku.");
        }
        echo "Tabulka Typ_clanku uspesne vytvorena. </br>";
        
        //Vytvorenie tabulky Rubrika
        $sql="CREATE TABLE Rubrika(
            rubrika_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            nazov_rubriky VARCHAR(50) NOT NULL UNIQUE KEY,
            tema_id INT NOT NULL
            )";
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa vytvorit tabulku Rubrika.");
        }
        echo "Tabulka Rubrika uspesne vytvorena. </br>";
        
        //Vytvorenie tabulky tema
        $sql="CREATE TABLE Tema(
            tema_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            nazov_temy VARCHAR(50) NOT NULL UNIQUE KEY
            )";
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa vytvorit tabulku Tema.");
        }
        echo "Tabulka Tema uspesne vytvorena. </br>";
        
        //Vytvorenie tabulky kategoria
        $sql="CREATE TABLE Kategoria(
            kategoria_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            nazov_kategorie VARCHAR(50) NOT NULL UNIQUE KEY
            )";
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa vytvorit tabulku Kategoria.");
        }
        echo "Tabulka Kategoria uspesne vytvorena. </br>";
        
        //Vytvorenie tabulky klucove slovo
        $sql="CREATE TABLE Klucove_slovo(
            klucove_slovo_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            klucove_slovo VARCHAR(50) NOT NULL
            )";
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa vytvorit tabulku Klucove slovo.");
        }
        echo "Tabulka Klucove slovo uspesne vytvorena. </br>";
        
        /*
         * Prepojovacie tabulky
         */
        
        //Vytvorenie tabulky Clanok_uzivatel
        $sql="CREATE TABLE Clanok_uzivatel(
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            clanok_id INT NOT NULL,
            uzivatel_id INT NOT NULL
            )";
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa vytvorit tabulku Clanok_uzivatel.");
        }
        echo "Tabulka Clanok_uzivatel uspesne vytvorena. </br>";
        
        //Vytvorenie tabulky Clanok_klucove_slovo
        $sql="CREATE TABLE Clanok_klucove_slovo(
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            clanok_id INT NOT NULL,
            klucove_slovo_id INT NOT NULL
            )";
        if(!$db->query($sql)){
            echo $db->error."</br>";
            if($db->query("DROP DATABASE $database")) echo "Databaza $database vymazana</br>";
            $db->close();
            die ("Nepodarilo sa vytvorit tabulku Clanok_klucove_slovo");
        }
        echo "Tabulka Clanok_klucove_slovo uspesne vytvorena. </br>";
        
        echo "Databaza uspesne vytvorena";
        $db->close();
    }
    
    /*
     * Uzivatelske rozhranie
     */

    /*
     * Pripoji sa na databazu
     */
    public function connect(){
        $this->database->connect($this->server, $this->login, $this->password, $this->db_name);
        if($this->database->connect_errno){
            return;
        }
        //nastavi kodovanie databazy na UTF-8
        $this->database->query("SET CHARACTER SET utf8");
        $this->connected=true;
    }
    
    public function connected(){
        return $this->connected;
    }

    /*
     * Odpoji sa od databazy
     */
   public function close(){
       $this->database->close();
       $this->connected=false;
   }
    
    /*
     * dotaz
     */
   public function query($query){
       return $this->database->query($query);
   }
    
   public function last_id(){
       return $this->database->insert_id;
   }
   /*
    * odstrani neziaduce znaky zo vstupu
    */
   public function escape_string($string){
       $result=$string;
       if(!get_magic_quotes_gpc())
           $result=  stripslashes ($result);
        
       $result=  addslashes($result);
       $result=  $this->database->real_escape_string($result);
       return $result;
   }
   
   
}

?>
