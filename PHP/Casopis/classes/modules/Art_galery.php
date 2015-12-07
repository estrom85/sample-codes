<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Art_galery
 *
 * @author mato
 */
class Art_galery implements display{
    //put your code here
    private $data;
    public function __construct($id,$home) {
        $path=$home."/articles/$id/$id.art";
        $this->data=new SimpleXMLElement($path,0,true);
    }
    
    public function display() {
        echo "<div class='desc'><hr>".$this->data->info->description."</div>";
        echo "<div class='galery'>";

        $galery=  $this->data->galery;
        if(!empty($galery->image))
            foreach ($galery->image as $gal_data)
                $this->displayPicture ($gal_data);
        echo "</div>";
    }

    public function label() {
        
    }
    
    public function displayPicture($XML_image){
        ?>
<div class="galery_item_wrapper">
<a class="galery_item" rel="galery" href="<?php echo $XML_image->src; ?>" title="<?php echo $XML_image->desc; ?>">
    <img src="<?php echo $XML_image->thumb ?>" alt="">
</a><br>
<!--
<span class="galery_item_description">--><?php //echo $XML_image->desc."</span><br>";

  
                        ?>
</div>
<?php
    }
}

?>
