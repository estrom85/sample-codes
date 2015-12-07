/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//widget je akakolvek funkcia, ktora vracia jQuery element reprezentujuci widget
(function(){
    $.fn.addWidget=function(widget){
        var container=$("<div class='widget'/>");
        container.append(widget);
        this.append(container);
        return this;
    }
})(jQuery);

var aktualny_cas=function(){
    var widget=$("<div id='current_time'/>");
    
    var update=function(){
        var cas=new Date();
        var hod=cas.getHours();
        var min=cas.getMinutes();
        var sec=cas.getSeconds();
        if(hod<10)
            hod="0"+hod;
        if(min<10)
            min="0"+min;
        if(sec<10)
            sec="0"+sec;
        
        var display="<span class='label'>Aktuálny čas: </span><br/>"+hod+":"+min+":"+sec;
        widget.html(display);
        
        window.setTimeout(update, 1000);
    }
    
    update();
    return widget;
};

var dnesny_datum=function(){
    var widget=$("<div id='current_date'/>");
    
    var update=function(){
        var cas=new Date();
        var days=["Nedeľa","Pondelok","Utorok","Streda","Štvrtok","Piatok","Sobota"];
        var day=cas.getDate();
        var month=cas.getMonth()+1;
        var year=cas.getFullYear();
        var weekday=days[cas.getDay()];
        
        
        var display="<span class='label'>Dnes je: </span><br/>"+weekday+", "+day+"."+month+"."+year;
        widget.html(display);
        window.setTimeout(update, 60000);
    }
    
    update();
    return widget;
};

var links_widget=(function(){
    var links=new Array();
    
    var add_link=function(label,link){
        var link={label:label,link:link};
        links.push(link);
    }
    
    var display_links=function(){
        var widget=$("<div id='links_widget'/>");
        widget.append($("<div class='label'>Zaujímavé linky:</div>"));
        for(var link in links){
            widget.append($("<div class='link'><a href='http://"+links[link].link+"'>"+links[link].label+"</a></div>"));
        }
        return widget;
    }
    
    return{
        add:add_link,
        display:display_links
    }
})();

var meniny=(function(){
    
    var getName=function(den,mesiac,callback){
        $.ajax({
            url:"scripts/meniny.php",
            data:{
                day:den,
                month:mesiac
            },
            type:"GET",
            success:function(data){
                callback(data);
            }
        });
        
        
    }
    
    var getWidget=function(){
        var datum=new Date();
        var den=datum.getDate();
        var mesiac=datum.getMonth()+1;
        var $container=$("<div id='meniny_wg'/>");
        var $name_cont=$("<div id='meniny_wg_value'/>");
        $container.append($("<div id='meniny_wg_label'>Meniny má:</div>"))
                    .append($name_cont);
        getName(den,mesiac,function(data){
            $name_cont.html(data);
        });
        return $container;
    }
    
    return{
        getName:getName,
        getWidget:getWidget
    };
    
})();

var kalendar=(function(){
    var months={
        1:"Január",
        2:"Február",
        3:"Marec",
        4:"Apríl",
        5:"Máj",
        6:"Jún",
        7:"Júl",
        8:"August",
        9:"September",
        10:"Október",
        11:"November",
        12:"December"
    }
    var month;
    var year;
    
    var $container=$("<div id='kalendar_wg'/>");
    var $content=$("<div id='kalendar_wg_content'/>");
    var $month=$("<span id='kalendar_wg_month' class='kalendar_wg_layer'/>");
    var $year=$("<span id='kalendar_wg_year' class='kalendar_wg_layer'/>");
    
    var init=function(){
        var date=new Date();
        month=date.getMonth()+1;
        year=date.getFullYear();
        $year.html(year);
        $month.html(months[month]);
    }
    
    var set_year=function(){
        $year.html(year);
        set_content();
    }
    
    var prev_year=function(){
        if(typeof(year)=="undefined")
            init();
        year-=1;
        set_year();
    }
    
    var next_year=function(){
        if(typeof(year)=="undefined")
            init();
        year+=1;
        set_year();
    }
    
    var set_month=function(){
        $month.html(months[month]);
        set_content();
    }
    
    var prev_month=function(){
        if(typeof(month)=="undefined")
            init();
        month-=1;
        
        if(month==0){
            month=12;
            prev_year();
        }
        set_month();
    }
    
    var next_month=function(){
        if(typeof(month)=="undefined")
            init();
        month+=1;
        
        if(month==13){
            month=1;
            next_year();
        }
        set_month();
    }
    
    var set_content=function(){
        //nastavi prvy den v mesiaci
        var today=new Date();
        var current_date=new Date();
        current_date.setMonth(month-1, 1);
        current_date.setYear(year);
        
        var day=current_date.getDay()-1;
        if(day==-1)
            day=6;
        if(day>0){
            current_date.setDate(-day+1);
        }
        
        var $container=$("<table class='kalendar_wg_content_table'/>");
        var days=["Po","Ut","St","Št","Pi","So","Ne"];
        var $row=$("<tr/>");
        for(var i=0;i<7;i++){
            $row.append($("<th>"+days[i]+"</th>"));
        }
        $container.append($row);
        while(true){
            $row=$("<tr/>");
            for(i=0;i<7;i++){
                var $td=$("<td>"+current_date.getDate()+"</td>");
                if(current_date.getMonth()+1==month)
                    $td.addClass("current_month");
                else
                    $td.addClass("other_month");
                if(i==6)
                    $td.addClass("holiday");
                if(current_date.getDate()==today.getDate()&&
                    current_date.getMonth()==today.getMonth()&&
                    current_date.getFullYear()==today.getFullYear())
                    $td.attr("id","kalendar_wg_today");
                current_date.setDate(current_date.getDate()+1);
                $row.append($td);
            }
            $container.append($row);
            if(current_date.getMonth()+1>month)
                break;
            else if(current_date.getFullYear()>year)
                break;
        }
        
        $content.html("");
        $content.append($container);
    }
    
    var get_widget=function(){
        var $prev_year_btn=$("<span class='kalendar_wg_button'><<</span>");
        $prev_year_btn.click(function(){
            prev_year();
        });
        
        var $next_year_btn=$("<span class='kalendar_wg_button'>>></span>");
        $next_year_btn.click(function(){
            next_year();
        });
        var $prev_month_btn=$("<span class='kalendar_wg_button'><<</span>");
        $prev_month_btn.click(function(){
            prev_month();
        });
        
        var $next_month_btn=$("<span class='kalendar_wg_button'>>></span>");
        $next_month_btn.click(function(){
            next_month();
        });
        init();
        set_content();
        var $year_cont=$("<div id='kalendar_wg_year_wrapper'/>");
        var $month_cont=$("<div id='kalendar_wg_month_wrapper'/>");
        $year_cont.append($prev_year_btn).append($year).append($next_year_btn);
        $month_cont.append($prev_month_btn).append($month).append($next_month_btn);
        $container.append($year_cont).append($month_cont).append($content);
        
        return $container;
    }
    
    return{
        getWidget:get_widget
    }
    
})();