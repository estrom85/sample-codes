<?php

/*
 * filters:
 * 
 * type=article_type
 * released=True/false
 * topic=topic_id
 * 
 * find:
 * 
 * nazov=like % %
 * 
 * display:
 * 
 * page=%
 * 
 */

/**
 * Description of Article_list
 *
 * @author mato
 */
class Article_list extends Module {
    /*
     * Parametre triedy
     */
    private $accessRights;
    private $articles;
    private $mode;
    private $page_size;
    private $max_page;
    private $page;
    /*
     * Konstruktor
     */
    public function __construct() {
        $this->enable();
        $this->mode="menu";
        if(isset($_GET['mode'])){
            if(!strcmp($_GET['mode'],"display")){
                    $this->mode="display";
                    
            }
        }
        $this->initialize();
        if($this->accessRights->approved('ADD')){
            $this->setFunction("add", "add_article");
            $this->setForm("add", "Pridaj článok", "add_article", "add_article_form");
        }
        
        if($this->accessRights->approved('REMOVE')){
            $this->setFunction("remove", "remove_article");
        }
        
    }
    /*
     * Implementovane funkcie
     */
    protected function getProgramID() {
        return ProgramManager::getId("Article_list");
    }
    
    public function display() {
        if(strcmp($this->mode, "display"))
            $this->display_filters ();
        else
            $this->display_articles ();
    }
    
    /*
     * Zakladne rozhranie triedy
     */
    protected function add_article(){
        if(empty($_POST['name'])){
            $this->setMsg(false, "Zadaj názov článku");
            return;
        }
        
        $data=new DBQuery(CDatabaza::getInstance());
        
        if(empty($_POST['type'])){
            $this->setMsg(false, "Zadaj typ článku");
            return;
        }
        
        $data->setTable("Typ_clanku");
        $data->setRecord("typ_clanku_id", $_POST['type']);
        $types=$data->queryDB("select");
        if(!$types){
            $this->setMsg(false, "Chybná požiadavka");
            return;
        }
        if(!$types->num_rows){
            $this->setMsg(false, "Zadaný typ neexistuje");
            return;
        }
        if(empty($_POST['topic'])){
            $this->setMsg(false, "Zadaj rubriku");
            return;
        }
        
        $data->setTable("Rubrika");
        $data->setRecord("rubrika_id", $_POST['topic']);
        $topics=$data->queryDB("select");
        if(!$topics){
            $this->setMsg(false, "Chybná požiadavka");
            return;
        }
        if(!$topics->num_rows){
            $this->setMsg(false, "Zadaná rubrika neexistuje");
            return;
        }
        
        
        //vlozi do databazy udaje o clanku
        $data->setTable("Clanok");
        $data->setField("nazov_clanku", $_POST['name'], true);
        $data->setField("typ_clanku_id", $_POST['type'],true);
        $data->setField("rubrika_id", $_POST['topic'],true);
        $data->setField("hodnotenie", 0);
        $data->setField("hodnotenie_pocet", 0);
        $data->setField("zobrazit", 0);
        $data->setField("casova_znamka", time());
        
        $id=$data->queryDB("insert");
        if(!$id){
            $this->setMsg(false, "Nepodarilo sa pridať článok");
            return;
        }
        //pokusi sa vytvorit adresar clanku ak sa to nepodari, vymaze vlozeny zaznam z databazy
        $dir=  ProgramManager::getHomeDir()."/../articles/$id";
        if(!mkdir($dir)){
            $this->setMsg(false, "Nepodarilo sa vytvoriť adresár");
            $data->setRecord("clanok_id", $id);
            $data->queryDB("delete");
            return;
        }
        chmod($dir, 0777);
        //pokusi sa vytvorit adresar obrazky, ak sa to nepodari, vymaze adresar clanku a zaznam z databazy
        if(!mkdir($dir."/pics")){
            $this->setMsg(false, "Nepodarilo sa vytvoriť adresár");
            rmdir($dir);
            $data->setRecord("clanok_id", $id);
            $data->queryDB("delete");
            return;
        }
        
        $this->setMsg(true, "Článok úspešne pridaný");
    }
    //pridaj clanok - formular
    protected function add_article_form(){
        $form_id=  $this->getFormID("add");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Názov článku");
        $form->addInputField("text", "name", "required nazov");
        $form->addLabel("Zadaj typ článku");
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Typ_clanku");
        $types=$data->queryDB("select");
        $form->addSelect("type");
        $form->addOption("type","","--Zadaj typ článku--","");
        while($type=$types->fetch_array()){
            $form->addOption("type", "type_".$type['typ_clanku_id'], $type['nazov'], $type['typ_clanku_id']);
        }
        $form->addLabel("Zadaj rubriku");
        $data->setTable("Rubrika");
        $topics=$data->queryDB("select");
        $form->addSelect("topic");
        $form->addOption("topic","","--Zadaj rubriku--","");
        while($topic=$topics->fetch_array()){
            $form->addOption("topic", "topic_".$topic['rubrika_id'], $topic['nazov_rubriky'], $topic['rubrika_id']);
        }
        $form->showForm();
        
    }
    //odstrani clanok z databazy
    protected function remove_article(){
        if(empty($_POST['id'])){
            $this->setMsg(false, "Zadajte článok");
            return;
        }
        $data=new DBQuery(CDatabaza::getInstance());
        $data->setTable("Clanok_uzivatel");
        $data->setRecord("clanok_id",$_POST['id']);
        if(!$data->queryDB("delete")){
            $this->setMsg(false, "Nepodarilo sa vymazať prepojenia");
            return;
        }
        $data->setTable("Clanok");
        $data->setRecord("clanok_id",$_POST['id']);
        if(!$data->queryDB("delete")){
            $this->setMsg(false, "Nepodarilo sa vymazať článok");
            return;
        }
        //odstrani adresar a vsetko co sa v nom nachadza
        $dir=  ProgramManager::getHomeDir()."/../articles/".$_POST['id'];
        $this->removeDir($dir);
        $this->setMsg(true, "Článok vymazaný");
    }
    
    protected function remove_article_form(){
        
    }
    /*
     * Privatne funkcie triedy
     */
    //inicializuje triedu
    private function initialize(){
        $data=  CDatabaza::getInstance();
        $connected=$data->connected();
        if(!$connected)
            $data->connect();
        //ziska uzivatelske prava
        $this->accessRights=new UserRights($data);
        //ziska informacie o clanku z databazy
        $sql="";
        $filters=  $this->get_filters();
        //ak ma uzivatel vseobecne prava na upravu clanku, zobrazi vsetky clanky
        //inak zobrazi len clanky, ku ktorym bol prideleny
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
                Rubrika.rubrika_id=Clanok.rubrika_id";
            if(strcmp($filters, ""))
                $sql.=" WHERE ".$filters;    
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
                WHERE Clanok_uzivatel.uzivatel_id=".$_SESSION['user'];
            
            
            
            
            if(strcmp($filters,""))
                    $sql.=" AND ".$filters;
        }
        //nastavi pravidla zobrazovania (filtre, zobrazena strana,...)
        $this->page_size=10;
        if(isset($_GET['size'])){
            $this->page_size=$_GET['size'];
        }
        $this->max_page=ceil($data->query($sql)->num_rows/$this->page_size);
        $this->page=0;
        if(isset($_GET['page']))
            $this->page=($_GET['page']-1)*$this->page_size;
        $sql=$sql." ORDER BY Clanok.nazov_clanku LIMIT $this->page, $this->page_size";
        
        
        
        $articles=$data->query($sql);
       
        //ziska udaje o clanku
        while($article=$articles->fetch_array()){
            $id=$article['clanok_id'];
            $this->articles[$id]['id']=$id;
            $this->articles[$id]['nazov']=$article['nazov_clanku'];
            $this->articles[$id]['typ_id']=$article['typ_clanku_id'];
            $this->articles[$id]['typ']=$article['nazov'];
            $this->articles[$id]['rubrika']=$article['nazov_rubriky'];
            $this->articles[$id]['zobrazit']=$article['zobrazit'];
            $this->articles[$id]['cas']=$article['casova_znamka'];
        }
        
        
        if(!$connected)
            $data->close();
    }
    //vygeneruje filtre zaznamov
    private function get_filters(){
        $data=  CDatabaza::getInstance();
        $filters="";
        if(!empty($_GET['type']))
            $filters.=" AND Clanok.typ_clanku_id=".$data->escape_string($_GET['type']);
        if(isset($_GET['released'])){
            if($_GET['released']==0)
                $filters.=" AND Clanok.zobrazit=0";
            else if($_GET['released']==1)
                $filters.=" AND Clanok.zobrazit=1";
        }
        if(!empty($_GET['topic']))
            $filters.=" AND Clanok.rubrika_id=".$data->escape_string($_GET['topic']);
        if(!empty($_GET['nazov'])){
           // echo ($_GET['nazov']);
            $nazvy=  explode(" ", $data->escape_string($_GET['nazov']));
            foreach($nazvy as $nazov)
                if (!empty($nazov))
                    $filters.=" AND Clanok.nazov_clanku LIKE '%".$nazov."%'";
        }
        $filters=  substr($filters, 5);
        return $filters;
    }
    //zobrazi filtre na pracovnu plochu
    private function display_filters(){
        $data=new DBQuery(CDatabaza::getInstance());
        echo "<script type='text/javascript'></script>";
        echo "<form id='filters'>";
        echo "<span class='label'>Hľadaj článok: </span><input id='name' type='text' name='nazov' /><br/>";
        echo "<span class='label'>Zverejnené: </span><select id='zverejnit' name='released'>";
        echo "<option value=''>Všetko</option><option value='1'>Áno</option><option value='0'>Nie</option></select> ";
        echo "<span class='label'>Rubrika: </span><select id='rubrika' name='topic'>";
        echo "<option value=''>Všetko</option>";
        $data->setTable('Rubrika');
        $query=$data->queryDB('select');
        while($rubrika=$query->fetch_array()){
            echo "<option value='".$rubrika['rubrika_id']."'>";
            echo $rubrika['nazov_rubriky'];
            echo "</option>";
        }
        echo "</select>";
        echo "<span class='label'>Typ: </span><select id='typ' name='type'>";
        echo "<option value=''>Všetko</option>";
        $data->setTable('Typ_clanku');
        $query=$data->queryDB('select');
        while($typ=$query->fetch_array()){
            echo "<option value='".$typ['typ_clanku_id']."'>";
            echo $typ['nazov'];
            echo "</option>";
        }
        echo "</select>";
        echo "</form>";
        echo "<div id='article_list'></div id='article_list'>";
        echo "";
        echo "<script type='text/javascript'>";
        echo "function filters_load(){";
        echo "$('#filters').submit(function(e){e.preventDefault();});";
        echo "filter_manager.set_manager('article_list',".$this->getProgramID().");";
        echo "filter_manager.add('name');";
        echo "filter_manager.add('zverejnit');";
        echo "filter_manager.add('rubrika');";
        echo "filter_manager.add('typ');";
        echo "filter_manager.set_max_page($this->max_page);";
        echo "filter_manager.apply();}";
        /*
        echo "content='article_list';program=".$this->getProgramID().";";
        echo "apply_filter();";
        echo "add_filter('name');";
        echo "add_filter('zverejnit');";
        echo "add_filter('rubrika');";
        echo "add_filter('typ');}";
         * 
         */
        echo "if(typeof filter_manager!='undefined') filters_load();";
        echo "else scriptloader.load_script('redakcia/scripts/filters.js','js',filters_load);";
        echo "scriptloader.load_script('redakcia/styles/article_list.css','css');";
        echo "scriptloader.load_script('redakcia/styles/topics.css','css');";
        //echo "$('#article_list').load('./request/main.php?id=".$this->getProgramID()."&mode=display');";
        echo "</script>";
    }
    //zobrazi zoznam clankov na pracovnu plochu
    private function display_articles(){
        
        echo "<script type='text/javascript'>";
        //echo "max_page=".$this->max_page.";";
        echo "filter_manager.set_max_page($this->max_page);";
        echo "</script>";
        $this->display_navigator();
        echo "<table id='article_list_table' class='table-display' cellspacing='0' cellpadding='0'><tr>";
        echo "<th>ID</th>";
        echo "<th>Nazov</th>";
        echo "<th>Typ článku</th>";
        echo "<th>Rubrika</th>";
        echo "<th>Zobrazené</th>";
        echo "<th>Casova známka</th><th></th></tr>";
        if(!empty($this->articles))
        foreach($this->articles as $article){
            $program_id=  $this->getProgram($article['typ_id']);
            $program="nastavProgram({id:'".$program_id."',article_id:'".$article['id']."'});";
            echo "<tr>";
            echo "<td>".$article['id']."</td>";
            echo "<td onclick=\"$program\" class='art_name'>".$article['nazov']."</td>";
            echo "<td>".$article['typ']."</td>";
            echo "<td>".$article['rubrika']."</td>";
            echo "<td>".($article['zobrazit']?"Áno":"Nie")."</td>";
            echo "<td>".date("d.n.Y",$article['cas'])."</td>";
            echo "<td>";
            if($this->accessRights->approved('REMOVE'))
            echo"<a href='' onclick=\"potvrdASpustiProgram(".$this->getProgramID().",'remove',{id:'"
                    .$article['id']."'},'Skutočne chcete vymazať článok. Vymažete tým aj celú rozrobenú prácu');return false;\">Vymaž</a>";
            echo"</td>";
            echo "</tr>";
        }
        echo "</table>";  
        $this->display_navigator();
    }
    
    private function display_navigator(){
        
        if($this->max_page<2)
            return;
        
        echo "<div class='nav_wrapper'><ul class='page_lister'>";
        echo "<li><div class='nav_item first_page' onclick='filter_manager.set_page(1);'></div></li>";
        echo "<li><div class='nav_item prev_page' onclick='filter_manager.prev();'></div></li>";
        $first_page=1;
        $last_page=  $this->max_page;
        $page= floor($this->page/$this->page_size)+1;
        if($this->max_page>7){
            if($page>3&&$page<$this->max_page-3){
                $first_page=  $page-3;
                $last_page=  $page+3;
            }
            else if($page<3)
                $last_page=7;
          
            else if($page>$this->max_page-3)
                $first_page=  $this->max_page-6;
            
        }
        
        for($i=$first_page;$i<=$last_page;$i++){
            $class="";
            if($i==$page)
                $class="active";
            echo "<li><div class='nav_item nav_page $class' onclick='filter_manager.set_page($i);'>$i</div></li>";
        }
        echo "<li><div class='nav_item next_page' onclick='filter_manager.next();'></div></li>";
        echo "<li><div class='nav_item last_page' onclick='filter_manager.set_page($this->max_page);'></div></li>";
        echo "</ul></div>";
        
    }
    //rekurzivne odstrani adresar a vsetko co sa v nom nachadza
    private function removeDir($dir){
        foreach(glob($dir."/*") as $file){
            if(is_dir($file))
                $this->removeDir($file);
            else
                unlink ($file);
        }
        rmdir($dir);
    }
    //ziska odkaz na editacny program, na zaklade typu clanku
    private function getProgram($id){
        switch($id){
            case 1:
                return ProgramManager::getId("Article_story");
            case 2:
                return ProgramManager::getId("Article_interview");
            case 3:
                return ProgramManager::getId("Article_post");
            case 4:
                return ProgramManager::getId("Article_quiz");
            case 5:
                return ProgramManager::getId("Article_galery");
                    
        }
    }
    
}

?>
