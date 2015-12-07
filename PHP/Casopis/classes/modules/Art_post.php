<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Art_post
 *
 * @author mato
 */
class Art_post implements display {
    //put your code here
    private $cats;
    private $page;
    private $max_page;
    private $posts;
    private $article;
    
    public function __construct($id,  CDatabaza $db) {
        $this->article=$id;
        
        $sql="SELECT Kategoria.kategoria_id AS id, Kategoria.nazov_kategorie AS name FROM 
            (Kategoria
            INNER JOIN
            Prispevok
            ON Prispevok.kategoria_id=Kategoria.kategoria_id)
            INNER JOIN
            Clanok
            ON
            Clanok.clanok_id=Prispevok.clanok_id
            WHERE 
            Clanok.clanok_id=$id 
            AND
            Clanok.zobrazit=1
            AND
            Prispevok.zobrazit=1";
        
        $cats_data=$db->query($sql);
        if($cats_data)
            while($cats=$cats_data->fetch_array()){
                $idc=$cats['id'];
                $this->cats[$idc]['id']=$idc;
                $this->cats[$idc]['name']=$cats['name'];
            }
        else echo "nastala chyba v spojeni";
        
        $post_select="SELECT Prispevok.prispevok_id AS id,
                    Prispevok.nazov_prispevku AS name,
                    Prispevok.prispevok AS post,
                    Prispevok.casova_znamka AS timestamp,
                    Prispevok.kategoria_id AS cat";
        $num_rows="SELECT Count(*) AS num";
        
        $from_stat=" FROM
                    Prispevok
              INNER JOIN
                    Clanok
              ON Prispevok.clanok_id=Clanok.clanok_id
              WHERE
                    Clanok.zobrazit=1
              AND   
                    Clanok.clanok_id=$id
              AND
                    Prispevok.zobrazit=1";
        
        if(isset($_GET['kategoria'])){
            $cat_exists=false;
            foreach($this->cats as $cat)
                if($cat['id']=$_GET['kategoria']){
                    $cat_exists=true;
                    break;
                }
            if($cat_exists)
                $from_stat.=" AND Prispevok.kategoria_id=".$_GET['kategoria'];
            
        }

        $sql=$num_rows.$from_stat;
        
        $num_on_page=10;
        $this->max_page=  ceil($db->query($sql)->num_rows/$num_on_page);
        
        $this->page=0;
        if(empty($_GET['strana'])){
            if($_GET['strana']>0&&$_GET['strana']<=$this->max_page)
                $this->page=$_GET['strana']-1;
        }
        $sql=$post_select.$from_stat." ORDER BY Prispevok.casova_znamka DESC LIMIT ".($this->page*$num_on_page).",$num_on_page";
        $post_data=$db->query($sql);
        if($post_data){
            while($post=$post_data->fetch_array()){
                $id=$post['id'];
                $this->posts[$id]['id']=$id;
                $this->posts[$id]['name']=$post['name'];
                $this->posts[$id]['post']=$post['post'];
                $this->posts[$id]['day']=date("j.n.Y",$post['timestamp']);
                $this->posts[$id]['cat']=$post['cat'];
            }
        }
   
    }
    
    public function display() {
       echo "<ul id='cat_select'>";
       echo "<li><a href='./?clanok=$this->article'>Všetky</a></li>";
       if(!empty($this->cats))
       foreach($this->cats as $cat){
           echo"<li><a href='./?clanok=$this->article&kategoria=".$cat['id']."'>".$cat['name']."</a></li>";
       }
       echo "</ul>";
       $this->navigator();
       echo "<div id='post_wrapper'>";
       if(!empty($this->posts))
       foreach($this->posts as $post){
           echo "<div class='post_display'>";
           echo "<div class='post_time'>".$post['day']."</div>";
           echo "<div class='post_cat'>".$this->cats[$post['cat']]['name']."</div>";
           echo "<div class='post_name'>".$post['name']."</div>";
           echo "<div class='post_content'>".preg_replace("/\n/", "<br/>", $post['post'])."</div>";
           echo "</div>";
       }
       echo "</div>";
       $this->navigator();
    }
    public function label() {
        
    }
    
    private function navigator(){
        echo "<div class='nav_wrapper'><ul class='page_lister'>";
        echo "<li><div class='nav_item first_page'></div></li>";
        echo "<li><div class='nav_item prev_page'></div></li>";
        $first_page=1;
        $last_page=  $this->max_page;
        if($this->max_page>7){
            if($this->page>3&&$this->page<$this->max_page-3){
                $first_page=  $this->page-3;
                $last_page=  $this->page+3;
            }
            else if($this->page<3)
                $last_page=7;
          
            else if($this->page>$this->max_page-3)
                $first_page=  $this->max_page-6;
            
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
