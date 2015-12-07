<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Article_quiz
 *
 * @author mato
 */
ProgramManager::includeProgram("Article");
class Article_quiz extends Article {
    /*
     * Parametre triedy
     */
    private $quiz_data;
    /*
     * Konstruktor
     */
    public function __construct() {
        parent::__construct();
        
        $this->setFunction("edit_quest", "edit_question");
        
        $this->setFunction("add_quest", "add_question");
        
        $this->setFunction("rem_quest", "remove_question");
        
        $this->setFunction("edit_desc", "edit_description");
        $this->setForm("edit_desc", "", "edit_desc", "edit_description_form");
        
        $this->setFunction("set_image", "set_image");
        $this->setForm("set_image", "Nastav obrázok", "set_image", "set_image_form");
    }
    /*
     * Implementovane metody triedy
     */
    protected function getProgramID() {
        return ProgramManager::getId("Article_quiz");
    }
    
    public function display(){
        if(!empty($_GET['mode']))
            if(!strcmp($_GET['mode'],"question")){
                $this->displayQuestion ($_GET['quest']);
                
                return;
            }
            
            $this->displayQuiz();
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
              
            echo "<hr/>";
            $this->displayFormButton("set_image", "Nastav obrázok");
            $this->displayFormButton("edit_desc", "Nastav popis kvízu");
            
            echo "<hr/>";
            $this->displayActionButton("add_quest", "Pridaj otázku", "", true, true, "Chcete pridať otázku?");
            
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
    protected function edit_question(){
        //print_r($_POST);
        
        $id=$_POST['quest_id'];
        $quest=$_POST['quest'];
        
        $this->setQuestion($id, $quest);
        $this->setAnswer ($id, "");
        $this->removeOptions($id);
        $answer=-1;
        if(!empty($_POST['answer']))
            $answer=$_POST['answer'];
        if(!empty($_POST['opt_id']))
            foreach($_POST['opt_id'] as $key=>$opt_id){
                $option=  $this->addOption($id,$_POST['opt_desc'][$key]);
                if($opt_id==$answer){
                    $this->setAnswer ($id, $option);
                }
            
            }
        $this->saveQuiz(); 
    }
    
    protected function add_question(){
        $this->addQuestion();
        $this->saveQuiz();
    }
    
    protected function remove_question(){
        if(empty($_POST['quest_id']))
            return;
        $this->removeQuestion($_POST['quest_id']);
        $this->resetQuestIds();
        $this->saveQuiz();
    }
    
    protected function edit_description(){
        if(empty($_POST['desc'])){
            $this->setMsg(false, "Zadaj popis");
            return;
        }
        $this->setData();
        $this->quiz_data->info->description=$_POST['desc'];
        $this->saveQuiz();
        $this->setMsg(true, "Popis zmeneny");
    }
    
    protected function edit_description_form(){
        $this->setData();
        $id=$this->getFormID("edit_desc");
        $form=new FormMaker();
        $form->setID($id);
        $form->addLabel("Zadaj popis");
        $form->addTextArea("desc");
        $form->setValue("desc", $this->quiz_data->info->description);
        $form->registerForm();
        $form->showForm();
    }
    
    protected function set_image(){
        /*
        if(empty($_POST['img_url'])){
            $this->setMsg(false, "Vložte prosím url obrázku");
            return;
        }

         */
        if(empty($this->intreview_data))
            $this->setData ();
        $this->quiz_data->info->img=$_POST['img_url'];
        if($this->saveQuiz())
            $this->setMsg (true, "Obrázok úspešne vložený");
        else
            $this->setMsg (false, "Nepodarilo sa vložiť obrázok");
    }
    protected function set_image_form(){
        $form_id=  $this->getFormID("set_image");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Zadaj URL obrázka");
        $form->addInputField("text", "img_url");
        $form->registerForm();
        $form->showForm();
        echo "<div id='browse_button'><button onclick=\"window.open('redakcia/utilities/browser/browser.php?article_id=".$this->article['id']."&target=img_url','image_browser','width=1000,height=500')\">Prehľadávaj...</button></div>";
    }
    /*
     * Sukromne metody triedy
     */
    private function setData(){
        if(!empty($this->quiz_data))
            return;
        $path=  $this->path."/".$this->article['id'].".art";
        
        if(!file_exists($path))
            $this->createQuiz ();
        $this->quiz_data=new SimpleXMLElement($path,0,true);
    }
    
    private function createQuiz($force=false){
        $path=  $this->path."/".$this->article['id'].".art";
        if(file_exists($path)&&!$force)
            return;
        
        $xml=
            "<?xml version='1.0' encoding='UTF-8'?>
            <article>
               <info>
                  <id>".$this->article['id']."</id>
                  <type>Kviz</type>
                  <img></img>
                  <description></description>
               </info>
               <quiz>
               </quiz>
            </article>";
        
        $file=fopen($path,'w');
        if($file){
            fwrite($file, $xml);
            fclose($file);
            chmod($path,0777);
        }
        else
            return false;
        return true;

    }
    
    private function saveQuiz(){
        if(empty($this->quiz_data))
            return;
        $file_path=  $this->path."/".$this->article['id'].".art";
        $this->setTimeStamp();
        return $this->quiz_data->asXML($file_path);
    }
    
    //quiz manipulation method
    private function addQuestion(){
        $this->setData();
        
        $quiz=  $this->quiz_data->quiz;
        $id=0;
        if(!empty($quiz))
            foreach($quiz->question as $question)
                $id=$question->id;
        $id+=1;
        $question=$quiz->addChild("question");
        $question->addChild("id",$id);
        $question->addChild("answer");
        $question->addChild("quest","Zadaj otázku");
        return $id;
    }
    
    private function removeQuestion($id){
        $this->setData();
        $xml=  $this->quiz_data->quiz;
        
        if(!empty($xml->question))
            foreach($xml->question as $quest)
                if($quest->id==$id){
                    $dom=  dom_import_simplexml($quest);
                    $dom->parentNode->removeChild($dom);
                    return;
                }
    }
    
    private function setQuestion($id,$quest){
        $this->setData();
        
        $quiz=  $this->quiz_data->quiz;
        if(!empty($quiz))
            foreach($quiz->question as $question)
                if($id==$question->id){
                    $question->quest=$quest;
                    return true;
                }
        return false;
    }
    
    private function addOption($id,$option){
        $this->setData(); 
        $quiz=  $this->quiz_data->quiz;
        if(!empty($quiz))
            foreach($quiz->question as $question)
                if($id==$question->id){
                    $opt_id=0;
                    if(!empty($question->option))
                        foreach($question->option as $opt)
                            $opt_id=$opt['id'];
                    $opt_id+=1;
                    $opt=$question->addChild("option",$option);
                    $opt->addAttribute("id",$opt_id);
                    return $opt_id;
                }
        return false;
    }
    
    private function removeOptions($id){
        $this->setData();     
        $quiz=  $this->quiz_data->quiz;
        
        if(!empty($quiz))  
            foreach($quiz->question as $key=>$question)
                if($id==$question->id){
                    if(!empty($question->option))
                        foreach($question->option as $opt)
                            $dom[]=  dom_import_simplexml($opt);
                    if(!empty($dom))
                     foreach($dom as $item)
                         $item->parentNode->removeChild($item);
                    return true;
                }
        return false;
    }
    
    private function editOption($quest_id,$opt_id,$option){
        $this->setData();
        
        $quiz=  $this->quiz_data->quiz;
        if(!empty($quiz))
            foreach($quiz->question as $question)
                if($quest_id==$question['id']){;
                    if(!empty($question))
                        foreach($question->option as $opt)
                            if($opt_id==$opt['id']){
                                $opt=$option;
                                return;
                            }
                }
        return false;
    }
    
    private function setAnswer($quest_id,$opt_id){
        $this->setData();
        
        $quiz=  $this->quiz_data->quiz;
        if(!empty($quiz->question))
            foreach($quiz->question as $question)
                if($quest_id==$question->id){
                    $question->answer=$opt_id;
                    return true;
                }
        return false;
    }
    
    private function resetQuestIds(){
        $this->setData();
        $quiz=  $this->quiz_data->quiz;
        $id=1;
        if(!empty($quiz->question))
            foreach($quiz->question as $question){
                $question->id=$id;
                $id+=1;
            }

    }
    
    //display methods
    private function displayQuestion($id){
        $this->setData();
        if(!empty($this->quiz_data->quiz))
            foreach($this->quiz_data->quiz->question as $question)
                if($question->id==$id){
                    $answer=$question->answer;
                
                    echo "<div class='quest_question_desc'><span class='quest_id'>$id</span>. <span class='quiz_quest'>".preg_replace("/\n/", "<br>", $question->quest)."</span></div>";
                    echo "<span class='quiz_answer'>".$answer."</span>";
                    echo "<div class='quest_answers'>";
                    if(!empty($question->option))
                        foreach($question->option as $option){
                            $class="";
                            if((string)$option['id']==$answer)
                                $class="class='right_answer'";
                            echo "<div $class><span class='opt_id'>".$option['id']."</span>. <span class='opt_desc'>$option</span></div>";  
                        }
                    echo "</div>";
                    if(!$this->article['zobrazit']){
                        echo "<span class='edit_button' onclick=\"$('#quest_$id').editQuizQuestion(".$this->getProgramID().",".$this->article['id'].");\">[upraviť]</span>";
                
                        $function="potvrdASpustiProgram(".$this->getProgramID().",'rem_quest',{article_id:'".$this->article['id']."',quest_id:'".$question->id."'},'Chcete odstrániť otázku?');";
                        echo " <span class='edit_button'  onclick=\"$function;\" class='edit_button'> [odstrániť]</span>";
                    }
                }
    }
    
    private function displayQuiz(){
         parent::display();
        $this->setData();
        echo "<script type='text/javascript'>scriptloader.load_script('redakcia/styles/quiz.css','css');</script>";
        echo "<script type='text/javascript'>scriptloader.load_script('redakcia/scripts/quiz.js','js');</script>";
        echo "<div id='quiz_info' style='text-align:left;height:170px;'>";
        $info=  $this->quiz_data->info;
        echo "<span id='foto' style='position:relative;width:150px;height:150px;background-color:grey;font-size:16px;text-align:center;";
        echo "float:left;vertical-align:middle;font-weight:bold;margin:5px;margin-right:15px;'>".((empty($info->img))?"Foto":"")."</span>";
        if(!empty($info->img))
            echo "<style>#foto{background:url('$info->img');background-size:cover;background-position:center}</style>";
       
        echo "<span class='quiz_info_label'>Popis:</span><br/>";
        echo "<span id='quiz_desc'>".$this->quiz_data->info->description."</span></br>";
        echo "</div>";
        echo "<div id='quiz_questions' style='text-align:left'>";
        echo "<span class='quiz_info_label'>Otázky:</span><br/><br/>";
        $quest_on_page=3;
        $num_of_pages=ceil($this->quiz_data->quiz->question->count()/$quest_on_page);
        
        if(!empty($this->quiz_data->quiz)){
            echo "<div id='quiz_question_wrapper'>";
            echo "<ul>";
            for($i=1;$i<=$num_of_pages;$i++)
                echo "<li><a href='#quest_page_$i'>Strana $i</a></li>";
            echo "</ul>";
            $i=1;

            foreach($this->quiz_data->quiz->question as $question){
                
                $page=floor($i/$quest_on_page);
                $start=$page*$quest_on_page+1;
                $page+=1;
                
                if($i==$start){
                    if($i!=1)
                        echo "</div>";
                    echo "<div id='quest_page_$page'>";
                }
                
                $i++;
                $id=$question->id;
                echo "<div id='quest_$id' class='quest_display'>";
                $this->displayQuestion ($question->id);
                echo "</div>";
            }
            echo "</div>";
            
            echo "</div>";
        }
        echo "</div>";
        echo "<script type='text/javascript'>$('#quiz_question_wrapper').tabs();</script>";
    }
    
    
}

?>
