// obiekt zaznaczenia obiektów galerii
var select = {
    id: "selection",
    obj: "#selection",

    container: null,
    ele: null,

    selecting: false,
    startSelect: false,
    stop: false,

    xDirection: null, // horizontalDirection
    xPrevDirection: null,
    xDirectionMove: null,
    yDirection: null, // verticalDirection
    yPrevDirection: null,
    yDirectionMove: null, 

    width: 0,
    height: 0,

    left: 0,
    right: 0,
    top: 0,
    bottom: 0,
    prevLeft: 0,
    prevRight: 0,
    prevTop: 0,
    prevBottom: 0,
    startTop: 0,
    startLeft: 0,

    semaphore: false,

    objHeight: 0,
    objWidth: 0,
    objTopMin: null,
    objTopMax: 0,
    objLeftMin: null,
    objLeftMax: 0,

    ctrlDown: false,
    shiftDown: false,

    inLine: 6, // (ile w lini) ważne!!
    lines: [],
    newLineId: null,

    init: function(container,ele) {
        var self = this;

        // pobranie rozmiarów obiektu
        this.container = container;
        this.ele = ele;
        this.objHeight = $(this.container).children().innerHeight();
        this.objWidth = $(this.container).children().innerWidth();

        // wrzucenie obiektów do strony
        $(this.container).append($("<div id='"+this.id+"'></div>"));
        // $("body").append("<div id='"+this.menu.id+"'><ul></ul></div>");
        // $(this.menu.obj+">ul").append("<li><a class='delete' ajax='yes' ajax-link='"+_directoryUrl+"/ajax/deleteImages' ajax-options='model:album,remove:true,count:many'><span class='name'>usuń</span> <span class='key'>Shitf + Del</span></a></li>");
        // $(this.menu.obj+">ul").append("<li><a class='hide' ajax='yes' ajax-link='"+_directoryUrl+"/ajax/hideImages' ajax-options='model:album,remove:true,count:many'><span class='name'>ukryj</span> <span class='key'>Shift + U</span></a></li>");
        // $(this.menu.obj+">ul li:last").addClass("last");
        // ajax.init($(this.menu.obj+">ul"));

        // zablokowanie prawego przycisku myszy dla obiektów galerii
        // $(document).bind("contextmenu",function(e) {
        //     if($(e.target).parents("li").is(".selected")) {
        //         select.menu.on(e);
        //         return false;
        //     }
        // });

        // zdarzenia dla klawiszy
        $(document).bind({
            'keydown':function(e) {
                if(e.which=='17') select.ctrl = true;
                else if(e.which=='16') select.shift = true;

                if(!select.selecting && $(self.container+">"+self.ele+".selected").length > 0) {
                    // shit + delete
                    // if(e.which=='46' && e.shiftKey) $(select.menu.obj+" li .delete").trigger("click");
                    // shit + u
                    // if(e.which=='85' && e.shiftKey) $(select.menu.obj+" li .hide").trigger("click");
                }
            },
            'keyup':function(e) {
                if(e.which=='17') select.ctrl = false;
                else if(e.which=='16') select.shift = false;
            }
        });
            

        // zdarzenia dla kontenera
        $(this.container).bind({
            'mousedown': function(e) {
                // wyłączenie zaznaczenia które pozostało
                if(select.stop) select.off(e);

                // sprawdzenie czy zaczynamy zaznaczać tylko na wyznaczonym obszarze (pomijając obiekty)
                // i tylko dla lewego przycisku myszy
                if(e.which=='1' && $(e.target).is(self.container)) {
                    // wyłączenie menu zaznaczenia
                    // select.menu.off();
                    // włączenie zaznaczania
                    select.startSelect = true;
                }

                // odznaczenie obiektów (gdy ctrl nie wciśnięty)
                if(!select.ctrl && e.which!='3') self.unselectAll();
            },
            'mouseup': function(e) { select.off(e); },
            'mousemove': function(e) { 
                // tylko w momencie zaczęcia zaznaczania
                if(select.startSelect) {
                    // ustawienie zaznaczenia
                    select.selecting = true; 
                    select.startSelect = false; 
                    select.setStartSelection(e);
                }

                // zaznaczanie włączone i któryś przycisk myszy włączony
                if(select.selecting && e.buttons!=0) {
                    // sprawdzenie czy zaznaczanie nie zatrzymane
                    if(!select.stop) {
                        // sprawdzenie czy zaznaczenie nie jest ukryte (w celu naprawienia po najechaniu)
                        // $(self.obj).show();
                        // powiększanie zaznaczenia
                        select.setSelection(e);
                        // aktualizacja zaznaczenia obiektów
                        select.updateSelectObj();
                    }
                }
                else select.off();
            },

            // zatrzymanie gdy opuszczamy element
            'mouseleave': function() { select.stop = true; },
            'mouseenter': function() { select.stop = false; }
        });

        $(this.obj).bind({
            'mousemove':function(e) { 
                // wyłączenie gdy najedziemy bez trzymania przycisku myszy na zaznaczenie
                if(e.buttons==0) select.off(e); 
                // else $(self.obj).hide();
            }
        });

        $(this.container+">"+this.ele).click(function() {
            console.log("left: "+$(this).offset().left+" top:"+$(this).offset().top);
        });
    },

    off: function(e) {
        // włączenie menu zaznaczenia
        if(select.selecting && false) select.menu.on(e);

        // wyłączenie zaznaczania
        select.selecting = false;
        select.startSelect = false; 

        // wyczyszczenie właściwości
        select.cleanSelection();

        // usunięcie klasy curSlct (znacznik obiektów które brały udział w minionych zaznaczaniu)
        $(this.container+">"+this.ele+".curSlct").removeClass("curSlct");
    },

    setStartSelection: function(e) {
        this.startTop = e.pageY;
        this.startLeft = e.pageX;
        $(this.obj).css({
            'top': this.startTop+'px',
            'left': this.startLeft+'px',
            'width': this.width+'px',
            'height': this.height+'px'
        }).show();
    },
    setSelection: function(e) {
        x = e.pageX - this.startLeft;
        y = e.pageY - this.startTop;

        // zapisanie starych wartości
        if(!this.semaphore) {
            this.prevTop = this.top;
            this.prevBottom = this.bottom;
            this.prevLeft = this.left;
            this.prevRight = this.right;
            this.semaphore = true;
        }
        else this.semaphore = false;

        // wyznaczenie długości i odległości od lewej krawędzi
        this.width = x<0 ? Math.abs(x):x;
        this.left = (x>0 && this.startLeft!=this.left) ? this.startLeft : (x<0 ? e.pageX:$(this.obj).offset().left);
        this.right = this.left + this.width;
        // wyznaczenie wysokości i odległości od górnej krawędzi
        this.height = y<0 ? Math.abs(y):y;
        this.top = (y>0 && this.startTop!=this.top) ? this.startTop : (y<0 ? e.pageY:$(this.obj).offset().top);
        this.bottom = this.top + this.height;

        // wyznaczenie kierunku zaznaczania
        this.xDirection = (this.left==this.startLeft) ? 'right':'left';
        this.yDirection = (this.top==this.startTop) ? 'bottom':'top';

        // wyznaczenie kierunku poruszania
        if(this.xDirection=='left') this.xDirectionMove = (this.left!=this.prevLeft) ? ((this.left-this.prevLeft < 0) ? 'left':'-left') : null;
        else if(this.xDirection=='right') this.xDirectionMove = (this.right!=this.prevRight) ? ((this.right-this.prevRight > 0) ? 'right':'-right') : null;
        if(this.yDirection=='top') this.yDirectionMove = (this.top!=this.prevTop) ? ((this.top-this.prevTop < 0) ? 'top':'-top') : null;
        else if(this.yDirection=='bottom') this.yDirectionMove = (this.bottom!=this.prevBottom) ? ((this.bottom-this.prevBottom > 0) ? 'bottom':'-bottom') : null;

        // przy zmianie kierunku zaznaczenia
        if((this.xDirection!=this.xPrevDirection) || (this.yDirection!=this.yPrevDirection)) {
            this.xPrevDirection = this.xDirection;
            this.yPrevDirection = this.yDirection;
            this.unselectAll();
        }

        $(this.obj).css({
            'top': this.top+'px',
            'left': this.left+'px',
            'width': this.width+'px',
            'height': this.height+'px'
        });
    },
    cleanSelection: function() {
        this.width = 0;
        this.height = 0;

        this.startTop = 0;
        this.startLeft = 0;
        this.top = 0;
        this.prevTop = 0;
        this.bottom = 0;
        this.prevBottom = 0;
        this.left = 0;
        this.prevLeft = 0;
        this.right = 0;
        this.prevRight = 0;

        this.xDirection = null,
        this.xPrevDirection = null,
        this.xDirectionMove = null,
        this.yDirection = null,
        this.yPrevDirection = null,
        this.yDirectionMove = null,

        $(this.obj).hide();
    },

    // zaznaczenie/odznaczenie zdjęć
    updateSelectObj: function() {
        // sprawdza czy zaznaczenie jest na obszarze obiektów
        if(this.inSelectSpace()) {
            if($(this.container+">"+this.ele+".selected").length>0) this.checkSelectionBorder();
            else this.checkAllObj();
        }
        else this.unselectAll();
    },
    // przejrzenie wszystkich obiektów w poszukiwaniu tych znajdujących się w obszarze zaznaczenia
    checkAllObj: function() { 
        // console.log('checkAllObj');
        var container = $(this.container+">"+this.ele);
        var eleLength = container.length;
        for(var i=0; i<eleLength; i++) {
            var obj = container.eq(i);
            var objLeft = obj.data("left");
            var objRight = objLeft + this.objWidth;
            var objTop = obj.data("top");
            var objBottom = objTop + this.objHeight;
            

            // gdy obiekt pod zaznaczeniem
            if(this.bottom < objTop) break;
            // gdy obiekt nad zaznaczeniem
            if(this.top > objBottom) {
                //przechodzi do następnej linijki
                i += this.inLine-1; 
                continue;
            }
            // gdy obiekt po lewej stronie zaznaczenia
            if(this.left > objRight) continue;
            // gdy obiekt po prawej stronie zaznaczenia
            if(this.right < objLeft) continue;

            // zaznacza obiekt
            if(!obj.is(".selected")) {
                obj.addClass("selected");
                this.addToArray(container.index(obj));
            }
        }
    },
    // przejrzenie elementów granicznych zaznaczenie (zaznaczonych i które mogą być zaznaczone)
    checkSelectionBorderOld: function() {
        // console.log('checkSelectionBorder');
        // przeglądanie tablicy zaznaczonych elementów
        var linesLength = this.lines.length;
        // console.log("linesLength:"+linesLength);
        for(var i=0; i<linesLength; i++) {
            // wyjście z pętli gdy natrafimy na brak lini
            if(typeof this.lines[i]=='undefined') break;

            var lineLength = this.lines[i].length;
            var vertCheckLogic = (this.yDirection=='bottom') ? (i==0 || i<linesLength-1) : ((linesLength>1) ? (i>0):(i==0));
            var horiCheckLogic = (this.yDirection=='bottom') ? (i==linesLength-1) : (i==0);

            // sprawdzenie elementów ostatniego w lini i następnego (niezaznaczonego)
            // *zależnie od kierunku
            if(vertCheckLogic) {
                // console.log("linia pionowa");
                var last = (this.xDirection=='right') ? (lineLength-1) : 0;
                this.setSelectionToObj(this.lines[i][last]);
                if(typeof this.lines[i]!='undefined') {
                    var afterLast = (this.xDirection=='right') ? (this.lines[i][last]+1) : (this.lines[i][last]-1);
                    if(afterLast % this.inLine != ((this.xDirection=='right') ? 0:this.inLine-1)) this.setSelectionToObj(afterLast);
                }
            }
            // sprawdza całą ostatnią linijkę
            else if(horiCheckLogic) {
                // console.log("ostatnia linia");
                for(var j=0; j<lineLength; j++) {
                    this.setSelectionToObj(this.lines[i][j]);
                    // dla ostatniego elementu który nie jest pierwszym w nowej lini
                    if((typeof this.lines[i]!='undefined') && (j==lineLength-1)) {
                        var afterLast = (this.xDirection=='right') ? (this.lines[i][j] + 1) : (this.lines[i][0] - 1);
                        if(afterLast % this.inLine != ((this.xDirection=='right') ? 0:this.inLine-1)) this.setSelectionToObj(afterLast);
                    }
                }
            }
            
            // sprawdza całą linijkę poniżej zaznaczenia
            if(horiCheckLogic) {
                // console.log("linia pozioma");
                if(typeof this.lines[i]!='undefined') {
                    var patternLine = this.lines[i];
                    var lineLength = patternLine.length;
                    for(var j=0; j<lineLength; j++) {
                        var vector = (this.yDirection=='bottom') ? this.inLine : -1*this.inLine;
                        this.setSelectionToObj(patternLine[j]+vector);
                        // dla ostatniego elementu który nie jest pierwszym w nowej lini
                        if(j==lineLength-1) {
                            var afterLast = (this.xDirection=='right') ? (patternLine[j] + vector + 1) : (patternLine[0] + vector - 1);
                            if(afterLast % this.inLine != ((this.xDirection=='right') ? 0:this.inLine-1)) this.setSelectionToObj(afterLast);
                        }
                    }
                }
            }
        }
    },
    checkSelectionBorder: function() {
        // console.log('checkSelectionBorder');
        // przeglądanie tablicy zaznaczonych elementów
        var linesLength = this.lines.length;
        var firstLine = this.lines[0].slice();
        var lastLine = this.lines[linesLength-1].slice();
        for(var i=0; i<linesLength; i++) {
            // wyjście z pętli gdy natrafimy na brak lini
            if(typeof this.lines[i]=='undefined') break;

            var curLine = this.lines[i].slice();
            var lineLength = curLine.length;
            
            // sprawdzenie całych lini
            if(i==0) {
                // console.log("cała linia");
                for(var j=0; j<lineLength; j++) {
                    // sprawdzenie elementów z pierwszej i ostatniej lini
                    this.setSelectionToObj(firstLine[j]);
                    if(linesLength > 1) this.setSelectionToObj(lastLine[j]);

                    // sprawdzenie elementów z przedpierwszej i zaostatniej lini
                    // if(typeof this.lines[i]!='undefined') {
                        if(firstLine[j]-this.inLine > 0) this.setSelectionToObj(firstLine[j]-this.inLine);
                        this.setSelectionToObj(lastLine[j]+this.inLine);
                    // }
                    
                    // sprawdzenie przedpierwszego/zaostatniego elementu w lini
                    // if((j==0) && (typeof this.lines[i]!='undefined')) {
                    if(j==0) {
                        // sprawdzenie czy elementy nie są z innej lini
                        if(firstLine[j] % this.inLine != 0) {
                            this.setSelectionToObj(firstLine[j]-1);
                            if(firstLine[j]-this.inLine > 0) this.setSelectionToObj(firstLine[j]-1-this.inLine);
                            if(linesLength > 1) this.setSelectionToObj(lastLine[j]-1);
                            this.setSelectionToObj(lastLine[j]-1+this.inLine);
                        }
                        if(firstLine[lineLength-1] % this.inLine != this.inLine-1) {
                            this.setSelectionToObj(firstLine[lineLength-1]+1);
                            if(firstLine[j]-this.inLine > 0) this.setSelectionToObj(firstLine[lineLength-1]+1-this.inLine);
                            if(linesLength > 1) this.setSelectionToObj(lastLine[lineLength-1]+1);
                            this.setSelectionToObj(lastLine[lineLength-1]+1+this.inLine);
                        }
                    }
                }
            }
            // sprawdzenie skrajnych elementów lini
            else if((i!=linesLength-1) && (linesLength > 2)) {
                // console.log("skrajne elementy");
                this.setSelectionToObj(this.lines[i][0]);
                if(typeof this.lines[i]!='undefined') {
                    if(this.lines[i][0] % this.inLine != 0) this.setSelectionToObj(this.lines[i][0]-1);
                    if(lineLength > 1) this.setSelectionToObj(this.lines[i][lineLength-1]);
                    if(this.lines[i][lineLength-1] % this.inLine != this.inLine-1) this.setSelectionToObj(this.lines[i][lineLength-1]+1);
                }
            }
        }
    },
    // sprawdza czy obiekt o podanym indeksie jest w obszarze zaznaczenia
    setSelectionToObj: function(index) {
        // console.log("spr. obiekt: "+index);
        var obj = $(this.container+">"+this.ele).eq(index);
        var objLeft = obj.data("left");
        var objRight = objLeft + this.objWidth;
        var objTop = obj.data("top");
        var objBottom = objTop + this.objHeight;
        // console.log("1:"+(this.top <= objTop && objTop <= this.bottom)+" 2:"+(this.top <= objBottom && objBottom <= this.bottom)+" (top:"+this.top+" bottom:"+this.bottom+" objBottom:"+objBottom+") 22:"+(this.top > objTop && objBottom > this.bottom)+" 3:"+(this.left <= objLeft && objLeft <= this.right)+" 4:"+(this.left <= objRight && objRight <= this.right)+" 44:"+(this.left > objLeft && objRight > this.right));
        if(((this.top <= objTop && objTop <= this.bottom) || 
        (this.top <= objBottom && objBottom <= this.bottom) ||
        (this.top > objTop && objBottom > this.bottom)) &&
        ((this.left <= objLeft && objLeft <= this.right) || 
        (this.left <= objRight && objRight <= this.right) ||
        (this.left > objLeft && objRight > this.right))) {
            if(!obj.is(".selected")) {
                // console.log("index: "+index+" -> selected");
                obj.addClass("selected");
                this.addToArray(index);
            }
        }
        else {
            if(obj.is(".selected")) {
                // console.log("index: "+index+" -> not selected");
                obj.removeClass("selected");
                this.removeFromArray(index);
            }
        }
    },
    // metoda tworzy tablice indeksów zaznaczonych elementów odwzorowując ich położenie w tablicy dwuwymiarowej
    addToArray: function(index) {
        // console.log("addToArray:"+index);
        a = this.lines.length;
        if(a==0) this.lines[0] = [index];
        else {
            // wstawia do poprzednio utworzonej lini
            if(typeof this.newLineId=='number' && typeof this.lines[this.newLineId]!='undefined') {
                this.lines[this.newLineId].push(index);
                // sprawdza czy wypełniono całą linie
                var patternLine = (this.yDirection=='top') ? this.lines[this.newLineId+1].length : this.lines[this.newLineId-1].length;
                // console.log("newLineId:"+this.newLineId+" patternLine:"+patternLine+" thisLength:"+this.lines[this.newLineId].length);
                if(this.lines[this.newLineId].length>=patternLine) this.newLineId = null;
            }
            else {
                var finded = false;
                // sprawdza czy indeks nie jest pierwszy w nowej lini
                if(this.yDirection=='bottom') var newLine = (this.xDirection=='right') ? ((index+1) % this.inLine == 1) : false;
                else if(this.yDirection=='top') var newLine = (this.xDirection=='left') ? ((index+1) % this.inLine == 0) : false;
                // przeszukuje linie
                if(!newLine) {
                    // console.log("not newLine");
                    for(var i=0; i < a; i++) {
                        // usuwanie obiektu z tabeli gdy istnieje
                        if(jQuery.inArray(index,this.lines[i]) >= 0) return false;

                        // czy porównywać za obiektem czy przed obiektem
                        var nextId = (this.xDirection=='right') ? (Number(this.lines[i][this.lines[i].length-1])+1) : (Number(this.lines[i][0])-1);
                        // console.log(nextId+" "+index);
                        if(nextId == index) {
                            if(this.xDirection=='right') this.lines[i].push(index);
                            else if(this.xDirection=='left') this.lines[i].unshift(index);
                            finded = true;
                            break;
                        }
                    }
                }

                // tworzy nową linie
                // (jeśli indeks nie pasuje do kolejnych indeksów istniejących lini) 
                if(!finded) {
                    // console.log("newLine");
                    // wstawia nową linijkę pod porównując wartość indeksu z linijki przedostatniej dla uniknięcia błędu
                    if((this.yDirection=='top') && (this.lines[this.lines.length-1][0] > index)) this.lines.unshift([index]);
                    else this.lines.push([index]);

                    // oznaczenie aby wrzucał obiektu do czasu zapełnienia lini
                    if(this.xDirection=='left') {
                        var newLineId = this.yDirection=='top' ? 0 : this.lines.length-1;
                        var patternLine = (this.yDirection=='top') ? newLineId+1 : newLineId-1;
                        if(this.lines[patternLine].length>1) this.newLineId = newLineId;
                    }
                }
            }
        }
        console.log(this.lines);
    },
    // usuwanie zaznaczonych elementów z tablicy
    removeFromArray: function(index) {
        // console.log("remove:"+index);
        a = this.lines.length;
        for(var i=0; i<a; i++) {
            var deleteId = jQuery.inArray(index,this.lines[i]);
            if(deleteId >= 0) {
                this.lines[i].splice(deleteId,1);
                if(this.lines[i].length==0) this.lines.splice(i,1);
                break;
            }
        }
        console.log(this.lines);
    },

    // sprawdzenie czy zaznaczenie jest w obrębie elementów
    inSelectSpace: function() {
        $(".bufor").html("<p>startTop: "+this.startTop+"</p>"
            +"<p class='margin'>startLeft: "+this.startLeft+"</p>"
            +"<p><b>top: "+this.top+"</b></p>"
            +"<p>prevTop: "+this.prevTop+"</p>"
            +"<p><b>bottom: "+this.bottom+"</b></p>"
            +"<p>prevBottom: "+this.prevBottom+"</p>"
            +"<p><b>left: "+this.left+"</b></p>"
            +"<p>prevLeft: "+this.prevLeft+"</p>"
            +"<p><b>right: "+this.right+"</b></p>"
            +"<p class='margin'>prevRight: "+this.prevRight+"</p>"
            +"<p>width: "+this.width+"</p>"
            +"<p class='margin'>height: "+this.height+"</p>"
            +"<p>topMax: "+this.objTopMax+"</p>"
            +"<p>topMin: "+this.objTopMin+"</p>"
            +"<p>leftMax: "+this.objLeftMax+"</p>"
            +"<p class='margin'>leftMin: "+this.objLeftMin+"</p>"
            +"<p>horizontalDirection: "+this.xDirection+"</p>"
            +"<p>horizontalPrevDirection: "+this.xPrevDirection+"</p>"
            +"<p>horizontalMove: "+this.xDirectionMove+"</p>"
            +"<p>verticalDirection: "+this.yDirection+"</p>"
            +"<p>verticalPrevDirection: "+this.yPrevDirection+"</p>"
            +"<p>verticalMove: "+this.yDirectionMove+"</p>");

        return ((this.objTopMin <= this.top && this.top <= this.objTopMax) || 
            (this.objTopMin <= this.bottom && this.bottom <= this.objTopMax) ||
            (this.objTopMin > this.top && this.bottom > this.objTopMax)) &&
            ((this.objLeftMin <= this.left && this.left <= this.objLeftMax) || 
            (this.objLeftMin <= this.right && this.right <= this.objLeftMax) ||
            (this.objLeftMin > this.left && this.right > this.objLeftMax)) ? true:false;
    },

    unselectAll: function() {
        var self = this;
        $(this.container+">"+this.ele+".selected").removeClass("selected");
        this.lines = [];
    },
    selectAll: function() {
        var self = this;
        $(this.container+">"+this.ele).each(function() {
            $(this).addClass("selected");
            self.addToArray($(self.container+">"+self.ele).index($(this)));
        });
    },

    // ustawienie pozycji obiektów i stałych wartości zmiennych
    setPosObjs: function() {
        var offLeft,offTop;
        var self = this;

        $(this.container+">"+this.ele).each(function() {
            offLeft = $(this).offset().left;
            offTop = $(this).offset().top;

            // ustalenie skrajnych wartości top i left obiektów
            self.objTopMin = (typeof self.objTopMin=='number')  ? ((offTop < self.objTopMin) ? offTop : self.objTopMin) : offTop;
            self.objTopMax = (offTop+$(this).innerHeight() > self.objTopMax) ? offTop+$(this).innerHeight() : self.objTopMax;
            self.objLeftMin = (typeof self.objLeftMin=='number') ? ((offLeft < self.objLeftMin) ? offLeft : self.objLeftMin) : offLeft;
            self.objLeftMax = (offLeft+$(this).innerWidth() > self.objLeftMax) ? offLeft+$(this).innerWidth() : self.objLeftMax;

            $(this).data({"left":offLeft,"top":offTop});
        });
    },


    menu: {
        id: 'selectMenu',
        obj: '#selectMenu',
        top: 0,
        left: 0,
        init: function(e) {
            this.top = e.pageY + $(this.obj).innerHeight() > winHeight ? e.pageY - $(this.obj).innerHeight() : e.pageY;
            this.left = e.pageX + $(this.obj).innerWidth() > winWidth ? e.pageX - $(this.obj).innerWidth() : e.pageX;
            $(this.obj).css({
                'top': this.top+'px',
                'left': this.left+'px'
            });

            $(this.obj).bind({
                'mouseleave':function() { select.menu.off(); },
                'click':function() { select.menu.off(); }
            });
        },
        on: function(e) {
            this.init(e);
            $(this.obj).show();
        },
        off: function() {
            $(this.obj).hide();
        }
    }
}

