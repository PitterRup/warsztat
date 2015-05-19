(function($){
    $.fn.info = function(options) { 
        //stworzenie kontenera i ustawienie licznika
        if($("#infoContainer").length==0) $("body").append("<div id='infoContainer'></div>");
        
        var id = nastepneId();
        var ramka = stworzRamke(id); 

        // zauktualizowanie ustawień z ustawieniami użytkownika
        var settings = $.extend({}, $.fn.info.defaults, options);

        return this.each(function() { 
            if($(this).attr("info")=='yes' && !$(this).attr("info-state")) { 
                if(settings.mode=='mouseover') {
                    //powiązanie obiektu z ramką
                    if(!$(this).attr("info-id")) $(this).attr("info-id",id);            
                    
                    // dodanie tekstu do ramki i dokończenie eliminowania błędu ustalania złej wysokości ramki
                    ramka.children(".text").css("width","auto"); 
                    
                    //pokazanie ramki bez najechania na obiekt (pokazuje ostatni dodany)
                    if(settings.afterCreate=='visible') $.fn.info.pokaz($(this),settings);

                    //zdarzenia dla ramki
                    setEvents($(this),settings);   
                }   

                // oznaczenie obiektu, że ma podpięte info
                $(this).attr("info-state","ready");
            }   
            // odświeżenie elementu
            else setEvents($(this),settings);
        });
    };

    //ustawienia domyślne
    $.fn.info.defaults = {
        openTime: 200,
        delay: 0,
        afterCreate: 'hidden',
        border: 1,
        strzalkaHeight: 5,
        strzalkaWidth: 5,
        strzalkaMarLeft: 20,
        strzalkaMarTop: 10,
        margin: 5,
        defaultPosition: 'top',
        mode: 'mouseover'
    };

    // obiekt opóźnienia pokazywania ramki
    $.fn.info.timeopen = null;

    $.fn.info.pokaz = function(obj,settings) {
        var infoPosition,infoText;
        var ramka = zwrocRamke(obj.attr("info-id"));

        //wstawienie zawartości do ramki
        infoText = getText(obj); 
        ramka.children(".text").html(infoText);

        //odczytanie kierunku wyświetlania
        if(obj.attr("info-position")) infoPosition = obj.attr("info-position");
        else infoPosition = settings.defaultPosition;

        //ustawienie elementów w przestrzeni 
        $.fn.info.ustaw(obj,infoPosition,settings);
        $.fn.info.timeopen = setTimeout(function(){ ramka.fadeIn(settings.openTime); },settings.delay);
    }

    $.fn.info.zamknij = function(obj) { 
        var ramka = $(".info[info-id="+obj.attr("info-id")+"]");
        if($.fn.info.timeopen) clearTimeout($.fn.info.timeopen);
        ramka.hide();
        ramka.find(".strzalka").removeClass("bottomPos topPos leftPos rightPos");
    }

    $.fn.info.ustaw = function(obj,infoPosition,settings) {
        var t = obj.offset().top;
        var l = obj.offset().left;
        var ramka = $(".info[info-id="+obj.attr("info-id")+"]");

        if(infoPosition=='top') {
            var scrTop = $(document).scrollTop();
            var przestrzen = obj.offset().top - scrTop - settings.margin - settings.strzalkaHeight;
            if(czySieZmiesci(ramka.innerHeight(),przestrzen)) {
                t = obj.offset().top - ramka.innerHeight() - settings.strzalkaHeight - settings.margin; 
                ustawObiekty(ramka,'top',settings);
            }
            else $.fn.info.ustaw(obj,'bottom',settings);
        }
        else if(infoPosition=='bottom') {
            var przestrzen = $(document).innerHeight() - (obj.offset().top + obj.innerHeight() + settings.margin);
            if(czySieZmiesci(ramka.innerHeight(),przestrzen)) {          
                t = obj.offset().top + obj.innerHeight() + 2*settings.border + settings.margin;
                ustawObiekty(ramka,'bottom',settings);
            }
            else $.fn.info.ustaw(obj,'top',settings);           
        }
        else if(infoPosition=='right') {
            if(czySieZmiesci(ramka.innerWidth(),offsetRight(obj))) {
                l = obj.offset().left + obj.innerWidth() + 2*settings.border + settings.margin;
                ustawObiekty(ramka,'right',settings);
            }
            else $.fn.info.ustaw(obj,'left',settings);     
        }
        else if(infoPosition=='left') {
            if(czySieZmiesci(ramka.innerWidth(),obj.offset().left)){
                l = obj.offset().left - ramka.innerWidth() - settings.strzalkaHeight - settings.margin;
                ustawObiekty(ramka,'left',settings);
            }
            else $.fn.info.ustaw(obj,'right',settings);    
        }        

        ramka.css({"left":l,"top":t});
    }

    function setEvents(obj,settings) {
        obj.bind({
            'mouseenter': function() { $.fn.info.pokaz(obj,settings); },
            'mouseleave': function() { $.fn.info.zamknij(obj); },
            'click': function() { $.fn.info.zamknij(obj); }
        }); 
    }

    function ustawObiekty(ramka,op,settings) {
        if(op=='top') {
            mt = Number(ramka.innerHeight()) - 1;
            ramka.css("padding","0 0 0 0"); 
            ramka.find(".strzalka").addClass("bottomPos").css({"top":mt+"px","left":settings.strzalkaMarLeft+"px"});   
        }
        if(op=='bottom') {
            ramka.css("padding","0 0 0 0"); 
            ramka.find(".strzalka").addClass("topPos").css({"top":-settings.strzalkaHeight+1+"px","left":settings.strzalkaMarLeft+"px"});   
        }
        if(op=='right') {
            ramka.css("padding","0 0 0 0");
            ramka.find(".strzalka").addClass("leftPos").css({"top":settings.strzalkaMarTop+"px","left":-settings.strzalkaWidth+1+"px"});      
        }
        if(op=='left') {
            ml = ramka.innerWidth()*1-1;
            ramka.css("padding","0 0 0 0");
            ramka.find(".strzalka").addClass("rightPos").css({"top":settings.strzalkaMarTop+"px","left":ml+"px"});      
        }
    }


    function stworzRamke(id) {
        $("#infoContainer").append('<div class="info" info-id="'+id+'"><div class="text"></div><div class="strzalka"></div></div>');
        return $(".info[info-id="+id+"]");
    }
    function zwrocRamke(id) {
        var ramka = $("#infoContainer").find("[info-id="+id+"]");
        if(ramka.length>0) return ramka;
        else return stworzRamke(id);
    }

    function getText(obj) {
        if(obj.attr("info-text")) return obj.attr("info-text");
        else return obj.html();    
    }

    function nastepneId() {
        var id;

        if($("#infoContainer .info").length==0) id = 1;
        else id = Number($("#infoContainer .info:last").attr("info-id")) + 1;
        
        if($("#infoContainer").find("[info-id="+id+"]").length==0) return id;
        else return id++;
    }

    function czySieZmiesci(przedmiot,przestrzen) { return przedmiot <= przestrzen ? true:false; }
    function offsetRight(obj) {
        var offsetRight = Number($(document).innerWidth()) - Number(obj.offset().left) - Number(obj.innerWidth());
        return offsetRight;
    }
}(jQuery));