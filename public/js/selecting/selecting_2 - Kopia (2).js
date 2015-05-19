$(window).resize(function() {
    select.setPosObjs();
    select.setInLine();
});

// obiekt zaznaczenia obiektów galerii
var select = {
    id: "selection",
    obj: "#selection",

    container: null,
    ele: null,

    selecting: false,
    startSelect: false,
    stop: false,

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

    containerLeft: 0,
    contaienrTop: 0,

    objHeight: 0,
    objWidth: 0,
    objTopMin: null,
    objTopMax: 0,
    objLeftMin: null,
    objLeftMax: 0,

    inLine: 0,
    firstId: -1,

    // synchronizacja zdarzeń
    objSelecting: false,

    ctrlDown: false,
    shiftDown: false,

    init: function(container, ele) {
        this.container = container;
        this.ele = ele;

        // ustalenie rozmiaru
        this.objHeight = $(this.container).children().innerHeight();
        this.objWidth = $(this.container).children().innerWidth();

        // wrzucenie obiektów do strony
        $(this.container).append($("<div id='"+this.id+"'></div>"));

        // ustalenie położenia obiektów
        select.setPosObjs();

        // utalenie ile w lini
        this.setInLine();

        // ustalenie położenia kontenera względem góry i lewej strony przegądarki
        this.containerLeft = $(this.container).offset().left;
        this.containerTop = $(this.container).offset().top;

        var self = this;
        // zdarzenia dla klawiszy
        $(document).bind({
            'keydown':function(e) {
                if(e.which=='17') select.ctrl = true;
                else if(e.which=='16') select.shift = true;
            },
            'keyup':function(e) {
                if(e.which=='17') select.ctrl = false;
                else if(e.which=='16') select.shift = false;
            }
        });
        
        // zdarzenia dla obiektów
        $(this.container+">"+this.ele).bind({
            'mousedown': function() {
                if(!select.ctrl) self.unselectAll(true);
                if(select.shift) self.selectSpaceTo($(this));
                if(!select.shift) $(this).toggleClass("selected constSelected");

                // oznaczenie ostatniego zaznaczonego obiektu
                if($(this).is(".selected") && !select.shift) {
                    $(self.container+" "+self.ele+".lastSelected").removeClass("lastSelected");
                    $(this).addClass("lastSelected");
                }
            }
        });

        // zdarzenia dla kontenera
        $(this.container).bind({
            'mousedown': function(e) {
                // wyłączenie zaznaczenia które pozostało
                if(select.stop) select.off(e);

                // sprawdzenie czy zaczynamy zaznaczać tylko na wyznaczonym obszarze (pomijając obiekty)
                // i tylko dla lewego przycisku myszy
                if(e.which=='1' && $(e.target).is(self.container)) select.startSelect = true;

                // odznaczenie obiektów (gdy ctrl nie wciśnięty)
                if(!select.ctrl && $(e.target).is(self.container)) self.unselectAll(true);
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

        // zdarzenia dla selection
        $(this.obj).bind({
            'mousemove':function(e) { 
                // wyłączenie gdy najedziemy bez trzymania przycisku myszy na zaznaczenie
                if(e.buttons==0) select.off(e); 
            }
        });
    },
    off: function(e) {
        // wyłączenie zaznaczania
        select.selecting = false;
        select.startSelect = false; 

        // wyczyszczenie właściwości
        select.cleanSelection();

        // nadanie klasy constSelected
        $(this.container+" "+this.ele+".selected:not(.constSelected)").addClass("constSelected");
    },
    setStartSelection: function(e) {
        var mX = e.pageX - this.containerLeft;
        var mY = e.pageY - this.containerTop;
        this.startTop = mY;
        this.startLeft = mX;
        $(this.obj).css({
            'top': this.startTop+'px',
            'left': this.startLeft+'px',
            'width': this.width+'px',
            'height': this.height+'px'
        }).show();
    },
    setSelection: function(e) { 
        var mX = e.pageX - this.containerLeft;
        var mY = e.pageY - this.containerTop;
        x = mX - this.startLeft;
        y = mY - this.startTop;

        // zapisanie starych wartości
        // if(!this.semaphore) {
        //     this.prevTop = this.top;
        //     this.prevBottom = this.bottom;
        //     this.prevLeft = this.left;
        //     this.prevRight = this.right;
        //     this.semaphore = true;
        // }
        // else this.semaphore = false;

        // wyznaczenie długości i odległości od lewej krawędzi
        this.width = x<0 ? Math.abs(x):x;
        this.left = (x>0 && this.startLeft!=this.left) ? this.startLeft : (x<0 ? mX:$(this.obj).position().left);
        this.right = this.left + this.width;
        // wyznaczenie wysokości i odległości od górnej krawędzi
        this.height = y<0 ? Math.abs(y):y;
        this.top = (y>0 && this.startTop!=this.top) ? this.startTop : (y<0 ? mY:$(this.obj).position().top);
        this.bottom = this.top + this.height;

        // wyznaczenie kierunku zaznaczania
        this.xDirection = (this.left==this.startLeft) ? 'right':'left';
        this.yDirection = (this.top==this.startTop) ? 'bottom':'top';

        // wyznaczenie kierunku poruszania
        // if(this.xDirection=='left') this.xDirectionMove = (this.left!=this.prevLeft) ? ((this.left-this.prevLeft < 0) ? 'left':'-left') : null;
        // else if(this.xDirection=='right') this.xDirectionMove = (this.right!=this.prevRight) ? ((this.right-this.prevRight > 0) ? 'right':'-right') : null;
        // if(this.yDirection=='top') this.yDirectionMove = (this.top!=this.prevTop) ? ((this.top-this.prevTop < 0) ? 'top':'-top') : null;
        // else if(this.yDirection=='bottom') this.yDirectionMove = (this.bottom!=this.prevBottom) ? ((this.bottom-this.prevBottom > 0) ? 'bottom':'-bottom') : null;

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

        this.firstId = -1;

        $(this.obj).hide();
    },
    // zaznaczenie/odznaczenie zdjęć
    updateSelectObj: function() {
        // sprawdza czy zaznaczenie jest na obszarze obiektów
        if(this.inSelectSpace()) {
            console.log("start checking");
            if(this.firstId < 0) this.checkAllObj();
            else this.checkSpace();
        }
        else this.unselectAll();
    },
    checkAllObj: function() {
        var first = true;
        this.firstId = $(this.container+" "+this.ele).index($(this.container+" "+this.ele+".selected"));

        // ustawienia dla kierunku zaznaczania
        if(this.yDirection=='bottom') {
            var forFrom = (this.firstId<0) ? 0 : this.firstId;
            var forTo = $(this.container+" "+this.ele).length;
        }
        else if(this.yDirection=='top') {
            // znalezienie ostatniego elementu w lini
            if(this.firstId >= 0) {
                var r = this.firstId % this.inLine;
                this.firstId += this.inLine - 1 - r;
            }
            var forFrom = this.firstId;
            var forTo = 0;
        }

        // analiza obiektów
        for(var i=forFrom; i<forTo; i++) { 
            // console.log(i);
            var obj = $(this.container+" "+this.ele).eq(i);

            // wyklucza wszystkie pod zaznaczeniem
            if(this.isUnderSelection(i,obj)) break;
            // wyklucza wszystkie powyżej zaznaczenia
            if(this.isAboveSelection(i,obj)) {
                i += this.inLine-1;
                continue;
            }
            // wyklucza wszystkie po lewej i prawej stronie zaznaczenia
            if(this.isNearSelection(obj)) continue;

            // zaznacza obiekt
            if(!obj.is(".selected")) {
                obj.addClass("selected");
                if(first) {
                    $(this.container+" "+this.ele+".lastSelected").removeClass("lastSelected");
                    obj.addClass("lastSelected");
                    first = false;
                }
            }
        }
    },
    checkSpace: function() {
        if(this.yDirection=='bottom') {

        }
        else if(this.yDirection=='top') {

        }
    },
    // metody sprawdzające położenie obiektu względem zaznaczenia
    isUnderSelection: function(i,obj) {
        var objTop = obj.data("top");
        if(this.bottom < objTop) {
            for(var j=0; j<=this.inLine; j++) if(!$(this.container+" "+this.ele).eq(i+j).is(".constSelected")) $(this.container+" "+this.ele).eq(i+j).removeClass("selected");
            return true;
        }
        else return false;
    },
    isAboveSelection: function(i,objBottom) {
        var objBottom = objTop + this.objHeight;
        if(this.top > objBottom) {
            for(var j=0; j<=this.inLine; j++) if(!$(this.container+" "+this.ele).eq(i+j).is(".constSelected")) $(this.container+" "+this.ele).eq(i+j).removeClass("selected");
            return true;
        }
        else return false;
    },
    isNearSelection: function(obj,objLeft,objRight) {
        var objLeft = obj.data("left");
        var objRight = objLeft + this.objWidth;
        if(this.left > objRight || this.right < objLeft) {
            if(!obj.is(".constSelected")) obj.removeClass("selected");
            return true;
        }
        else return false;
    },

    // ustawienie pozycji obiektów i stałych wartości zmiennych
    setPosObjs: function() {
        var offLeft,offTop;
        var self = this;

        $(this.container+">"+this.ele).each(function() {
            offLeft = $(this).position().left+parseInt($(this).css("margin-left"));
            offTop = $(this).position().top+parseInt($(this).css("margin-top"));

            // ustalenie skrajnych wartości top i left obiektów
            self.objTopMin = (typeof self.objTopMin=='number')  ? ((offTop < self.objTopMin) ? offTop : self.objTopMin) : offTop;
            self.objTopMax = (offTop+$(this).innerHeight() > self.objTopMax) ? offTop+$(this).innerHeight() : self.objTopMax;
            self.objLeftMin = (typeof self.objLeftMin=='number') ? ((offLeft < self.objLeftMin) ? offLeft : self.objLeftMin) : offLeft;
            self.objLeftMax = (offLeft+$(this).innerWidth() > self.objLeftMax) ? offLeft+$(this).innerWidth() : self.objLeftMax;

            $(this).data({"left":offLeft,"top":offTop});
        });
    },
    setInLine: function() {
        var cw = $(this.container).innerWidth();
        var cpl = parseInt($(this.container).css("padding-left"));
        var cpr = parseInt($(this.container).css("padding-right"));
        omr = parseInt($(this.container).children().css("margin-right"));
        oml = parseInt($(this.container).children().css("margin-left"));
        this.inLine = parseInt((cw - cpl - cpr) / (this.objWidth + oml + omr));
    },
    // sprawdzenie czy zaznaczenie jest w obrębie elementów
    inSelectSpace: function() {
        return ((this.objTopMin <= this.top && this.top <= this.objTopMax) || 
            (this.objTopMin <= this.bottom && this.bottom <= this.objTopMax) ||
            (this.objTopMin > this.top && this.bottom > this.objTopMax)) &&
            ((this.objLeftMin <= this.left && this.left <= this.objLeftMax) || 
            (this.objLeftMin <= this.right && this.right <= this.objLeftMax) ||
            (this.objLeftMin > this.left && this.right > this.objLeftMax)) ? true:false;
    },

    unselectAll: function(op) {
        $(this.container+">"+this.ele+".selected:not(.constSelected)").removeClass("selected");
        if(op) {
            $(this.container+">"+this.ele+".constSelected").removeClass("selected constSelected");
            // $(this.container+">"+this.ele+".lastSelected").removeClass("lastSelected");
        }
    },
    selectAll: function() {
        $(this.container+">"+this.ele).addClass("selected constSelected");
    },
    selectSpaceTo: function(obj) {
        var last = $(this.container+" "+this.ele+".lastSelected");
        var indexLast = $(this.container+" "+this.ele).index(last);
        var indexCur = $(this.container+" "+this.ele).index(obj);

        // zaznaczenie obiektów 
        if(indexLast > indexCur) {
            for(var i=indexLast; i>=indexCur; i--) $(this.container+" "+this.ele).eq(i).addClass("selected constSelected");
        }
        else {
            for(var i=indexLast; i<=indexCur; i++) $(this.container+" "+this.ele).eq(i).addClass("selected constSelected");
        }
    }
}