<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Art_interview
 *
 * @author mato
 */
class Art_interview implements display{
    //put your code here
    private $data;
    public function __construct($id,$home) {
        $path=$home."/articles/$id/$id.art";
        $this->data=new SimpleXMLElement($path,0,true);
    }
    public function display() {
        $info=  $this->data->info;
        echo "<hr/>";
        echo "<div id='interview_info'>";
        if(!empty($info->img)){
           echo "<span id='interview_foto'></span>";
            echo "<style>#interview_foto{background:url('$info->img');background-size:cover;background-position:center}</style>";
        }
        $info=  $this->data->person;
        echo "<span id='interview_data_wrapper'>";
        echo "<table>";
        $this->display_info("Meno a priezvisko", "$info->name $info->surname");
        if(!empty($info->birth_date))
            $this->display_info ("Dátum narodenia", $info->birth_date);
        if(!empty($info->birth_place))
            $this->display_info ("Miesto narodenia", $info->birth_place);
        if(!empty($info->occupation))
            $this->display_info ("Povolanie", $info->occupation);
        echo "</table>";
        echo "<br/><span class='interview_label'>Biografia: </span></br>";
        echo "<span class='interview_data'>$info->biography</span></br>";
        echo "</span>";
        echo "</div>";
        echo "<hr/>";
        echo "<div id='question_wrapper'>";
        foreach($this->data->questions->question as $quest){
        echo "<div class='interview_question_wrapper'>";
        echo "<span class='interview_question'>".$quest->quest."</span><br/>";
        echo "<span class='interview_answer'>".$quest->answer."</span>";
        echo "</div>";
        }
        echo "</div>";
    }
    
    
    public function label() {
        
    }
    
    private function display_info($type,$data){
        echo "<tr>";
        echo "<td><span class='interview_label'>$type: </span></td>";
        echo "<td><span class='interview_data'>$data</span></td>";
        echo "</tr>";
    }
}

?>
