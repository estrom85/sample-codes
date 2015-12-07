<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Article
 *
 * @author mato
 */
class Article_manager extends DisplayModule {
    //put your code here
    private $article;
    private $info;
    private $topic;
    private $users;
    public function __construct(CDatabaza $db, $home) {
        parent::__construct($db);
        
        if(empty($_GET['clanok']))
            $this->exit_program();
        
        $article=$db->escape_string($_GET['clanok']);
        $sql=
        "SELECT
            Clanok.clanok_id AS id,
            Clanok.nazov_clanku AS name,
            Clanok.typ_clanku_id AS type,
            Clanok.casova_znamka AS timestamp,
            Clanok.rubrika_id AS topic_id
         FROM
            Clanok     
         WHERE
            Clanok.clanok_id=$article
                AND
            Clanok.zobrazit=1";
        
        $articles=$db->query($sql);
        $info=$articles->fetch_array();
        
        if(!$info)
            $this->exit_program ();
        
        $this->info['id']=$info['id'];
        $this->info['name']=$info['name'];
        $this->info['date']=date("j.n.Y", $info['timestamp']);
        
        $sql="SELECT Rubrika.nazov_rubriky AS topic,
                     Tema.nazov_temy AS theme
              FROM
                Rubrika
                    INNER JOIN
                Tema
                    ON Rubrika.tema_id=Tema.tema_id
              WHERE Rubrika.rubrika_id=".$info['topic_id'];
        $top_nfo=$db->query($sql);
        $top_nfo_data=$top_nfo->fetch_array();
        $this->topic['topic']=$top_nfo_data['topic'];
        $this->topic['theme']=$top_nfo_data['theme'];
        $this->topic['id']=$info['topic_id'];
        
        $this->set_program($info['type'],$home);
        
        $sql=
        "SELECT 
            Uzivatel_info.uzivatel_id AS id,
            Uzivatel_info.meno AS name,
            Uzivatel_info.priezvisko AS surname,
            Uzivatel_info.trieda AS class
         FROM
            Uzivatel_info
                INNER JOIN
            Clanok_uzivatel
                ON Uzivatel_info.uzivatel_id=Clanok_uzivatel.uzivatel_id
         WHERE
            Clanok_uzivatel.clanok_id=$article";
        
        $users=$db->query($sql);
        while ($user=$users->fetch_array()){
            $id=$user['id'];
            $this->users[$id]['name']=$user['name'];
            $this->users[$id]['surname']=$user['surname'];
            
            $class=$user['class'];
            if($class){
                $i=  strpos($class, ".");
                $this->users[$id]['year']=  substr($class, 0, $i);
            }
        }
        
    }
    public function display() {
        echo "<div id='article_wrapper'>";
        
        echo "<div class='art_main_name'>".$this->info['name']."</div>";
        echo "<div class='art_main_topic_navi'>".$this->topic['theme']."->
            <a href='./?rubrika=".$this->topic['id']."'>".$this->topic['topic']."</a></div>";
        echo "<div class='art_main_info'>";
        echo "<span class='art_main_date'>".$this->info['date']."</span>";
        
        $users="";
        if(!empty($this->users)){
            foreach($this->users as $user){
                $users.=", ".$user['name']." ".$user['surname'];
                
                if(!empty($user['year']))
                    $users.=" (".$user['year'].". ročník)";
            }
            //$users=substr($users,2);
            
        }
        echo "<span class='art_main_users'>$users</span>";
        echo "</div>";
        
        
        if(!empty($this->article))
            $this->article->display();
        else
            echo "Požadovaný článok neexistuje.";
        echo "</div>";
    }

    public function label() {
        return $this->topic['topic'];
    }
 
    private function set_program($type,$home){
        $require_home=$home."/classes/modules/";
        switch($type){
            case 1:
                require_once $require_home."Art_story.php";
                $this->article=new Art_story($this->info['id'],$home);
                break;
            case 2:
                require_once $require_home."Art_interview.php";
                $this->article=new Art_interview($this->info['id'],$home);
                break;
            case 3:
                require_once $require_home."Art_post.php";
                $this->article=new Art_post($this->info['id'],  $this->db);
                break;
            case 4:
                require_once $require_home."Art_quiz.php";
                $this->article=new Art_quiz($this->info['id'],$home);
                break;
            case 5:
                require_once $require_home."Art_galery.php";
                $this->article=new Art_galery($this->info['id'], $home);
                break;
        }
    }
}

?>
