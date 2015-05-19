(function($) {
	$.fn.searchBox = function() {

		var formObj = null;
    	var box = null;
    	var loader = null;
    	var acceptKeys = [8,173,188,190,191,222,61,59,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,48,49,50,51,52,53,54,55,56,57,96,97,98,99,100,101,102,103,104,105];
    	var searching = false; 
    	var block = false; // blokowanie dalszego wyszukiwania
    	var url = null;
			
		function init() {
			url = formObj.attr("searchBox-link");
    		box = $("<div class='searchBox'><ul></ul><div class='searchLoader'></div></div>");
    		formObj.after(box);
    		box.css("min-width",$(formObj).innerWidth()+"px");
    		loader = box.find(".searchLoader");

    		formObj.bind({
	        	'keydown': function(e) { 
	        		// przesuwanie strzałkami po liście
	        		if(e.which=='38' || e.which=='40') {
	        			e.preventDefault();
	        			if(box.is(":hidden") && !box.children("ul").is(":empty")) box.show();
	        			selectObj(e.which);
	        		}
	        		// wybranie elementu przez enter lub strzałke w prawo
	        		if(e.which=='39') box.find("li.hover").trigger("click");
	        		if(e.which=='13') {
	        			e.preventDefault();
	        			box.find("li.hover").trigger("click");
	        			if(box.is(":visible")) box.hide(); 
	        		}
	        	},
	        	'keyup': function(e) { 
	        		if(jQuery.inArray(e.which,acceptKeys)>=0) {
	        			if(e.which==8) block = false;
	        			if(!searching && !block) search();
	        		}
	        	},
	        	'change':function(e) {
	        		// if(box.is(":visible")) box.hide(); 
	        	}
	        });
    	}

    	function search() {
    		var searchString = formObj.val();
    		var columns = formObj.attr("name");
    		    		
    		// console.log(searchString);
    		if(searchString.trim().length > 0) {
				box.children("ul").empty();
				if(box.is(":hidden")) box.show();

	    		$.ajax({
	    			url: url,
	    			type: 'POST',
	    			data: {'searchString':searchString, 'columns':columns},
	    			async: true,
	    			beforeSend: function() {
	    				loader.show();
	    				searching = true;
	    			},
	    			error: function() { alert('Wystąpił błąd!'); },
	    			success: function(result) {
	    				// alert(result);
	    				if(result) {
	    					loader.hide();
	    					box.children("ul").append(result);
	    					// zablokowanie szukania wyników dla dalszego pisania
	    					// gdy nie znaleziono nic
	    					if(box.children("ul").find(".noResults").length==1) block = true;
	    				}
	    			},
	    			complete: function() {
	    				// zdarzenia dla listy
	    				box.find("li").unbind("click").bind({
	    					'click': function() {
		    					var liVal = $(this).text();
		    					formObj.val(liVal);
		    					box.hide().find("li.hover").removeClass("hover");
		    				},
		    				'mouseenter': function() { $(this).addClass("hover"); },
		    				'mouseleave': function() { box.find("li").removeClass("hover"); }
	    				});

	    				var timeout = setTimeout(function() { searching = false; }, 200);
	    			}
	    		});
	    	}
	    	else box.hide().children("ul").empty();
    	}

    	function selectObj(key) {
    		if(key=='40') a = 'first';
    		else if(key=='38') a = 'last';

    		if(box.find("li.hover").length==0) box.find("li:"+a).addClass("hover");
			else {
				var active = box.find("li.hover");
				if(key=='40') b = active.next();
    			else if(key=='38') b = active.prev();

				active.removeClass("hover")
				if(b.is("li")) b.addClass("hover");
				else box.find("li:"+a).removeClass("hover");
			}
    	}

		return this.each(function() {
			formObj = $(this);
			init();
		});
	}
})(jQuery);