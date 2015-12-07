<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Topic
 *
 * @author mato
 */
class Topic extends DisplayModule {
    //put your code here
    private $articles;
    private $topic;
    private $num_of_pages;
    private $num_on_page;
    private $page;
    private $home;
    
    public function __construct(CDatabaza $db) {
        parent::__construct($db);
        if(empty($_GET['rubrika']))
            $this->exit_program ();
        $this->page=0;
        if(!empty($_GET['strana']))
            $this->page=$_GET['strana']-1;
        
        $this->num_on_page=10;
        
        $topic=$db->escape_string($_GET['rubrika']);
        
        $sql="SELECT * FROM Rubrika WHERE rubrika_id=".$topic;
        $topics=$db->query($sql);
        $topic_data=$topics->fetch_array();
        if(!$topic_data){
            $this->exit_program();
        }
        $this->topic=$topic_data['nazov_rubriky'];
        
        
        
        $sql="SELECT COUNT(clanok_id) AS num_pages FROM Clanok WHERE Clanok.zobrazit=1 AND Clanok.rubrika_id=".$topic;
        $num_pages=$db->query($sql)->fetch_array();
        $this->num_of_pages=  ceil($num_pages['num_pages']/$this->num_on_page);
        if($this->page<0)
            $this->page=0;
        if($this->page>$this->num_of_pages)
            $this->page=  $this->num_of_pages-1;
        $start_record=  $this->page*$this->num_on_page;
        $sql="SELECT
                Clanok.clanok_id AS id,
                Clanok.nazov_clanku AS name,
                Clanok.casova_znamka AS timestamp,
                Clanok.typ_clanku_id AS type
              FROM
                Clanok
              WHERE
                Clanok.zobrazit=1 AND Clanok.rubrika_id=".$topic."
              ORDER BY Clanok.casova_znamka DESC
              LIMIT $start_record,$this->num_on_page";
        
        $articles=$db->query($sql);
        while($art=$articles->fetch_array()){
            $id=$art['id'];
            $this->articles[$id]['id']=$id;
            $this->articles[$id]['name']=$art['name'];
            $this->articles[$id]['type']=$art['type'];
            $this->articles[$id]['day']=date("j.n.Y",$art['timestamp']);
            if($art['type']==3)
                $this->readPost ($db,$id);
        }
        
        
        
    }
    public function display() {
        $this->navigator();
        
        if(empty($this->articles))
            echo "V danej rubrike sa nenachádzajú články";
        else{
            echo "<div class='articles_layout'>";
   
            foreach($this->articles as $art){
                echo "<div class='article_desc_wrapper'>";
                $this->display_article($art['id']);
                echo "</div>";
            }
        
            echo "</div>";
        }
        $this->navigator();
    }

    public function label() {
        return $this->topic;
    }
    
    public function setHome($home){
        $this->home=$home;
    }
    
    private function display_article($id){
        $type=  $this->articles[$id]['type'];
        //$path=  $this->home+"/articles/$id/$id.art";
        
        
        switch($type){
            case 1:
                $this->display_story($id);
                break;
            case 2:
                $this->display_interview($id);
                break;
             case 3:
                $this->display_post($id);
                break;
            case 4:
                $this->display_quiz($id);
                break;
           
        }
    }
    private function display_story($id){
        $path=  $this->home."/articles/$id/$id.art";
        $text="";
        $file=fopen($path,'r');
        $words=0;
        if($file){
            while(!feof($file)){
                $temp=  fgetss($file);
                $temp= htmlspecialchars_decode($temp);
                //$temp=  html_entity_decode($temp);
                $temp=preg_replace("/&nbsp/", "", $temp);
                $temp=preg_replace("/&asymp/", "", $temp);
                $temp=preg_replace("/&scaron/", "", $temp);
                $temp=preg_replace("/[\n\s;]+/", " ", $temp);
                $text.=$temp;
                $words+=  str_word_count($temp);
                if($words>50)
                    break;
            }
            fclose($file);
        }
        /*
        echo "<span class='article_name'>".$this->articles[$id]['name']."</span></br>";  
        echo "<span class='article_type'>(Reportáž)</span>";
        echo "<span class='article_date'>".$this->articles[$id]['day']."</span></br>";
        echo "<span class='article_description'>".trim($text)." ...[viac]</span></br>";
         */
        $this->display_article_desc($id, "Reportáž", trim($text));
        
    }
    
    private function display_interview($id){
        $path=  $this->home."/articles/$id/$id.art";
        $text="";
        $xml=  new SimpleXMLElement($path,0,true);
        
        $text.=$xml->person->name." ".$xml->person->surname.": ";
        
        $temp=$xml->person->biography;
        $interrupt=false;
        while($i=stripos($temp, " ")){
            $text.=substr($temp, 0,$i+1);
            $temp=substr($temp,$i+1);
            if(str_word_count($text)>50){
                $interrupt=true;
                break;
            }
        }
        if(!$interrupt){
            $text.=$temp;
        }
        /*
        echo "<span class='article_name'>".$this->articles[$id]['name']."</span></br>";
        echo "<span class='article_type'>(Rozhovor)</span>";
        echo "<span class='article_date'>".$this->articles[$id]['day']."</span></br>";
        echo "<span class='article_description'>".trim($text)." ...[viac]</span></br>";
        */
        $this->display_article_desc($id, "Rozhovor", trim($text));
    }
    
    private function display_quiz($id){
        $path=  $this->home."/articles/$id/$id.art";
        $text="";
        $xml=  new SimpleXMLElement($path,0,true);
        
        
        $temp=$xml->info->description;
        $interrupt=false;
        while($i=stripos($temp, " ")){
            $text.=substr($temp, 0,$i+1);
            $temp=substr($temp,$i+1);
            if(str_word_count($text)>50){
                $interrupt=true;
                break;
            }
        }
        if(!$interrupt){
            $text.=$temp;
        }
        /*
        echo "<span class='article_name'>".$this->articles[$id]['name']."</span></br>";
        echo "<span class='article_type'>(Kvíz)</span>";
        echo "<span class='article_date'>".$this->articles[$id]['day']."</span></br>";
        echo "<span class='article_description'>".trim($text)." ...[viac]</span></br>";
        */
        $this->display_article_desc($id, "Kvíz", trim($text));
    }
    
    private function display_post($id){
        /*
        echo "<span class='article_name'>".$this->articles[$id]['name']."</span></br>";
        echo "<span class='article_type'>(Príspevok)</span>";
        echo "<span class='article_date'>".$this->articles[$id]['day']."</span></br>";
        echo "<span class='article_description'>".$this->articles[$id]['post']." ...[viac]</span></br>";
         * 
         */
        $this->display_article_desc($id, "Príspevok", $this->articles[$id]['post']);
        
    }
    
    private function display_article_desc($id,$type,$text){
        $href="./?clanok=$id";
        echo "<span class='article_name'><a href='$href'>".$this->articles[$id]['name']."</a></span></br>";
        echo "<span class='article_type'>($type)</span>";
        echo "<span class='article_date'>".$this->articles[$id]['day']."</span></br>";
        echo "<span class='article_description'>".$text." ...<a href='$href'>[viac]</a></span></br>";
    }
    private function strip_html_tags( $text ){
        $text = preg_replace(
            array(
                // Remove invisible content
                '@<head[^>]*?>.*?</head>@siu',
                '@<style[^>]*?>.*?</style>@siu',
                '@<script[^>]*?.*?</script>@siu',
                '@<object[^>]*?.*?</object>@siu',
                '@<embed[^>]*?.*?</embed>@siu',
                '@<applet[^>]*?.*?</applet>@siu',
                '@<noframes[^>]*?.*?</noframes>@siu',
                '@<noscript[^>]*?.*?</noscript>@siu',
                '@<noembed[^>]*?.*?</noembed>@siu',
                // Add line breaks before and after blocks
                '@</?((address)|(blockquote)|(center)|(del))@iu',
                '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
                '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
                '@</?((table)|(th)|(td)|(caption))@iu',
                '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
                '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
                '@</?((frameset)|(frame)|(iframe))@iu',
            ),
            array(
                ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
                "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
                "\n\$0", "\n\$0",
            ),
                $text );
    return strip_tags( $text );
    }

    private function readPost(CDatabaza $db,$id){
        $sql="SELECT prispevok FROM Prispevok WHERE clanok_id=$id ORDER BY casova_znamka DESC LIMIT 0,1";
        $query=$db->query($sql);
        $post=$query->fetch_array();
        $this->articles[$id]['post']=$post['prispevok'];
    }
    
    private function navigator(){
        echo "<div class='nav_wrapper'><ul class='page_lister'>";
        echo "<li><div class='nav_item first_page'></div></li>";
        echo "<li><div class='nav_item prev_page'></div></li>";
        $first_page=1;
        $last_page=  $this->num_of_pages;
        if($this->num_of_pages>7){
            if($this->page>3&&$this->page<$this->num_of_pages-3){
                $first_page=  $this->page-3;
                $last_page=  $this->page+3;
            }
            else if($this->page<3)
                $last_page=7;
          
            else if($this->page>$this->num_of_pages-3)
                $first_page=  $this->num_of_pages-6;
            
        }
        for($i=$first_page;$i<=$last_page;$i++){
            echo "<li><div class='nav_item nav_page'>$i</div></li>";
        }
        echo "<li><div class='nav_item next_page'></div></li>";
        echo "<li><div class='nav_item last_page'></div></li>";
        echo "</ul></div>";
    }
}

?>
