<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ImageList
 *
 * @author mato
 */
class ImageList {
    private $image;
    private $article;
    private $dir;
    //put your code here
    public function __construct($home,$article_id) {
        $this->dir=$home."/articles/$article_id/pics";
        
        $images=glob($this->dir."/{*.jpg,*.jpeg,*.gif,*.tif,*.png,*.JPG,*.JPEG,*.GIF,*.TIF,*.PNG}",GLOB_BRACE);
        
        if(empty($images))
            return;
        foreach($images as $path){
            
            $this->image[]= basename($path);
        }
        $this->article=$article_id;
    }
    
    public function display(){
        
        if(empty($this->image)){
            echo "Nenasiel sa ziaden obrazok";
            return;
        }
        $i=0;
        foreach($this->image as $img){
            $path=  $this->dir."/$img";
            echo "<div id='image_$i' class='image_list_item' onclick=\"select('image_$i','articles/$this->article/pics/$img')\">";
            echo "<div id='img_show_$i'></div>";
            echo "<style>#img_show_$i{";
            echo "width: 150px;height:150px;margin-left:auto;margin-right:auto;";
            echo "background-image:url('articles/$this->article/pics/$img');";
            echo "background-position:center;";
            echo "background-size:cover";
            echo "}</style>";
            echo "<span class='subor'>$img</span><br/> Veľkosť súboru: ". $this->filesize_string(filesize($path))."<br/>";
            $size=  getimagesize($path);
            echo "Rozmery: ".$size[0]."x".$size[1];
            echo "</div>";
            $i++;
            
        }
    }
    private function filesize_string($size){
        
        $result=0;
        $i=-1;
        $temp=1;
        do{
            $i+=1;
            $temp=$temp<<1;
        }while($temp<$size);
        $i=floor($i/10.0);
        if($i>0)
            $result=round($size/(1<<($i*10)),2);
        
        else
            $result=$size;
        
        $suffix="B";
        switch($i){
            case 1:
                $suffix="kB";
                break;
            case 2:
                $suffix="MB";
                break;
            case 3:
                $suffix="GB";
                break;
            case 4:
                $suffix="TB";
                break;
        }
        return $result." ".$suffix;
    }
}

?>
