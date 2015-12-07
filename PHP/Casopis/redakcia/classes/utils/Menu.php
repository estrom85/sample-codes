<?php


/**
 * Trieda predstavujuca hlavne menu programu
 *
 * @author mato
 */
class Menu {
    /*
     * Parametre triedy
     */
    private $menuItem;
    private $end=0;
    
    private $displayed=false;
    /*
     * Konstruktor
     */
    public function __construct() {
        if(!isset($_SESSION['user']))
            return;
        $user=$_SESSION['user'];
        $hasInfo=true;
        //ziska informacie z databazy
        $data=  CDatabaza::getInstance();
        $data->connect();
        $rights=new UserRights($data); //ziska uzivatelske prava
        if(mysqli_num_rows($data->query("SELECT * FROM Uzivatel_info WHERE uzivatel_id=$user"))==0)
            $hasInfo=false;
            
        $data->close();
        //prida polia hlavneho menu na zaklade uzivatelskych prav
        $this->addItem("Domov", ProgramManager::getId("Intro"));
        if($hasInfo)
            $this->addItem("Môj profil",  ProgramManager::getId("User_info"));
        if($rights->approved('EDIT_USERS'))
            $this->addItem("Užívatelia", ProgramManager::getId("Users"));
        if($rights->approved('EDIT_ENUMS')){
            $this->addItem("Rubriky",  ProgramManager::getId("Topics"));
        }
        $this->addItem("Články",  ProgramManager::getId("Article_list"));
        //$this->addItem("Príspevky", 0);
        //$this->addItem("Nastavenia", 0);
        //$this->addItem("Odhlásiť","?id=".ProgramManager::getId("Login")."&func=logout",0);
        
        $this->displayed=true;
        
    }
    //prida pole do menu
    private function addItem($item,$id){
        $this->menuItem[$this->end]['item']=$item;
        $this->menuItem[$this->end]['id']=$id;
        $this->end++;
    }
    //zobrazi menu
    public function display(){
        if(!isset($_SESSION['user']))
            return;
        echo "<ul id='menu_items' class='menu_items'>";
        for($i=0;$i<$this->end;$i++){
            echo "<li onclick='nastavProgram({id:".$this->menuItem[$i]['id']."})'>";
            echo $this->menuItem[$i]['item'];
            echo "</li>"; 
        }
        echo "<li onclick=\"window.location.replace('./redakcia/login.php?func=logout');\">Odhlásiť</li>";
        echo "</ul>";
    }
    //vrati indikator zobrazenia menu
    public function isDisplayed(){
        return $this->displayed;
    }
}

?>
