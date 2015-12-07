/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
(function($){
    $.fn.editQuizQuestion=function(prog_id,art_id){
        
        var answer;
        var options=new Array();
        var last_opt_id=0;
        var $this=this;
        var quest_id;
        var $form=$("<form id='quiz_send_form' />");
        
        
        var create_option=function(opt_id,value){
            if(opt_id)
                last_opt_id=opt_id;
            else
                opt_id=++last_opt_id;
            value=value||"";
            var id=options.length;
          var $container=$("<div id='option_"+id+"'/>");
          var $id_field=$("<input type='hidden' name='opt_id[]' value='"+opt_id+"'/>");
          var $radio_field=$("<input type='radio' name='answer' value='"+opt_id+"'>");
          if(opt_id==answer||(opt_id==1&&!answer))
              $radio_field.attr("checked","checked");
          //alert("ahoj"+answer);
          var $input_field=$("<input type='text' name='opt_desc[]' size='30' value='"+value+"'>");
          var $remove_button=$("<button id='remove_opt_"+opt_id+"'>Odstrániť možnosť</button>");
          $remove_button.click(function(){
              options[id].remove();
              options=options.slice(0,id).concat(options.slice(id+1));
          });
          $container.append($id_field).append($radio_field).append($input_field).append($remove_button);
          options.push($container);
          return $container;
        };
        var clear=function(){
            $(".editing").show().removeClass("editing");
            $("#quiz_send_form").remove();
            
        };
        var init=function(){
            clear();
            
            answer=$this.find(".quiz_answer").text();
            var quest=$this.find(".quiz_quest").text();
            
            var id=$this.find(".quest_id").text();
            quest_id=id;
            $this.find("div.quest_answers div").each(function(){
                var id=$(this).find(".opt_id").text();
                var value=$(this).find(".opt_desc").text();
                create_option(id,value);
            });
            var $quest=$("<textarea name='quest' cols='55' rows='3' id='quest_desc'>"+quest+"</textarea>");
            var $quest_id=$("<input type='hidden' name='quest_id' value='"+id+"'>");
            var $submit_button=$("<input type='submit'/>");
            var $remove_button=$("<input type='button' id='remove_form' value='Zrušiť'/>");
            var $add_button=$("<input type='button' id='add_option' value='Pridaj'/>");
            
            $form.append($("<span>Otázka:<br/></span>")).append($quest).append($quest_id).append($("<span><br/>Možnosti:<br/></span>"));
            for(var i=0;i<options.length;i++)
                $form.append(options[i]);
            $form.append($add_button).append($submit_button).append($remove_button);
            
            $form.submit(function(e){
                e.preventDefault();
                var data=$(this).serializeArray();
                $.ajax({
                    url:"redakcia/request/action.php?id="+prog_id+"&article_id="+art_id+"&func=edit_quest",
                    type:"POST",
                    data:data,
                    success:function(data){
                        //alert($(data).text());
                        alert(data);
                        $this.load("redakcia/request/main.php?id="+prog_id+"&article_id="+art_id+"&mode=question&quest="+quest_id);
                    }
                });
                clear();
            });
            $remove_button.click(function(){
                clear();
            });
            $add_button.click(function(){
                var $option=create_option();
                if(options.length==1)
                    $option.insertAfter($quest);
                $option.insertAfter(options[options.length-2]);
            });
            $this.addClass("editing");
            $this.hide();
            $form.insertAfter($this);
        };
        
        init();
    };
})(jQuery);

