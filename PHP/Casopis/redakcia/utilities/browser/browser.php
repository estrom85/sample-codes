<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
        //session_start();
        if(!isset($_COOKIE['user'])){
            echo "Nie je prihlásený žiaden užívateľ.";
            exit;
        }
        if(!isset($_GET['article_id'])){
            echo "Nie je zadaný žiaden článok";
            exit;
        }
        require dirname(__FILE__)."/../../classes/utils/CDatabaza.php";
        require dirname(__FILE__)."/../../classes/utils/UserRights.php";
        
        $data=CDatabaza::getInstance();
        if(!$data)
        {
            exit ("Nemozem sa pripojit na databazu");
        }
        $data->connect();
        $clanok=$data->escape_string($_GET['article_id']);
        $user=new UserRights($data,$_COOKIE['user']);
        
        $sql="SELECT * FROM Clanok_uzivatel WHERE clanok_id=$clanok AND uzivatel_id=".$_COOKIE['user'];
        $query=$data->query($sql);
        if(!$query){
            echo "Chyba v pripojení na databázu";
            $data->close();
            exit;
        }
        if(!$query->num_rows&&!$user->approved('EDIT_ALL')){
            echo "Nemáte oprávnenie na prezeranie obsahu. Prístup zamietnutý.";
            $data->close();
            exit;
        }
        $data->close();
        
        ?>
<html>
    <head>
        <base href="../../../">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="redakcia/utilities/browser/styles/browser.css">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript" src="redakcia/utilities/browser/scripts/browser.js"></script>
        <script type="text/javascript" src="redakcia/utilities/browser/scripts/jquery.ajaxfileupload.js"></script>
         
         <script type="text/javascript">
             $(document).ready(function(){
                 $("#list").load("redakcia/utilities/browser/list.php?<?php echo http_build_query($_GET); ?>");
                 $("#file_upload").ajaxfileupload({
                     action:"redakcia/utilities/browser/upload.php?<?php echo http_build_query($_GET); ?>",
                     submit_button:$("#submit_btn"),
                     onComplete:function(response){
                         $("#list").load("redakcia/utilities/browser/list.php?<?php echo http_build_query($_GET); ?>");
                         $("#file_upload").val(null);
                         alert(response);
                     }
                 }); 
             });
             
         </script>
        <title>Prehliadavač obrázkov</title>
    </head>
    <body>
        <?php
            $function="";
            if(empty($_GET['target']))
                $function="CKSend_img(".$_GET['CKEditorFuncNum'].")";
            else
                $function="send_img('".$_GET['target']."')";
        ?>
        <button onclick="<?php echo $function; ?>;">Otvor</button> 
        <button onclick="delete_img(<?php echo $_GET['article_id'].", 'redakcia/utilities/browser/list.php?".http_build_query($_GET)."'" ; ?>)">Vymaž</button>
        <span id="img_url">&nbsp</span>
        <div style="float:none; width:100%">
            Upload:<br/>
        <input type="file" name="file" id="file_upload"/><button id="submit_btn">Odošli</button>
        </div>
        <div id="list"></div>
        
    </body>
</html>
