$(function(){
    var ul = $("#order-content .order-objs");
    var allowedImgExtension = $("#upload input[name=allowedImgExtension]").val().split(",");

    $('#drop .button').click(function() {
        $("#upload input[name=upl]").click();
    });

    $("#order-page #addMoreImages").click(function() {
        $("#upload input[name=upl]").click();
    });

    // Initialize the jQuery File Upload plugin
    $('#upload').fileupload({

        // This element will accept file drag/drop uploading
        dropZone: $('#drop'),

        // This function is called when a file is added to the queue;
        // either via the browse button, or via drag/drop:
        add: function (e, data) {

            // sprawdzenie czy plik nie ma prawidłowego rozszerzenia
            var extension = getExtension(data.files[0].name.toLowerCase());
            if($.inArray(extension, allowedImgExtension) < 0) alert('Rozszerzenie .'+extension+' jest niedozwolone. Plik "'+data.files[0].name+'" nie zostanie wrzucony.');
            else {
                var tpl = $('<li class="order-obj working" queueLp="'+queue.lp+'"><div class="uplStatus" style="margin-top: 67px"><div class="valueSpace"></div></div><div class="img">'
                        +'</div><div class="order-ops ool" place="ool"></div><div class="order-ops oor" place="oor"></div><p class="delete">x</p></li>');

                // Add the HTML to the UL element
                data.context = tpl;
                ul.append(tpl);
                
                // Initialize the knob plugin
                tpl.find('input').knob();

                // Listen for clicks on the cancel icon
                tpl.find('p.delete').click(function() {
                    if(tpl.hasClass('working')) {
                        var lp = $(this).parent().attr("queueLp");
                        if(lp==queue.curLp) queue.data[lp].obj.abort();
                        else queue.data[lp].status = false;
                        tpl.fadeOut(200,function(){ tpl.remove(); });
                        // aktualizacja kolejki uploadu
                        queue.count.inQueue--;
                        // odświeżenie order
                        order.initAfterRemove();
                    }
                    else deleteImg(tpl);       
                });

                //kolejkowanie uploadu plików
                queue.count.inQueue++;
                queue.data[queue.lp] = {obj:data, status:true};
                if(!queue.uploading) { queue.uploading=true; queue.uploadFile(queue.lp); }
                queue.lp++;
            }
        },

        progress: function(e, data) {

            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);

            // Update the hidden input field and trigger a change
            // so that the jQuery knob plugin knows to update the dial
            if(data.context.find('.uplStatus').is(":hidden")) data.context.find('.uplStatus').show();
            data.context.find('.uplStatus .valueSpace').css("width",progress+"%");

            // aktualizacja statusBar
            queue.statusBar.update(progress);

            if(progress == 100){
                data.context.removeClass('working');
                data.context.find(".uplStatus").hide();
            }
        },

        fail:function(e, data) { 
            data.context.find('.img').addClass("uploaded").append('<img alt="" src="'+_baseUrl+'/public/template/img/admin/errorImage.png" status="fail" />');
            data.context.addClass("error");

            //aktualizacja statusu operacji w %
            queue.statusBar.update(null);
            upload.initObj(data.context.find(".uploaded").children("img"));
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
    curLp: 0, // indeks aktualnego uploadowanego obiektu
    data: [], //kolejka plików
    statusBar: { //wyliczenie ilości zauplodowanych plików z przeciągniętych w procentach
        obj: "#uploadStatus",
        soundOn: false,
        start: function() {
            $(this.obj).slideDown(400).find(".progress").text("0%");
            var a = (queue.count.inQueue < 100 || queue.count.inQueue > 199) ? 'z':'ze';
            $("head title").text("Trwa ładowanie zdjęć ("+queue.count.uploaded+" "+a+" "+queue.count.inQueue+")");

            // pokazanie order-content
            $("#upload").fadeOut(400,function() {
                order.content.obj.fadeIn(200).removeClass("editing");
            });

            // wyłączenie zaznaczenia (jesli włączone)
            select.stateOff();
        },
        end: function() {
            // odtworzenie dźwięku zakończenia uploadu
            if(this.soundOn) $("#upload-complete")[0].play();
            $("head title").text("Ładowanie zakończone!");
            $(this.obj).slideUp(200);
            
            // włączenie trybu edycji order-content
            order.content.obj.addClass("editing");
            order.content.init();
        },
        update: function(progress) {
            if(typeof progress=='object' && !progress) queue.count.uploaded++;
            var process = queue.statusBar.set(progress);
            $(this.obj).find(".progress").text(process+"%");
            $(this.obj).find(".uplStatus .valueSpace").css("width",process+"%");
            var a = (queue.count.inQueue < 100 || queue.count.inQueue > 199) ? 'z':'ze';
            $("head title").text("Trwa uploading zdjęć ("+queue.count.uploaded+" "+a+" "+queue.count.inQueue+")");
        },
        set: function(progress) { 
            if(typeof progress=='null') return Math.floor((queue.count.uploaded/queue.count.inQueue)*100, 0);
            else return Math.floor(((queue.count.uploaded + progress/100)/queue.count.inQueue)*100, 0);
        }
    }, 
    count: {        
        "inQueue": 0, //ilość przeciągniętych plików
        "uploaded": 0 //ilosć zauplodowanych plików za jednym przeciągnięciem
    },

    uploading: false,
    uploadFile: function(lp) {
        // sprawdzenie czy nie przerwano przesyłania tego pliku
        if(!queue.data[lp].status) {
            this.nextUpload(lp);
            return false;
        }
        // indeks aktualnego uploadowanego obiektu
        queue.curLp = lp;

        var data = queue.data[lp].obj;
        var tpl = data.context;

        //pokazanie statusBar przy wrzucaniu pierwszego pliku
        if(queue.count.uploaded==0) queue.statusBar.start();

        // ukrycie delete
        tpl.find(".delete").remove();

        //upload pliku
        var jqXHR = data.submit().success(function(result, textStatus, jqXHR) {   
            // alert(result);
            
            var transactionId = $("#upload input[name=transactionId]").attr("value");
            var dir = $("#upload input[name=transactionId]").attr("dir");
            var miniDir = $("#upload input[name=transactionId]").attr("miniDir");

            //wstawienie błędu        
            if(result=='') tpl.addClass("error").find('.img').addClass("uploaded").append('<img alt="" class="mini" src="'+_baseUrl+'/public/template/img/admin/errorImage.png" status="noResult" />');
            else {
                var dane = eval('('+result+')');               
                var imgname = dane.name;
                var status = dane.status;

                tpl.attr({"imgName":imgname,"title": imgname});
                if(status=='success') {
                    tpl.find(".img").addClass("uploaded").append('<img alt="" title="'+imgname+'" src="'+_baseUrl+'/'+dir+transactionId+"/"+miniDir+imgname+'"/>');
                }
                else {
                    tpl.find('.img').addClass("uploaded").append('<img alt="" src="'+_baseUrl+'/public/template/img/admin/errorImage.png" status="phperror" />');
                    tpl.addClass("error");
                }
            }
        }).done(function() {
            //aktualizacja statusu operacji w %
            queue.statusBar.update(null);
            upload.initObj(tpl.find(".uploaded").children("img"));

            //upload kolejnego elementu
            queue.nextUpload(lp);
        });
    },
    nextUpload: function(lp) {
        lp++;
        if(queue.data[lp]) queue.uploadFile(lp);
        else {
            queue.statusBar.end();
            queue.count.inQueue = 0;
            queue.count.uploaded = 0;
            queue.uploading = false;
        }
    } 
};

// wykrywanie korzystania aktualnie ze strony
$(window).bind({
    'focus': function() {
        queue.statusBar.soundOn = false;
    },
    'blur': function() {
        queue.statusBar.soundOn = true;
    }
});