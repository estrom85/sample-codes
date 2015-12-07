<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Article_interview
 *
 * @author mato
 */
ProgramManager::includeProgram("Article");
class Article_interview extends Article{
    /*
     * Parametre triedy
     */
    private $intreview_data;
    /*
     * Konstruktor
     */
    public function __construct() {
        parent::__construct();
        //$this->createArticle(true);
        $this->setFunction("set_image", "set_image");
        $this->setForm("set_image", "Nastav obrázok", "set_image", "set_image_form");
        
        $this->setFunction("edit_info", "edit_info");
        
        $this->setFunction("edit_quest", "edit_question");
        
        $this->setFunction("add_quest", "add_question");
        
        $this->setFunction("rem_quest", "remove_question");
    }
    /*
     * Implementovane funkcie triedy
     */
    protected function getProgramID() {
        return ProgramManager::getId("Article_interview");
    }
    
    public function display(){

        if(!empty($_GET['mode'])){
            if(!strcmp($_GET['mode'],'question')){
                $this->displayQuestion ($_GET['quest_id']);
                return;
            }
            else if(!strcmp($_GET['mode'],'info')){
                $this->displayInfo ();
                return;
            }
        }
            $this->displayInterview ();
       
    }
    
    public function toolbox(){
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
            echo "<hr>";
            $this->displayFormButton("set_image", "Nastav obrázok");
            $this->displayActionButton("add_quest", "Pridaj otázku", "", true, true, "Chcete pridať otázku");
            
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
     * Zakladne rozhranie triedy
     */
    protected function set_image(){
        /*
        if(empty($_POST['img_url'])){
            $this->setMsg(false, "Vložte prosím url obrázku");
            return;
        }

         */
        if(empty($this->intreview_data))
            $this->setData ();
        $this->intreview_data->info->img=$_POST['img_url'];
        if($this->saveInterview())
            $this->setMsg (true, "Obrázok úspešne vložený");
        else
            $this->setMsg (false, "Nepodarilo sa vložiť obrázok");
    }
    protected function set_image_form(){
        echo "<div>";
        $form_id=  $this->getFormID("set_image");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Zadaj URL obrázka");
        $form->addInputField("text", "img_url");
        $form->registerForm();
        $form->showForm();
        echo "</div>";
        
        echo "<div id='browse_button'><button onclick=\"window.open('redakcia/utilities/browser/browser.php?article_id=".$this->article['id']."&target=img_url','image_browser','width=500,height=250')\">Prehľadávaj...</button></div>";
    }
    
    protected function edit_info(){
        if(empty($_GET['field']))
            return;
        $field=$_GET['field'];
        $value="";
        if(!empty($_POST['value']))
            $value=$_POST['value'];
        
        if(empty($this->intreview_data))
            $this->setData ();
        $info=$this->intreview_data->person;
        
            if(!strcmp($field,'name'))
                    $info->name=$value;
            else if(!strcmp($field,'surname'))
                    $info->surname=$value;
            else if(!strcmp($field,'occup'))
                    $info->occupation=$value;
            else if(!strcmp($field,'date'))
                    $info->birth_date=$value;
            else if(!strcmp($field,'place'))
                    $info->birth_place=$value;
            else if(!strcmp($field,'bio'))
                    $info->biography=$value;
        $this->saveInterview();
        
    }
    
    protected function edit_question(){
        if(empty($_POST['question'])){
            $this->setMsg(false, "Nebola zadaná otázka");
            return;
        }
        if(empty($_POST['answer'])){
            $this->setMsg(false, "Nebola zadaná odpoveď");
            return;
        }
        if(!isset($_GET['quest_id'])){
            $this->setMsg(false, "Nepovolená akcia");
            return;
        }
        
        if(empty($this->intreview_data))
            $this->setData ();
        $info=$this->intreview_data->person;
        
        if($this->editQuestion($_GET['quest_id'],$_POST['question'],$_POST['answer']))
            $this->setMsg (true, "Otázka úspešne zmenená");
        else
            $this->setMsg (false, "Nepodarilo sa zmeniť otázku");
        $this->saveInterview();
    }
    
    protected function add_question(){
        $this->addQuestion("Zadaj otázku", "Zadaj odpoveď");
        $this->setMsg(true, "Otázka bola pridaná");
        $this->saveInterview();
    }
    
    protected function remove_question(){
        $this->removeQuestion($_POST['quest_id']);
        $this->setMsg(true, "Otázka bola odstránená");
        $this->saveInterview();
    }
    /*
     * Sukromne metody triedy
     */
    private function setData(){
        $file_path=  $this->path."/".$this->article['id'].".art";
        if(!file_exists($file_path))
            $this->createArticle ();
        $this->intreview_data=new SimpleXMLElement($file_path,0,true);
    }
    
    private function createArticle($force=false){
        $file_path=  $this->path."/".$this->article['id'].".art";
        
        
        if(file_exists($file_path)&&!$force)
            return false;
        $xml=
           "<?xml version='1.0' encoding='UTF-8'?>
            <article>
               <info>
                    <id>".$this->article['id']."</id>
                    <type>Rozhovor</type>
                    <img></img>
               </info>
               <person>
                    <name></name>
                    <surname></surname>
                    <birth_date></birth_date>
                    <birth_place></birth_place>
                    <occupation></occupation>
                    <biography></biography>
               </person>
               <questions>
               </questions>
            </article>";
        
        $file=  fopen($file_path, 'w');
        if($file){
            fwrite($file, $xml);
            fclose($file);
            chmod($file_path, 0777);
        }
        else
            return false;
        
        return true;
    }
    
    private function editPersonInfo($info,$value){
        if(empty($this->intreview_data))
            $this->setData();
        $xml=  $this->intreview_data->info;
        if(!isset($xml->$info))
            return;
        $xml->$info=$value;
    }
    
    private function addQuestion($question,$answer){
        if(empty($this->intreview_data))
            $this->setData();
        $xml=  $this->intreview_data->questions;
        
        $id=-1;
        if(!empty($xml->question))
            foreach($xml->question as $quest)
                $id=$quest->id;
        $id+=1;
        $quest=$xml->addChild("question");
        $quest->addChild("id",$id);
        $quest->addChild("quest",$question);
        $quest->addChild("answer",$answer);
    }
   private function removeQuestion($id){
       if(empty($this->intreview_data))
            $this->setData();
        $xml=  $this->intreview_data->questions;
        
        if(!empty($xml->question))
            foreach($xml->question as $quest)
                if($quest->id==$id){
                    $dom=  dom_import_simplexml($quest);
                    $dom->parentNode->removeChild($dom);
                    return;
                }
   }
    private function editQuestion($id,$question,$answer){
        if(empty($this->intreview_data))
            $this->setData();
        $xml=  $this->intreview_data->questions;
        
        if(empty($xml->question))
            return false;
        foreach($xml->question as $quest){
            if($quest->id==$id){
                $quest->quest=$question;
                $quest->answer=$answer;
                return true;
            }
        }
        return false;
        
    }

    private function saveInterview(){
        if(empty($this->intreview_data))
            return;
        $file_path=  $this->path."/".$this->article['id'].".art";
        $this->setTimeStamp();
        return $this->intreview_data->asXML($file_path);
        
    }
    
    private function displayQuestion($id){
        if(empty($this->intreview_data))
            $this->setData ();
        $questions=  $this->intreview_data->questions;
        
        foreach($questions->question as $question){
            if($question->id==$id){    
                echo "<br/><span id='quest_$question->id' class='question'>$question->quest</span><br/>";
                echo "<span id='answer_$question->id' class='answer'>$question->answer</span>";
                if(!$this->article['zobrazit']){
                    $action="redakcia/request/action.php?id=".$this->getProgramID()."&article_id=".$this->article['id']."&func=edit_quest&quest_id=$question->id";
                    $display="redakcia/request/main.php?id=".$this->getProgramID()."&article_id=".$this->article['id']."&mode=question&quest_id=$question->id"; 
                    echo "<br/><span onclick=\"$('#question_$question->id').editQuestion('$action','$display');\" class='edit_button'> [upraviť]</span>";
                    $function="potvrdASpustiProgram(".$this->getProgramID().",'rem_quest',{article_id:'".$this->article['id']."',quest_id:'".$question->id."'},'Chcete odstrániť otázku?');";
                    echo " <span onclick=\"$function;\" class='edit_button'> [odstrániť]</span>";
                }
            }
        }
    }
    private function displayInfo(){
        if(empty($this->intreview_data))
            $this->setData ();
        $info=$this->intreview_data->person;
        
        if(!empty($_GET['field'])){
            $field=$_GET['field'];
            if(!strcmp($field,'name'))
                    echo $info->name;
            else if(!strcmp($field,'surname'))
                    echo $info->surname;
            else if(!strcmp($field,'occup'))
                    echo $info->occupation;
            else if(!strcmp($field,'date'))
                    echo $info->birth_date;
            else if(!strcmp($field,'place'))
                    echo $info->birth_place;
            else if(!strcmp($field,'bio'))
                    echo $info->biography;
        }
       
    }
    private function displayInterview(){
        parent::display();
         if(empty($this->intreview_data))
            $this->setData ();
        $info=$this->intreview_data->info;
        echo "<script type='text/javascript'>
            scriptloader.load_script('redakcia/scripts/interview.js','js');
            scriptloader.load_script('redakcia/styles/interview.css','css');
                </script>";
        echo "<div id='interview_info' style='text-align:left;min-height:170px'>";
        
        echo "<span id='foto' style='position:relative;width:150px;height:150px;background-color:grey;font-size:16px;text-align:center;";
        echo "float:left;vertical-align:middle;font-weight:bold;margin:5px;margin-right:15px;'>".((empty($info->img))?"Foto":"")."</span>";
        if(!empty($info->img))
            echo "<style>#foto{background:url('$info->img');background-size:cover;background-position:center}</style>";
           
        
        $info=$this->intreview_data->person;
        echo "<span class=info_label>Meno: </span><span id='info_name'>$info->name</span> ".$this->displayEditButton("info_name","name")."<br/>";
        echo "<span class=info_label>Priezvisko: </span><span id='info_surname'>$info->surname</span> ".$this->displayEditButton("info_surname","surname")."<br/>";
        echo "<span class=info_label>Dátum narodenia: </span><span id='info_date'>$info->birth_date</span> ".$this->displayEditButton("info_date", "date")."<br/>";
        echo "<span class=info_label>Miesto narodenia: </span><span id='info_place'>$info->birth_place</span> ".$this->displayEditButton("info_place", "place")."<br/>";
        echo "<span class=info_label>Povolanie: </span><span id='info_occup'>$info->occupation</span> ".$this->displayEditButton("info_occup", "occup")."<br/>";
        echo "<span class=info_label>Biografia: </span><span id='info_bio'>$info->biography</span> ".$this->displayEditButton("info_bio", "bio", true);
        echo "</div>";
        
        echo "<div id='interview_questions'>";
        echo "<span class='info_label'>Otázky:</span><br/>";
        $questions=  $this->intreview_data->questions;
        foreach($questions->question as $question){
            echo "<div id='question_$question->id' style='text-align:left'>";
            $this->displayQuestion ($question->id);
            echo "</div>";
        }
        echo "</div>";
    }
    private function displayEditButton($id,$field,$text_area=false){
        if($this->article['zobrazit'])
            return;
        $action_base="redakcia/request/action.php?id=".$this->getProgramID()."&article_id=".$this->article['id']."&func=edit_info";
        $display_base="redakcia/request/main.php?id=".$this->getProgramID()."&article_id=".$this->article['id']."&mode=info"; 
        
        $action=$action_base."&field=$field";
        $display=$display_base."&field=$field";
        
        $function="$('#$id').editField('$action','$display'".($text_area?",true":"").")";
        return "<span onclick=\"$function;$(this).hide();\" class='edit_button'>[upraviť]</span>";
    }
}

?>
