<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users
 *
 * @author mato
 */
class Users extends Module{
    /*
     * Parametre triedy
     */
    private $users;
    /*
     * Konstruktor
     */
    public function __construct() {
        $rights=new UserRights(CDatabaza::getInstance());
        if(!$rights->approved("EDIT_USERS")){
            $this->disable();
            return;
        }
        $this->enable();
        $this->initialize();
        
        $this->setFunction("add", "add_user");
        $this->setForm("add", "Pridaj užívateľa", "add_user", "add_user_form");
        
        $this->setFunction("edit", "edit_user");
        $this->setForm("edit", "Uprav informácie o užívateľovi", "edit_user", "edit_user_form");
        
        $this->setFunction("remove", "remove_user");
        $this->setForm("remove", "Vymaž užívateľa", "remove_user", "remove_user_form");
        
        $this->setFunction("set_rights", "set_user_rights");
        $this->setForm("set_rights", "Nastav užívateľské práva", "set_rights", "set_user_rights_form");
        
        $this->setFunction("reset", "reset_password");
        $this->setForm("reset", "Resetuj heslo", "remove_user", "remove_user_form");
    }
    /*
     * Implementovane funkcie
     */
    protected function getProgramID() {
        return ProgramManager::getId("Users");
    }
    public function display() {
        echo "<table class='user_table' border='2px'>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Login</th>";
        echo "<th>Meno</th>";
        echo "<th>Priezvisko</th>";
        echo "<th>Trieda</th>";
        echo "<th>Pristupové práva<sup>*</sup></th>";
        echo "<tr>";
        if(!empty($this->users))
        foreach($this->users as $user){
            echo "<tr>";
            echo "<td>".$user['id']."</td>";
            echo "<td>".$user['login']."</td>";
            echo "<td>".$user['name']."</td>";
            echo "<td>".$user['surname']."</td>";
            echo "<td>".$user['class']."</td>";
            echo "<td>".$user['rights']."</td>";
            /*
            echo "<span onclick=\"spustiProgram(".$this->getProgramID().",'reset',{user_id:'".$user['id']."'})\">
                Resetuj heslo</span>";
             * 
             */
            echo "<tr>";
        }
        echo "</table>";
        echo "<br/><div id='help' style='text-align:left;font-size:12px'><sup>*</sup><strong>Vysvetlivky:</strong>";
        echo "<br/>EDIT_USERS: Umožňuje spravovať užívateľské kontá. (pridávať, odstráňovať, upravovať užívateľské účty).";
        echo "<br/>EDIT_ALL: Umožňuje upravovať všetky články.";
        echo "<br/>ADD: Umožňuje pridávať články.";
        echo "<br/>REMOVE: Umožňuje odstraňovať články.";
        echo "<br/>RELEASE: Umožňuje zverejňovať články.";
        echo "<br/>DROP: Umožňuje zakázať zobrazovanie zverejnených článkov.";
        echo "<br/>ASSIGN: Umožňuje sprístupniť článok aj iným užívateľom (pokiaľ má pridaný užívateľ právo ASSIGN má tiež možnosť pridať ale aj odstrániť užívateľa).";
        echo "<br/>EDIT_ENUMS: Umožňuje upravovať zoznamy (témy, rubriky, kategórie,...)."; 
        echo "</div>";
    }
    
    /*
     * Zakladne rozhranie triedy
     */
    protected function add_user(){
        if(empty($_POST['login'])){
            $this->setMsg(false, "Zadajte užívateľské heslo");
            return;
        }
        
        if(empty($_POST['name'])){
            $this->setMsg(false, "Zadajte meno");
            return;
        }
        if(empty($_POST['surname'])){
            $this->setMsg(false, "Zadajte priezvisko");
            return;
        }
        
        $users_data=new DBQuery(CDatabaza::getInstance());
        $users_data->setTable('Uzivatel');
        $users_data->setField('prihlasovacie_meno', $_POST['login'], true);
        $users_data->setField('heslo', $_POST['login'], true, true);
        $id=$users_data->queryDB('insert');
        if(!$id){
            $this->setMsg(false, "Nepodarilo sa pridať užívateľa");
            return;
        }
        
        $info_data=new DBQuery(CDatabaza::getInstance());
        $info_data->setTable('Uzivatel_info');
        $info_data->setField('uzivatel_id', $id);
        $info_data->setField('meno', $_POST['name'],true);
        $info_data->setField('priezvisko', $_POST['surname'], true);
        if(!empty($_POST['class'])&&strcmp($_POST['class'],"null")){
            $info_data->setField('trieda', $_POST['class'], true);
        }
        if(!$info_data->queryDB('insert')){
            $users_data->setRecord('uzivatel_id', $id);
            $users_data->queryDB('delete');
            $this->setMsg(false, "Nepodarilo sa pridať info užívateľa");
            return;
        }
        $this->setMsg(true, "Užívateľ úspešne pridaný");
        
    }
    
    protected function add_user_form(){
        $form_id=  $this->getFormID("add");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Prihlasovacie meno");
        $form->addInputField("text", "login", "required login");
        $form->addLabel("Meno");
        $form->addInputField("text", "name", "required meno");
        $form->addLabel("Priezvisko");
        $form->addInputField("text", "surname", "required meno");
        $form->addLabel("Trieda");
        $form->addInputField("text", "class", "trieda");
        $form->registerForm();
        $form->showForm();
    }
    
    protected function edit_user(){
        if(empty($_POST['user_id'])){
            $this->setMsg(false, "Zadaj užívateľa");
            return;
        }
        if(empty($this->users[$_POST['user_id']])){
            $this->setMsg(false, "Zadaný užívateľ neexistuje");
            return;
        }
        
        if(strcmp($_POST['login'],  $this->users[$_POST['user_id']]['login'])){
            $this->setMsg(false, "Neoprávnená zmena údajov");
            return;
        }
        
        
        
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Uzivatel_info");
        $data->setRecord("uzivatel_id",$_POST['user_id']);
        if(!empty($_POST['name']))
            $data->setField("meno", $_POST['name'], true);
        
        if(!empty($_POST['surname']))
            $data->setField("priezvisko", $_POST['surname'], true);
        
        if(!empty($_POST['class'])){
            $class=$_POST['class'];
            if(!strcmp($class,"null"))
                    $class="";
            $data->setField ("trieda", $class, true);
        }
        
        if(!$data->queryDB('update')){
            $this->setMsg(false, "Nepodarilo sa upraviť záznam");
        }
        $this->setMsg(true, "Záznam úspešne upravený");
    }
    
    protected function edit_user_form(){
        $form_id=  $this->getFormID("edit");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Vyber užívateľa");
        $form->addInputField("text", "user_id", "required cislo");
        $form->addLabel("Zadaj prihlasovacie meno");
        $form->addInputField("text","login","required login");
        $form->addLabel("Zadaj meno");
        $form->addInputField("text", "name", "name");
        $form->addLabel("Zadaj priezvisko");
        $form->addInputField("text", "surname","name");
        $form->addLabel("Zadaj triedu");
        $form->addInputField("text", "class", "trieda");
        $form->registerForm();
        $form->showForm();
    }
    
    protected function remove_user(){
        if(empty($_POST['user_id'])){
            $this->setMsg(false, "Zadajte užívateľa");
            return;
        }
        if(empty($this->users[$_POST['user_id']])){
            $this->setMsg(false, "Zadaný užívateľ neexistuje");
            return;
        }
        if(empty($_POST['login'])){
            $this->setMsg(false, "Zadajte prihlasovacie meno užívateľa");
            return;
        }
        if(strcmp($_POST['login'],  $this->users[$_POST['user_id']]['login'])){ 
            $this->setMsg(false, "Neoprávnená zmena údajov");
            return;
        }
        
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable('Uzivatel');
        $data->setRecord('uzivatel_id', $_POST['user_id']);
        if(!$data->queryDB('delete')){
            $this->setMsg(false, "Nepodarilo sa zmazať užívateľa");
            return;
        }
        $data->setTable('Uzivatel_info');
        $data->setRecord('uzivatel_id', $_POST['user_id']);
        if(!$data->queryDB('delete')){
            $this->setMsg(false, "Nepodarilo sa zmazať informácie o užívateľovi");
            return;
        }
        $data->setTable('Casopis_uzivatel');
        $data->setRecord('uzivatel_id', $_POST['user_id']);
        if(!$data->queryDB('delete')){
            $this->setMsg(false, "Nepodarilo sa zmazať informácie o užívateľovi");
            return;
        }
        $this->setMsg(true, "Užívateľ úspešne odstránený");
        
    }
    
    protected function remove_user_form(){
        $form_id=  $this->getFormID("remove");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Vyber užívateľa");
        $form->addInputField("text", "user_id", "required cislo");
        $form->addLabel("Zadaj prihlasovacie meno");
        $form->addInputField("text","login","required login");
        $form->registerForm();
        $form->showForm();
    }
    
    protected function set_user_rights(){
        if(empty($_POST['user_id'])){
            $this->setMsg(false, "Zadajte užívateľa");
            return;
        }
        if(empty($this->users[$_POST['user_id']])){
            $this->setMsg(false, "Zadaný užívateľ neexistuje");
            return;
        }
        if(empty($_POST['login'])){
            $this->setMsg(false, "Zadajte prihlasovacie meno užívateľa");
            return;
        }
        if(strcmp($_POST['login'],  $this->users[$_POST['user_id']]['login'])){ 
            $this->setMsg(false, "Neoprávnená zmena údajov");
            return;
        }
        
        $rights="";
        if(!empty($_POST['rights'])){
            foreach($_POST['rights'] as $right)
                $rights.=",".$right;
            $rights=  substr($rights, 1);
        }
        
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Uzivatel");
        $data->setRecord("uzivatel_id", $_POST['user_id']);
        $data->setField("pristupove_prava", $rights,true);
        if(!$data->queryDB('update')){
            $this->setMsg(false, "Nepodarilo sa upraviť prístupové práva");
            return;
        }
        
        $this->setMsg(true, "Prístupové práva upravené");
 
    }
    
    protected function set_user_rights_form(){
        $form_id=  $this->getFormID("set_rights");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Vyber užívateľa");
        $form->addInputField("text", "user_id", "required cislo");
        $form->addLabel("Zadaj prihlasovacie meno");
        $form->addInputField("text","login","required login");
        $form->addLabel("Práva");
        $form->addInputField("checkbox", "rights");
        $form->addOption("rights", "right01", "Spravovanie užívateľov", "EDIT_USERS");
        $form->addOption("rights", "right02", "Úprava článkov", "EDIT_ALL");
        $form->addOption("rights", "right03", "Pridanie článkov", "ADD");
        $form->addOption("rights", "right04", "Odstránenie článkov", "REMOVE");
        $form->addOption("rights", "right05", "Zverenenie článku", "RELEASE");
        $form->addOption("rights", "right06", "Stiahnutie článku", "DROP");
        $form->addOption("rights", "right07", "Priradenie užívateľa", "ASSIGN");
        $form->addOption("rights", "right08", "Úprava zoznamov", "EDIT_ENUMS");
        //,,'','','','','EDIT_ENUMS'
        $form->registerForm();
        $form->showForm();
    }
    
    protected function reset_password(){
        if(empty($_POST['user_id'])){
            $this->setMsg(false, "Zadajte užívateľa");
            return;
        }
        if(empty($this->users[$_POST['user_id']])){
            $this->setMsg(false, "Zadaný užívateľ neexistuje");
            return;
        }
        if(empty($_POST['login'])){
            $this->setMsg(false, "Zadajte prihlasovacie meno užívateľa");
            return;
        }
        if(strcmp($_POST['login'],  $this->users[$_POST['user_id']]['login'])){ 
            $this->setMsg(false, "Neoprávnená zmena údajov");
            return;
        }
        
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable('Uzivatel');
        $data->setRecord("uzivatel_id", $_POST['user_id'],true);
        $data->setField("heslo", $_POST['login'],true,true);
        if(!$data->queryDB('update')){
            $this->setMsg(false, "Nepodarilo resetovať heslo.");
            return;
        }
        
        $this->setMsg(true, "Heslo úspešne zresetované.");
        
    }


    /*
     * Privatne funkcie triedy
     */
    
    private function initialize(){
        $data=  CDatabaza::getInstance();
        $connected=$data->connected();
        if(!$connected)
            $data->connect();
        
        $sql="SELECT * FROM Uzivatel INNER JOIN Uzivatel_info ON Uzivatel.uzivatel_id=Uzivatel_info.uzivatel_id
            WHERE Uzivatel.uzivatel_id<>".$_SESSION['user'];
        $query=$data->query($sql);
        if($query){
            while($user=$query->fetch_array()){
                $id=$user['uzivatel_id'];
                $this->users[$id]['id']=$id;
                $this->users[$id]['login']=$user['prihlasovacie_meno'];
                $this->users[$id]['name']=$user['meno'];
                $this->users[$id]['surname']=$user['priezvisko'];
                $this->users[$id]['class']=$user['trieda'];
                $this->users[$id]['rights']=$user['pristupove_prava'];
            }
        }
        
        
        if(!$connected)
            $data->close();
    }
}

?>
