$(function(){
    var ul = $('#upload ul.currentUpload');

    $('#drop .button').click(function(){
        // Simulate a click on the file input button
        // to show the file browser dialog
        $("#upload input[name=upl]").click();
    });

    // Initialize the jQuery File Upload plugin
    $('#upload').fileupload({

        // This element will accept file drag/drop uploading
        dropZone: $('#drop'),

        // This function is called when a file is added to the queue;
        // either via the browse button, or via drag/drop:
        add: function (e, data) {

            if($("#upload ul.unsave .title").length>0) $(".currentUpload .title").removeClass("hidden");

            var tpl = $('<li class="working"><input type="text" class="canvas" value="0" data-width="80" data-height="80"'+
                ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><div class="imgHolder"><p class="delete hidden"></p><p class="hidden imgTitle"></p></div></li>');

            
            // Add the HTML to the UL element
            data.context = tpl.appendTo(ul);
            
            // Initialize the knob plugin
            tpl.find('input').knob();

            // Listen for clicks on the cancel icon
            tpl.find('p.delete').click(function() {
                // if(tpl.hasClass('del')) {
                //     jqXHR.abort();
                //     tpl.fadeOut(function(){ tpl.remove(); });
                // }
                // else 
                deleteImg(tpl);      
            });

            //kolejkowanie uploadu plików
            queue.count.inQueue++;
            queue.data[queue.lp] = data;
            if(queue.uploading) { queue.uploading=false; queue.uploadFile(queue.lp); }
            queue.lp++;
        },

        progress: function(e, data) {

            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);

            // Update the hidden input field and trigger a change
            // so that the jQuery knob plugin knows to update the dial
            data.context.find('input').val(progress).change();

            // aktualizacja statusBar
            queue.statusBar.update(progress);

            if(progress == 100){
                data.context.removeClass('working');
                data.context.find("canvas").fadeOut(100);
            }
        },

        fail:function(e, data) { 
            data.context.find('.imgHolder').addClass("img uploaded").append('<img class="mini" src="'+_baseUrl+'/public/template/img/admin/errorImage.png" status="fail" />');
            
            //aktualizacja statusu operacji w %
            queue.statusBar.update(null);
            upload.initObj(data.context.find(".uploaded"));
        }

    });

    // Prevent the default action when a file is dropped on the window
    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    // Helper function that formats the file sizes
    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }

        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }

        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }

        return (bytes / 1000).toFixed(2) + ' KB';
    }
});

var queue = {
    lp: 0, //liczba plików wrzuconych przez użytkownika
    data: [], //kolejka plików
    statusBar: { //wyliczenie ilości zauplodowanych plików z przeciągniętych w procentach
        obj: "#saveBox .statusBar",
        start: function() {
            if($(this.obj).length==0) $("#saveBox").append("<span class='statusBar'>Ukończono: 0%</span>")
            else $(this.obj).text("Ukończono: 0%").show();
            $("head title").text("Trwa uploading zdjęć...");
        },
        end: function() {
            $(this.obj).text("Ukończono!");
            $("head title").text("Zakończono upload!");
        },
        update: function(progress) {
            if(typeof progress=='object' && !progress) queue.count.uploaded++;
            var result = queue.statusBar.set(progress);
            $(this.obj).text("Ukończono: "+result+"%");
            $("head title").text("Trwa uploading zdjęć ("+result+"%)");
        },
        set: function(progress) { 
            if(typeof progress=='null') return round((queue.count.uploaded/queue.count.inQueue)*100, 0);
            else return round(((queue.count.uploaded + progress/100)/queue.count.inQueue)*100, 0);
        }
    }, 
    count: {        
        "inQueue": 0, //ilość plików przeciągniętych plików
        "uploaded": 0 //ilosć zauplodowanych plików za jednym przeciągnięciem
    },
    uploading: true,
    uploadFile: function(lp) {
        var data = queue.data[lp];
        var tpl = data.context;

        //pokazanie statusBar przy wrzucaniu pierwszego pliku
        if(queue.count.uploaded==0) queue.statusBar.start();

        //upload pliku
        var jqXHR = data.submit().success(function(result, textStatus, jqXHR) {    
            //dołaczenie obrazka
            var categoryId = $("#upload input[name=categoryId]").attr("value");
            var albumsDir = $("#upload input[name=categoryId]").attr("albumsDir");
            var adminMiniDir = $("#upload input[name=categoryId]").attr("adminMiniDir");

            //wstawienie błędu        
            if(result=='') tpl.find('.imgHolder').addClass("img uploaded").append('<img class="mini" src="'+_baseUrl+'/public/template/img/admin/errorImage.png" status="noResult" />');
            else {
                var dane = eval('('+result+')');               
                var imgname = dane.name;
                var status = dane.status;

                if(status=='success') {
                    tpl.find(".imgTitle").html(imgname);
                    tpl.find(".imgHolder").removeClass("imgHolder").addClass("img uploaded").append('<img class="mini" src="'+_baseUrl+'/'+albumsDir+categoryId+"/"+adminMiniDir+"/"+imgname+'"/>');
                    tpl.find(".img").after('<p class="album"></p>');
                    if($(".currentUpload").attr("imgDesc")) tpl.find(".album").after('<p class="description"><textarea class="descriptionImg" textBlur="Opis...">Opis...</textarea></p>');
                }
                else {
                    tpl.find('.imgHolder').addClass("img uploaded").append('<img class="mini" src="'+_baseUrl+'/public/template/img/admin/errorImage.png" />');
                    showMsg("false",dane.errorText);
                }

                //dołączenie albumu
                var titleCat = $(".listOfCategory label");
                var categories = $(".listOfCategory .category");
                tpl.find('.album').append(titleCat.clone());
                tpl.find('.album').append(categories.clone());
            }
        }).done(function() {
            // pokazanie delete
            tpl.find(".delete").removeClass("hidden");
            //aktualizacja statusu operacji w %
            queue.statusBar.update(null);
            upload.initObj(tpl.find(".uploaded"));

            //upload kolejnego elementu
            lp++;
            if(queue.data[lp]) queue.uploadFile(lp);
            else {
                queue.statusBar.end();
                queue.count.inQueue = 0;
                queue.count.uploaded = 0;
                queue.uploading = true;
            }
        });
    } 
};

function deleteImg(tpl) {
    var categoryId = $("#upload input[name=categoryId]").attr("value"); 
    var nameImg = tpl.find(".imgTitle").text();
    var source = $("body").attr("controllerName");
        
    $.ajax({             
        url: _directoryUrl+'/ajax/delImgFromServer',
        dataType: 'HTML',
        type: 'POST',
        data: {'name':nameImg,'categoryId':categoryId,'source':source},
        error: function() {
            showMsg("false","Wystąpił błąd. Zdjęcie nie zostąło usunięte.");
        },
        complete: function() {                
            tpl.fadeOut(function() {
                tpl.remove();
                if($(".unsave li").length==0) $(".unsave .title").addClass("hidden");
                if($(".currentUpload li").length==0) $(".currentUpload .title").addClass("hidden");
            }); 
        }
    });
}