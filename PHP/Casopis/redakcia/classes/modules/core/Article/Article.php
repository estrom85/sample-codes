<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Article
 *
 * @author mato
 */
abstract class Article extends Module {
    /*
     * Parametre triedy
     */
    protected $article;
    protected $users;
    protected $accessRights;
    protected $path;
    
    public function __construct() {
        
        if(empty($_GET['article_id'])&&empty($_POST['article_id'])){
            
            $this->disable();
            return;
        }
        
        
        $this->initialize();
        
        if(empty($this->article)){
            $this->disable();
            return;
        }
        $this->enable();
        if(!$this->article['zobrazit']){
            if($this->accessRights->approved('ADD')){
                $this->setFunction("edit", "edit_article");
                $this->setForm("edit", "Zmeň názov článku", "edit_name", "edit_article_form");
            
                $this->setFunction("change_topic", "change_topic");
                $this->setForm("change_topic", "Zmeň rubriku", "change_topic", "change_topic_form");
                }    
            
            }
            if($this->accessRights->approved('RELEASE'))
                $this->setFunction("release", "release_article");
            if($this->accessRights->approved('ASSIGN')){
                $this->setFunction("add_user", "add_user");
                $this->setForm("add_user", "Pridaj užívateľa", "add_user", "add_user_form");
                
                $this->setFunction("rem_user", "remove_user");
                $this->setForm("rem_user", "Odstráň užívateľa", "rem_user", "remove_user_form");
            }
            $this->path=ProgramManager::getHomeDir()."/../articles/".$this->article['id'];
    }
    /*
     * Implementovane metody
     */
    public function display(){
        echo "<script type='text/javascript'>scriptloader.load_script('redakcia/styles/articles.css','css')</script>";
        echo "<div id='article_info' style='text-align:left'>";
        echo "<span class='art_desc'>Názov článku:</span> <span class='art_desc_name art_val'>".$this->article['nazov']."</span><br/>";
        echo "<span class='art_desc'>Rubrika:</span> <span class='art_topic art_val'>".$this->article['rubrika']."</span><br/>";
        echo "<span class='art_desc'>Typ článku:</span> <span class='art_type art_val'>".$this->article['typ']."</span><br/>";
        echo "<span class='art_desc'>Dátum poslednej úpravy:</span> <span class='art_time art_val'>".  date("d.n.Y H:i:s",  $this->article['cas'])."</span><br/>";
        if($this->users){
            echo "<span class='art_desc'>Zodpovedný redakčný tím: </span><br/>";
            echo "<div class='art_users art_val'>";
            foreach($this->users as $user){
                echo "<div class='art_usr'>";
                echo $user['id'].". ".$user['name']." ".$user['surname'];
                if($user['class'])
                    echo " (".$user['class'].")";
                echo "</div>";
            }
            echo "</div>";
        }
        else
            echo "<span class='art_msg'>K článku zatiaľ neboli priradení žiadni redaktori.</span><br/>";
        
        if($this->article['zobrazit'])
            echo "<span class='art_msg'>Článok je zverejnený, nie sú možné úpravy.</span>";
        else
            echo "<span class='art_msg'>Článok je nezverejnený.</span>";
        
        echo "</div><br/>";
         
    }
    public function toolbox(){
        
    }
    /*
     * Verejne rozhranie triedy
     */
    protected function edit_article(){
        if(empty($_POST['name'])){
            $this->setMsg(false, "Zadajte názov článku.");
            return;
        }
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Clanok");
        $data->setField("nazov_clanku", $_POST['name'],true);
        $data->setRecord("clanok_id", $this->article['id']);
        if(!$data->queryDB("update")){
            $this->setMsg(false, "Nepodarilo sa zmeniť názov článku.");
            return;
        }
        $this->setMsg(true, "Názov článku zmenený.");
            return;
    }
    
    protected function edit_article_form(){
        $form_id=  $this->getFormID("edit");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Zadajte názov článku");
        $form->addInputField("text", "name", "required nazov");
        $form->addInputField("hidden", "article_id");
        $form->setValue("article_id", $this->article['id']);
        $form->registerForm();
        $form->showForm();
    }
    
    protected function release_article(){
        if(!isset($_POST['release'])){
            $this->setMsg(false, "Nesprávne parametre funkcie");
            return;
        }
        if($_POST['release']&&!$this->accessRights->approved('RELEASE')){
            $this->setMsg(false, "Nemáte oprávnenie na vykonanie tejto akcie");
            return;
        }
        if(!$_POST['release']&&!$this->accessRights->approved('DROP')){
            $this->setMsg(false, "Nemáte oprávnenie na vykonanie tejto akcie");
            return;
        }
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Clanok");
        $data->setField("zobrazit", $_POST['release'],true);
        $data->setRecord("clanok_id", $this->article['id']);
        if(!$data->queryDB("update")){
            $this->setMsg(false, "Nepodarilo sa zverejniť/stiahnuť článok.");
            return;
        }
        if($_POST['release'])
            $this->setMsg (true, "Článok zverejnený");
        else
            $this->setMsg (true, "Článok stiahnutý");
    }
    
    protected function change_topic(){
        if(empty($_POST['topic'])){
            $this->setMsg(false, "Zadajte rubriku.");
            return;
        }
        $topic_data=new DBQuery(CDatabaza::getInstance());
        $topic_data->setTable("Rubrika");
        $topic_data->setRecord("rubrika_id", $_POST['topic']);
        $topic = $topic_data->queryDB("select");
        if(!$topic){
            $this->setMsg(false, "Nastala neočakávaná chyba.");
            return;
        }
        else if(!$topic->num_rows){  
            $this->setMsg(false, "Zadaná rubrika neexistuje.");
            return;
        }
        
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Clanok");
        $data->setField("rubrika_id", $_POST['topic'],true);
        $data->setRecord("clanok_id", $this->article['id']);
        if(!$data->queryDB("update")){
            $this->setMsg(false, "Nepodarilo sa zmeniť rubriku.");
            return;
        }
        $this->setMsg(true, "Rubrika zmenená.");
            return;
    }
    
    protected function change_topic_form(){
        $form_id=  $this->getFormID("change_topic");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Zadajte rubriku");
        $form->addSelect("topic");
        $form->addOption("topic", "topic_0", "--Zadaj rubriku--", "");
        $topic_data=new DBQuery(CDatabaza::getInstance());
        $topic_data->setTable("Rubrika");
        $topics = $topic_data->queryDB("select");
        while($topic=$topics->fetch_array()){
            $form->addOption("topic", "topic_".$topic['rubrika_id'], $topic['nazov_rubriky'], $topic['rubrika_id']);
        }
        
        $form->addInputField("hidden", "article_id");
        $form->setValue("article_id", $this->article['id']);
        
        $form->registerForm();
        $form->showForm();
    }
   
    protected function add_user(){
        if(empty($_POST['user_id'])){
            $this->setMsg(false, "Zadajte užívateľa");
            return;
        }
        if(!$this->accessRights->approved('ASSIGN')){
            $this->setMsg(false, "Nemáte oprávenie na vykonanie tejto operácie");
            return;
        }
        $data=  CDatabaza::getInstance();
        $connected=$data->connected();
        if(!$connected)
            $data->connect();
        
        $user=$data->escape_string($_POST['user_id']);
        $sql="SELECT * FROM Clanok_uzivatel WHERE uzivatel_id=$user AND clanok_id=".$this->article['id'];
        if($data->query($sql)->num_rows){
            $this->setMsg(false, "Užívateľ je už pridaný");
            if($connected)
                $data->close();
            return;
        }
        $info=new DBQuery($data);
        $info->setTable("Clanok_uzivatel");
        $info->setField('uzivatel_id',$user);
        $info->setField('clanok_id',  $this->article['id']);
        if(!$info->queryDB("insert"))
            $this->setMsg(false, "Nepodarilo sa pridať užívateľa.");
        else
            $this->setMsg(true, "Užívateľ pridaný");
        
        
        
        if(!$connected)
            $data->close();
    }
    
    protected function add_user_form(){
        $form_id=  $this->getFormID("add_user");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Vyber užívateľa");
        $data=  CDatabaza::getInstance();
        $connected=$data->connected();
        if(!$connected)
            $data->connect();
        $sql="SELECT 
            Uzivatel_info.uzivatel_id AS id,
            Uzivatel_info.meno AS name,
            Uzivatel_info.priezvisko AS surname,
            Uzivatel_info.trieda AS class
            FROM 
            Uzivatel_info 
            LEFT JOIN
            (SELECT uzivatel_id AS id, clanok_id as clanok FROM Clanok_uzivatel WHERE clanok_id=".$this->article['id'].")
                AS clanky
            ON
            Uzivatel_info.uzivatel_id=clanky.id
            WHERE clanky.clanok IS NULL";        
        
        $query=$data->query($sql);
        $form->addSelect("user_id");
        $form->addOption("user_id","user_00","--Vyber užívateľa","");
        while($user=$query->fetch_array()){
            //print_r($user);
            $form->addOption("user_id", "user_".$user['id'], $user['name']." ".$user['surname']."(".$user['class'].")", $user['id']);
        }
        $form->registerForm();
        $form->showForm();
        if(!$connected)
            $data->close();
    }
    
    protected function remove_user(){
        if(empty($_POST['user_id'])){
            $this->setMsg(false, "Zadajte užívateľa");
            return;
        }
        if($_POST['user_id']==$_SESSION['user']){
            $this->setMsg(false, "Neoprávnený pokus o vymazanie");
            return;
        }
        if(!$this->accessRights->approved('ASSIGN')){
            $this->setMsg(false, "Nemáte oprávenie na vykonanie tejto operácie");
            return;
        }
        $data=  CDatabaza::getInstance();
        $connected=$data->connected();
        if(!$connected)
            $data->connect();
        $user=$data->escape_string($_POST['user_id']);
        $sql="DELETE FROM Clanok_uzivatel WHERE uzivatel_id=$user AND clanok_id=".$this->article['id'];
        if(!$data->query($sql))
            $this->setMsg(false, "Nepodarilo sa vymazať užívateľa");
        else
            $this->setMsg(true, "Užívateľ vymazaný");
        
        if(!$connected)
            $data->close();
    }
    
    protected function remove_user_form(){
        $form_id=  $this->getFormID("rem_user");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Vyber užívateľa");
        $data=  CDatabaza::getInstance();
        $connected=$data->connected();
        if(!$connected)
            $data->connect();
        $sql="SELECT * FROM 
            Uzivatel_info 
            INNER JOIN
            (SELECT * FROM Clanok_uzivatel WHERE clanok_id=".$this->article['id'].")
                AS clanky
            ON
            Uzivatel_info.uzivatel_id=clanky.uzivatel_id
            WHERE Uzivatel_info.uzivatel_id<>".$_SESSION['user'];
        $query=$data->query($sql);
        
        $form->addSelect("user_id");
        $form->addOption("user_id","user_00","--Vyber užívateľa","");
        while($user=$query->fetch_array()){
            $form->addOption("user_id", "user_".$user['uzivatel_id'], $user['meno']." ".$user['priezvisko']."(".$user['trieda'].")", $user['uzivatel_id']);
        }
        $form->registerForm();
        $form->showForm();
        if(!$connected)
            $data->close();
    }
    
    protected function setTimeStamp(){
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Clanok");
        $data->setField("casova_znamka", time());
        $data->setRecord("clanok_id", $this->article['id']);
        if(!$data->queryDB("update")){
            return false;
        }
        return true;
    }
    
    protected function displayActionButton($action,$label,$params='',$refresh=true,$confirm=false,$confirm_label=''){
        $parametres="article_id:".$this->article['id'];
        if(strcmp($params,''))
                $parametres.=", ".$params;
        $program_id=  $this->getProgramID();
        $func="";
        if($confirm){
            $func.="potvrdASpustiProgram($program_id,'$action',{".$parametres."},'$confirm_label');";
        }
        else{
            $func.="spustiProgram($program_id,'$action',{".$parametres."});";
        }
        
        if($refresh){
            $func.="nastavProgram({id:".$program_id.",article_id:".$this->article['id']."});";
        }
        echo "<li onclick=\"".$func."\">$label</li>";
    }
    
    protected function displayFormButton($action,$title){
        $form_src_base="./redakcia/request/form.php?id=".$this->getProgramID()."&article_id=".$this->article['id'];
        $action_base="./redakcia/request/action.php?id=".$this->getProgramID()."&article_id=".$this->article['id'];
        $func="&func=$action";
        $src=$form_src_base.$func;
        $form_id=  $this->getFormID($action);
        $action=$action_base.$func;
        $function_desc="nastavFormular('$src','$title','$form_id','$action')";
        echo "<li onclick=\"".$function_desc."\">$title</li>";
    }
    
    protected function displayBackButton(){
        $func="nastavProgram({id:".ProgramManager::getId("Article_list")."});";
        echo "<li onclick=\"".$func."\">Späť na články</li>";
    }
    /*
     * Sukromne metody triedy
     */
    private function initialize(){
 
        $data=  CDatabaza::getInstance();
        $connected=$data->connected();
        if(!$connected)
            $data->connect();
        $article_id="";
        if(!empty($_GET['article_id'])){
            $article_id=$data->escape_string($_GET['article_id']);
        }
        else{
            $article_id=$data->escape_string($_POST['article_id']);
        }
        
        $this->accessRights=new UserRights($data);
        $sql="";
        if($this->accessRights->approved("EDIT_ALL")){
            $sql="SELECT * FROM 
                (
                Clanok 
                INNER JOIN 
                Typ_clanku 
                ON Clanok.typ_clanku_id=Typ_clanku.typ_clanku_id
                )
                INNER JOIN
                Rubrika
                ON
                Rubrika.rubrika_id=Clanok.rubrika_id
                WHERE Clanok.clanok_id=$article_id";
        }
        else{
            $sql="SELECT * FROM 
                ((
                Clanok 
                INNER JOIN 
                Typ_clanku 
                ON Clanok.typ_clanku_id=Typ_clanku.typ_clanku_id
                )
                INNER JOIN
                Rubrika
                ON
                Rubrika.rubrika_id=Clanok.rubrika_id
                )
                INNER JOIN 
                Clanok_uzivatel 
                ON Clanok.clanok_id=Clanok_uzivatel.clanok_id
                WHERE Clanok_uzivatel.uzivatel_id=".$_SESSION['user']."
                    AND Clanok.clanok_id=$article_id";

        }
        
        $article=$data->query($sql);
        if(!empty($article->num_rows)){
        
            $art_data=$article->fetch_array();
       
            $this->article['id']=$art_data['clanok_id'];
            $this->article['nazov']=$art_data['nazov_clanku'];
            $this->article['typ']=$art_data['nazov'];
            $this->article['rubrika']=$art_data['nazov_rubriky'];
            $this->article['zobrazit']=$art_data['zobrazit'];
            $this->article['cas']=$art_data['casova_znamka'];
            
            $sql="SELECT * FROM Clanok_uzivatel 
                    INNER JOIN Uzivatel_info
                    ON Clanok_uzivatel.uzivatel_id=Uzivatel_info.uzivatel_id
                    WHERE Clanok_uzivatel.clanok_id=".$this->article['id'];
            $users=$data->query($sql);
            if($users){
                while($user=$users->fetch_array()){
                    $id=$user['uzivatel_id'];
                    $this->users[$id]['id']=$id;
                    $this->users[$id]['name']=$user['meno'];
                    $this->users[$id]['surname']=$user['priezvisko'];
                    $this->users[$id]['class']=$user['trieda'];
                }
            }
            
        
        }  
        
        if(!$connected)
            $data->close();
    }
    
    
}

?>
