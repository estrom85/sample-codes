/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var menu_displayed="";
function hide_all(){
    $(".sub_menu").hide(1000);
}

function onMenuClick(id){
    hide_all(100);
    if(id!=menu_displayed){
        $(id).show(1000);
        menu_displayed=id;
    }
    else
        menu_displayed="";
}


