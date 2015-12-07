<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Menu
 *
 * @author mato
 */
class Menu extends DisplayModule{
    //put your code here
    private $menu;
    public function __construct(CDatabaza $db) {
        parent::__construct($db);
        $sql="SELECT * FROM Tema ORDER BY tema_id";
        $temy=$db->query($sql);
        while($tema=  $temy->fetch_array()){
            $this->menu[$tema['tema_id']]['id']=$tema['tema_id'];
            $this->menu[$tema['tema_id']]['nazov']=$tema['nazov_temy'];
            
            $sql="SELECT * FROM Rubrika WHERE tema_id='".$tema['tema_id']."'
                ORDER BY rubrika_id";
            $rubriky=$db->query($sql);
            
            while($rubrika=$rubriky->fetch_array()){
                
                $this->menu[$rubrika['tema_id']]['rubriky'][$rubrika['rubrika_id']]['id']=$rubrika['rubrika_id'];
                $this->menu[$rubrika['tema_id']]['rubriky'][$rubrika['rubrika_id']]['nazov']=$rubrika['nazov_rubriky'];
                
            }
        }
        
        
    }
    
    public function onLoad(){
        
    }
    public function display() {
        echo "<ul class='main_menu'>";
        echo "<li><a href='./'>Domov</a></li>";
        if(!empty($this->menu)){
            foreach($this->menu as $item){
                echo "<li onclick='onMenuClick(\"#tema_".$item['id']."\")'>".$item['nazov']."</li>";
            }
        }
        echo "<li><a href='./?rozne=herna'>Herňa</a></li>";
        echo "<li>Kniha návštev</li>";
        echo "<li>O nás</li>";
        echo "</ul>";
        if(!empty($this->menu)){
            foreach($this->menu as $tema){
                if(!empty($tema['rubriky'])){
                    echo "<ul class='sub_menu' id='tema_".$tema['id']."'>";
                    foreach($tema['rubriky'] as $rubrika){
                        echo "<li><a href='./?rubrika=".$rubrika['id']."'>".$rubrika['nazov']."</a></li>";
                    }
                    echo "</ul>";
                    
                }
                
                
                
            }
        }
        
    }
    public function label() {
        return "";
    }
}

?>
