$(document).ready(function() { 
    // inicjacja elementów strony
    // initMenu('init');

    // nadanie funkcji linkowi save z menu
    $("#saveBox a").off("click").on("click",function(){ initSaveButton(); });

    // wyświetlenie błędu
    initErrorBox();

    // init page
    page.init(); 

    // init dla modułów (metody w plikach odpowiednich dla modułu)
    if(typeof page.initModule=='function') page.initModule();

    // ustawienie loadera
    // loader.init();

    // pokazanie strony po zalogowaniu (animacja) 
    // showContent();       


    //zainicjowanie wstawiania tagów
    // if($(".tagsField").length>0) tagsField('init');

    //przygotowanie tablePage
    if($(".tablePage").length>0) $(".tablePage").css("min-height",$(window).innerHeight() - 45 +"px");

    // wstawienie licznika obiektów do fixedBox
    if($("#objCounter").length>0) $("#fixedBox").append($("#objCounter"));

    //ustawienie informacji
    if($(".information").length>0) $(".information").css("margin-top",(docHeight-315)/2+"px");

    // włączenie loadera przy przchodzeniu przez stronę przygotowującą
    if(findInUrl2('type')=='prepare') loader.on();
});


$(window).resize(function() { 
    // inicjacja elementów strony
    // initMenu('init');
    loader.init();
    
    //ustalenie wartości dynamicznych zmiennych
    page.scrollTopMax = $(document).innerHeight() - $(window).innerHeight() - page.mbScroll;
    if(page.loadObjects.on) page.loadObjects.spL = $(document).innerHeight() - $(window).innerHeight() - page.loadObjects.mbL;
}).load(function() {
    // inicjacja tabeli
    if(typeof page.initTable=='function') page.initTable();
});


///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////

// obiekt do którego ładuje zmienne, metody do strony o tych samych nazwach
var page = {
    // odległość od góry (w px) okna przeglądarki w której fixedBox przyczepia się do góry okna przeglądarki
    fixedScrollTop: 250,
    // maksymalna wartość scrolla pionowego
    scrollTopMax: 0,
    // odległość od dołu (w px) okna przeglądarki w której włącza się funkcja ładowania większej ilości obiektów
    mbScroll: 200,

    error: {
        showTime: 400,
        hideTime: 400,
        delayTime: 400
    },
    menu: {
        firstOpenTime: 100,
        openTime: 300,
        delayTime: 290
    },
    // ładowanie większej ilości obiektów np. albumów, zdjęć
    loadObjects: {
        on: false,
        firstLoaded: true,
        loaderOn: true,
        data: {},
        url: null,
        mbL: this.mbScroll,
        spL: null,
        clear: function() {
            this.data = {};
            this.url = null;
        }
    },

    // metoda inicjująca stronę
    // używana po każdej zmianie na stronie do odświeżenie obiektów
    init: function() {
        //ustalenie wartości dynamicznych zmiennych
        page.scrollTopMax = $(document).innerHeight() - $(window).innerHeight() - page.mbScroll;
        if(page.loadObjects.on) page.loadObjects.spL = $(document).innerHeight() - $(window).innerHeight() - page.loadObjects.mbL;

        // inicjacja obiektów
        if(typeof page.initObjects=='function') page.initObjects();

        // aktualizacja licznika obiektów
        if($("#objCounter").length>0) {
            $("#objCounter .loaded").html($(".albums li").length); 
            if(typeof ajax.sendData!='undefined' && ajax.sendData && ajax.sendData.objects) {
                var all = $("#objCounter .all").text();
                $("#objCounter .all").html(all-ajax.sendData.objects.length);
            }
        }

        //zablokowanie wyłączonych linków
        $("a.disabled").off("click").on("click",function() { 
            if($(this).is(".disabled")) return false;
        });

        //włączenie loadera przed przejściem do adresu w odpowiednich linkach
        $("[beforeUnload=loader]").off("click").on("click",function() {
            $(window).bind('beforeunload', function() {
                loader.on();
            });
        });
    }
}

// otwierane menu dla obiektów (np. editMenu i deleteMenu w albumach)
var objectMenu = {
    init: function(container) {
        var menu = container.children(".objectMenu");
        container.bind({
            'mouseleave': function() { menu.hide(); }
        });

        // ostatni element bez border
        if(menu.find(".row").length>0) menu.find(".row:not(.hidden):last").addClass("last");
        // funkcje
        if(menu.find("[link=edit]").length>0) menu.find("[link=edit]").click(function(){ editElement('on',$(this).parents("li")); });
       
        ajax.init(container);
        menu.data("ready",1);
    },
    toggle: function(container) { 
        var menu = container.children(".objectMenu");
        if(!menu.data("ready")) this.init(container);
        menu.toggle();
    },
    state: function(container) { return container.children(".objectMenu:visible").length>0 ? true:false; }
}

//funkcja ustawiajaca znacznik, że block został zmieniony
function changeBlock(op,block,html,field) {
    if(op=='add') {
        if($("#changeBlockList form input[name="+block+"]").length==0) $("#changeBlockList form").append(html);
    }
    if(op=='edit') {
        if($("#changeBlockList form input[name="+block+"]").length==1) $("#changeBlockList form input[name="+block+"]").attr(field,html); 
    }
    if(op=='init') {   
        var categoryId = $("#changeBlockList form input[name=categoryId]").val();
        var editType = getFieldName(block.attr("name"));
        if(editType=='tags') var editValue = escapeHtml(block.attr("value"));
        else var editValue = escapeHtml(block.val());
        editValue = editValue=='' ? ' ':editValue;

        if($("#changeBlockList form input[name=obj"+categoryId+"]").length==0) changeBlock('add','obj'+categoryId,'<input type="hidden" name="obj'+categoryId+'" '+editType+'="'+editValue+'" model="Blog">'); 
        else changeBlock('edit','obj'+categoryId,editValue,editType);       
    }
    if($("#saveBox").is(":hidden")) saveBox("on");
}
// usuwa wszystko dodane do changeBlogList 
//po kliknięciu w zapisz (poza tym co jest tam domyślnie)
function clearBlock(obj) {
    obj.children().each(function() {
        if($(this).attr("name")!='parentId' && $(this).attr("name")!='currentUrl') $(this).remove();
    });
}

var loader = {
    content: '#overflow',
    init: function() {
        //wymiary dokumentu i przeglądarki
        docWidth = $(document).innerWidth();
        docHeight = $(document).innerHeight();
        winWidth = $(window).innerWidth();
        winHeight = $(window).innerHeight();


        if($(this.content).is(":visible")) {
            var st = $(window).scrollTop();
            var opTop = (st <= page.fixedScrollTop) ? page.fixedScrollTop - st : 0;

            ml = (winWidth - $("#loader").width())/2;
            mt = ((winHeight + opTop - $("#loader").height())/2); 

            //ustaweienie odpowiednich rozmiarów dla overflow
            $(this.content).css({"width":docWidth+"px","height":docHeight+"px"});
            $(this.content+" #loader").css({"top":mt+"px","left":ml+"px"}).show();
        }
    },
    on: function() {
        $(this.content).show();
        this.init();
    },
    off: function() {
        $(this.content).hide();
        $(this.content+" #loader").hide();
    }
};


function showContent()
{
    if($("#bg").is(":visible")) {
        initErrorBox();
        cs.showContent();
    }
    else {
        var delayMenu = setTimeout(function() {
            $("#menu:hidden").fadeIn(500,function() {
                $(".pageText:hidden").fadeIn(500);
                $("#bg:hidden").slideDown(400,function() {
                    //nadanie wartości zmiennym
                    bgScrollTop = $("#bg").position().top==0 ? bgScrollTop:$("#bg").position().top;
                    initErrorBox();
                    cs.afCre = true;
                    cs.showContent();
                });
            });
        },'500');
    }
}


//funkcja wywoływana po załadowaniu strony
//sprawdza czy isntnieje jakiś błąd do wyświetlenia
function initErrorBox()
{
    //sprawdzenie bloku informującego o błędach (errorBox)
    if($("#errorBox").length>0 && $("#errorBox").text().length>0) var timeErrorDelay = setTimeout(function(){ errorBox(); },page.error.delayTime);   
}

//funkcja odpowiadająca za pokazanie i ukrycie informacji/błędu
var timeErrorBoxHide;
function errorBox() {
    clearTimeout(timeErrorBoxHide);

    if($(".errorHolder").is(":hidden")) $(".errorHolder").slideDown(page.error.showTime,function(){ $(this).addClass("fixed").attr("style",""); });
    $("#errorBox").slideDown(page.error.showTime,function() {
        timeErrorBoxHide = setTimeout(function() {
            $("#errorBox,.errorHolder").slideUp(page.error.hideTime,function() {
                $(".errorHolder").removeClass("fixed").attr("style","");
                // nadanie elementom galerii nowej pozycji w przestrzeni
                if(typeof select=='object') select.setPosObjs();
            });
        },6000);
        // nadanie elementom galerii nowej pozycji w przestrzeni
        if(typeof select=='object') select.setPosObjs();
    });
}

//funkcja ustawiajaca komunikat i nadająca klase błędowi
function showMsg(state,msgText) {
    state = state ? 'true':'false';
    $("#errorBox").removeClass("false true").addClass(state).html(msgText);
    errorBox();
}


// ładowanie obiektów do strony
page.loadObjects.new = function() {
    // ustalenie modelu
    this.set();

    //sprawdzenie czy aktualnie nie ładuje obiektów
    if(!this.container.is("[loadState=1]") || this.container.attr("block")=='yes') return false;    

    //zablokowanie włączenia tej funkcji do czasu załadowania obiektów
    this.container.attr("block","yes");

    // ustalenie potrzebnych zmiennych
    this.data.model = this.container.attr("model");
    this.data.visibleCount = this.container.find("li:not(.naglowek)").length;

    // wysłanie zapytania
    var self = this;
    var delayShowLoader;
    $.ajax({
        url: _directoryUrl+"/"+self.url,
        type: 'POST',
        data: self.data,
        beforeSend: function(){
            if(self.loaderOn) {
                clearTimeout(delayShowLoader);
                delayShowLoader = setTimeout(function(){ loader.on(); },1000);
            }
        },
        error: function() {
            showMsg(false,'Wystąpił błąd przy ładowaniu dalszej części strony. Proszę odświeżyć stronę.');
        },
        success: function(obj) { 
            // alert(obj);  
            if(obj=='off') self.container.attr("loadState","0");
            else self.container.find("li:last").after(obj);

            self.clear();
        },
        complete: function() {
            if(self.loaderOn) {
                clearTimeout(delayShowLoader);
                loader('off');
            }

            //odblokowanie włączenia tej funkcji 
            self.container.attr("block","no");
            
            page.init();
        }
    });
};


function initSaveButton() { 
    // wysłanie formularza po kliknięciu
    if($(".saveBoxOn").length>0 && typeof page=='object') {
        if(typeof upload=='object') page.sendForm();
        else $(".saveBoxOn").submit();
    }
    // wysłanie danych changeBlockList
    else if($("#changeBlockList form input").length>2) { 
        ajax.destinationUrl = $("#changeBlockList form").attr("action"); 
        ajax.settings = {'model':'page'};  
        var objs = {};

        $("#changeBlockList form input").each(function() {
            if($(this).attr("newPos")) {
                obj = eval("objs.newPos_"+$(this).attr('name')+" = {}");
                obj.model = $(this).attr("model");
                if($(this).attr("treePos")) obj.treePos = $(this).attr("treePos");
                if($(this).attr("treeType")) obj.treeType = $(this).attr("treeType");
                obj.parentId = $("#changeBlockList form input[name=parentId]").val();
                if($(this).attr("productTypeId")) obj.productTypeId = $(this).attr("productTypeId");
                if($(this).is("[name=albums]")) obj.newPosElements = getArrayOfList($(".albums li"));  
                else if($(this).is("[name=table]")) {
                    var table = (typeof obj.productTypeId!='undefined') ? $(".table[productTypeId="+obj.productTypeId+"] li") : $(".table li");
                    obj.newPosElements = getArrayOfList(table,".naglowek");
                    ajax.settings.changeLp = true;
                }
            }
            else if($(this).is("[name!=currentUrl]") && $(this).is("[name!=parentId]")) {   
                obj = eval("objs."+$(this).attr('name')+" = {}");
                obj.id = $(this).attr("name").substr(3);
                obj.model = $(this).attr("model");
                if($(this).attr("treeType").length>0) obj.treeType = $(this).attr("treeType");
                if($(this).attr("treeEdit").length>0) obj.treeEdit = $(this).attr("treeEdit");
                if($(this).attr("newText") && $(this).attr("newText").length>0) {
                    if(obj.model=='Gallery') obj.description = $(this).attr("newText");
                    else obj.name = $(this).attr("newText"); 
                    //usunięcie okna edycji 
                    editElement('remove',$(".albums li[objid="+obj.id+"]"));
                }
                if($(this).attr("title") && $(this).attr("title").length>0) obj.title = $(this).attr("title");
                if($(this).attr("text") && $(this).attr("text").length>0) obj.text = $(this).attr("text");
                if($(this).attr("videoLink")) obj.videoLink = $(this).attr("videoLink");
                if($(this).attr("tags")) obj.tags = $(this).attr("tags");
                if($(this).attr("state")) obj.state = $(this).attr("state");
                if($(this).attr("editState")) obj.editState = $(this).attr("editState");
            }
        });

        //wyczyszczenie #changeBlockList
        clearBlock($("#changeBlockList form"));

        //wysłanie danych
        saveBox("off");
        ajax.setObjects(objs);  
        ajax.request();     
    }
    else if($(".modifyImg-container").length>0) {
        var posTop = -$(".modifyImg-container .selection").position().top;
        var destinationUrl = location.href.split("/");
        destinationUrl.splice(destinationUrl.length-2, 2);
        destinationUrl = destinationUrl.join("/");

        saveBox("off");
        self.location.href=destinationUrl+"/type/noContent/top/"+posTop;
    }
}


function saveBox(op) {
    if(op=='on') {
        if($("#saveBox").is(":hidden")) {
            if($(".saveHolder").is(":hidden")) $(".saveHolder").slideDown(page.error.showTime,function(){ $(this).addClass("fixed").attr("style",""); });
            $("#saveBox").slideDown(page.error.showTime,function() {
                // nadanie elementom galerii nowej pozycji w przestrzeni
                if(typeof select=='object') select.setPosObjs();
            });
        }
    }
    if(op=='off') {
        if($("#saveBox").is(":visible")) {
            $("#saveBox,.saveHolder").slideUp(page.error.hideTime,function() {
                $(".saveHolder").removeClass("fixed").attr("style","");
                // nadanie elementom galerii nowej pozycji w przestrzeni
                if(typeof select=='object') select.setPosObjs();
            });
        }
    }
}


function editElement(op,obj) {
    if(op=='on') {
        var objEdit = ajax.model=='tableTransactions' ? $(obj[0]).find(".objEdit"):$(obj[0]).children(".objEdit");
        var editTextObj = objEdit.find(".editText");
        var editText = editTextObj.text();
        var hei = objEdit.height(); 
        var lineHeight,model;

        //ukrycie menu
        obj.parent().addClass("editing");
        if(ajax.model=='album') imgPanel.off(obj);
        // if(ajax.model=='table') dynamiMenu("off",obj);

        //inicjalizacja pola
        editTextObj.hide();
        var objEditForm = objEdit.find(".editForm");
        if(objEditForm.length==0) {
            if(ajax.model=='album') objEdit.append("<textarea class='editForm'>"+editText+"</textarea>");
            if(ajax.model=='table') objEdit.append("<input type='text' class='editForm' value='"+editText+"'>");
            if(ajax.model=='tableTransactions') {
                var states = $("#states select[name=state]").clone().addClass("editForm");
                states.find("option").each(function() {
                    if($(this).text()==editText) $(this).attr("selected",true);
                })
                objEdit.append(states);
            }
        }
        else objEditForm.show();

        //ustawienie zaznaczenia na końcu tekstu
        if(ajax.model!='tableTransactions') {
            var t = objEdit.find(".editForm").get(0);
            t.focus();
            t.selectionStart = t.selectionEnd = t.value.length;

            if(ajax.model=='album') {
                objEditForm.css({'height':hei+"px"});
                //zmiana rozmiarów pola
                objEditForm.autogrow({speed:100});
            }

            //ustawienie zdarzenia zamykającego edycji po kliknięciu poza obszar albumów
            objEdit.find(".editForm").focusout(function(){ editElement("off",obj); });      
        
            // zablokowanie i odblokowanie przekierowania po kliknięciu w link
            if(ajax.model=='album') $(".img").on("click", function(){ $(".img").off("click"); return false; });
        }
        else {
            //ustawienie zdarzenia zamykającego edycji po kliknięciu poza obszar albumów
            objEdit.find(".editForm").change(function(){ editElement("off",obj); }); 
        }
    }
    if(op=='off') {
        var objEdit = ajax.model=='tableTransactions' ? $(obj[0]).find(".objEdit"):$(obj[0]).children(".objEdit");
        var editTextObj = objEdit.find(".editText");
        var objEditForm = objEdit.find(".editForm");

        //sprawdzenie czy zaszły zmiany 
        if(editTextObj.text() != objEditForm.val()) {
            //dodanie obiektu 
            var model = obj.parent("ul").attr("model");
            var treeType = obj.parent("ul").attr("treeType");
            var treeEdit = obj.parent("ul").attr("treeEdit") ? obj.parent("ul").attr("treeEdit") : 'yes';
            var fieldName = (ajax.model=='tableTransactions') ? 'state':'newText';
            var editState = (ajax.model=='tableTransactions') ? 'yes':'no';
            if($("#changeBlockList form input[name=obj"+obj.attr("objid")+"]").length==0) changeBlock('add','obj'+obj.attr("objid"),'<input type="hidden" name="obj'+obj.attr("objid")+'" '+fieldName+'="'+objEditForm.val()+'" model="'+model+'" treeEdit="'+treeEdit+'" treeType="'+treeType+'" editState="'+editState+'">');   
            else changeBlock('edit','obj'+obj.attr("objid"),objEditForm.val(),fieldName);
        }
        
        //inizjalizacja pola
        if(ajax.model=='tableTransactions') editTextObj.html(objEditForm.find(":selected").text()).show();
        else editTextObj.html(objEditForm.val()).show(); 
        if(ajax.model=='album') {
            editTextObj.attr("href",objEdit.attr("link-href"));
            imgPanel.off(obj);
        }
        objEditForm.hide();
        obj.parent().removeClass("editing");
    }
    if(op=='remove') $(obj[0]).children(".objEdit").find(".editForm:first").remove();
}


// funkcja zamyka i otwiera gałęzie drzewa (ładuje gałęzie ajaxem)
function toggleTree(buttonObj) {
    var obj = buttonObj.parents("li:first");
    // zamknięcie
    if(buttonObj.is(".open")) {
        buttonObj.removeClass("open");
        buttonObj.text("+");
        // zamknięcie gałęzi drzewa
        if(obj.find(".child").length>0) obj.find(".child:first").hide();
    }
    // otworzenie
    else { 
        buttonObj.addClass("open");
        buttonObj.text("-");
        // otworzenie gałęzi drzewa
        if(obj.children(".child").length>0) obj.find(".child:first").show();
        else { 
            // załadowanie gałęzi drzewa przez ajaxa
            var model = obj.parent().attr("model");
            var parentId = obj.attr("objId");
            ajax.settings.model = 'page';
            ajax.noContent = false;
            ajax.async = false;
            ajax.sendType = 'POST';
            ajax.sendData = {'parentId':parentId,'model':model};
            ajax.settings.newData = true;
            ajax.destinationUrl = buttonObj.attr("ajax-link");
            ajax.obj = obj;
            ajax.request();
            var pl = obj.is(".last") ? '10':'9';
            var tW = obj.find(".titleCol").width() - 10;
            // var trW = obj.find(".treeCol").width();
            ajax.newData.css("padding-left","+="+pl+"px");
            ajax.newData.find(".titleCol").css("width",tW+"px");
            // ajax.newData.find(".treeCol").css("width",trW+"px");
            ajax.newData.show();
            obj.removeClass("unload");
        }
    }
}


function initMenu(op,obj) {
    // if(op=='init') {
    //     //zmiana menu
    //     var DelOpMO;
    //     $("#menu .firstLevel li").off("mouseenter mouseleave").bind({
    //         "mouseenter":function() { 
    //             var obj = $(this);
    //             if(!$(this).is(".active") || ($(this).is(".active") && $("#menu").is(".fixed"))) DelOpMO = setTimeout(function(){ initMenu("change",obj); },page.menu.delayTime); 
    //         },
    //         "mouseleave":function() { clearTimeout(DelOpMO); }
    //     });

    //     //ustawienie menu środku
    //     var widFL = 0;
    //     $("#menu .firstLevel li").each(function() { widFL += $(this).width(); });
    //     var mlFL = (winWidth - widFL) / 2;
    //     $("#menu .firstLevel").css({"margin-left":mlFL+"px","width":widFL+"px"});
    //     $("#menu .firstLevel li").fadeIn(page.menu.firstOpenTime);

    //     $("#bg").attr("block","no");
    // }
    // if(op=='init' || op=='change') {
    //     //zmiana submenu i menu
    //     if(op=='change') {
    //         //zamknięcie wszystkich otwartych submenu i usunięcie znaczników z menu
    //         $("#menu .firstLevel li.active").removeClass("active");
    //         $("#bg ul:visible").hide();
    //         obj.addClass("active");
    //     }

    //     var activeClass = $("#menu .firstLevel li.active").attr("id");
    //     var submenuObj = $("#bg ul[class="+activeClass+"]");

    //     //wysrodkowanie submenuObj
    //     var widBG = 0;
    //     submenuObj.find("li").each(function() { widBG += $(this).width(); });
    //     var countOfGroup = submenuObj.find(".group").length;
    //     var margin = parseInt(submenuObj.find(".group").css("padding-left")) + parseInt(submenuObj.find(".group").css("padding-right"));
    //     widBG += countOfGroup * (margin ? margin:1);
    //     var mlBG = (winWidth - widBG) / 2;
    //     submenuObj.css({"left":mlBG+"px","width":widBG+"px"}).fadeIn(page.menu.openTime);
    // }
}


function sliderChangeOption(obj) {
    var f = obj.find(".sliderLink.hidden").removeClass("hidden");
    obj.find(".sliderLink.visible").hide().removeClass("visible").addClass('hidden');
    f.addClass("visible").show();
    if(obj.find(".inSlider").length>0) obj.find(".inSlider").remove();
    else {
        obj.find(".imgPanel").after($("#constObjects").find(".inSlider").clone());
        page.init();
        obj.find(".inSlider").show();
    }
}
function visibleChangeOption(obj,settings) {
    var obj = $(obj[0]);
    var visibleButton = $(obj.find(".visibleButton")[0]);
    var ajaxLink = visibleButton.attr("ajax-link");

    var findLink = findInLink(ajaxLink,'cmd');
    var cmd = findLink=='show' ? 'hide':'show';
    var visibleText = findLink=='show' ? $("#constObjects .visibleTxtHide").text() : $("#constObjects .visibleTxtShow").text();

    //edycja ajaxLink
    ajaxLink = editLink(ajaxLink,'cmd',cmd);
    visibleButton.attr("ajax-link",ajaxLink);
    if(settings.txt=='yes') visibleButton.children(".txt").text(visibleText);
    if(settings.text=='yes') visibleButton.text(visibleText);
    else visibleButton.attr("info-text",visibleText);

    if(settings.model=='album') {
        //oznaczenie obiektu ikoną 
        if(obj.find(".hiddenObjClass").length>0) obj.find(".hiddenObjClass").remove();
        else {
            obj.find(".imgPanel").after($("#constObjects").find(".hiddenObjClass").clone());
            page.init();
            obj.find(".hiddenObjClass").show();
        }
    }   
    else if(settings.model=='table') obj.toggleClass("hiddenObj");
}

function tagsField(op) {
    if(op=='init' && $("#tags").length>0)
    {
        $("#tags").tagsInput({
            'height':'36px',
            'width':'1000px',
            'removeWithBackspace' : true,
            'placeholderColor' : '#666666',
            onChange: function(){ changeBlock('init',$("#tags")); },
            onAddTag: function(){ saveBox("on"); }
        });
        $(".tagsinput").corner("5px");
        $(".tagsinput").sortable({
            update: function(){ 
                $("#tags").updateTagsField();
                changeBlock('init',$("#tags")); 
            } 
        });
    }
}


// obiekt "synchronizacji"
// uruchamia funkcje obserwującą zmiane jego włąściwości
var cs = {
    langs: false,
    hiddenLink: false,
    afCre: false,
    showContent: function() {
        this.langs = true;
        this.hiddenLink = true;
    }
}



// obiekt dla zapytań ajaxowych
// ustawia zdarzenia do linków oraz wykonuje zapytanie
var ajax = {
    model: null,
    sendType: 'GET',
    sendData: null,
    destinationUrl: null,
    settings: {},
    noContent: true,
    async: true,
    backTo: null,

    init: function(parent,off) { 
        parent.find("[ajax=yes]").each(function() {
            if(!$(this).data("ajax-state-ready")) { 
                $(this).data("ajax-state-ready",true);
                $(this).click(function(e) { 
                    // sprawdzenie czy link jest aktywny
                    if(!$(this).is("[ajax-state]") || $(this).is("[ajax-state=active]")) {
                        var obj = $(this).parents('li');
                        var options = $(this).attr("ajax-options") ? $(this).attr("ajax-options") : '';
                        ajax.destinationUrl = $(this).attr("ajax-link");
                        ajax.settings = addStringToObject({},options);
                        ajax.settings.model = !ajax.settings.model ? ajax.model : ajax.settings.model;

                        // ustalenie obiektów
                        ajax.setObjects(obj);

                        // wysłanie zapytania
                        ajax.request();

                        //ukrycie menu
                        if(ajax.model=='album') obj.find(".imgPanel:visible").hide().find(".absoluteMenu").hide();
                    }

                    // wyłączenie po pierwszym kliknięciu
                    if(off) $(this).attr("ajax-state","unactive");
                });
            }
        });
    },
    // data - json object
    setObjects: function(data) {
        // dla changeBlockList (zmiana pozycja, edycja elementów)
        if(ajax.settings.model=='page') {
            this.sendType = 'POST';
            this.sendData = data;
        }
        // dla pozostałych mamy doczynienia z pojedyńczym obiektem
        else {
            // dla operacji na wielu obiektach
            if(ajax.settings.count=='many') {
                this.obj = $(".albums li.selected");
                this.sendType = 'POST';
                this.sendData = {objects: []};
                this.sendData.categoryId = $(".albums").attr("categoryId");
                this.sendData.model = $(".albums").is(".imagesBlog") ? 'BlogImages':'Gallery';
                var i = 0;
                this.obj.each(function() { 
                    ajax.sendData.objects[i] = [$(this).attr("objId"),$(this).attr("objName")]; 
                    i++;
                });
            }
            // dla jednego obiektu
            else this.obj = data;
        }
    },

    // funkcja pracyje na 3 obiektach: 1. album (albumy lub zdjęcia), 2. table (blog, podstrony),
    // 3. page (np. przy zapisywaniu, linki) 4. data
    request: function() {
        var noContentParam = ajax.noContent ? '/type/noContent':'';
        var delayLoadeOpen;

        $.ajax({
            url: ajax.destinationUrl+noContentParam,
            type: ajax.sendType,
            data: ajax.sendData,
            async: ajax.async,
            beforeSend: function() {
                if(ajax.settings.model=='album') ajax.obj.find(".img").addClass("load").find("img").hide();
                if(ajax.settings.model=='page' || ajax.settings.model=='table') delayLoadeOpen = setTimeout(function() { loader.on(); },200);
            },
            error: function(jqXHR,errorText,errorThrown) {
                // alert(errorText+" "+errorThrown);
                if(ajax.settings.model=='album') ajax.obj.find(".img").removeClass("load").find("img").show();
                showMsg(false,'Wystąpił błąd. Proszę odświeżyć stronę i spróbować ponownie.');
            },
            success: function(result) { 
                // alert(result);
                if(result=='') showMsg(false,"Istnieje problem ze skryptem. W razie powtarzania błędu zgłoś to twórcy strony.");
                else {
                    // dla wywołaniu zwracającego wynik
                    if(ajax.settings.newData) {
                        ajax.newData = $(result);
                        if(ajax.settings.empty) ajax.obj.empty();
                        ajax.obj.append(ajax.newData);
                        ajax.settings = {model:ajax.settings.model};
                        ajax.clean();
                    }
                    else {
                        var data = eval('('+result+')');
                        if(data.state) {
                            // usuwanie obiektu
                            if(ajax.settings.remove) { 
                                ajax.obj = $(ajax.obj[0]);
                                $.when(ajax.obj.fadeOut(200)).then(function() { 
                                    ajax.obj.remove();
                                    // zmniejszenie licznika obiektów poprwaić!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                                    if($("#objCounter").length>0) $("#objCounter .all").text(Number($("#objCounter .all").text())-1);
                                    page.init(); 
                                    if(ajax.settings.model=='table') page.updateTable(); 
                                    ajax.clean(); 
                                });
                            }
                            else {
                                if(ajax.settings.changeClass) {
                                    if(ajax.settings.changeClass=='slider') sliderChangeOption(ajax.obj);
                                    if(ajax.settings.changeClass=='visible') visibleChangeOption(ajax.obj,ajax.settings); 
                                }
                                if(ajax.settings.model=='album') ajax.obj.find(".img").removeClass("load").find("img").show();
                            }

                            // odświeżenie elementu
                            if(ajax.settings.refresh) {
                                var func = $(ajax.settings.refresh).attr("refresh-function");
                                eval(func);
                            }

                            if(ajax.settings.model=='page') if(ajax.settings.changeLp) page.updateTable();
                        }

                        //wyświetlenie komunikatu
                        if(data.state || !data.state) showMsg(data.state,data.msgText);
                    }
                }

                // przekierowanie
                if(ajax.backTo) var t = setTimeout(function() { window.location.href=ajax.backTo; },1000);
            },
            complete: function() {
                if(ajax.settings.model=='page') page.init();
                if(ajax.settings.model=='page' || ajax.settings.model=='table') {
                    clearTimeout(delayLoadeOpen);
                    loader.off();    
                }
            }
        });
    },
    clean: function() {
        ajax.sendType = 'GET';
        ajax.sendData = null;
        ajax.destinationUrl = null;
        ajax.noContent = true;
        ajax.async = true;
        ajax.backTo = null;
    },
    objState: function(obj) {
        return (obj.data("ajax-state-ready")) ? true:false;
    }
}