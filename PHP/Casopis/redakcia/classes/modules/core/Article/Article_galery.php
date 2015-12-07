<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Article_galery
 *
 * @author mato
 */
ProgramManager::includeProgram("Article");
class Article_galery extends Article {
    /*
     * Parameters
     */
    private $galeryData;
    
    public function __construct() {
        parent::__construct();
        $this->readGalery();
        $this->setFunction("edit_desc", "edit_description");
        $this->setForm("edit_desc", "Edit description", "edit_desc", "edit_description_form");
        
        $this->setFunction("add_img", "add_img");
        $this->setForm("add_img","Add image","add_img","add_img_form");
        
        $this->setFunction("rem_picture", "remove_picture");
        $this->setFunction("edit_pic", "edit_picture");
    }
    
    protected function getProgramID() {
        return ProgramManager::getId("Article_galery");
    }
    
    public function display(){
        
        if(!empty($_GET['mode'])){
            if(!strcmp($_GET['mode'],'info')){
                $this->displayInfo ();
                return;
            }
        }
        parent::display();
        $this->displayGalery();
    }
    
    public function toolbox(){
        echo "<ul class='toolbox'>";
        if($this->article['zobrazit']){
            if($this->accessRights->approved('DROP')){
                $this->displayActionButton("release", "Stiahni článok", "release:0");
            }
            
        }
        else{
            if($this->accessRights->approved('RELEASE')){
                $this->displayActionButton("release", "Zverejni článok", "release:1");
            }
            if($this->accessRights->approved('ADD')){
                echo "<hr/>";
                $this->displayFormButton("edit", "Zmeň názov článku");
                $this->displayFormButton("change_topic", "Zmeň rubriku");
            }
              
            /*
             * here come all specific tools
             */
            echo "<hr/>";
            $this->displayFormButton("edit_desc","Zmeň popis");
            $this->displayFormButton("add_img","Pridaj obrázok");
            
            if($this->accessRights->approved('ASSIGN')){
                echo "<hr/>";
                $this->displayFormButton('add_user', "Priraď redaktora");
                $this->displayFormButton('rem_user', "Odstráň redaktora");
            }
        }
        echo "<hr/>";
        $this->displayBackButton();
        echo "</ul>";
    }
    
    protected function edit_description(){
        if(empty($_POST['desc']))
            return;
        $this->galeryData->info->description=$_POST['desc'];
        
        $this->saveGalery();
        $this->setMsg(true, "Popis úspešne zmenený");
    }
    
    protected function edit_description_form(){
        $form_id=  $this->getFormID("edit_desc");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Zadaj popis");
        $form->addTextArea("desc");
        $form->setValue("desc", $this->galeryData->info->description);
        $form->registerForm();
        $form->showForm();
    }
    
    protected function add_img(){
        if(empty($_POST["img_url"])||empty($_POST["desc"])){
            $this->setMsg(false, "Všetky polia musia byť vyplnené");
            return;
        }
        $this->add_Image($_POST['desc'], $_POST['img_url']);
        $this->saveGalery();
    }
    protected function remove_picture(){
        if(empty($_POST["img_id"]))
            return;
        $this->remove_Image($_POST["img_id"]);
        $this->saveGalery();
    }
    
    protected function edit_picture(){
        if($this->article['zobrazit'])
            return;
        if(empty($_GET["pic_id"]))
            return;
        
        $galery=  $this->galeryData->galery;
        if(!empty($galery->image))
            foreach ($galery->image as $gal_data)
                if($gal_data->id==$_GET['pic_id']){
                    echo $gal_data->desc=$_POST['value'];
                    break;
                }
        $this->saveGalery();
        
    }
    protected function add_img_form(){
        $form_id=  $this->getFormID("add_img");
        $form=new FormMaker();
        $form->setID($form_id);
        $form->addLabel("Zadaj popis");
        $form->addTextArea("desc");
        $form->addLabel("Zadaj URL obrázka");
        $form->addInputField("text", "img_url");
        $form->registerForm();
        $form->showForm();
        echo "<div id='browse_button'><button onclick=\"window.open('redakcia/utilities/browser/browser.php?article_id=".$this->article['id']."&target=img_url','image_browser','width=1000,height=500,scrollbars=yes')\">Prehľadávaj...</button></div>";
    }
    private function readGalery(){
        $path=  $this->path."/".$this->article['id'].".art";
        if(!file_exists($path))
            $this->createGalery ();
        $this->galeryData=  new SimpleXMLElement($path, 0, true);
    }
    
    private function add_Image($desc,$url){
        if($this->image_exists($url)){
            $this->setMsg(false, "Obrazok uz existuje");
            return false;
        }
        
        $thumb_url=  $this->createThumbFile($url, 100, 100);
        
        if($thumb_url==null){
            $this->setMsg(false, "Nepodarilo sa vytvorit miniaturu");
            return false;
        }
        
        
        
        $galery=  $this->galeryData->galery;
        $id=0;
        if(!empty($galery->image))
            foreach ($galery->image as $gal_data)
                $id=$gal_data->id;
        $id+=1;
        $gal_item=$galery->addChild("image");
        $gal_item->addChild("id",$id);
        $gal_item->addChild("src",$url);
        $gal_item->addChild("thumb",$thumb_url);
        $gal_item->addChild("desc",$desc);
        $this->setMsg(true, "Obrazok uspesne pridany");
    }
    private function image_exists($src){
        $galery=  $this->galeryData->galery;
        if(empty($galery->image))
            return false;
        foreach ($galery->image as $gal_data){
            if(!strcmp($gal_data->src, $src))
                    return true;
        }
        return false;
                
        
    }
    private function remove_Image($id){
        if($this->article['zobrazit'])
            return;
        $galery=$this->galeryData->galery;
        $xml=  $this->quiz_data->quiz;
        $galery=  $this->galeryData->galery;
        if(!empty($galery->image))
            foreach ($galery->image as $gal_data)
                if($gal_data->id==$id){
                    $path=$gal_data->src;
                    $filename= basename($path);
                    $extIndex=strrpos($filename, ".");
                    //creates path to thumb file
                    $thumbUrl=dirname($path)."/thumbs/".substr($filename, 0,$extIndex)."_thumb.png";
                    $thumbPath=ProgramManager::getHomeDir()."/../".$thumbUrl;
                    unlink($thumbPath);
                    $dom=  dom_import_simplexml($gal_data);
                    $dom->parentNode->removeChild($dom);
                    return;
                }
    }
    
    private function createGalery($force=false){
        $path=  $this->path."/".$this->article['id'].".art";
        if(file_exists($path)&&!$force)
            return;
        
        $xml=
            "<?xml version='1.0' encoding='UTF-8'?>
            <article>
               <info>
                  <id>".$this->article['id']."</id>
                  <type>Galeria</type>
                  <description></description>
               </info>
               <galery>
               </galery>
            </article>";
        
        $file=fopen($path,'w');
        if($file){
            fwrite($file, $xml);
            fclose($file);
            chmod($path,0777);
        }
        else
            return false;
        return true;
    }
    
    private function saveGalery(){
        if(empty($this->galeryData))
            return;
        $file_path=  $this->path."/".$this->article['id'].".art";
        $this->setTimeStamp();
        return $this->galeryData->asXML($file_path);
    }
    
    private function createThumbFile($url,$width,$height){
        $path=ProgramManager::getHomeDir()."/../".$url;
        if(!file_exists(dirname($path)."/thumbs")){
            mkdir(dirname($path)."/thumbs");
            chmod(dirname($path)."/thumbs", 0777);
        }
        else{
            chmod(dirname($path)."/thumbs", 0777);
        }
        if(!file_exists($path))
            return null;
        //get extention index
        
        $filename= basename($url);
        $extIndex=strrpos($filename, ".");
        //creates path to thumb file
        $thumbUrl=dirname($url)."/thumbs/".substr($filename, 0,$extIndex)."_thumb.png";
        $thumbPath=ProgramManager::getHomeDir()."/../".$thumbUrl;
        if(file_exists($thumbPath)){
            chmod($thumbPath,0777);
            return $thumbUrl;
        }
        //create source image
        $src_image=$this->getImage($path);
        if($src_image==null)
            return null;
        
        //get size of source image
        $srcSize=  getimagesize($path);  
        //create thumb image
        $dest_image=$this->getThumb($src_image,$srcSize[0],$srcSize[1],$width,$height);
        //save thumb image as png file
        $file=  fopen($thumbPath,"w");
        fclose($file);
        chmod($thumbPath, 0777);
        imagepng($dest_image, $thumbPath);
        imagedestroy($src_image);
        imagedestroy($dest_image);
        //return path to new thumb file
        return $thumbUrl;
    }
    
    private function getImage($path){
        $extIndex=strrpos($path, ".")+1;
        $extType=  substr($path, $extIndex);
        
        switch ($extType){
            case "jpg":
            case "jpeg":
            case "JPG":
            case "JPEG":
                return imagecreatefromjpeg($path);
            case "gif":
            case "GIF":
                return imagecreatefromgif($path);
            case "png":
            case "PNG":
                return imagecreatefrompng($path);
            case "bmp":
            case "BMP":
                return imagecreatefromwbmp($path);
        }
        
        return null;
    }
    
    private function getThumb($src,$srcWidth,$srcHeight,$dstWidth,$dstHeight){
        
        $srcX=0;
        $srcY=0;
        $srcW=$srcWidth;
        $srcH=$srcHeight;
        //compute widthHeight rates of source and destination
        $srcWHRate=$srcW/$srcH;
        $dstWHRate=$dstWidth/$dstHeight;
        
        //compute resize parameters, Thumb should be clipped and centered
        if($srcWHRate<$dstWHRate){
            $srcH=$srcW/$dstWHRate;
            $srcY=($srcHeight-$srcH)/2;
        }
        else if($dstWHRate<$srcWHRate){
            $srcW=$srcH*$dstWHRate;
            $srcX=($srcWidth-$srcW)/2;
        }
        
        //echo $srcX."->".$srcY."->".$srcW."->".$srcH;
        //create empty thumb image
        $dst=  imagecreatetruecolor($dstWidth,$dstHeight);
        //copy resized source image to thumb image with clipping coordinates
        imagecopyresized($dst, $src, 0, 0, $srcX, $srcY, $dstWidth, $dstHeight, $srcW, $srcH);
        //return thumb image
        return $dst;
    }
    
    public function displayPicture($XML_image){
        $id=$XML_image->id;
        ?>
<div class="galery_item_wrapper">
<a class="galery_item" rel="galery" href="<?php echo $XML_image->src; ?>" title="<?php echo $XML_image->desc; ?>">
    <img src="<?php echo $XML_image->thumb ?>" alt="">
</a><br>
<span id='item_desc_<?php echo $id;?>' class="galery_item_description"><?php echo $XML_image->desc."</span><br>";
if(!$this->article['zobrazit']){
    $function="potvrdASpustiProgram(".$this->getProgramID().",'rem_picture',{article_id:'".$this->article['id']."',img_id:'".$XML_image->id."'},'Chcete odstrániť obrazok?');";
    echo " <span class='edit_button'  onclick=\"$function;\" class='edit_button'> [odstrániť]</span>";
    echo $this->displayEditButton("item_desc_$id", $id);
    
}
                        ?>
</div>
<?php
    }
    
    public function displayGalery(){
        
        echo "<div class='desc'><hr>".$this->galeryData->info->description."<hr></div>";
        
        echo "<div class='galery'>";
        echo "<h3>Obrázky</h3>";
        $galery=  $this->galeryData->galery;
        if(!empty($galery->image))
            foreach ($galery->image as $gal_data)
                $this->displayPicture ($gal_data);
        echo "</div>";
        ?>
<script type="text/javascript">
    var load = function(){
        $(".galery_item").fancybox({
            helpers	: {
			title	: {
				type: 'outside'
			},
			thumbs	: {
				width	: 50,
				height	: 50
			},
                        buttons:{}
		}
            });
    }
    scriptloader.load_script("scripts/fancyapps-fancyBox-e4836f7/lib/jquery.mousewheel-3.0.6.pack.js","js");
    //scriptloader.load_script("scripts/fancyapps-fancyBox-e4836f7/source/jquery.fancybox.js","js");
    scriptloader.load_script("scripts/fancyapps-fancyBox-e4836f7/source/jquery.fancybox.css?v=2.1.3","css");
    scriptloader.load_script("scripts/fancyapps-fancyBox-e4836f7/source/jquery.fancybox.pack.js?v=2.1.3","js",load);
    
    scriptloader.load_script("scripts/fancyapps-fancyBox-e4836f7/source/helpers/jquery.fancybox-buttons.css?v=1.0.5","css");  
    scriptloader.load_script("scripts/fancyapps-fancyBox-e4836f7/source/helpers/jquery.fancybox-buttons.js?v=1.0.5","js");
    scriptloader.load_script("scripts/fancyapps-fancyBox-e4836f7/source/helpers/jquery.fancybox-media.js?v=1.0.5","js");
    
    scriptloader.load_script("scripts/fancyapps-fancyBox-e4836f7/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7","css");
    scriptloader.load_script("scripts/fancyapps-fancyBox-e4836f7/source/helpers/jquery.fancybox-thumbs.js","js");
    
    scriptloader.load_script("redakcia/styles/galery.css","css");
    scriptloader.load_script('redakcia/scripts/interview.js','js');

</script>
<?php
    }
    
    private function displayEditButton($id,$pic_id,$text_area=true){
        if($this->article['zobrazit'])
            return;
        $action_base="redakcia/request/action.php?id=".$this->getProgramID()."&article_id=".$this->article['id']."&func=edit_pic";
        $display_base="redakcia/request/main.php?id=".$this->getProgramID()."&article_id=".$this->article['id']."&mode=info"; 
        
        $action=$action_base."&pic_id=$pic_id";
        $display=$display_base."&pic_id=$pic_id";
        
        $function="$('#$id').editField('$action','$display'".($text_area?",true":"").",25,6)";
        return "<span onclick=\"$function;$(this).hide();\" class='edit_button'>[upraviť]</span>";
    }
    
    private function displayInfo(){
        $galery=  $this->galeryData->galery;
        if(!empty($galery->image))
            foreach ($galery->image as $gal_data)
                if($gal_data->id==$_GET['pic_id']){
                    echo $gal_data->desc;
                    break;
                }
    }
}

?>
