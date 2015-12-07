<?php


/**
 * Trieda na zjednodusenie tvorby formularov
 *
 * @author mato
 */
class FormMaker {
    /*
     * Parametre
     */
    private $class;
    private $id;
    private $method;
    private $action;
    private $name;
    private $nonce;
    
    private $items;
    private $labelID=0;
    
    private $hasOptions;
    /*
     * Konstruktor
     */
    public function __construct($name="", $method="post", $action="./") {
        if(strcmp($method,"post")&&strcmp($method, "get"))
                return;
        
        $this->name=$name;
            
        $this->method=$method;
        $this->action=$action;
        
        $this->addFieldType("text",false);
        $this->addFieldType("password",false);
        $this->addFieldType("checkbox",true);
        //$this->addFieldType("file", false);
        $this->addFieldType("hidden", false);
        $this->addFieldType("radio", true);
        
        
    }
    
    /*
     * Verejne rozhranie triedy
     */
    
    //nastavi class atribut formulara
    public function setClass($class){
        $this->class=$class;
    }
    //nastavi id atribut formulara
    public function setID($id){
        $this->id=$id;
    }
    //prida popis pola formulara
    public function addLabel($text){
        $name="label".$this->labelID;
        $this->labelID++;
        $this->items[$name]['type']="label";
        $this->items[$name]['text']=$text;
    }
    //prida input pole formulara
    public function addInputField($type, $name, $validate=""){
        if(!empty($this->items[$name]))
            return;
        
        $this->items[$name]['name']=$name;
        $this->items[$name]['type']=$type;
        if((strcmp($validate,"")&&  !strcmp($type, "text"))||(strcmp($validate,"")&&  !strcmp($type, "password")))
            $this->items[$name]['validate']=$validate;
    }
    //nastavi hodnotu daneho pola
    public function setValue($name, $value){
        if(empty($this->items[$name]))
            return;
        $this->items[$name]['value']=$value;
    }
    //prida textove pole
    public function addTextArea($name,$rows=5,$cols=25){
        if(!empty($this->items[$name]))
            return;
        $this->items[$name]['type']="textarea";
        $this->items[$name]['name']=$name;
        $this->items[$name]['rows']=$rows;
        $this->items[$name]['cols']=$cols;
    }
    //prida vyberovy zoznam
    public function addSelect($name, $multiple=false, $size=1){
        if(!empty($this->items[$name]))
            return;
        $this->items[$name]['type']="select";
        $this->items[$name]['name']=$name;
        $this->items[$name]['multiple']=$multiple;
        $this->items[$name]['size']=$size;
    }
    //prida moznost do vyberoveho pola
    public function addOption($name, $id, $label, $value){
        if(empty($this->items[$name]))
            return;
        if(!empty($this->items[$name]['options'][$id]))
            return;
        $this->items[$name]['options'][$id]['id']=$id;
        $this->items[$name]['options'][$id]['label']=$label;
        $this->items[$name]['options'][$id]['value']=$value;
    }
    //zobrazi formular
    public function showForm(){
        if(empty($this->items))
            return;
        //nastavi triedu ak existuje
        $class="";
        if(strcmp($this->class, ""))
                $class="class='$this->class'";
        //nastavi id existuje
        $id="";
        if(strcmp($this->id,""))
                $id="id='$this->id'";
        //kontajner formulara
        echo "<form $id method='$this->method' action='$this->action' $class>";
        //vlozi identifikator formulara ak existuje
        if(!empty($this->nonce)){
            echo "<input type='hidden' name='nonce' value='$this->nonce'/>";
        }
        //zobrazi polia formulara
        foreach($this->items as $item){
            echo "<span class='form-element'>";
            //zobrazi input pole
            if(isset($this->hasOptions[$item['type']]))
                $this->showInputField($item['name']);
            //zobrazi select pole
            else if(!strcmp($item['type'],"select"))
                $this->showSelectField ($item['name']);
            //zobrazi textarea 
            else if(!strcmp($item['type'],"textarea")){
                echo "<textarea name='".$item['name']."' rows='".$item['rows']."' cols='".$item['cols']."'>";
                if(!empty($item['value']))
                    echo $item['value'];
                echo "</textarea></br>";
            }
            //zobrazi popis pola
            else if(!strcmp($item['type'],"label"))
                    echo "<b>".$item['text'].":</b><br/>";
            echo "</span>";        
        }
        
        echo "</form>";
    }
    //registruje formular a ziska identifikacne cislo formulara (zabrani opatovnemu odoslaniu formulara)
    public function registerForm(){
        $this->nonce=Nonce::getNonce();
    }
    
    /*
     * Sukromne metody triedy
     */
    //ak typ pola obsahuje moznosti nastavi hodnotu na true
    private function addFieldType($type,$hasOptions){
        $this->hasOptions[$type]=$hasOptions;
    }
    //zobrazi input pole
    private function showInputField($key){
        
        $type=  $this->items[$key]['type'];
        $name= $this->items[$key]['name'];
        $value="";
        $validate="";
        //ziska pociatocnu hodnotu pola
        if(!empty($this->items[$key]['value']))
            $value="value=".$this->items[$key]['value'];
        //ziska indikatory pre validaciu pola
        if(!empty($this->items[$key]['validate']))
            $validate="class='".$this->items[$key]['validate']."'";
        //kontroluje, ci dany prvok ma viacero moznosti
        if($this->hasOptions[$type]){
            if(empty($this->items[$key]['options']))
                return;
            if(!strcmp($type,'checkbox'))
                        $name=$name."[]";
            foreach($this->items[$key]['options'] as $option){
                $id=$option['id'];
                $label=$option['label'];
                $val=$option['value'];
                
                echo "<input type='$type' name='$name' id='$id' value='$val' />";
                echo "<label for='$id'>$label</label><br/>";
            }
        }
        else
            echo "<input type='$type' id='$name' name='$name' $value $validate/>";
    }
    //zobrazi pole select
    private function showSelectField($key){
        $name=  $this->items[$key]['name'];
        $size=  $this->items[$key]['size'];
        $multiple="";
        //zisti, ci je mozne vybrat viacero moznosti
        if($this->items[$key]['multiple']){
            $multiple="multiple='multiple'";
            $name=$name."[]";
        }
        if(empty ($this->items[$key]['options']))
            return;
        echo "<select name='$name' size='$size' $multiple>";
        foreach($this->items[$key]['options'] as $option){
            $label=$option['label'];
            $value=$option['value'];
            echo "<option value='$value'>$label</option>";
        }
        echo "</select>";
    }
    
    
}

?>
