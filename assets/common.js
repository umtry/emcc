!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery"],e):e("undefined"!=typeof jQuery?jQuery:window.Zepto)}(function(e){"use strict";function t(t){var r=t.data;t.isDefaultPrevented()||(t.preventDefault(),e(t.target).ajaxSubmit(r))}function r(t){var r=t.target,a=e(r);if(!a.is("[type=submit],[type=image]")){var n=a.closest("[type=submit]");if(0===n.length)return;r=n[0]}var i=this;if(i.clk=r,"image"==r.type)if(void 0!==t.offsetX)i.clk_x=t.offsetX,i.clk_y=t.offsetY;else if("function"==typeof e.fn.offset){var o=a.offset();i.clk_x=t.pageX-o.left,i.clk_y=t.pageY-o.top}else i.clk_x=t.pageX-r.offsetLeft,i.clk_y=t.pageY-r.offsetTop;setTimeout(function(){i.clk=i.clk_x=i.clk_y=null},100)}function a(){if(e.fn.ajaxSubmit.debug){var t="[jquery.form] "+Array.prototype.join.call(arguments,"");window.console&&window.console.log?window.console.log(t):window.opera&&window.opera.postError&&window.opera.postError(t)}}var n={};n.fileapi=void 0!==e("<input type='file'/>").get(0).files,n.formdata=void 0!==window.FormData;var i=!!e.fn.prop;e.fn.attr2=function(){if(!i)return this.attr.apply(this,arguments);var e=this.prop.apply(this,arguments);return e&&e.jquery||"string"==typeof e?e:this.attr.apply(this,arguments)},e.fn.ajaxSubmit=function(t){function r(r){var a,n,i=e.param(r,t.traditional).split("&"),o=i.length,s=[];for(a=0;o>a;a++)i[a]=i[a].replace(/\+/g," "),n=i[a].split("="),s.push([decodeURIComponent(n[0]),decodeURIComponent(n[1])]);return s}function o(a){for(var n=new FormData,i=0;i<a.length;i++)n.append(a[i].name,a[i].value);if(t.extraData){var o=r(t.extraData);for(i=0;i<o.length;i++)o[i]&&n.append(o[i][0],o[i][1])}t.data=null;var s=e.extend(!0,{},e.ajaxSettings,t,{contentType:!1,processData:!1,cache:!1,type:u||"POST"});t.uploadProgress&&(s.xhr=function(){var r=e.ajaxSettings.xhr();return r.upload&&r.upload.addEventListener("progress",function(e){var r=0,a=e.loaded||e.position,n=e.total;e.lengthComputable&&(r=Math.ceil(a/n*100)),t.uploadProgress(e,a,n,r)},!1),r}),s.data=null;var c=s.beforeSend;return s.beforeSend=function(e,r){r.data=t.formData?t.formData:n,c&&c.call(this,e,r)},e.ajax(s)}function s(r){function n(e){var t=null;try{e.contentWindow&&(t=e.contentWindow.document)}catch(r){a("cannot get iframe.contentWindow document: "+r)}if(t)return t;try{t=e.contentDocument?e.contentDocument:e.document}catch(r){a("cannot get iframe.contentDocument: "+r),t=e.document}return t}function o(){function t(){try{var e=n(g).readyState;a("state = "+e),e&&"uninitialized"==e.toLowerCase()&&setTimeout(t,50)}catch(r){a("Server abort: ",r," (",r.name,")"),s(k),j&&clearTimeout(j),j=void 0}}var r=f.attr2("target"),i=f.attr2("action"),o="multipart/form-data",c=f.attr("enctype")||f.attr("encoding")||o;w.setAttribute("target",p),(!u||/post/i.test(u))&&w.setAttribute("method","POST"),i!=m.url&&w.setAttribute("action",m.url),m.skipEncodingOverride||u&&!/post/i.test(u)||f.attr({encoding:"multipart/form-data",enctype:"multipart/form-data"}),m.timeout&&(j=setTimeout(function(){T=!0,s(D)},m.timeout));var l=[];try{if(m.extraData)for(var d in m.extraData)m.extraData.hasOwnProperty(d)&&l.push(e.isPlainObject(m.extraData[d])&&m.extraData[d].hasOwnProperty("name")&&m.extraData[d].hasOwnProperty("value")?e('<input type="hidden" name="'+m.extraData[d].name+'">').val(m.extraData[d].value).appendTo(w)[0]:e('<input type="hidden" name="'+d+'">').val(m.extraData[d]).appendTo(w)[0]);m.iframeTarget||v.appendTo("body"),g.attachEvent?g.attachEvent("onload",s):g.addEventListener("load",s,!1),setTimeout(t,15);try{w.submit()}catch(h){var x=document.createElement("form").submit;x.apply(w)}}finally{w.setAttribute("action",i),w.setAttribute("enctype",c),r?w.setAttribute("target",r):f.removeAttr("target"),e(l).remove()}}function s(t){if(!x.aborted&&!F){if(M=n(g),M||(a("cannot access response document"),t=k),t===D&&x)return x.abort("timeout"),void S.reject(x,"timeout");if(t==k&&x)return x.abort("server abort"),void S.reject(x,"error","server abort");if(M&&M.location.href!=m.iframeSrc||T){g.detachEvent?g.detachEvent("onload",s):g.removeEventListener("load",s,!1);var r,i="success";try{if(T)throw"timeout";var o="xml"==m.dataType||M.XMLDocument||e.isXMLDoc(M);if(a("isXml="+o),!o&&window.opera&&(null===M.body||!M.body.innerHTML)&&--O)return a("requeing onLoad callback, DOM not available"),void setTimeout(s,250);var u=M.body?M.body:M.documentElement;x.responseText=u?u.innerHTML:null,x.responseXML=M.XMLDocument?M.XMLDocument:M,o&&(m.dataType="xml"),x.getResponseHeader=function(e){var t={"content-type":m.dataType};return t[e.toLowerCase()]},u&&(x.status=Number(u.getAttribute("status"))||x.status,x.statusText=u.getAttribute("statusText")||x.statusText);var c=(m.dataType||"").toLowerCase(),l=/(json|script|text)/.test(c);if(l||m.textarea){var f=M.getElementsByTagName("textarea")[0];if(f)x.responseText=f.value,x.status=Number(f.getAttribute("status"))||x.status,x.statusText=f.getAttribute("statusText")||x.statusText;else if(l){var p=M.getElementsByTagName("pre")[0],h=M.getElementsByTagName("body")[0];p?x.responseText=p.textContent?p.textContent:p.innerText:h&&(x.responseText=h.textContent?h.textContent:h.innerText)}}else"xml"==c&&!x.responseXML&&x.responseText&&(x.responseXML=X(x.responseText));try{E=_(x,c,m)}catch(y){i="parsererror",x.error=r=y||i}}catch(y){a("error caught: ",y),i="error",x.error=r=y||i}x.aborted&&(a("upload aborted"),i=null),x.status&&(i=x.status>=200&&x.status<300||304===x.status?"success":"error"),"success"===i?(m.success&&m.success.call(m.context,E,"success",x),S.resolve(x.responseText,"success",x),d&&e.event.trigger("ajaxSuccess",[x,m])):i&&(void 0===r&&(r=x.statusText),m.error&&m.error.call(m.context,x,i,r),S.reject(x,"error",r),d&&e.event.trigger("ajaxError",[x,m,r])),d&&e.event.trigger("ajaxComplete",[x,m]),d&&!--e.active&&e.event.trigger("ajaxStop"),m.complete&&m.complete.call(m.context,x,i),F=!0,m.timeout&&clearTimeout(j),setTimeout(function(){m.iframeTarget?v.attr("src",m.iframeSrc):v.remove(),x.responseXML=null},100)}}}var c,l,m,d,p,v,g,x,y,b,T,j,w=f[0],S=e.Deferred();if(S.abort=function(e){x.abort(e)},r)for(l=0;l<h.length;l++)c=e(h[l]),i?c.prop("disabled",!1):c.removeAttr("disabled");if(m=e.extend(!0,{},e.ajaxSettings,t),m.context=m.context||m,p="jqFormIO"+(new Date).getTime(),m.iframeTarget?(v=e(m.iframeTarget),b=v.attr2("name"),b?p=b:v.attr2("name",p)):(v=e('<iframe name="'+p+'" src="'+m.iframeSrc+'" />'),v.css({position:"absolute",top:"-1000px",left:"-1000px"})),g=v[0],x={aborted:0,responseText:null,responseXML:null,status:0,statusText:"n/a",getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(t){var r="timeout"===t?"timeout":"aborted";a("aborting upload... "+r),this.aborted=1;try{g.contentWindow.document.execCommand&&g.contentWindow.document.execCommand("Stop")}catch(n){}v.attr("src",m.iframeSrc),x.error=r,m.error&&m.error.call(m.context,x,r,t),d&&e.event.trigger("ajaxError",[x,m,r]),m.complete&&m.complete.call(m.context,x,r)}},d=m.global,d&&0===e.active++&&e.event.trigger("ajaxStart"),d&&e.event.trigger("ajaxSend",[x,m]),m.beforeSend&&m.beforeSend.call(m.context,x,m)===!1)return m.global&&e.active--,S.reject(),S;if(x.aborted)return S.reject(),S;y=w.clk,y&&(b=y.name,b&&!y.disabled&&(m.extraData=m.extraData||{},m.extraData[b]=y.value,"image"==y.type&&(m.extraData[b+".x"]=w.clk_x,m.extraData[b+".y"]=w.clk_y)));var D=1,k=2,A=e("meta[name=csrf-token]").attr("content"),L=e("meta[name=csrf-param]").attr("content");L&&A&&(m.extraData=m.extraData||{},m.extraData[L]=A),m.forceSync?o():setTimeout(o,10);var E,M,F,O=50,X=e.parseXML||function(e,t){return window.ActiveXObject?(t=new ActiveXObject("Microsoft.XMLDOM"),t.async="false",t.loadXML(e)):t=(new DOMParser).parseFromString(e,"text/xml"),t&&t.documentElement&&"parsererror"!=t.documentElement.nodeName?t:null},C=e.parseJSON||function(e){return window.eval("("+e+")")},_=function(t,r,a){var n=t.getResponseHeader("content-type")||"",i="xml"===r||!r&&n.indexOf("xml")>=0,o=i?t.responseXML:t.responseText;return i&&"parsererror"===o.documentElement.nodeName&&e.error&&e.error("parsererror"),a&&a.dataFilter&&(o=a.dataFilter(o,r)),"string"==typeof o&&("json"===r||!r&&n.indexOf("json")>=0?o=C(o):("script"===r||!r&&n.indexOf("javascript")>=0)&&e.globalEval(o)),o};return S}if(!this.length)return a("ajaxSubmit: skipping submit process - no element selected"),this;var u,c,l,f=this;"function"==typeof t?t={success:t}:void 0===t&&(t={}),u=t.type||this.attr2("method"),c=t.url||this.attr2("action"),l="string"==typeof c?e.trim(c):"",l=l||window.location.href||"",l&&(l=(l.match(/^([^#]+)/)||[])[1]),t=e.extend(!0,{url:l,success:e.ajaxSettings.success,type:u||e.ajaxSettings.type,iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank"},t);var m={};if(this.trigger("form-pre-serialize",[this,t,m]),m.veto)return a("ajaxSubmit: submit vetoed via form-pre-serialize trigger"),this;if(t.beforeSerialize&&t.beforeSerialize(this,t)===!1)return a("ajaxSubmit: submit aborted via beforeSerialize callback"),this;var d=t.traditional;void 0===d&&(d=e.ajaxSettings.traditional);var p,h=[],v=this.formToArray(t.semantic,h);if(t.data&&(t.extraData=t.data,p=e.param(t.data,d)),t.beforeSubmit&&t.beforeSubmit(v,this,t)===!1)return a("ajaxSubmit: submit aborted via beforeSubmit callback"),this;if(this.trigger("form-submit-validate",[v,this,t,m]),m.veto)return a("ajaxSubmit: submit vetoed via form-submit-validate trigger"),this;var g=e.param(v,d);p&&(g=g?g+"&"+p:p),"GET"==t.type.toUpperCase()?(t.url+=(t.url.indexOf("?")>=0?"&":"?")+g,t.data=null):t.data=g;var x=[];if(t.resetForm&&x.push(function(){f.resetForm()}),t.clearForm&&x.push(function(){f.clearForm(t.includeHidden)}),!t.dataType&&t.target){var y=t.success||function(){};x.push(function(r){var a=t.replaceTarget?"replaceWith":"html";e(t.target)[a](r).each(y,arguments)})}else t.success&&x.push(t.success);if(t.success=function(e,r,a){for(var n=t.context||this,i=0,o=x.length;o>i;i++)x[i].apply(n,[e,r,a||f,f])},t.error){var b=t.error;t.error=function(e,r,a){var n=t.context||this;b.apply(n,[e,r,a,f])}}if(t.complete){var T=t.complete;t.complete=function(e,r){var a=t.context||this;T.apply(a,[e,r,f])}}var j=e("input[type=file]:enabled",this).filter(function(){return""!==e(this).val()}),w=j.length>0,S="multipart/form-data",D=f.attr("enctype")==S||f.attr("encoding")==S,k=n.fileapi&&n.formdata;a("fileAPI :"+k);var A,L=(w||D)&&!k;t.iframe!==!1&&(t.iframe||L)?t.closeKeepAlive?e.get(t.closeKeepAlive,function(){A=s(v)}):A=s(v):A=(w||D)&&k?o(v):e.ajax(t),f.removeData("jqxhr").data("jqxhr",A);for(var E=0;E<h.length;E++)h[E]=null;return this.trigger("form-submit-notify",[this,t]),this},e.fn.ajaxForm=function(n){if(n=n||{},n.delegation=n.delegation&&e.isFunction(e.fn.on),!n.delegation&&0===this.length){var i={s:this.selector,c:this.context};return!e.isReady&&i.s?(a("DOM not ready, queuing ajaxForm"),e(function(){e(i.s,i.c).ajaxForm(n)}),this):(a("terminating; zero elements found by selector"+(e.isReady?"":" (DOM not ready)")),this)}return n.delegation?(e(document).off("submit.form-plugin",this.selector,t).off("click.form-plugin",this.selector,r).on("submit.form-plugin",this.selector,n,t).on("click.form-plugin",this.selector,n,r),this):this.ajaxFormUnbind().bind("submit.form-plugin",n,t).bind("click.form-plugin",n,r)},e.fn.ajaxFormUnbind=function(){return this.unbind("submit.form-plugin click.form-plugin")},e.fn.formToArray=function(t,r){var a=[];if(0===this.length)return a;var i,o=this[0],s=this.attr("id"),u=t?o.getElementsByTagName("*"):o.elements;if(u&&!/MSIE [678]/.test(navigator.userAgent)&&(u=e(u).get()),s&&(i=e(':input[form="'+s+'"]').get(),i.length&&(u=(u||[]).concat(i))),!u||!u.length)return a;var c,l,f,m,d,p,h;for(c=0,p=u.length;p>c;c++)if(d=u[c],f=d.name,f&&!d.disabled)if(t&&o.clk&&"image"==d.type)o.clk==d&&(a.push({name:f,value:e(d).val(),type:d.type}),a.push({name:f+".x",value:o.clk_x},{name:f+".y",value:o.clk_y}));else if(m=e.fieldValue(d,!0),m&&m.constructor==Array)for(r&&r.push(d),l=0,h=m.length;h>l;l++)a.push({name:f,value:m[l]});else if(n.fileapi&&"file"==d.type){r&&r.push(d);var v=d.files;if(v.length)for(l=0;l<v.length;l++)a.push({name:f,value:v[l],type:d.type});else a.push({name:f,value:"",type:d.type})}else null!==m&&"undefined"!=typeof m&&(r&&r.push(d),a.push({name:f,value:m,type:d.type,required:d.required}));if(!t&&o.clk){var g=e(o.clk),x=g[0];f=x.name,f&&!x.disabled&&"image"==x.type&&(a.push({name:f,value:g.val()}),a.push({name:f+".x",value:o.clk_x},{name:f+".y",value:o.clk_y}))}return a},e.fn.formSerialize=function(t){return e.param(this.formToArray(t))},e.fn.fieldSerialize=function(t){var r=[];return this.each(function(){var a=this.name;if(a){var n=e.fieldValue(this,t);if(n&&n.constructor==Array)for(var i=0,o=n.length;o>i;i++)r.push({name:a,value:n[i]});else null!==n&&"undefined"!=typeof n&&r.push({name:this.name,value:n})}}),e.param(r)},e.fn.fieldValue=function(t){for(var r=[],a=0,n=this.length;n>a;a++){var i=this[a],o=e.fieldValue(i,t);null===o||"undefined"==typeof o||o.constructor==Array&&!o.length||(o.constructor==Array?e.merge(r,o):r.push(o))}return r},e.fieldValue=function(t,r){var a=t.name,n=t.type,i=t.tagName.toLowerCase();if(void 0===r&&(r=!0),r&&(!a||t.disabled||"reset"==n||"button"==n||("checkbox"==n||"radio"==n)&&!t.checked||("submit"==n||"image"==n)&&t.form&&t.form.clk!=t||"select"==i&&-1==t.selectedIndex))return null;if("select"==i){var o=t.selectedIndex;if(0>o)return null;for(var s=[],u=t.options,c="select-one"==n,l=c?o+1:u.length,f=c?o:0;l>f;f++){var m=u[f];if(m.selected){var d=m.value;if(d||(d=m.attributes&&m.attributes.value&&!m.attributes.value.specified?m.text:m.value),c)return d;s.push(d)}}return s}return e(t).val()},e.fn.clearForm=function(t){return this.each(function(){e("input,select,textarea",this).clearFields(t)})},e.fn.clearFields=e.fn.clearInputs=function(t){var r=/^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i;return this.each(function(){var a=this.type,n=this.tagName.toLowerCase();r.test(a)||"textarea"==n?this.value="":"checkbox"==a||"radio"==a?this.checked=!1:"select"==n?this.selectedIndex=-1:"file"==a?/MSIE/.test(navigator.userAgent)?e(this).replaceWith(e(this).clone(!0)):e(this).val(""):t&&(t===!0&&/hidden/.test(a)||"string"==typeof t&&e(this).is(t))&&(this.value="")})},e.fn.resetForm=function(){return this.each(function(){("function"==typeof this.reset||"object"==typeof this.reset&&!this.reset.nodeType)&&this.reset()})},e.fn.enable=function(e){return void 0===e&&(e=!0),this.each(function(){this.disabled=!e})},e.fn.selected=function(t){return void 0===t&&(t=!0),this.each(function(){var r=this.type;if("checkbox"==r||"radio"==r)this.checked=t;else if("option"==this.tagName.toLowerCase()){var a=e(this).parent("select");t&&a[0]&&"select-one"==a[0].type&&a.find("option").selected(!1),this.selected=t}})},e.fn.ajaxSubmit.debug=!1});
function ssconfirm(tourl, msg) {
    if (typeof (msg) == "undefined" || confirm(msg)) {
        location.href = tourl;
    }
    return false;
}


/**
 * 通用的异步提交
 * @returns {undefined}
 */
$(document).on('click', '.ajsubmit', function (e) {
    var t = $(this);
    var form = t.parents('form');
    var linktype = t.attr("linktype");
    var url = form.attr('action');
    var dataurl = t.attr('dataurl');
    if(typeof(dataurl)!='undefined'){
        url = dataurl;
    }
    if (linktype == "del") {
        if (!confirm("确定删除？")) {
            return false;
        }
    }
    var widgetbox = t.parents('div.widget-box:first');
    var tmptext = "";
    if(widgetbox.length>0){//在窗口小部件内
        widgetbox.one({
            'reload.ace.widget' : function(ev) {
                ev.stopPropagation();
                var $box = $(this);
                form.ajaxSubmit({
                    dataType: 'json',
                    url:url,
                    beforeSubmit: function () {
                        t.removeClass("ajsubmit");
                        tmptext = t.html();
                        t.html("提交中...");
                    },
                    success: function (data) {
                        if (t.attr("location") != undefined) {
                            data.location = t.attr("location");
                        }
                        responseLoading(data);
                        t.addClass("ajsubmit");
                        t.html(tmptext);
                        $box.trigger('reloaded.ace.widget');
                    }
                });
            }
        });
        widgetbox.trigger('reload.ace.widget').widget_box('reload');
    }else{
        form.ajaxSubmit({
            dataType: 'json',
            url:url,
            beforeSubmit: function () {
                $('.page-content').append('<div class="widget-box-overlay"><i class=" ace-icon loading-icon fa fa-spinner fa-spin fa-2x white"></i></div>');
                t.removeClass("ajsubmit");
                tmptext = t.html();
                t.html("提交中...");
            },
            success: function (data) {
                if (t.attr("location") != undefined) {
                    data.location = t.attr("location");
                }
                responseLoading(data);
                t.addClass("ajsubmit");
                t.html(tmptext);
                $('.page-content').find('.widget-box-overlay').remove();
            }
        });
    }
});

$(document).on('click', '.ajlink', function (e) {
    var t = $(this);
    var href = t.attr("dataurl");
    var params = t.attr("data-param");
    var linktype = t.attr("linktype");
    var method = t.attr("data-method");
    var widgetbox = t.parents('div.widget-box');
    if (linktype == "del") {
        if (!confirm("确定删除？")) {
            return false;
        }
    }
    
    var ntime = "";
    if (href.indexOf('?') == -1) {
        ntime = '?t=' + (new Date().getTime());
    }
    
    if(widgetbox.length>0){//在窗口小部件内
        widgetbox.one({
            'reload.ace.widget' : function(ev) {
                ev.stopPropagation();
                var $box = $(this);
                $.ajax({
                    type: method || 'POST',
                    url: href + ntime,
                    data: params,
                    success: function (data) {
                        var data = eval("(" + data + ")");
                        responseLoading(data);
                        $box.trigger('reloaded.ace.widget');
                    }
                });
            }
        });
        widgetbox.trigger('reload.ace.widget').widget_box('reload');
    }else{
        $('.page-content').append('<div class="widget-box-overlay"><i class=" ace-icon loading-icon fa fa-spinner fa-spin fa-2x white"></i></div>');
        $.ajax({
            type: 'POST',
            url: href + ntime,
            data: params,
            success: function (data) {
                var data = eval("(" + data + ")");
                responseLoading(data);
                $('.page-content').find('.widget-box-overlay').remove();
            }
        });
    }
});

$(document).on('click', '.ajdialog', function (e) {
    var t = $(this);
    var url = t.attr("dataurl");
    var title = t.attr("datatitle");
    var width = t.attr("dataw");
    var height = t.attr("datah");
    dialog(url,title,width,height);
});

/**
 * 动态加载数据
 */
var dynamicLoading = function(url,data,sync1,method1){
    var sync = sync1 || 'yes';
    var method = method1 || 'POST';
    if(sync == 'yes'){
        var ntime = "";
        if(url.indexOf('?') == -1){
            ntime = '?t='+(new Date().getTime());
        }
        $.ajax({
            type:method,
            url:url+ntime,
            data:data,
            success:function(data){
                var data = eval("("+data+")");
                responseLoading(data);
            }
        });
    }else{
        $.ajax({
            type:method,
            url:url+'?t='+(new Date().getTime())+'&jsoncallback=responseLoading',
            data:data,
            dataType:'jsonp'
        });    
    }

}


/**
 * 返回装载值
 */
var responseLoading = function (data) {
    if (data.method == 'write') {
        if (data.remove) {
            $.each(data.remove, function (k, v) {
                if (!k) {
                    return true;
                }
                if (k.substr(0, 1) == "." || k.substr(0, 1) == "#") {
                    $(k).remove();
                } else {
                    $("[data-name='" + k + "']").remove();
                }
            });
        }
        if (data.append) {
            $.each(data.append, function (k, v) {
                if (!k) {
                    return true;
                }
                if (k.substr(0, 1) == "." || k.substr(0, 1) == "#") {
                    $(k).append(v);
                } else {
                    $("[data-name='" + k + "']").append(v);
                }
            });
        }
        if (data.html) {
            $.each(data.html, function (k, v) {
                if (!k) {
                    return true;
                }
                if (k.substr(0, 1) == "." || k.substr(0, 1) == "#") {
                    $(k).html(v);
                } else {
                    $("[data-name='" + k + "']").html(v);
                }
            });
        }
        if (data.val) {
            $.each(data.val, function (k, v) {
                if (!k) {
                    return true;
                }
                if (k.substr(0, 1) == "." || k.substr(0, 1) == "#") {
                    $(k).val(v);
                } else {
                    $("[data-name='" + k + "']").val(v);
                }
            });
        }
        if (data.attr) {
            $.each(data.attr, function (k, v) {
                if (!k) {
                    return true;
                }
                var varr = v.split(',');
                if (k.substr(0, 1) == "." || k.substr(0, 1) == "#") {
                    $(k).attr(varr[0],varr[1]);
                } else {
                    $("[data-name='" + k + "']").attr(varr[0],varr[1]);
                }
            });
        }
        if (data.runFunction) {
            eval('' + data.runFunction + '(' + data.data + ');');
        }
        if (data.runFunction2) {
            eval('' + data.runFunction2 + '(' + data.data2 + ');');
        }
        if (data.runFunction3) {
            eval('' + data.runFunction3 + ';');
        }
        //有msg参数就提示
        if (data.msg != undefined && data.msg != "") {
            tips(data.msg);
        }
    } else if (data.method == 'alert') {   // 仅提示一下
        tips(data.msg);
        return false;
    } else if (data.method == 'location') { // 跳转url
        window.location.href = data.location;
        return false;
    } else if (data.method == 'reload') { // 刷新当前页面
        window.location.reload();
        return false;
    } else if (data.method == 'location2') { // 提示后跳转
        tips(data.msg);
        setTimeout(function () {
            window.location.href = data.location;
        }, 3000);
    }
}

var tips = function (msg) {
    var msg_arr = msg.split('|');
    $.gritter.add({
        title: msg_arr[3] || '信息提示',
        text: msg_arr[0],
        time:msg_arr[2] || '3000',
        class_name: msg_arr[1]=='S'?'gritter-success gritter-light':'gritter-error gritter-light'
    });
    return false;
}

var dialog = function(url,title,w,h){
    var diag = new top.Dialog();
	diag.Title = title;
	diag.Width = parseInt(w)||900;
	diag.Height = parseInt(h)||400;
	diag.URL = url;
	diag.show();
}


/**
 * 地区联动
 * @returns {undefined}
 */
var _region = function () {
    $('.region_cate').on("change", function () {
        var t = $(this);
        var type = t.attr("id");
        var type_id = t.val();
        if (type == "pro_id") {
            _region_c(type, type_id, "city_id");
        }
        if (type == "city_id") {
            _region_c(type, type_id, "area_id");
        }
        if (type == "area_id") {
            _region_c(type, type_id, "street_id");
        }
        if (type == "street_id") {
            _region_c(type, type_id, "shequ_id");
        }
    });
};
var _region_c = function (type, type_id, lk) {
    if (type == "pro_id") {
        $("#city_id,#area_id,#street_id,#shequ_id").html("<option value=''>请选择</option>");
    }
    if (type == "city_id") {
        $("#area_id,#street_id,#shequ_id").html("<option value=''>请选择</option>");
    }
    if (type == "area_id") {
        $("#street_id,#shequ_id").html("<option value=''>请选择</option>");
    }
    if (type == "street_id") {
        $("#shequ_id").html("<option value=''>请选择</option>");
    }
    if (type_id == "") {
        return false;
    }
    $.ajax({
        type: 'GET',
        url: '/ssadmin/ajax/region.html',
        data: {type_id: type_id},
        dataType: 'json',
        success: function (data) {
            $.each(data.msg, function (k, v) {
                $("#" + lk).append("<option value=" + v.region_id + ">" + v.region_name + "</option>");
            });

        }
    });
};