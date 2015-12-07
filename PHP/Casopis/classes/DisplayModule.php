<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DisplayModule
 *
 * @author mato
 */
abstract class DisplayModule implements display {
    //put your code here
    protected $db;
    public function __construct(CDatabaza $db) {
        $this->db=$db;
    }
    protected function exit_program(){
        header("Location: ./");
        $this->db->close();
        exit;
    }
}

?>
