/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var select=function(id,src){
    $("#img_url").html("");
    $(".selected").removeClass("selected");
    $("#"+id).addClass("selected");
    $("#img_url").html(src);
}

var CKSend_img=function(func){
    var url=$("#img_url").html();
    window.opener.CKEDITOR.tools.callFunction(func,url);
    window.close();
}

var send_img=function(target){
    var url=$("#img_url").html();
    window.opener.document.getElementById(target).value=url;
    window.close();
}

var delete_img=function(art_id,src){
    $.ajax({
        url:"redakcia/utilities/browser/delete.php",
        type:"GET",
        data:{
            article_id:art_id,
            filename:$("#img_url").html()
        },
        success:function(data){
            $("#img_url").html("");
             $("#list").load(src);
             alert(data);
        }
    });
}