var _baseUrl,_directoryUrl,_paramsLink,_lang;
var docWidth,docHeight;
var winWidth = window.innerWidth;
var winHeight = window.innerHeight;
jQuery.curCSS = jQuery.css;

$(document).ready(function(){
	docWidth = $(document).innerWidth();
	docHeight = $(document).innerHeight();
});
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////// FUNKCJE /////////////////////////////////////////////////////////////

//funkcja zwracająca liczbę zaokrągloną do wartości podanej w argumencie drugim
function round(num,x)
{
	var result = num.toFixed(x);
	return Number(result);
}

//funkcja konwertuje dane GET do tablicy i zwraca element podany w argumencie
function findInUrl(op)
{
	var urlArray = decodeURIComponent(location.href).split("&");
	var paramArray = new Array();
	var par,firtsParam;

	for(var i=0;i<urlArray.length;i++) 
	{
		if(i==0)
		{
			firstParam = urlArray[i].split("?");
			par = firstParam[1].split("=");
			paramArray[par[0]] = par[1];
		}
		else 
		{
			par = urlArray[i].split("=");
			paramArray[par[0]] = par[1];
		}
	}

	return paramArray[op];    
}
function findInUrl2(op)
{
	var urlArray = decodeURIComponent(location.href).split("/");
	var actionName = $("body").attr("actionName");
	var paramArray = new Array();
	var par,firtsParam;  

	for(var i=urlArray.length - 1; i>=0; i--) 
	{
		if(urlArray[i]==actionName) break;
		paramArray[urlArray[i-1]] = urlArray[i];
		i=i-1;
	}  

	return paramArray[op];
}
function findInLink(link,op)
{
	var urlArray = decodeURIComponent(link).split("/");
	var actionName = $("body").attr("actionName");
	var paramArray = new Array();
	var par,firtsParam;  

	for(var i=urlArray.length - 1; i>=0; i--) 
	{
		if(urlArray[i]==actionName) break;
		paramArray[urlArray[i-1]] = urlArray[i];
		i=i-1;
	}  

	return paramArray[op];    
}
function editLink(link,key,value) {
	var urlArray = decodeURIComponent(link).split("/");
	var actionName = $("body").attr("actionName");  
	var edited = false;

	for(var i=urlArray.length - 1; i>=0; i--) 
	{
		if(urlArray[i]==actionName) { edited=true; break; }
		if(urlArray[i]==key) { urlArray[i+1] = value; edited=true; }
	}  
	//gdy parametr nie istnieje dodaje go na koniec łańcucha
	if(!edited) 
	{
		i = urlArray.length;
		urlArray[i] = key;
		urlArray[i+1] = value;
	}
	link = urlArray.join("/");

	return link;      
}

function setElementsOnTheMiddle()
{

	posTopCenter = round(($(window).innerHeight() - $("#center").height())/2,1);    
	posTop = round(($(window).innerHeight() - 400)/2,1); //odległość menu od góry strony
	logoPosTop = posTop-45-$("#logo").height();     // 45-odleglosc loga od #center
	centerTwoTop = logoPosTop - 65;

	if(logoPosTop<0) 
	{
		logoPosTop = 65;
		centerTwoTop = 0;
		posTopCenter = 65;
	}
	$("#logo").css("margin-top",logoPosTop+"px");
	//if($("#centerTwo").length>0) $("#centerTwo").css("margin-top",centerTwoTop+"px");
	if($("#center").length>0) $("#center").css("margin-top",posTopCenter+"px");

	//ustawienie fb_buttons w galerii
	if(typeof gallery!=='undefined' && gallery.fb.on) {
		if($("#images").is(":visible")) $("#images "+gallery.fb.obj).css("top",posTopCenter-30+"px");
	}
}
// v1.0
function setElementsOnMiddle(container,element)
{   
	//container.find("img:visible").hide();
	container.each(function(){setImgOnMiddle($(this));});
}
// v1.0
function setImgOnMiddle(container)
{
	var ele = container.find("img");
	container.imagesLoaded().done(function()
	{
		var conWid = container.width();
		var eleWid = ele.width();
		var ml = (conWid-eleWid)/2;

		//zabezpieczenie przed przesunięciem zdjecia w poziomie (przeglądarka prawdopodobnie niewczytuje całego zdjecia tylko 24px)
		if(eleWid<50) delayTime = setTimeout(function()
		{
			setImgOnMiddle(container);
			return false;
		},"500");

		ele.css("margin-left",ml+"px");
		if(ele.height() > container.height())
		{
			var mt = (ele.height() - container.height())/2;
			ele.css("margin-top",-mt+"px");
		}

		container.removeClass("load");
		ele.fadeIn(100);     
	});
}
// v1.0
function wysrodkuj(container,element) { setOnMiddle(container,element); }
// od v.1.1 
function setOnMiddle(container,element) { 
	var obj = container.find(element);
	var objWidth = obj.width()!=0 ? obj.width():Number(obj.attr("w"));
	var objHeight = obj.height()!=0 ? obj.height():Number(obj.attr("h"));
	var ml = (container.width() - objWidth)/2;
	obj.css("margin-left",ml+"px");

	if(objHeight > container.height()) {
		var mt = (objHeight - container.height())/2;
		obj.css("margin-top",-mt+"px");
	}

	obj.show();
	container.removeClass("load");
}

function getArrayOfList(objects,exception) {
	var listEle = new Array();
	var i = 0;
	objects.each(function() {
		if(exception) {
			if(!$(this).is(exception)) {
				listEle[i] = $(this).attr("objId");
				i++;
			}
		}
		else {
			listEle[i] = $(this).attr("objId");
			i++;
		}
	});

	return listEle;
}


function showObject(obj,op)
{
	for(i in obj){
		if(op=='key') alert(i);
		if(op=='value') alert(obj[i]);
		if(!op) alert(i+": "+obj[i]);
	}
}

//funkcja blokująca/odblokowująca obiekt (ustawia znacznik blokady)
function blockade(obj,op)
{
	if(op=='yes') 
	{
		obj.css("cursor","default");
		obj.attr("block","yes");
	}
	if(op=='no') 
	{
		obj.css("cursor","pointer");
		obj.attr("block","no");
	} 
	if(!op)
	{
		if(obj.attr("block")=='yes') return true;
		else return false;
	}
}

//funkcja pomniejszająca/powiększająca obrazek z zachowaniem proporcji
function imageScale(img,maxWidth,maxHeight) {
	var newHei,newWid;
	var wid = img.attr("w");
	var hei = img.attr("h");

	//wrazie nie odczytania rozmiaru odczytyje z obrazka
	if(typeof wid=='undefined' || typeof hei=='undefined') {
		wid = img.width();
		hei = img.height();
	}

	x = Number(hei)/Number(wid); //współczynnik do wyznaczania wysokosci mnozac go przez szerokosc
	y = Number(wid)/Number(hei); //współczynnik do wyznaczania szerokosc mnozac go przez wysokosc

	newWid = maxWidth;
	newHei = round(newWid*x);

	if(newHei>maxHeight) {
		newHei = maxHeight;
		newWid = round(newHei*y);
	}  

	if(newWid<=1 || newHei<=1) return {"width":wid,"height":hei};
	else return {"width":newWid,"height":newHei}; 
}

//funkcja dodająca string w formacie json jako wartości do podanego obiektu
function addStringToObject(obj,string) {
	if(string.length>0) {
		var arrayObj = string.split(",");
		var op;
		for(var v in arrayObj) {
			op = arrayObj[v].split(":");
			eval('obj.'+op[0]+' = op[1]');
		}
	}

	return obj;
}

//funkcja wyłuskuje z "xxx[yyy]"" yyy
function getFieldName(string)
{
	var newString = string.split("[")[1];
	newString = newString.substr(0,newString.length-1)
	return newString;
}

//odpowiednik htmlspecialchars z PHP
function escapeHtml(text) 
{
  return text
	  .replace(/&/g, "&amp;")
	  .replace(/</g, "&lt;")
	  .replace(/>/g, "&gt;")
	  .replace(/"/g, "&quot;")
	  .replace(/'/g, "&#039;");
}

//odpowiednik array_diff z PHP
function array_diff(arr1) {
  //  discuss at: http://phpjs.org/functions/array_diff/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Sanjoy Roy
  //  revised by: Brett Zamir (http://brett-zamir.me)
  //   example 1: array_diff(['Kevin', 'van', 'Zonneveld'], ['van', 'Zonneveld']);
  //   returns 1: {0:'Kevin'}

  var retArr = {},
	argl = arguments.length,
	k1 = '',
	i = 1,
	k = '',
	arr = {};

  arr1keys: for (k1 in arr1) {
	for (i = 1; i < argl; i++) {
	  arr = arguments[i];
	  for (k in arr) {
		if (arr[k] === arr1[k1]) {
		  // If it reaches here, it was found in at least one array, so try next value
		  continue arr1keys;
		}
	  }
	  retArr[k1] = arr1[k1];
	}
  }

  return retArr;
}

//funkcja oblicza łączną szerokość "ele" (np li) w kontenerze
function getContainerWidth(container,ele,exception)
{
	var allWid = 0;
	container.find(ele).each(function()
	{
		if(exception) 
		{
			if(!$(this).is(exception)) allWid += $(this).innerWidth();
		}
		else allWid += $(this).innerWidth();
	});

	return Number(allWid);
}


// tylko w v1.0
//funkcja edytująca link w buttonach fb
function editFbShare(fbButton,newUrl)
{
	var fbIframe = fbButton.find("iframe");
	var baseUrl = $("body").attr("baseUrl");
	newUrl = newUrl.replace("gallery","gallery.php");
	newUrl = newUrl.replace("video","video.php");


	//podmiana linku
	if(fbIframe.length==0) fbButton.attr("data-href",baseUrl+'/'+newUrl);
	else 
	{
		var urlArray = decodeURIComponent(fbIframe.attr("src")).split("&");
		var paramArray = new Array();
		var par;

		for(var i=0;i<urlArray.length;i++) 
		{
			par = urlArray[i].split("=");
			paramArray[par[0]] = par[1];
		}
		paramArray['href'] = encodeURIComponent(baseUrl+"/"+newUrl);

		var newParams = new Array();
		var i=0;
		for(var x in paramArray) 
		{
			newParams[i] = x+"="+paramArray[x];
			i++;
		}
		var newIframeUrl = newParams.join("&");

		//podmiana src iframe
		fbIframe.attr("src",newIframeUrl);
	}
}

// odpowiednik php'owego number_format
function number_format(number, decimals, dec_point, thousands_sep) {
  //  discuss at: http://phpjs.org/functions/number_format/
  // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: davook
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Theriault
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // bugfixed by: Michael White (http://getsprink.com)
  // bugfixed by: Benjamin Lupton
  // bugfixed by: Allan Jensen (http://www.winternet.no)
  // bugfixed by: Howard Yeend
  // bugfixed by: Diogo Resende
  // bugfixed by: Rival
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  //  revised by: Luke Smith (http://lucassmith.name)
  //    input by: Kheang Hok Chin (http://www.distantia.ca/)
  //    input by: Jay Klehr
  //    input by: Amir Habibi (http://www.residence-mixte.com/)
  //    input by: Amirouche
  //   example 1: number_format(1234.56);
  //   returns 1: '1,235'
  //   example 2: number_format(1234.56, 2, ',', ' ');
  //   returns 2: '1 234,56'
  //   example 3: number_format(1234.5678, 2, '.', '');
  //   returns 3: '1234.57'
  //   example 4: number_format(67, 2, ',', '.');
  //   returns 4: '67,00'
  //   example 5: number_format(1000);
  //   returns 5: '1,000'
  //   example 6: number_format(67.311, 2);
  //   returns 6: '67.31'
  //   example 7: number_format(1000.55, 1);
  //   returns 7: '1,000.6'
  //   example 8: number_format(67000, 5, ',', '.');
  //   returns 8: '67.000,00000'
  //   example 9: number_format(0.9, 0);
  //   returns 9: '1'
  //  example 10: number_format('1.20', 2);
  //  returns 10: '1.20'
  //  example 11: number_format('1.20', 4);
  //  returns 11: '1.2000'
  //  example 12: number_format('1.2000', 3);
  //  returns 12: '1.200'
  //  example 13: number_format('1 000,50', 2, '.', ' ');
  //  returns 13: '100 050.00'
  //  example 14: number_format(1e-8, 8, '.', '');
  //  returns 14: '0.00000001'

  number = (number + '')
	.replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
	prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	s = '',
	toFixedFix = function(n, prec) {
	  var k = Math.pow(10, prec);
	  return '' + (Math.round(n * k) / k)
		.toFixed(prec);
	};
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
	.split('.');
  if (s[0].length > 3) {
	s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '')
	.length < prec) {
	s[1] = s[1] || '';
	s[1] += new Array(prec - s[1].length + 1)
	  .join('0');
  }
  return s.join(dec);
}

function getExtension(filename) {
	var arr = filename.split(".");
	var extension = arr[arr.length-1];
	return extension;
}


// metoda pokazuje/ukrywa ukrytą część strony
function toggleHiddenSpace(obj) {
	var hiddenSpace = obj.parent().find(".hiddenSpace");
	if(obj.is(":checked")) hiddenSpace.show();
	else hiddenSpace.hide();
}

function addslashes(str) {
	str = str.replace(/\\/g, '\\\\');
	str = str.replace(/\'/g, '\\\'');
	str = str.replace(/\"/g, '\\"');
	str = str.replace(/\0/g, '\\0');
	return str;
}
 
function stripslashes(str) {
	str = str.replace(/\\'/g, '\'');
	str = str.replace(/\\"/g, '"');
	str = str.replace(/\\0/g, '\0');
	str = str.replace(/\\\\/g, '\\');
	return str;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////// OBIEKTY /////////////////////////////////////////////////////////
var stoper = {
	counter: 0,
	objTime: null,
	start: function(intervalTime) {
		this.intervalTime = intervalTime;
		this.objTime = setInterval(function() { stoper.increment(); },intervalTime);
	},
	stop: function() {
		clearInterval(this.objTime);
		var wynik = round((this.intervalTime/1000)*this.counter,3);
		alert(wynik+" sekund");
		this.counter = 0;
	},
	increment: function() { this.counter++; }
}

var helper = {
	setOnMiddle: function(obj) {
		var container = obj.parent();
		var objWidth = obj.width()!=0 ? obj.width():Number(obj.attr("w"));
		var objHeight = obj.height()!=0 ? obj.height():Number(obj.attr("h"));
		if(typeof objWidth=='undefined' || typeof objHeight=='undefined') return false;

		// wyśrodkowanie w poziomie
		var ml = (container.width() - objWidth)/2;
		obj.css("margin-left",ml+"px");
		// wyśrodkowanie w pionie
		var mt = (container.height() - objHeight)/2;
		obj.css("margin-top",mt+"px");
	}
}