<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GameCenter
 *
 * @author mato
 */
class GameCenter implements display {
    //put your code here
    public function display() {
        ?>
<div id="herna_label">Vitajte v Herni</div>
<APPLET codebase="applets/classes" code="games_set/CGameSetApp.class" width=600 height=500></APPLET>
        <?php
    }

    public function label() {
        return "Herňa";
    }
}

?>
