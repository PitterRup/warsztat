$(window).scroll(function() {
    // sprawdzenie czy jesteśmy na wysokości obiektu następnego do załadowania
    if(imgQueue.data[imgQueue.loaded] && imgQueue.data[imgQueue.loaded][1] <= $(this).scrollTop()+winHeight-100) {
        imgQueue.load(imgQueue.loaded);
    }
});

var imgQueue = {
    status: false, // status używania obiektu
	index: 0, //index ostatniego obiektu dodanego do kolejki
	count: 0, // liczba obiektów w kolejce
    loaded: 0, // ilość załadowanych obiektów
    curLoaded: 0, // ilość załadowanych obiektów w porcji do ładowania
    curCount: 0, // ilość obiektów w porcji do ładowania

    data: [], // kolejka obiektów
    limit: 20, // ilość ladowanych obiektów
    loading: false, // gdy obiekty są ładowane: true

    container: null,
    ele: null,

    set: function(container,ele) {
        this.status = true;
        this.container = container;
        this.ele = ele;

    	// ustalenie liczby obiektów
    	this.count = this.container.find(this.ele).length;
    	this.initObjects();
    },
    initObjects: function() {
        imgQueue.data = [];  
        imgQueue.index = 0;
        this.container.find(this.ele).each(function() { 
            imgQueue.data[imgQueue.index] = [$(this),$(this).offset().top];
            imgQueue.index++;
        });    

        this.loaded = this.container.find(this.ele).index(this.container.find(this.ele+".loaded:last")) + 1;
    },
    load: function(index) {
        if(!this.loading) {
            this.loading = true;
            for(var i=0; i<this.limit; i++) {
                if(typeof this.data[index+i]=='undefined') break;
                this.curCount++;
                var obj = this.data[index+i][0].find(".img img");
                obj.hide();
                obj.attr("src",obj.attr("link")); 
                $(obj.parents("li")[0]).addClass("loaded");
                this.showObj(obj);
            }
        }
    },
    showObj: function(obj) {
        obj.imagesLoaded().done(function() {
            helper.setOnMiddle(obj);
            obj.show();
            imgQueue.curLoaded++;
            imgQueue.loaded++;

            // odblokowanie funkcji
            if(imgQueue.curLoaded==imgQueue.curCount) {
                imgQueue.curLoaded = imgQueue.curCount = 0;
                imgQueue.loading = false;
            }
        });
    }
};