<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Art_story
 *
 * @author mato
 */
class Art_story implements display {
    //put your code here
    private $path;
    public function __construct($id,$home) {
        $this->path=$home."/articles/$id/$id.art";
    }
    public function display() {
        include $this->path;
    }
    public function label() {
        
    }
}

?>
