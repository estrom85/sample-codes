/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var quiz_manager=(function(){
    var questions={};
    var answers={};
    var correct={};
    var answered=false;
    var article;
    var element;
    var quiz_container;
    var bg_img;
    
    var current_page=0;
    var max_page=0;
    var timer;
    var elapsed_time=-1;
    
    var quest_on_page=3;

    var add_question=function(id,question,answ){
        if(element)
            clear();
        questions[id]={};
        questions[id]["quest"]=question;
        questions[id]["answers"]={};
        answers[id]=0;
        for(var answer in answ)
            questions[id]["answers"][answer]=answ[answer];
        
    };
    
    var clear=function(){
        element=null;
        answered=false;
        questions={};
        answers={};
        $("#quiz_wrapper").remove();
    };
    
    var create_quiz_form=function(){
        var container=$("<div id='quiz_wrapper'/>");
        var page_indicator=$("<div id='quiz_page_indicator'/>");
        var timer=$("<div id='quiz_timer'/>");
        container.append(timer).append(page_indicator);
        var i=0;
        var quest_mod=0;
   
        var subcontainer=$("<div id='quiz_quest_set_0' class='quiz_quest_set'>");
        for(var quest in questions){
            var quest_container=$("<div class='quiz_quest'>");
            quest_container.append($("<div class='quiz_quest_question'>"+quest+". "+questions[quest]["quest"]+"</div>"));
            var opt_container=($("<div class='quiz_quest_options'/>"));
            for(var answ in questions[quest]['answers']){
                var cont=$("<div class='quiz_quest_opt'/>");
                var answer=$("<input type='radio' id='answer_"
                    +quest+"_"+answ+"' name='question_"+quest+"' value='"+answ+"'>");
                answer.change(function(){
                    var value=$(this).val();
                    var question=$(this).attr("name");
                    question=question.toString().substr(9);
                    set_answer(question,value);
                });
               
                cont.append(answer)
                                .append($("<label for='answer_"+quest+"_"+answ+"'>"+questions[quest]["answers"][answ]+"</label>"));
                opt_container.append(cont);
                
        
            }
            quest_container.append(opt_container);
            subcontainer.append(quest_container);
            
            quest_mod=quest%quest_on_page;
            var j=Math.floor(quest/quest_on_page);
            
            if(j>i){
                container.append(subcontainer);
                subcontainer=$("<div id='quiz_quest_set_"+j+"' class='quiz_quest_set'>");
                i=j;
            }
        }
        max_page=i;
        if(quest_mod>0)
            container.append(subcontainer);
        else
            max_page--;
        page_indicator.html("Strana: "+(current_page+1)+" z "+(max_page+1));

        container.submit(function(e){
            e.preventDefault();
        });
        container.find(".quiz_quest_set").hide();
        container.find("#quiz_quest_set_"+current_page).show();
        if(bg_img){
            container.css("background-image","url:('"+bg_img+"')");
        }
        
        container.append(get_back_button);
        container.append(get_continue_button());
        
        return container;
    };
    
    
    var start_timer=function(){
        elapsed_time+=1;
        $('#quiz_timer').html(elapsed_time);
        timer=window.setTimeout(start_timer, 1000);
    };
    
    var stop_timer=function(){
        window.clearTimeout(timer);
    };
    
    var get_continue_button=function(){
        $('#quiz_continue_button').remove();
        var button=$("<button id='quiz_continue_button'>Ďalej</button>");
        button.click(function(){
            next_page();
            //$("#quiz_continue_button").remove();
        });
        return button;
    };
    
    var get_back_button=function(){
        $('#quiz_back_button').remove();
        var button=$("<button id='quiz_back_button'>Späť</button>");
        button.click(function(){
            prev_page();
            //$("#quiz_continue_button").remove();
        });
        return button;
    }
    var get_submit_button=function(){
        $('#quiz_submit_button').remove();
        var button=$("<button id='quiz_submit_button'>Vyhodnoť kvíz</button>");
        button.click(function(){
            send();
        });
        return button;
    };
    
    var set_answer=function(id,answer){
        answers[id]=answer;
        var start=current_page*quest_on_page;
        var end=start+quest_on_page;
        var i=0;
        /*
        for(var j in answers){
            
            if(i>=start&&i<end){
                
                if(answers[j]==0){

                    return;
                }
            }
            if(i>end)
                break;
            i+=1;
        }
        */
       
       /*
        if(current_page<max_page);
            //quiz_container.append(get_continue_button());
        else
            quiz_container.append(get_submit_button());
        */
       for(var j in answers) if(answers[j]==0) return;
       $('#quiz_continue_button').remove();
       quiz_container.append(get_submit_button());
       quiz_container.append(get_continue_button());
    };
    
    var create_quiz=function(id){
        if(element){
            element.show();
        }
        element=$("#"+id);
        element.hide();
        quiz_container=create_quiz_form();
        quiz_container.insertAfter(element);
        start_timer();
    };
    
    var display_page=function(){
        $("#quiz_page_indicator").html("Strana: "+(current_page+1)+" z "+(max_page+1));
    };
    
    var prev_page=function(){
        if(current_page-1<0)
            return;
        $("#quiz_quest_set_"+current_page).slideUp(500);
        current_page-=1;
        $("#quiz_quest_set_"+current_page).slideDown(500);
        
        display_page();
        
    };
    
    var next_page=function(){
        if(current_page+1>max_page)
            return;
        $("#quiz_quest_set_"+current_page).slideUp(500);
        current_page+=1;
        $("#quiz_quest_set_"+current_page).slideDown(500);
        
        display_page();
    };
    
    var send=function(){
        if(answered)
            return;
        answered=true;
        stop_timer();
        $.ajax({
            url:"./request/quiz.php",
            type:"GET",
            data:{clanok:article},
            success:function(data){
                parse_received_data(data);
                display_result();
            }
        });
    };
    
    var display_result=function(){
       var quests=0;
       var corr=0;
       var rate;
       for(var id in correct){
          if(answers[id]==correct[id])
              corr+=1;
          quests+=1;
       }
       rate=100*corr/quests;
       rate=Math.round(rate*100)/100;
       var sec=elapsed_time;
       var hod=Math.floor(sec/3600);
       sec-=hod*3600;
       var min=Math.floor(sec/60);
       sec-=min*60;
       //alert("Otazok:"+quests+"\nSpravne odpovede:"+corr+"\nZnamka:"+rate+"%"+"\nČas:"+hod+":"+min+":"+sec);
       quiz_container.remove();
       create_result_display();
    };
    
    var create_result_display=function(){
        current_page=0;
        quiz_container=$("<div id='quiz_wrapper'/>");
        quiz_container.append($("<div id='quiz_result_heading'>Vyhodnotenie kvízu</div>"));
        quiz_container.append(show_result());
        quiz_container.append(show_correct_answers());
        if(bg_img){
            quiz_container.css("background-image","url:('"+bg_img+"')");
        }
        quiz_container.insertAfter(element);
    };
    
    var show_result=function(){
        var range={1:0.90,2:0.75,3:0.50,4:0.3}
        var quests=0;
        var corr=0;
        var rate;
        var mark;
        for(var id in correct){
          if(answers[id]==correct[id])
              corr+=1;
          quests+=1;
        }
        rate=corr/quests;
        var perc=100*corr/quests;
        perc=Math.round(perc*100)/100;
        var sec=elapsed_time;
        var hod=Math.floor(sec/3600);
        sec-=hod*3600;
        var min=Math.floor(sec/60);
        sec-=min*60;
        
        if(hod<10)
            hod="0"+hod;
        if(min<10)
            min="0"+min;
        if(sec<10)
            sec="0"+sec;
        
        if(rate>=range[1])
            mark=1;
        else if(rate>=range[2])
            mark=2;
        else if(rate>=range[3])
            mark=3;
        else if(rate>=range[4])
            mark=4;
        else
            mark=5;
        
        var container=$("<table id='quiz_result'/>");
        var sub_container=$("<tr class='quiz_result_item'/>");
        sub_container.append($("<td class='quiz_result_label'>Počet otázok: </td>"));
        sub_container.append($("<td id='quiz_result_quests' class='quiz_result_value'>"+quests+"</td>"));
        container.append(sub_container);
        
        var sub_container=$("<tr class='quiz_result_item'/>");
        sub_container.append($("<td class='quiz_result_label'>Počet správnych odpovedí: </td>"));
        sub_container.append($("<td id='quiz_result_correct' class='quiz_result_value'>"+corr+"</td>"));
        container.append(sub_container);
        
        var sub_container=$("<tr class='quiz_result_item'/>");
        sub_container.append($("<td class='quiz_result_label'>Počet nesprávnych odpovedí: </td>"));
        sub_container.append($("<td id='quiz_result_incorrect' class='quiz_result_value'>"+(quests-corr)+"</td>"));
        container.append(sub_container);
        
         var sub_container=$("<tr class='quiz_result_item'/>");
        sub_container.append($("<td class='quiz_result_label'>Čas: </td>"));
        sub_container.append($("<td id='quiz_result_time' class='quiz_result_value'>"+hod+":"+min+":"+sec+"</td>"));
        container.append(sub_container);
        
        var sub_container=$("<tr class='quiz_result_item'/>");
        sub_container.append($("<td class='quiz_result_label'>Pomer správnych odpovedí: </td>"));
        sub_container.append($("<td id='quiz_result_rate' class='quiz_result_value'>"+perc+"%</td>"));
        container.append(sub_container);
        
        var sub_container=$("<tr class='quiz_result_item'/>");
        sub_container.append($("<td class='quiz_result_label'>Známka: </td>"));
        sub_container.append($("<td id='quiz_result_mark' class='quiz_result_value'>"+mark+"</td>"));
        container.append(sub_container);
        var sub_container=$("<tr class='quiz_result_item'/>");
        
        var button=$("<button id='show_correct_answers_button'>Ukaž správne odpovede</button>");
        button.click(function(){
            $('#quiz_result').hide();
            $('#quest_correct_answers').show();
        });
        sub_container.append(($("<td colspan='2' style='text-align:center'/>").append(button)));
        container.append(sub_container);
        return container;
    };
    
    
    var show_correct_answers=function(){
        var container=$("<div id='quest_correct_answers'>");
        container.hide();
        var subcontainer=$("<div id='quiz_quest_set_0' class='quiz_quest_set'>");
        var i=0;
        var quest_mod;
        for(var quest in questions){
            var quest_container=$("<div class='quiz_quest'>");
            quest_container.append($("<div class='quiz_quest_question'>"+quest+". "+questions[quest]["quest"]+"</div>"));
            var opt_container=($("<div class='quiz_quest_options'/>"));
            for(var answ in questions[quest]['answers']){
                var cont=$("<div class='quiz_quest_opt'>"+questions[quest]["answers"][answ]+"</div>");
                if(answ==correct[quest])
                    cont.addClass("quiz_correct");
                else if(answ==answers[quest])
                    cont.addClass("quiz_incorrect");
                opt_container.append(cont);
            }
            quest_container.append(opt_container);
            subcontainer.append(quest_container);
            
            quest_mod=quest%quest_on_page;
            var j=Math.floor(quest/quest_on_page);
            
            if(j>i){
                container.append(subcontainer);
                subcontainer=$("<div id='quiz_quest_set_"+j+"' class='quiz_quest_set'>");
                subcontainer.hide();
                i=j;
            }
        }
        if(quest_mod>0){
            container.append(subcontainer);
            subcontainer.hide();
        }
        
        var prev_button=$("<button id='prev_page_button'>Späť</button>");
        prev_button.click(function(){
            prev_page();
        });
        
        var next_button=$("<button id='next_page_button'>Ďalej</button>");
        next_button.click(function(){
            next_page();
        });
        
        var show_button=$("<button id='show_results_button'>Ukáž výsledky</button>");
        show_button.click(function(){
            $('#quiz_result').show();
            $('#quest_correct_answers').hide();
        });
        
        container.append(prev_button).append(show_button).append(next_button);
        
        return container;
    };
    
    var parse_received_data=function(data){
        var $regex=new RegExp("Chyba");
        if($regex.test(data))
            return;
        var answers=data.toString().trim()+" ";
        var start=0;
        var end=0;
        while((end=answers.toString().indexOf(" ", start))!=-1){
            var sub=answers.toString().substring(start, end);
            var i=sub.indexOf(":");
            var id=sub.toString().substr(0, i);
            var answ=sub.toString().substr(i+1);
            correct[id]=answ;
            start=end+1;
        }
        
    };
    
    
    
    var set_action=function(src){};
    
    var set_article=function(art){
        article=art;
    };
    
    var set_bg_img=function(src){
        bg_img=src;
    };
    
    return {
        add:add_question,
        clear:clear,
        create:create_quiz,
        set_article:set_article,
        set_image:set_bg_img
    };
    
})();
