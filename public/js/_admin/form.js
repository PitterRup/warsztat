var form = {
    model: null,
	init: function(obj) {
        if(obj.find("textarea,input").length>0) {
            obj.find("textarea,input").bind({
                'focus':function() {
                    if($(this).val()==$(this).attr("textBlur")) $(this).val(""); //stare
                    // if($(this).val()==$(this).attr("placeHolder")) $(this).val("");
                },
                'blur':function() {
                    if($(this).val()=='') $(this).val($(this).attr("textBlur")); //stare
                    // if($(this).val()=='') $(this).val($(this).attr("placeHolder"));
                }
            });
        }
        if(obj.find("select").length>0) if(navigator.userAgent.toLowerCase().indexOf('chrome') > -1) obj.find("select").css("padding-top","0px");
    }
}

$(document).ready(function() {
	if($(".form").length>0) {
		form.init($("form"));

        // ckeditor
		if($(".form textarea.description").length>0) {
            var settings = null;
            if(form.model=='subpages') {
                var ckeHeight = winHeight - 200;
                settings = {height: ckeHeight};
            }

            if(settings) $(".form textarea.description").ckeditor(settings);
            else $(".form textarea.description").ckeditor();

    		// otwarcie saveBox dla edycji
            for(var i in CKEDITOR.instances) CKEDITOR.instances[i].on('change', function() { saveBox("on"); });
        }
	}
	if($(".editFormAjax").length>0) $(".editFormAjax").on("change",function(){ changeBlock('init',$(this)); });
    if($(".saveBoxOn").length>0) setTimeout(function() { saveBox("on"); $(this).off("change"); }, 500);
    if($("#upload").length>0) $("#upload").on("change",function() { saveBox("on"); $(this).off("change"); });
});