<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Article_anekdote
 *
 * @author mato
 */
ProgramManager::includeProgram("Article");
class Article_post extends Article {
    /*
     * Parametre funkcie
     */
    private $posts;
    private $categories;
    private $post_info;
    /*
     * Konstruktor
     */
    public function __construct() {
        parent::__construct();
        $this->setFunction("add_cat", "add_cathegory");
        $this->setForm("add_cat", "", "add_cat", "add_cathegory_form");
        
        $this->setFunction("edit_cat", "edit_cathegory");
        $this->setForm("edit_cat", "", "edit_cat", "edit_cathegory_form");
        
        $this->setFunction("rem_cat", "remove_cathegory");
        $this->setForm("rem_cat", "", "rem_cat", "remove_cathegory_form");
        
        $this->setFunction("add_post", "add_post");
        $this->setForm("add_post", "", "add_post", "add_post_form");
        
        $this->setFunction("edit_post", "edit_post");
        
        $this->setFunction("rem_post", "remove_post");
        
        $this->setFunction("rel_post", "release_post");
        
        $this->setFunction("drop_post", "drop_post");
    }
    /*
     * Implementované metódy funkcie
     */
    protected function getProgramID() {
        return ProgramManager::getId("Article_post");
    }
    public function display(){
        
        if(!empty($_GET['mode'])){
            if(!strcmp($_GET['mode'],"post")){
                $this->displayPost ($_GET['post_id']);
                return;
            }
            if(!strcmp($_GET['mode'],"display")){
                $this->displayList();
                return;
            }
        }
        $this->displayFilters();
    }
    public function toolbox(){
        $form_src_base="./redakcia/request/form.php?id=".$this->getProgramID()."&article_id=".$this->article['id'];
        $action_base="./redakcia/request/action.php?id=".$this->getProgramID()."&article_id=".$this->article['id'];
                
        echo "<ul class='toolbox'>";
        if($this->article['zobrazit']){
            if($this->accessRights->approved('DROP')){
                $this->displayActionButton("release", "Stiahni článok", "release:0");
            }
            
        }
        else{
            if($this->accessRights->approved('RELEASE')){
                $this->displayActionButton("release", "Zverejni článok", "release:1");
            }
            if($this->accessRights->approved('ADD')){
                echo "<hr/>";
                $this->displayFormButton("edit", "Zmeň názov článku");
                $this->displayFormButton("change_topic", "Zmeň rubriku");
            }
            
        }
        
        if($this->accessRights->approved('EDIT_ENUMS')){
            echo "<hr/>";
            $this->displayFormButton("add_cat", "Pridaj kategóriu");
            $this->displayFormButton("edit_cat", "Uprav kategóriu");
            $this->displayFormButton("rem_cat", "Odstráň kategóriu");
        }
        echo "<hr/>";
        $this->displayFormButton("add_post", "Pridaj príspevok");
        if(!$this->article['zobrazit']){
            if($this->accessRights->approved('ASSIGN')){
                echo "<hr/>";
                $this->displayFormButton('add_user', "Priraď redaktora");
                $this->displayFormButton('rem_user', "Odstráň redaktora");
            }
        }
        echo "<hr/>";
        $this->displayBackButton();
        echo "</ul>";
    }
    /*
     * Základné rozhranie triedy
     */
    protected function add_cathegory(){
        if(empty($_POST['name'])){
            $this->setMsg(false, "Zadaj nazov kategorie");
            return;
        }
        if($this->catNameExists($_POST['name'])){
            $this->setMsg(false, "Kategoria uz existuje");
            return;
        }
        $this->addCathegory($_POST['name']);
        $this->setMsg(true, "Kategoria pridana");
    }
    protected function add_cathegory_form(){
        $form_id=  $this->getFormID("add_cat");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Zadaj nazov kategorie");
        $form->addInputField("text", "name", "required nazov");
        $form->registerForm();
        $form->showForm();
    }
    
    protected function edit_cathegory(){
        if(empty($_POST['cat_id'])){
            $this->setMsg(false, "Zadaj kategoriu");
            return;
        }
        if(empty($_POST['name'])){
            $this->setMsg(false, "Zadaj nazov kategorie");
            return;
        }
        if($this->catNameExists($_POST['name'])){
            $this->setMsg(false, "Nazov kategorie uz existuje");
            return;
        }
        if(!$this->catExists($_POST['cat_id'])){
            $this->setMsg(false, "Kategoria neexistuje");
            return;
        }
        
        $this->editCathegory($_POST['cat_id'], $_POST['name']);
        $this->setMsg(true, "Kategoria zmenena");
    }
    protected function edit_cathegory_form(){
        $this->readPosts();
        $form_id=  $this->getFormID("edit_cat");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Zadaj kategoriu");
        $form->addSelect("cat_id");
        $form->addOption("cat_id", "cat_id_0", "--Vyber kategoriu--", "");
        foreach($this->categories as $cat)
            $form->addOption ("cat_id", "cat_id_".$cat['id'], $cat['name'], $cat['id']);
        $form->addLabel("Zadaj nazov kategorie");
        $form->addInputField("text", "name", "required nazov");
        $form->registerForm();
        $form->showForm();
    }
    
    protected function remove_cathegory(){
        if(empty($_POST['cat_id'])){
            $this->setMsg(false, "Zadaj kategoriu");
            return;
        }
       
        if(!$this->catExists($_POST['cat_id'])){
            $this->setMsg(false, "Kategoria neexistuje");
            return;
        }
        
        if(!$this->catEmpty($_POST['cat_id'])){
            $this->setMsg(false, "Kategoria nie je prazdna");
            return;
        }
        
        $this->deleteCathegory($_POST['cat_id']);
        $this->setMsg(true, "Kategoria odstranena");
    }
    protected function remove_cathegory_form(){
        $this->readPosts();
        $form_id=  $this->getFormID("rem_cat");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Zadaj kategoriu");
        $form->addSelect("cat_id");
        $form->addOption("cat_id", "cat_id_0", "--Vyber kategoriu--", "");
        foreach($this->categories as $cat)
            $form->addOption ("cat_id", "cat_id_".$cat['id'], $cat['name'], $cat['id']);
        $form->registerForm();
        $form->showForm();
    }
    
    protected function add_post(){
        if(empty($_POST['cat_id'])){
            $this->setMsg(false, "Zadaj kategoriu");
            return;
        }
        if(!$this->catExists($_POST['cat_id'])){
            $this->setMsg(false, "Kategoria neexistuje");
            return;
        }
        
        if(empty($_POST['name'])){
            $this->setMsg(false, "Zadaj názov príspevku");
            return;
        }
        if(empty($_POST['post'])){
            $this->setMsg(false, "Zadaj príspevok");
            return;
        }
        
        $this->addPost($_POST['cat_id'], $_POST['name'], $_POST['post']);
        $this->setMsg(true, "Príspevok pridaný");
            
            
    }
    protected function add_post_form(){
        $this->readPosts();
        $form_id=  $this->getFormID("add_post");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Zadaj kategoriu");
        $form->addSelect("cat_id");
        $form->addOption("cat_id", "cat_id_0", "--Vyber kategoriu--", "");
        foreach($this->categories as $cat)
            $form->addOption ("cat_id", "cat_id_".$cat['id'], $cat['name'], $cat['id']);
        $form->addLabel("Zadaj názov príspevku");
        $form->addInputField("text", "name", "required nazov");
        $form->addLabel("Zadaj príspevok");
        $form->addTextArea("post");
        $form->registerForm();
        $form->showForm();
    }
    
    protected function edit_post(){
        if(empty($_GET['post_id'])){
            $this->setMsg(false, "Zadaj prispevok");
            return;
        }
        
        if(!$this->postEditable($_GET['post_id'])){
            $this->setMsg(false, "Nemozes zmenit prispevok");
            return;
        }
        $post_id=$_GET['post_id'];
        
        if(!empty($_POST['cat_id'])&&$this->catExists($_POST['cat_id']))
            $this->changeCat($post_id, $_POST['cat_id']);

        if(!empty($_POST['name']))
            $this->editPostName($post_id,$_POST['name']);
            
        if(!empty($_POST['post']))
            $this->editPost ($post_id, $_POST['post']);
        
        $this->setMsg(true, "Príspevok upraveny");
    }
    
    protected function remove_post(){
        if(empty($_POST['post_id'])){
            $this->setMsg(false, "Zadaj prispevok");
            return;
        }
        if(!$this->postEditable($_POST['post_id'])){
            $this->setMsg(false, "Nemozes odstranit prispevok");
            return;
        }
        $this->removePost($_POST['post_id']);
        
        $this->setMsg(true, "Príspevok odstaneny");
        
    }
    
    protected function release_post(){
        if(empty($_POST['post_id'])){
            $this->setMsg(false, "Zadaj prispevok");
            return;
        }
        if(!$this->postEditable($_POST['post_id'])){
            $this->setMsg(false, "Nemozes zverejnit prispevok");
            return;
        }
        if(!$this->accessRights->approved("RELEASE")){
            $this->setMsg(false, "Nemozes zverejnit prispevok");
            return;
        }
        
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Prispevok");
        $data->setRecord("prispevok_id", $_POST['post_id']);
        $data->setField("zobrazit", 1);
        if(!$data->queryDB("update")){
            $this->setMsg(false, "Nepodarilo sa zverejnit prispevok");
            return;
        }
        $this->setMsg(true, "Príspevok zverejneny");
    }
    
    protected function drop_post(){
        if(empty($_POST['post_id'])){
            $this->setMsg(false, "Zadaj prispevok");
            return;
        }
        if(!$this->postEditable($_POST['post_id'])){
            $this->setMsg(false, "Nemozes stiahnut prispevok");
            return;
        }
        if(!$this->accessRights->approved("RELEASE")){
            $this->setMsg(false, "Nemozes stiahnut prispevok");
            return;
        }
        
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Prispevok");
        $data->setRecord("prispevok_id", $_POST['post_id']);
        $data->setField("zobrazit", 0);
        if(!$data->queryDB("update")){
            $this->setMsg(false, "Nepodarilo sa stiahnut prispevok");
            return;
        }
        $this->setMsg(true, "Príspevok stiahnuty");
    }


    /*
     * Súkromné metódy triedy
     */
    //xml manipulation methods
    private function edit_description($desc){
        $this->getInfo();
        $this->post_info->post->description=$desc;
    }
    
    private function change_image($src){
        $this->getInfo();
        $this->post_info->info->img=$src;
    }
    
    private function saveInfo(){
        $this->getInfo();
        $this->post_info->asXML($this->path."/".$this->article['id'].".art");
    }
    
    private function createInfo(){
        $xml=
        "<?xml version='1.0' encoding='UTF-8'?>
            <article>
                <info>
                    <id>".$this->article['id']."</id>
                    <type>Príspevok</type>
                    <img></img>
                </info>
                <post>
                    <description />
                </post>
            </article>";
        
        $this->post_info=new SimpleXMLElement($xml);
        $this->post_info->asXML($this->path."/".$this->article['id'].".art");
    }
    
    private function getInfo(){
        if(!empty($this->post_info))
            return;
        $path=  $this->path."/".$this->article['id'].".art";
        if(!file_exists($path))
            $this->createInfo ();
        $this->post_info=new SimpleXMLElement($path,0,true);
    }
    //db manipulation methods
    private function addCathegory($name){
        if(!$this->accessRights->approved("EDIT_ENUMS"))
            return;
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Kategoria");
        $data->setField("nazov_kategorie", $name,true);
        return $data->queryDB("insert");
    }
    
    private function editCathegory($cat_id,$name){
        if(!$this->accessRights->approved("EDIT_ENUMS"))
            return;
        if(!$this->catExists($cat_id))
            return;
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Kategoria");
        $data->setRecord("kategoria_id", $cat_id);
        $data->setField("nazov_kategorie", $name);
        return $data->queryDB("update");
    }
    
    private function deleteCathegory($cat_id){
        if(!$this->accessRights->approved("EDIT_ENUMS"))
            return;
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Kategoria");
        $data->setRecord("kategoria_id", $cat_id);
        return $data->queryDB("delete");
    }
    
    private function catEmpty($cat_id){
        $this->readPosts();
        $data=  new DBQuery(CDatabaza::getInstance());
        $data->setTable("Prispevok");
        $data->setRecord("kategoria_id", $cat_id);
        if($data->queryDB("select")->num_rows)
            return false;
        return true;
    }
    
    private function catExists($cat_id){
        /*
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Kategoria");
        $data->setRecord("kategoria_id", $cat_id);
        if($data->queryDB("select")->num_rows)
            return true;

         */
        $this->readPosts();
        return !empty($this->categories[$cat_id]);
    }
    private function catNameExists($name){
        $this->readPosts();
        if(!empty($this->categories))
            foreach($this->categories as $cat)
                if(!strcmp($cat['name'],$name))
                    return true;
        
        return false;
    }
    
    private function addPost($cat_id,$post_name,$post){
        if(!$this->catExists($cat_id))
            return;
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Prispevok");
        $data->setField("nazov_prispevku", $post_name,true);
        $data->setField("kategoria_id", $cat_id,true);
        $data->setField("prispevok", $post);
        $data->setField("uzivatel_id", $_SESSION['user']);
        $data->setField("clanok_id", $this->article['id']);
        $data->setField("zobrazit", 0);
        $data->setField("casova_znamka", time());
        return $data->queryDB("insert");
        
    }
    
    private function postEditable($post_id){
        $data=  CDatabaza::getInstance();
        $connected=$data->connected();
        if(!$connected)
            $data->connect();
        $post_id=$data->escape_string($post_id);
        $sql="SELECT * FROM Prispevok WHERE prispevok_id=$post_id AND clanok_id=".$this->article['id'];
        if(!$this->accessRights->approved("EDIT_ALL"))
            $sql=$sql." AND uzivatel_id=".$_SESSION['user'];
        if($data->query($sql)->num_rows){
            if(!$connected)
                $data->close();
            return true;
        }
        if(!$connected)
            $data->close();
        return false;
    }
    private function editPostName($post_id,$post_name){
        if(!$this->postEditable($post_id))
            return false;
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Prispevok");
        $data->setField("nazov_prispevku", $post_name,true);
        $data->setRecord("prispevok_id", $post_id);
        return $data->queryDB("update");
    }
    
    private function editPost($post_id,$post){
        if(!$this->postEditable($post_id))
            return false;
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Prispevok");
        $data->setField("prispevok", $post,true);
        $data->setRecord("prispevok_id", $post_id);
        return $data->queryDB("update");
    }
    
    private function changeCat($post_id,$cat_id){
        if(!$this->postEditable($post_id))
            return false;
        if(!$this->catExists($cat_id))
            return false;
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Prispevok");
        $data->setField("kategoria_id", $cat_id,true);
        $data->setRecord("prispevok_id", $post_id);
        return $data->queryDB("update");
    }
    
    private function removePost($post_id){
        if(!$this->postEditable($post_id))
            return false;
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Prispevok");
        $data->setRecord("prispevok_id", $post_id);
        return $data->queryDB("delete");
    }
    
    //init methods
    private function readPosts(){
        if(!empty($this->posts))
            return;
        $data=  CDatabaza::getInstance();
        $connected=$data->connected();
        if(!$connected)
            $data->connect();
        //$post_id=$data->escape_string($post_id);
        $sql="SELECT * FROM Prispevok WHERE clanok_id=".$this->article['id'];
        if(!$this->accessRights->approved("EDIT_ALL"))
            $sql=$sql." AND uzivatel_id=".$_SESSION['user'];
        $filters=  $this->getFilters();
        
        if($filters){
            $sql=$sql." AND ".$filters;
        }
        
        $posts=$data->query($sql);
        if($posts){
            while($post=$posts->fetch_array()){
                $id=$post['prispevok_id'];
                $this->posts[$id]['id']=$id;
                $this->posts[$id]['cat_id']=$post['kategoria_id'];
                $this->posts[$id]['name']=$post['nazov_prispevku'];
                $this->posts[$id]['post']=$post['prispevok'];
                $this->posts[$id]['time']=$post['casova_znamka'];
                $this->posts[$id]['released']=$post['zobrazit'];
            }
        }
        
        $sql="SELECT * FROM Kategoria";
        $cats=$data->query($sql);
        if($posts)
            while($cat=$cats->fetch_array()){
                $id=$cat['kategoria_id'];
                $this->categories[$id]['id']=$id;
                $this->categories[$id]['name']=$cat['nazov_kategorie'];
            }
        if(!$connected)
            $data->close();
        
    }
    private function getFilters(){
        $filters="";
        if(!empty($_GET['cat']))
            $filters.=" AND kategoria_id=".$_GET['cat'];
        if(isset($_GET['released']))
            $filters.=" AND zobrazit=".$_GET['released'];
        
        if(strcmp($filters,"")){
            $filters=  substr($filters, 5);
            return $filters;
        }
        return false;
    }
    //display methods
    private function displayPost($post_id){
        $this->readPosts ();
        $post=  $this->posts[$post_id];
        $cat_data="{";
        foreach($this->categories as $cat)
            $cat_data.=$cat['id'].":{id:".$cat['id'].",name:'".$cat['name']."'},";
        $cat_data=  substr($cat_data, 0, strlen($cat_data)-1)."}";
        if(empty($post)){
            echo "Príspevok neexistuje alebo nemáte dostatočné práva na zobrazenie tohoto príspevku";
            return;
        }
        $released="";
        if($post['released'])
            $released="released";
        echo "<div id='post_$post_id' class='display_post $released'>";
        echo "<div class='post_cat'>".$this->categories[$post['cat_id']]['name']."</div>";
        
        echo "<div class='post_time'>".date("d.n.Y h:i:s",$post['time'])."</div>";
        echo "<div class='post_name'>".$post['name']."</div>";
        echo "<div class='post_content'>".preg_replace("/\n/", "<br/>", $post['post'])."</div>";
        if(!$post['released']){
            echo "<span class='post_button' onclick=\"$('#post_$post_id').post_edit(".$this->getProgramID().",".$this->article['id'].",$post_id,$cat_data);\">[upraviť]</span>";
            echo "<span class='post_button' onclick=\"potvrdASpustiProgram(".$this->getProgramID().",'rem_post',{article_id:".$this->article['id'].",post_id:$post_id},'Naozaj chcete odstranit prispevok?')\">[odstanit]</span>";
            if($this->accessRights->approved('RELEASE'))
                echo "<span class='post_button' onclick=\"potvrdASpustiProgram(".$this->getProgramID().",'rel_post',{article_id:".$this->article['id'].",post_id:$post_id},'Naozaj chcete zverejnit prispevok?')\">[zverejnit]</span>";
        }
        else if($this->accessRights->approved('DROP'))
            echo "<span class='post_button' onclick=\"potvrdASpustiProgram(".$this->getProgramID().",'drop_post',{article_id:".$this->article['id'].",post_id:$post_id},'Naozaj chcete stiahnut prispevok?')\">[stiahnut]</span>";
        echo "</div>";
    }
    private function displayList(){
        $this->readPosts();
        
        if(!empty($this->posts))
        foreach($this->posts as $post)
            $this->displayPost ($post['id']);
        
    }
    private function displayFilters(){
        $this->readPosts();
        parent::display();
        echo "<div style='text-align:left; font-weight:bold;'>";
        echo "Kategória: <select name='cat' id='kategoria_filter' onchange=\"post_filter.set_category(this);\">";
        echo "<option value='-1'>Všetko</option>";
        foreach($this->categories as $cat)
            echo "<option value='".$cat['id']."'>".$cat['name']."</option>";
        echo "</select>";
        echo " Zverejnené: <select name='released' id='zverejnene_filter' onchange=\"post_filter.set_release(this);\">";
        echo "<option value='-1'>Všetko</option>";
        echo "<option value='0'>Nezverejnené</option>";
        echo "<option value='1'>Zverejnené</option>";
        echo "</select>";
        echo "</div>";
        echo "<div id='post_wrapper' style='text-align:left;'>";
        $this->displayList();
        echo "</div>";
        echo "<script type='text/javascript'>
            function load_filters(){
            post_filter.set_filters(".$this->getProgramID().",".$this->article['id'].",'post_wrapper');
            }
            if(scriptloader.empty('redakcia/scripts/posts.js'))
                scriptloader.load_script('redakcia/scripts/posts.js','js',function(){
                    load_filters();});
            else
                load_filters();
            scriptloader.load_script('redakcia/styles/posts.css','css');
            
            </script>";
        
    }
    
}

?>
