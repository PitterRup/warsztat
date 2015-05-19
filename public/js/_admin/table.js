// inicjacja obiektów
page.initObjects = function() {
	$(".table").find(".imgCol").each(function() {
		if(!$(this).data("state")) {
			var img = $(this).children("img");
			var mt = $(this).innerHeight() < img.innerHeight() ? -(img.innerHeight()-$(this).innerHeight())/2:0;
			var ml = $(this).innerWidth() < img.innerWidth() ? -(img.innerWidth()-$(this).innerWidth())/2:0; 
			img.css("margin",mt+"px 0 0 "+ml+"px");
			if(img.innerHeight()>0) $(this).data("state",true);
		}
	});
};

// odświeżenie tabeli
page.updateTable = function() { 
    // ponumerowanie wierszy
    $(".table").each(function() {
        if($(this).find("li>.lpCol").length>0) {
            var i=1;
            $(this).find("li:visible").not(".naglowek").each(function() {
                $(this).find(".lpCol").html(i);
                i++;
            });
        }
    });

    // poprawienie lini drzewa
    if($(".table>li:visible:not(.naglowek)>.treeCol").length>0) {
        var container = $(".table li:visible:not(.naglowek)");
        if(container.length>1) { 
            container.each(function() { 
                if($(this).prev().is(".naglowek")) $(this).children(".treeCol").addClass("ft"); 
                else if($(this).prev().length==0) $(this).children(".treeCol").addClass("ftC");
                if($(this).next().length==0) $(this).children(".treeCol").addClass("lt");
                if(!$(this).is(".unload") && $(this).children(".child").children("li").length==0) $(this).children(".more").html("");     
            });
        }
        else if(container.length==1) { $(".table>li>.treeCol").addClass("hide"); }
    }
};