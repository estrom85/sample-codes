/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

(function($){
    $.fn.editQuestion= function(action,display){
    
    var question=this.find(".question");
    var answer=this.find(".answer");
    
    var deleteForm=function(element){
        element.show();
        element.removeClass("editing");
        
        $("#edit_question_form").hide().remove();
        
    }
    
    
    //zisti, ci sa v kontajneri nachadzaju elementy question a answer ak nie 
    //funkcia sa ukonci
    if(!(question||answer))
        return;
    this.hide();
    deleteForm($(".editing"));
    
    this.addClass('editing');
    
    var container=$("<div id='edit_question_form' style='text-align:left;'/>");
    var questEdit=$("<textarea id='questEdit' cols='80' rows='3'>"+question.html()+"</textarea><br/>");
    var answerEdit=$("<textarea id='answerEdit' cols='80' rows='6'>"+answer.html()+"</textarea><br/>");
    var edit=$("<button id='edit_question'>upraviť </button>");
    var cancel=$("<button id='cancel_editing'>zrušiť</button>");
    
    container
        .append($("<span><b>Zadaj otázku: </b><br/></span>"))
        .append(questEdit)
        .append($("<span><b>Zadaj odpoveď: </b><br/></span>"))
        .append(answerEdit)
        .append(edit)
        .append(cancel)
        .insertAfter(this);
    //odkaz na volajuci element    
    var element=this;
    edit.click(function(){
        
        var sent_data={};
            sent_data["question"]=questEdit.val();
            sent_data["answer"]=answerEdit.val();

            $.ajax({
                url:action,
                type:"POST",
                data:sent_data,
                success:function(data){
                    alert($(data).html());
                    element.load(display);
                    deleteForm(element);
                }
            })
    });
    
    cancel.click(function(){
        deleteForm(element);
    });
};

$.fn.editField=function(action,display,text_area, cols, rows){
   
        text_area=text_area||false;
        cols=cols||80;
        rows=rows||10;
        var key=this.attr("id");
        var value=this.html();
        var element=this;
        var reset_field=function (){
            $("#edit_field_wrapper").remove();
            $(".editing_field").show()
                            .removeClass("editing_field");
            $(".edit_button").show();
        };
        reset_field();
        
        
        
        var $editfield;
        if(text_area)
            $editfield=$("<textarea id='edit_field' rows='"+rows+"' cols='"+cols+"'>"+this.html()+"</textarea>");
        else
            $editfield=$("<input type='text' id='edit_field' value='"+this.html()+"'>");
        this.hide().addClass("editing_field");
        var $resetbutton=$("<button id='reset_button'>Zrušiť</button>");
        var $sendbutton=$("<button id='send_button'>Odoslať</button>");
        var $container=$("<span id='edit_field_wrapper' />");
        $container.append($editfield).append($sendbutton).append($resetbutton).insertAfter(this);

        
        $resetbutton.click(function(){
            reset_field();
        });
        $sendbutton.click(function(){
            var sent_data={};
            sent_data['value']=$editfield.val();
            $.ajax({
                url:action,
                type:"POST",
                data:sent_data,
                success:function(data){
                    element.load(display);
                    reset_field();
                }
            })
        });
    };
})(jQuery);