<?php

/**
 * Description of Topics
 *
 * @author mato
 */
class Topics extends Module {
    /*
     * Parametre triedy
     */
    
    private $themes;
    private $topics;
    private $orders;
    /*
     * Konstruktor
     */
    public function __construct() {
        //zisti, ci uzivatel ma pravo menit dane udaje
        $rights=new UserRights(CDatabaza::getInstance());
        if(!$rights->approved("EDIT_ENUMS")){
            $this->disable();
            return;
        }
        $this->enable();
        //inicializuje premenne
        $this->initialize();
        //nastavi spustitelne funkcie a prislusne formulare triedy
        $this->setFunction("add_topic", "add_topic");
        $this->setForm("add_topic", "Pridaj rubriku", "add_topic", "add_topic_form");
        
        $this->setFunction("edit_topic", "edit_topic");
        $this->setForm("edit_topic", "Uprav rubriku", "edit_topic", "edit_topic_form");
        
        $this->setFunction("remove_topic", "remove_topic");
        $this->setForm("remove_topic", "Odstráň rubriku", "remove_topic", "remove_topic_form");
        
        $this->setFunction("add_theme", "add_theme");
        $this->setForm("add_theme", "Pridaj tému", "add_theme", "add_theme_form");
        
        $this->setFunction("edit_theme", "edit_theme");
        $this->setForm("edit_theme", "Uprav tému", "edit_theme", "edit_theme_form");
        
        $this->setFunction("remove_theme", "remove_theme");
        $this->setForm("remove_theme", "Odstráň tému", "remove_theme", "remove_theme_form");
    }
    /*
     * Implementovane funkcie triedy
     */
    protected function getProgramID() {
        return ProgramManager::getId("Topics");
    }
    
    public function display() {
        $this->styles();
        echo "<div id='table-wrapper'>";
        $this->displayTopics();
        $this->displayThemes();
        echo "</div>";
    }
    
    /*
     * Zakladne rozhranie triedy
     */
    
    //prida rubriku do databazy
    protected function add_topic(){
        //skontroluje, ci dana tema existuje ak nie vrati sa spat
        if(empty($_POST['tema'])){
            $this->setMsg(false, "Zadajte tému rubriky prosím.");
            return;
        }
        if(empty($this->themes[$_POST['tema']])){
            $this->setMsg(false, "Zadaná téma sa v databáze nenachádza");
            return;
        }
           
        //skontroluje, ci je zadany nazov rubriky
        if(empty($_POST['nazov'])){
            $this->setMsg(false, "Zadajte názov rubriky");
            return;
        }
        
        if(empty($_POST['tema'])){
            $this->setMsg(false, "Zadajte tému rubriky prosím.");
            return;
        }
        if(empty($this->themes[$_POST['tema']])){
            $this->setMsg(false, "Zadaná téma sa v databáze nenachádza");
            return;
        }
        
        //pripoji sa na databazu a ulozi hodnoty
        $query=new DBQuery(CDatabaza::getInstance());
        $query->setTable("Rubrika");
        $query->setField("nazov_rubriky", $_POST['nazov'],true);
        $query->setField("tema_id", $_POST['tema'],true);
        if(!$query->queryDB("insert")){
            $this->setMsg(false, "Nepodarilo sa pridať rubriku.");
        }
        $this->setMsg(true, "Rubrika <strong><i>".$_POST['nazov']."</i></strong> úspešne pridaná.");
    }
    //vytvori formular na pridavanie rubrik
    protected function add_topic_form(){
        $form_id=  $this->getFormID("add_topic");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->setClass("formular");
        $form->addLabel("Názov rubriky");
        $form->addInputField("text", "nazov", "required nazov");
        $form->addLabel("Vyber tému");
        $form->addSelect('tema');
        $form->addOption('tema','nazov','---Vyber tému---','');
        foreach ($this->themes as $theme){
            $form->addOption('tema','tema_'.$theme['id'], $theme['name'], $theme['id']);
        }
        $form->registerForm();
        $form->showForm();
    }
    
    protected function edit_topic(){
        /*
         * kontrolne funkcie
         */
        if(empty($_POST['rubrika'])){
            $this->setMsg(false, "Nebola vybraná žiadna rubrika");
            return;
        }
        if(empty($this->topics[$_POST['rubrika']])){
            $this->setMsg(false, "Zadaná rubrika neexistuje");
            return;
        }
        
        if(empty($_POST['nazov'])){
            $this->setMsg(false, "Zadajte názov rubriky");
            return;
        }
        
        if(empty($_POST['tema'])){
            $this->setMsg(false, "Zadajte tému rubriky prosím.");
            return;
        }
        
        if(empty($this->themes[$_POST['tema']])){
            $this->setMsg(false, "Zadaná téma sa v databáze nenachádza");
            return;
        }
        //pripojenie na databazu
        $query=new DBQuery(CDatabaza::getInstance());
        $query->setTable("Rubrika");
        $query->setField("nazov_rubriky", $_POST['nazov'],true);
        $query->setField("tema_id", $_POST['tema'],true);
        $query->setRecord("rubrika_id", $_POST['rubrika']);
        if(!$query->queryDB("update")){
            $this->setMsg(false, "Nepodarilo sa zmeniť rubriku");
            return;
            
        }
        
        $this->setMsg(true, "Rubrika bola zmenená");
    }
    //formular
    protected function edit_topic_form(){
        $form_id=  $this->getFormID("edit_topic");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->setClass("formular");
        $form->addLabel("Vyber rubriku");
        $form->addSelect('rubrika');
        $form->addOption('rubrika','name','---Vyber rubriku---','');
        foreach($this->topics as $topic){
            $form->addOption('rubrika', 'topic_'.$topic['id'], $topic['name'], $topic['id']);
        }
        $form->addLabel("Názov rubriky");
        $form->addInputField("text", "nazov", "required nazov");
        $form->addLabel("Vyber tému");
        $form->addSelect('tema');
        $form->addOption('tema','nazov','---Vyber tému---','');
        foreach ($this->themes as $theme){
            $form->addOption('tema','tema_'.$theme['id'], $theme['name'], $theme['id']);
        }
        $form->registerForm();
        $form->showForm();
    }
    
    protected function remove_topic(){
        if(empty($_POST['rubrika'])){
            $this->setMsg(false, "Nie je zadaná žiadna rubrika.");
            return;
        }
        //skontroluje, ci nie je k danej rubrike priradeny ziaden clanok
        $db=  CDatabaza::getInstance();
        $connected=$db->connected();
        if(!$connected)
            $db->connect();
        $sql="SELECT * FROM Rubrika INNER JOIN Clanok ON Rubrika.rubrika_id=Clanok.rubrika_id";
        $query=$db->query($sql);
        if(!$query){
            $this->setMsg(false, "Nastala neočakávaná chyba, skúste znova");
            if(!$connected)
                $db->close();
            return;
            
        }
        if(!$query->num_rows){
            $this->setMsg(false, "K danej rubrike sú priradené články. Preraďte články do inej rubriky a skúste znova");
            if($connected)
                $db->close();
            return;
        }
        $query=new DBQuery($db);
        $query->setTable('Rubrika');
        $query->setRecord('rubrika_id',$_POST['rubrika'],true);
        if(!$query->queryDB('delete')){
            $this->setMsg(false, 'Nepodarilo sa rubriku odstrániť');
        }
        if($connected)
            $db->close();
        $this->setMsg(true, "Rubrika úspešne odstránená.");
    }
    
    protected function remove_topic_form(){
        $form_id=  $this->getFormID("remove_topic");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->setClass("formular");
        $form->addLabel("Vyber rubriku");
        $form->addSelect('rubrika');
        $form->addOption('rubrika','name','---Vyber rubriku---','');
        foreach($this->topics as $topic){
            $form->addOption('rubrika', 'topic_'.$topic['id'], $topic['name'], $topic['id']);
        }
        $form->registerForm();
        $form->showForm();
        
    }
    
    protected function add_theme(){
        if(empty($_POST['nazov'])){
            $this->setMsg(false, "Zadajte názov témy");
            return;
        }
        $query=new DBQuery(CDatabaza::getInstance());
        $query->setTable('Tema');
        $query->setField('nazov_temy', $_POST['nazov'], true);
        if(!$query->queryDB("insert")){
            $this->setMsg(false, "Nepodarilo sa pridať tému");
            return;
        }
        
        $this->setMsg(true, "Téma ".$_POST['nazov']." úspešne pridaná");
    }
    
    protected function add_theme_form(){
        $form_id=  $this->getFormID("add_theme");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->setClass("formular");
        $form->addLabel("Názov témy");
        $form->addInputField("text", "nazov", "required nazov");
        $form->registerForm();
        $form->showForm();
    }
    
    protected function edit_theme(){
        if(empty($_POST['tema'])){
            $this->setMsg(false, "Zadajte tému rubriky prosím.");
            return;
        }
        if(empty($this->themes[$_POST['tema']])){
            $this->setMsg(false, "Zadaná téma sa v databáze nenachádza");
            return;
        }
        if(empty($_POST['nazov'])){
            $this->setMsg(false, "Zadajte názov témy");
            return;
        }
        $query=new DBQuery(CDatabaza::getInstance());
        $query->setTable('Tema');
        $query->setRecord("tema_id", $_POST['tema'],true);
        $query->setField("nazov_temy", $_POST['nazov'],true);
        if(!$query->queryDB("update")){
            $this->setMsg(false, "Nepodarilo sa zmeniť tému");
            return;
        }
        $this->setMsg(true, "Téma bola úspešne zmenená");
    }
    
    protected function edit_theme_form(){
        $form_id=  $this->getFormID("edit_theme");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->setClass("formular");
       
        $form->addLabel("Vyber tému");
        $form->addSelect('tema');
        $form->addOption('tema','nazov','---Vyber tému---','');
        foreach ($this->themes as $theme){
            $form->addOption('tema','tema_'.$theme['id'], $theme['name'], $theme['id']);
        }
        $form->addLabel("Názov témy");
        $form->addInputField("text", "nazov", "required nazov");
        $form->registerForm();
        $form->showForm();
    }
    
    protected function remove_theme(){
        if(empty($_POST['tema'])){
            $this->setMsg(false, "Zadajte tému rubriky prosím.");
            return;
        }
        if(empty($this->themes[$_POST['tema']])){
            $this->setMsg(false, "Zadaná téma sa v databáze nenachádza");
            return;
        }
        foreach($this->topics as $topic){
            if($topic['theme']==$_POST['tema']){
                $this->setMsg(false, "K danej téme sú priradené rubriky. Preraďte rubriky na inú tému a skúste znovu");
                return;
            }
        }
        $query=new DBQuery(CDatabaza::getInstance());
        $query->setTable('Tema');
        $query->setRecord("tema_id", $_POST['tema'],true);
        if(!$query->queryDB("delete")){
            $this->setMsg(false, "Nepodarilo sa vymazať tému");
            return;
        }
        
        $this->setMsg(true, "Téma úspešne vymazaná");
    }
    
    protected function remove_theme_form(){
        $form_id=  $this->getFormID("remove_theme");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->setClass("formular");
       
        $form->addLabel("Vyber tému");
        $form->addSelect('tema');
        $form->addOption('tema','nazov','---Vyber tému---','');
        foreach ($this->themes as $theme){
            $form->addOption('tema','tema_'.$theme['id'], $theme['name'], $theme['id']);
        }
        $form->registerForm();
        $form->showForm();
    }
    /*
     * Sukromne funkcie triedy
     */
    
    //inicializuje parametre triedy hodnotami z databazy
    private function initialize(){
        $data=new DBQuery(CDatabaza::getInstance());
        //zakladne nastavenie zobrazenia
        $this->orders['theme']['key']="tema_id";
        $this->orders['theme']['order']="DESC";
        $this->orders['topic']['key']="rubrika_id";
        $this->orders['topic']['order']="DESC";
        
        /*nastavi usporiadanie zaznamov v tabulke*/
        //pole zoradienia temy
        if(isset($_GET['theme_key'])){
            $key=$_GET['theme_key'];
            if(!strcmp($key, 'id'))
                $this->orders['theme']['key']="tema_id";
            else if(!strcmp($key, 'name'))
                $this->orders['theme']['key']="nazov_temy";
        }
        //poradie zoradenia temy
        if(isset($_GET['theme_order'])){
            if(!strcmp($_GET['theme_order'], "ASC"))
                $this->orders['theme']['order']="ASC";
        }
        
        if(isset($_GET['topic_key'])){
            $key=$_GET['topic_key'];
            
            if(!strcmp($key, 'id'))
                $this->orders['topic']['key']="rubrika_id";
            else if(!strcmp($key, 'name'))
                $this->orders['topic']['key']="nazov_rubriky";
            else if(!strcmp($key, 'theme'))
                $this->orders['topic']['key']="tema_id";
            
        }
        
        if(isset($_GET['topic_order'])){
            if(!strcmp($_GET['topic_order'], "ASC"))
                $this->orders['topic']['order']="ASC";
        }
        
        $data->setTable("Tema");
        $data->setOrder($this->orders['theme']['key'],  $this->orders['theme']['order']);
        $themes=$data->queryDB("select");
        while($theme=$themes->fetch_array()){
            $id=$theme['tema_id'];
            $name=$theme['nazov_temy'];
            $this->themes[$id]['id']=$id;
            $this->themes[$id]['name']=$name;
        }
        
        $data->setTable("Rubrika");
        $data->setOrder($this->orders['topic']['key'],  $this->orders['topic']['order']);
        $topics=$data->queryDB("select");
        while($topic=$topics->fetch_array()){
            $id=$topic['rubrika_id'];
            $name=$topic['nazov_rubriky'];
            $theme=$topic['tema_id'];
            $this->topics[$id]['id']=$id;
            $this->topics[$id]['name']=$name;
            $this->topics[$id]['theme']=$theme;
        }
    }
    /*
     * Zobrazovacie metody
     */
    private function displayTopics(){
        $id= $this->getProgramID();
        //ziska parametre pre funkciu zodpovednu za zoradenie zaznamov
        $theme_key="";
        $class="";
        if(!strcmp($this->orders['theme']['key'], "tema_id"))
                $theme_key="id";
        else if(!strcmp($this->orders['theme']['key'], "nazov_temy"))
                $theme_key="name";
        $theme_order=$this->orders['theme']['order'];
        
        $topic_key="";
        if(!strcmp($this->orders['topic']['key'], "rubrika_id"))
                $topic_key="id";
        else if(!strcmp($this->orders['topic']['key'], "nazov_rubriky"))
                $topic_key="name";
        else if(!strcmp($this->orders['topic']['key'], "tema_id"))
                $topic_key="theme";
        $topic_order=$this->orders['topic']['order'];
        
        //zobrazi tabulku
        echo "<table id='topic-display' class='table-display' cellspacing='0' cellpadding='0'>";
        echo "<tr>";
        //ak je dane pole nastavene ako pole zoradenia a uzivatel klikne na hlavicku, zoradi sa pole opacnym smerom
        if(!strcmp($topic_key, "id")){
            if(!strcmp($topic_order, "ASC"))
                    $topic_order="DESC";
            else
                $topic_order="ASC";
            $class="class='ordered'";
        }  
        //nastavi funkciu pre zoradienie zaznamov
       $func="id:$id, theme_key:'$theme_key', theme_order:'$theme_order', topic_key:'id', topic_order:'$topic_order'";
        
       echo "<th onclick=\"nastavPracovnuPlochu({".$func."})\" $class>ID</th>";
       $class='';
       if(!strcmp($topic_key, "name")){
            if(!strcmp($topic_order, "ASC"))
                    $topic_order="DESC";
            else
                $topic_order="ASC";
            $class="class='ordered'";
        }       
        $func="id:$id, theme_key:'$theme_key', theme_order:'$theme_order', topic_key:'name', topic_order:'$topic_order'";
        echo "<th onclick=\"nastavPracovnuPlochu({".$func."})\" $class>Názov rubriky</th>";
        $class='';
        if(!strcmp($topic_key, "theme")){
            if(!strcmp($topic_order, "ASC"))
                    $topic_order="DESC";
            else
                $topic_order="ASC";
            $class="class='ordered'";
        }       
        $func="id:$id, theme_key:'$theme_key', theme_order:'$theme_order', topic_key:'theme', topic_order:'$topic_order'";
        
        
        echo "<th onclick=\"nastavPracovnuPlochu({".$func."})\" $class>Názov témy</th>";
        echo "</tr>";
        foreach($this->topics as $topic){
            echo "<tr>";
            echo "<td>".$topic['id']."</td>";
            echo "<td>".$topic['name']."</td>";
            echo "<td>".$this->themes[$topic['theme']]['name']."</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    private function displayThemes(){
        $id= $this->getProgramID();
        $theme_key="";
        $class="";
        //nastavi hodnoty pre zoradenie zaznamov
        if(!strcmp($this->orders['theme']['key'], "tema_id"))
                $theme_key="id";
        else if(!strcmp($this->orders['theme']['key'], "nazov_temy"))
                $theme_key="name";
        $theme_order=$this->orders['theme']['order'];
        
        $topic_key="";
        if(!strcmp($this->orders['topic']['key'], "rubrika_id"))
                $topic_key="id";
        else if(!strcmp($this->orders['topic']['key'], "nazov_rubriky"))
                $topic_key="name";
        else if(!strcmp($this->orders['topic']['key'], "tema_id"))
                $topic_key="theme";
        $topic_order=$this->orders['topic']['order'];
        
        //zobrazi tabulku
        echo "<table id='themes-display' class='table-display' cellspacing='0' cellpadding='0'>";
        echo "<tr>";
        //nastavi parametre funkcie pre zoradenie zaznamov
        if(!strcmp($theme_key, "id")){
            if(!strcmp($theme_order, "ASC"))
                    $theme_order="DESC";
            else
                $theme_order="ASC";
            $class="class='ordered'";
        }       
        $func="id:$id, theme_key:'id', theme_order:'$theme_order', topic_key:'$topic_key', topic_order:'$topic_order'";
        
        echo "<th onclick=\"nastavPracovnuPlochu({".$func."})\" $class>ID</th>";
        $class='';
        if(!strcmp($theme_key, "name")){
            if(!strcmp($theme_order, "ASC"))
                    $theme_order="DESC";
            else
                $theme_order="ASC";
            $class="class='ordered'";
        }       
        $func="id:$id, theme_key:'name', theme_order:'$theme_order', topic_key:'$topic_key', topic_order:'$topic_order'";
        
        
        echo "<th onclick=\"nastavPracovnuPlochu({".$func."})\" $class>Názov témy</th>";
        $class='';
        echo "</tr>";
        foreach($this->themes as $theme){
            echo "<tr>";
            echo "<td>".$theme['id']."</td>";
            echo "<td>".$theme['name']."</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    //zobrazi styly
    private function styles(){
        echo "<script type='text/javascript'>";
        echo "scriptloader.load_script('redakcia/styles/topics.css','css');";
        echo "</script>";
    }
}

?>
