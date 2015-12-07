<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Art_quiz
 *
 * @author mato
 */
class Art_quiz implements display {
    //put your code here
    private $data;
    private $article;
    public function __construct($id,$home) {
        $path=$home."/articles/$id/$id.art";
        $this->data=new SimpleXMLElement($path,0,true);
        $this->article=$id;
    }
    public function display() {
        echo "<div id='quiz_info'>";
        echo "<div id='quiz_description'>".$this->data->info->description."</div>";
        echo "<div id='quiz_image'><img src='".$this->data->info->img."'></div>";
        echo "<div id='quiz_start_button' onclick='set_quiz();'>Spusti kvíz</div>";
        echo "<style>#quiz_wrapper:before{background-image:url('".$this->data->info->img."');}</style>";
        echo "</div>";
        echo "<script type='text/javascript'>";
        echo "function set_quiz(){";
        echo "quiz_manager.clear();";
        echo "quiz_manager.set_article(".$this->article.");";
        //echo "quiz_manager.set_image('".$this->data->info->img."');";
        foreach($this->data->quiz->question as $question)
            echo $this->setQuestion ($question).";";
        echo "quiz_manager.create('quiz_info');}";
        echo "</script>";
    }
    public function label() {
        
    }
    public function setQuestion(SimpleXMLElement $question){
        $id=$question->id;
        $quest=  preg_replace("/\n/", "<br>", $question->quest);
        $quest= trim(preg_replace("/\s+/", " ", $quest));
        $answers="";
        foreach($question->option as $option){
            $answers.=", ".$option['id'].":'".$option."'";
        }
        $answers="{".substr($answers, 2)."}";
        $function="quiz_manager.add($id,'$quest',$answers)";
        return $function;
    }
    
    public function printAnswers(){
        $response="";
        foreach($this->data->quiz->question as $quest)
            $response.=$quest->id.":".$quest->answer." ";
        echo $response;
    }
}

?>
