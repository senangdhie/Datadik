/* general function */
var decodeHtmlEntity = function(str) {
	if(str!='' && str!=null) {
		return str.replace(/&#(\d+);/g, function(match, dec) {
			return String.fromCharCode(dec);
		});
	}else{
		return '';
	}
};

var encodeHtmlEntity = function(str) {
  var buf = [];
  for (var i=str.length-1;i>=0;i--) {
    buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
  }
  return buf.join('');
};

var validEmail = function(email) {
  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
};

/* hash navigate*/
!function(n,t,e){"$:nomunge";var r,o="hashchange",i=document,u=n.event.special,c=i.documentMode,f="on"+o in t&&(c===e||c>7);function a(n){return"#"+(n=n||location.href).replace(/^[^#]*#?(.*)$/,"$1")}n.fn[o]=function(n){return n?this.bind(o,n):this.trigger(o)},n.fn[o].delay=50,u[o]=n.extend(u[o],{setup:function(){if(f)return!1;n(r.start)},teardown:function(){if(f)return!1;n(r.stop)}}),r=function(){var r,i={},u=a(),c=function(n){return n},f=c,s=c;function h(){var e=a(),i=s(u);e!==u?(f(u=e,i),n(t).trigger(o)):i!==u&&(location.href=location.href.replace(/#.*/,"")+i),r=setTimeout(h,n.fn[o].delay)}return i.start=function(){r||h()},i.stop=function(){r&&clearTimeout(r),r=e},i}()}(jQuery,this);

var vhsi = '';
var navmap = [];
var sp = [
	['blank','/apps/depan','Terminal'],
	['index','/apps/depan','Terminal'],
	['refsp','/apps/refsp','Satuan Pendidikan']
];

function xzc() {
	$.get('/apps/sampul?_s='+vhsi, function(data, status){
		$('body').append(data);
	});
};

function ReloadContent(hash) {
	var uri = window.location.href;
	var ex = uri.split('/');
	var param = 'blank';
	if(ex.length>5) {
		param = ex[5];
	}
	if(hash!='blank' && param=='blank') {
		param = hash;
	}
	navmap.map(function (uri) {
		if (uri[0] == param) {
			var xuri = uri[1]+'?_s='+vhsi;
			if(ex.length>6) {
				xuri = uri[1];
				for(i=6;i<ex.length;i++) {
					xuri += '/'+ex[i];
				}
				xuri += '?_s='+vhsi;
			}
			$.get(xuri, function(data, status){
				document.title = uri[2] + ' - Sistem Informasi Pendidikan';
				//$('#boardSubTitle').html(uri[2]);
				//$('#smallSubTitle').html(uri[2]);
				$('#vinividivici').html(data);
			});
		}
	});
};

function InitPage(nm) {
	navmap = nm;
}

$(document).ready(function() {
    $(window).hashchange(function() {
        var uri = window.location.href;
		var ex = uri.split('/');
		var param = 'blank';
		if(ex.length>5) {
			param = ex[5];
		}
		var hash = location.hash.replace('#/','') || 'blank';
		var onright = uri.substr(uri.length - 1);
		if(param=='blank' && onright!='/') {
			location.href = uri + '/';
		}else{
			var ldr = '<center>loading...</center>';
			$('#vinividivici').html(ldr);
			ReloadContent(param);
		}
	});
	$(window).hashchange();
});