<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User_info
 *
 * @author mato
 */
class User_info extends Module {
    /*
     * Parametre triedy
     */
    private $user_info;
    
    /*
     * Konstruktor
     */
    public function __construct() {
        
        $this->enable();
        
        $this->initialize();
        
        $this->setFunction("change_pswd", "change_password");
        $this->setForm("change_pswd", "Zmeň heslo", "change_pswd", "change_password_form");
    }
    /*
     * Implementovane funkcie
     */
    protected function getProgramID() {
        return ProgramManager::getId("User_info");
    }
    public function display() {
        if(empty($this->user_info)){
            echo "Neexistujú údaje o užívateľovi. Kontaktujte prosím administrátora.";
            return;
        }
        echo "<strong>Meno: </strong>".$this->user_info['name']."<br/>";
        echo "<strong>Priezvisko: </strong>".$this->user_info['surname']."<br/>";
        echo "<strong>Trieda: </strong>".$this->user_info['class']."<br/>";
    }
    /*
     * Zakladne rozhranie triedy
     */
    protected function change_password(){
        if(strcmp($_POST['user_id'],$_SESSION['user'])){
            $this->setMsg(false, "Nemáte oprávenie meniť heslo (".$_POST['user_id'].",".$_SESSION['user'].").");
            return;
        }
        if(empty($_POST['old_psswd'])){
            $this->setMsg(false, "Zadajte staré heslo.");
            return;
        }
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Uzivatel");
        $data->setRecord("uzivatel_id", $_SESSION['user']);
        
        $auth=$data->queryDB("select");
        if(!$auth){
            $this->setMsg(false, "Nepodarilo sa pripojiť na databazu.");
            return;
        }
        if(!$auth->num_rows){
            $this->setMsg(false, "Užívateľ neexistuje.");
            return;
        }
        $auth=$auth->fetch_array();
        if(strcmp($auth['heslo'],md5($_POST['old_psswd']))){
            $this->setMsg(false, "Nesprávne heslo.");
            return;
        }
        
        if(empty($_POST['new_psswd'])){
            $this->setMsg(false, "Zadajte nové heslo.");
            return;
        }
        if(strcmp($_POST['new_psswd'],$_POST['confirm_psswd'])){
            $this->setMsg(false, "Heslá nie sú totožné.");
            return;
        }

        $data->setField("heslo", $_POST['new_psswd'], true, true);
        if(!$data->queryDB("update")){
            $this->setMsg(false, "Nepodarilo sa zmeniť heslo.");
            return;
        }
        
        $this->setMsg(true, "Heslo úspešne zmenené.");
    }
    
    protected function change_password_form(){
        $form_id=  $this->getFormID("change_pswd");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addInputField("hidden", "user_id");
        $form->setValue("user_id","'".$_SESSION['user']."'");
        $form->addLabel("Zadaj staré heslo");
        $form->addInputField("password", "old_psswd","required");
        $form->addLabel("Zadaj nové heslo");
        $form->addInputField("password", "new_psswd","required psswd");
        $form->addLabel("Potvrď heslo");
        $form->addInputField("password", "confirm_psswd", "required psswd equals{new_psswd}");
        $form->registerForm();
        $form->showForm();
    }
    /*
     * Privatne funkcie triedy
     */
    private function initialize(){
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Uzivatel_info");            
        $data->setRecord("uzivatel_id", $_SESSION['user']);
        
        $query=$data->queryDB("select");
        
        if($query){
            $usr_nfo=$query->fetch_array();
            $this->user_info['name']=$usr_nfo['meno'];
            $this->user_info['surname']=$usr_nfo['priezvisko'];
            $this->user_info['class']=$usr_nfo['trieda'];
            
        }
        
        
    }
}

?>
