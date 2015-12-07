<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of News
 *
 * @author mato
 */
class News extends DisplayModule{
    //put your code here
    private $news;
    public function __construct(CDatabaza $db) {
        parent::__construct($db);
        $sql="SELECT 
            Clanok.clanok_id AS id,
            Clanok.nazov_clanku AS name,
            Typ_clanku.nazov AS type,
            Rubrika.nazov_rubriky AS topic,
            Clanok.casova_znamka AS timestamp
            FROM 
            (Clanok
            INNER JOIN
            Rubrika
            ON Clanok.rubrika_id=Rubrika.rubrika_id)
            INNER JOIN
            Typ_clanku
            ON Clanok.typ_clanku_id=Typ_clanku.typ_clanku_id
            WHERE
            Clanok.zobrazit=1
            ORDER BY Clanok.casova_znamka DESC
            LIMIT 0,10";
        
        $news_recs=$db->query($sql);
        while($news=$news_recs->fetch_array()){
            $id=$news['id'];
            $this->news[$id]['id']=$id;
            $this->news[$id]['name']=$news['name'];
            $this->news[$id]['type']=$news['type'];
            $this->news[$id]['topic']=$news['topic'];

            $this->news[$id]['day']=  date("j.n.Y", $news['timestamp']);
            $this->news[$id]['time']= date("h:i:s",$news['timestamp']);
        }
    }
    
    public function display() {
        if(!empty($this->news))
        foreach($this->news as $news){
            echo "<div class='news_item'>";
            echo "<span class='news_topic'>".$news['topic']."</span>";
            echo "<span class='news_day'>".$news['day']."</span><br/>";
            echo "<span class='news_name'><a href='./?clanok=".$news['id']."'>".$news['name']."</a></span><br/>";
            echo "<span class='news_type'>".$news['type']."</span>";
            echo "</div>";
        }
    }
    public function label() {
        
    }
}

?>
