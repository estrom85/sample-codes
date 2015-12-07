<?php

/**
 * DBQuery - trieda sluziaca na zjednodusenie pristupu na databazu. Obsahuje rutinne
 * operacie s databazou. Vytvara jednoduche SQL prikazu pomocou jednoduchej sekvecie
 * volani funcii.
 *
 * @author mato
 */
class DBQuery {
    /*
     * Parametre triedy
     */
    private $db;        //odkaz na databazu
    private $connected; //indikator pripojenia
    private $table;     //vybrana tabulka
    private $items;     //vybrane polia tabulky a ich hodnoty
    private $record;    //vybrane zaznamy s tabulky
    private $orderby;   //pole na zaklade ktoreho sa usporiadaju vybrane zaznamy
    
    /*
     * Konstruktor
     */
    public function __construct(CDatabaza $db) {
        $this->db=$db;
        $this->connected=false;
        
        if(!$this->db->connected()){
             $this->db->connect ();
            if($this->db->connected())
                $this->connected=true;
        }   
    }
    /*
     * Destruktor
     */
    public function __destruct() {
       if($this->connected)
           $this->db->close ();
    }
    
    /*
     * Zakladne rozhranie triedy
     */
    
    //nastavi pole, na zaklade ktoreho sa budu zaznamy zoradovat.
    public  function setOrder($key, $order=""){
        $this->orderby['key']=$key;
        unset($this->orderby['desc']);
        if(strcmp($order, 'DESC'))
            $this->orderby['desc']='DESC'; 
    }
    //nastavi tabulku
    public function setTable($table){
        //zisti ci tabulka existuje
        if(!mysqli_num_rows($this->db->query("SHOW TABLES LIKE '$table'")))
            return false;
        $this->table=$table;
        return true;
    }
    //nastavi pole tabulky
    public function setField($field, $value, $protect=false,$hash=false){
        //zisti, ci je nastavena tabulka
        if(empty($this->table))
            return;
        //zisti, ci existuje pole
        if(!mysqli_num_rows($this->db->query("SHOW COLUMNS FROM $this->table LIKE '$field'")))
            return;
        //ochrani hodnotu pred nebezpecnymi hodnotami
        if($protect)
            $value=  $this->protectValue($value);
        //vytvori hash hodnoty
        if($hash)
            $value=  md5 ($value);
        $this->items[$field]['column']=$field;
        $this->items[$field]['value']=$value;
    }
    //nastavi zaznam z tabulky
    public function setRecord($key, $value){
        if(empty($this->table))
            return false;
        if(!mysqli_num_rows($this->db->query("SHOW COLUMNS FROM $this->table LIKE '$key'")))
            return false;
        $this->protectValue($value);
        $this->record['key']=$key;
        $this->record['value']=$value;
        return true;
    }
    //posle poziadavku na databazu
    public function queryDB($action){
        $sql="";
        $insert=false;
        $select=false;
        //ziska SQL prikaz na zaklade hodnoty $action
        if(!strcmp($action, "insert")){
            $sql=  $this->getInsertStatement();
            $insert=true;
        }
        else if(!strcmp($action, "delete"))
                $sql=  $this->getDeleteStatement();
        else if(!strcmp($action, "update"))
                $sql=  $this->getUpdateStatement ();
        else if(!strcmp($action, "select")){
            $sql=  $this->getSelectStatement();
            $select=true;
        }
        //vrati hodnotu na zaklade typu akcie
        if(!strcmp($sql,""))
                return false;
        $result=  $this->db->query($sql);
        if(!$result)
            return false;
        if($select)
            return $result;
        if($insert){
            if($this->db->last_id ())
                return $this->db->last_id ();
            return $result;
        }
        return true;
    }
    //zobrazi zaznamy z vybranej tabulky
    public function showTable($full=true,$where=""){
        $sql=  $this->getSelectStatement(true,$full);
        if(strcmp($where,"")){
            $sql=$sql." WHERE $where";
        }
        
        //$result=new mysqli_result();
        $result=  $this->db->query($sql);
        
        $fields=  $result->fetch_fields();
        
        echo "<table border='2px'>";
        echo "<tr>";
        foreach($fields as $field){
            
            echo "<th>".$field->name."</th>";
        }
       
        while($row=$result->fetch_array(MYSQLI_NUM)){
            echo"<tr>";
            foreach($row as $item){
                echo "<td>$item</td>";
            }
            echo"</tr>";
        }
        echo "</table>";
    }
    
    /*
     * Sukromne metody triedy
     */
    
    //ochrani vstupnu hodnotu
    private function protectValue($value){
        $result=$value;
        
        $result=  $this->db->escape_string($result);
        
        return $result;
    }
    //vytvori prikaz SELECT
    private function getSelectStatement($ignoreWhere=false,$selectAll=false){
        //zisti, ci je vybrana tabulka
        if(empty($this->table))
            return "";
        //vyberie polia z tabulky
        $fields="";
        if(empty($this->items)||$selectAll)
            $fields="*";
        else{
            foreach($this->items as $item){
                $fields=$fields.", '".$item['column']."'";
            }
            $fields=  substr($fields, 2); 
        }
        //vytvori SELECT prikaz
        $result= "SELECT $fields FROM $this->table";
        //prida WHERE prikaz
        if((!empty($this->record))&&(!$ignoreWhere))
            $result=$result." WHERE ".$this->record['key']."='".$this->record['value']."'";
        //prida ORDER BY prikaz
        if(!empty($this->orderby)){
            $result=$result." ORDER BY ".$this->orderby['key'];
        
            if(!empty($this->orderby['desc'])){
            $result=$result." DESC";
            }
            else{
                $result=$result." ASC";
            }
        }   
        return $result;
    }
    //vytvori INSERT prikaz
    private function getInsertStatement(){
        if(empty($this->table))
            return "";
        if(empty($this->items))
            return "";
        //nastavi jednotlive polia
        $keys="";
        $values="";
        foreach ($this->items as $item){
            $keys=$keys.", ".$item['column'];
            $values=$values.", '".$item['value']."'";
        }
        $keys=  substr($keys, 2);
        $values=  substr($values, 2);
        
        return "INSERT INTO $this->table ($keys) VALUES ($values)";
        
    }
    //vytvori UPDATE prikaz
    private function getUpdateStatement(){
        if(empty($this->table))
            return "";
        if(empty($this->items))
            return "";
        if(empty($this->record))
            return "";
        
        //nastavi jednotlive polia
        $set="";
        foreach($this->items as $item){
            $set=$set.", ".$item['column']."='".$item['value']."'";
        }
        $set=substr($set,2);
        
        return "UPDATE $this->table SET $set WHERE ".$this->record['key']."='".$this->record['value']."'";
    }
    //nastavi DELETE prikaz
    private function getDeleteStatement(){
        if(empty($this->table))
            return "";
        if(empty($this->record))
            return "";
        
        return "DELETE FROM $this->table WHERE ".$this->record['key']."='".$this->record['value']."'";
    }
}

?>
