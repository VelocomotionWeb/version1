/**
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*/
!function(a){function t(){"function"==typeof reloadProductComparison&&reloadProductComparison()}function e(t){yaasBlock.find(".yaas-criterion").each(function(){a(this).data("yaas-count",a(this).data("yaas-kept-count"));var t=a(this).data("type");"link"==t?(a(this).find(".yaasCount").html("("+a(this).data("yaas-count")+")"),a(this).show("blind")):"combo"==t&&($text=a(this).data("yaas-name"),"visible"==J&&($text+=" ("+a(this).data("yaas-count")+")"),$padding=10*(parseInt(a(this).data("yaas-indent"))-2),a(this).css("padding-left",$padding+"px"),a(this).text($text),a(this).show())}),$selects=yaasBlock.find("select.yaas-select"),$selects.each(function(){var t=!0;a(this).find("option").each(function(){t?(a(this).prop("selected",!0),t=!1):a(this).prop("selected",!1)})}),i(t)}function i(t){yaasBlock.find(".yaas_group_container").each(function(){var e=!1,i=a(this);i.find(".yaas-criterion").each(function(){0!=parseInt(a(this).data("yaas-count"))&&(e=!0)}),e?t?i.show("blind"):i.show():t?i.hide("blind"):i.hide()})}function n(){localStorage.removeItem("yaasMenu"),a.removeCookie("yaasCriteria")}function s(){var a="ajax=1";R.length>0&&(a+="&df="+R);for(var t=0;t<B.length;t++)a+="&c[]="+B[t];return a+="&p="+I,-1!=j&&(a+="&n="+j),a+="&orderby="+F,a+="&orderway="+S}function l(){yaasBlock=a(".yaas_block"),A=yaasBlock.data("loader-img"),P=yaasBlock.data("search-link"),V=yaasBlock.data("scroll-top"),J=yaasBlock.data("display-count"),R=yaasBlock.data("default-filter")}function r(t,e){for(var n=0;n<t.length;n++){var s=t[n],l=yaasBlock.find(".yaas-criterion[data-internal-id="+s.name+"]");l.each(function(){a(this).data("yaas-count",s.count);var t=a(this).data("type");"link"==t?(a(this).find(".yaasCount").html("("+a(this).data("yaas-count")+")"),0!=s.count||a(this).hasClass("selected")?e?a(this).show("blind"):a(this).show():e?a(this).hide("blind"):a(this).hide()):"combo"==t&&($text=a(this).data("yaas-name"),"visible"==J&&($text+=" ("+a(this).data("yaas-count")+")"),a(this).text($text),$padding=10*(parseInt(a(this).data("yaas-indent"))-2),a(this).css("padding-left",$padding+"px"),0==s.count&&0==a(this).prop("disabled")?a(this).hide():a(this).show())})}i(e)}function o(){a("form.showall").on("submit",function(a){return a.preventDefault(),I=1,j=2147483647,C(),!1})}function c(){a("#center_column .pagination a").click(function(t){t.preventDefault();var e=getURLParameter(a(this).attr("href"),"p");I=parseInt(e),C()}),a(".nbrItemPage select").unbind().removeAttr("onChange"),a(".nbrItemPage select").on("change",function(t){t.preventDefault();var e=a(this).val();I=1,j=parseInt(e),C()}),a("select.selectProductSort").unbind().removeAttr("onChange"),a("select.selectProductSort").on("change",function(t){t.preventDefault();var e=a(this).val().split(":");F=e[0],S=e[1],C()}),$compareForms=a("#center_column .bt_compare").closest("form"),$compareForms.each(function(){var t=a(this).attr("action");t+=t.indexOf("?")>0?"&yaas=1":"?yaas=1",a(this).attr("action",t)})}function d(t){if(t.length>0)for(var e=0;e<t.length;e++){var i=t[e];if(i.length>0){var n=i.indexOf("p"),s=i;n>=0&&(s=i.substr(0,n));var l="[data-internal-id="+s+"],";l+="[data-type=slide][data-internal-id-min="+s+"],",l+="[data-type=slide][data-internal-id-max="+s+"]",$divs=a(l),$divs.each(function(){var e=a(this).data("type");if("combo"==e){var l=a(this).closest("select").data("id-criteria-field");void 0===q[l]?q[l]=i.toString():q[l].indexOf(t)<0&&(q[l]+=","+i.toString())}var r=i.substr(n+1);a(this).data("internal-id-min")==s?a(this).data("selected-min",r):a(this).data("internal-id-max")==s&&a(this).data("selected-max",r),h(a(this),i,!0,!1)})}}}function u(t,e,i,n){var s=t.data("type");if("link"==s){if(!t.hasClass("selected")||n)t.addClass("selected"),i&&B.push(e);else if(t.removeClass("selected"),i){var l=a.inArray(e,B);l>=0&&B.splice(l,1)}}else if("slide"==s){var r=e.split("p");i&&(f(r[0]),-1!=r[1]&&B.push(e))}else if("combo"==s)if(0==t.prop("selected")||n)t.prop("selected",!0),-1!=t.val()&&t.closest("select").find("option:first").prop("selected",!1),i&&B.push(e);else if(t.prop("selected",!1),i){var l=a.inArray(e,B);l>=0&&B.splice(l,1)}i&&a.cookie("yaasCriteria",jQuery.unique(B).join(","),{path:"/"})}function h(t,e,i,n){var s=t.data("type");if("link"==s){if(i)t.addClass("selected"),n&&B.push(e);else if(t.removeClass("selected"),n){var l=a.inArray(e,B);l>=0&&B.splice(l,1)}}else if("combo"==s)if(i)t.prop("selected",!0),-1!=t.val()&&t.closest("select").find("option:first").prop("selected",!1),n&&B.push(e);else if(t.prop("selected",!1),n){var l=a.inArray(e,B);l>=0&&B.splice(l,1)}n&&a.cookie("yaasCriteria",jQuery.unique(B).join(","),{path:"/"})}function f(a){for(var t=new Array,e=0;e<B.length;e++){var i=B[e];i.indexOf(a)<0&&t.push(i)}B=t}function p(){e(!1)}function y(t){var e=t.data("min"),i=t.data("max"),n=t.data("symbol"),s=1,l=0,r="#0 ",o=-1!=t.data("selected-min")?t.data("selected-min"):e,c=-1!=t.data("selected-max")?t.data("selected-max"):i,d=t.data("internal-id-min"),h=t.data("internal-id-max"),f=new Array,p=(i-e)/O;p>1?p=Math.round(p):(s=.1,l=1,r="#0.0 ");var y=Math.round(e)+p;f.push(e);for(var v=1;O>v;v++)f.push(y),y+=p;f.push(i),t.attr("value",o+";"+c),t.jslider({from:e,to:i,scale:f,limits:!1,step:.1,round:l,dimension:n,skin:"round_plastic",format:{format:r},callback:function(t){var n=t.split(";"),s=n[0],l=n[1];yaasBlock.find(".yaas-criterion[data-type=slide][data-internal-id-min="+d+"][data-internal-id-max="+h+"]").each(function(){a(this).jslider("value",s,l),a(this).data("selected-min",s==e&&l==i?"-1":s),a(this).data("selected-max",s==e&&l==i?"-1":l),u(a(this),d+"p"+a(this).data("selected-min"),!0,!1),u(a(this),h+"p"+a(this).data("selected-max"),!0,!1)}),C()}})}function v(){Q=a.cookie("yaasGroups").split(",");for(var t=0;t<Q.length;t++){var e=Q[t];e.length>0&&($div=a('[id^="yaas-group-title-'+e+'"]'),m($div,!1))}}function m(t,e){var i=t.data("id");Q=[],yaasBlock.find(".yaas_group_title").each(function(){var t=a(this).data("id");if(i==t){var n=a(this).next('[id^="yaas-group-"]');a(this).hasClass("selected")?(Q.push(""+t),a(this).removeClass("selected"),e?n.hide("blind"):n.hide()):(a(this).addClass("selected"),e?n.show("blind"):n.show())}}),Q=jQuery.unique(Q),a.cookie("yaasGroups",Q.join(","))}function $(){D=a.ajax({dataType:"json",url:P,data:s(),success:function(a){g(a,!0),localStorage.setItem("yaas",JSON.stringify(a))},cache:!1})}function g(e,i){D=null,$center=a("#center_column");var n=$center.children().length,s=0;$center.children().hide("fade",function(){if(s++,n==s){null==M&&(M=$center.children().detach()),$dHtml=a(e.html).hide(),$center.html($dHtml),t(),localStorage.setItem("yaasMenu",JSON.stringify(b(e.menu))),c(),o(),r(e.menu,i),$center.children().show("fade",{},"slow",function(){});var l=$center.offset().top;V&&a("html, body").scrollTop()>l&&a("html, body").animate({scrollTop:l},500),a(".yaas_reinit").removeClass("button_disabled").removeAttr("disabled")}})}function k(a,t){for(var e=0;e<t.length;e++){var i=t[e];if(a.name==i.name&&a.count==i.count)return!0}return!1}function b(a){for(var t=[],e=0;e<a.length;e++){var i=a[e];k(i,t)||t.push(i)}return t}function C(){if(null!=D&&D.abort(),B=jQuery.unique(B),0==B.length){$center=a("#center_column");var t=$center.children().length,i=0;$center.children().hide("fade",function(){i++,t==i&&(null!=M&&($center.html(""),M.appendTo($center),M=null),$center.find(".yaas_waiter").remove(),e(!0),n(),$center.children().show("fade",{},"slow",function(){}),a(".yaas_reinit").addClass("button_disabled").attr("disabled","disabled"))})}else{var s=a('<div class="yaas_waiter"></div>');s.append('<img class="yaas_waiter_inner" src="'+A+'" />'),a("#center_column").prepend(s),s.show("blind",{},"fast",function(){$()})}}function x(a,t){for(var e=a.split(","),i=[],n=0;n<e.length;n++){var s=e[n];t.indexOf(s)<0&&i.push(s)}return i}function w(t){var e,i;if(null==t.val())s=!0;else{$allowMultiple=t.data("allow-multiple"),$currVal=t.val().toString(),$idCriteria=t.data("id-criteria-field"),void 0===q[$idCriteria]&&(q[$idCriteria]="");var n=q[$idCriteria];1==$allowMultiple?e=x($currVal,n):(e=[],e.push($currVal));for(var s=!1,l=0;l<e.length;l++){var r=e[l];-1==r?s=!0:($divs=a("[data-internal-id="+r+"]"),$divs.each(function(){h(a(this),r,!0,!0)}))}}1==$allowMultiple?i=x(n,$currVal):(i=[],t.find("option[data-internal-id!="+$currVal+"]").each(function(){i.push(a(this).val())}));for(var l=0;l<i.length;l++){var r=i[l];$divs=a("[data-internal-id="+r+"]"),$divs.each(function(){h(a(this),r,!1,!0)})}s&&($selects=a("select.yaas-select[data-id-criteria-field="+$idCriteria+"]"),$selects.each(function(){var t=!0;a(this).find("option").each(function(){if(t)a(this).prop("selected",!0),t=!1;else if(a(this).prop("selected")){var e=a(this).val();$divs=a("[data-internal-id="+e+"]"),$divs.each(function(){h(a(this),e,!1,!0)})}})})),I=1,j=-1,C(),q[$idCriteria]=$currVal}function _(){a(".breadcrumb a").each(function(){var t=a(this).attr("href");t.indexOf("yaas=")<0&&(t.indexOf("?")>0?a(this).attr("href",t+"&yaas=1"):a(this).attr("href",t+"?yaas=1"))})}var B=[],I=1,j=-1,F="price",S="asc",M=null,O=4,A=null,D=null,P=null,Q=null,V=null,R=null,q=[],J=null;"function"!=typeof getURLParameter&&(getURLParameter=function(a,t){return decodeURIComponent((new RegExp("[?|&]"+t+"=([^&;]+?)(&|#|;|$)").exec(a)||[,""])[1].replace(/\+/g,"%20"))||null}),a("document").ready(function(){l(),$canInitFromCookie=!0,$defaultFilterFromCookie=a.cookie("yaasDefaultFilter"),null!==R?(null!==$defaultFilterFromCookie&&$defaultFilterFromCookie!==R&&($canInitFromCookie=!1),a.cookie("yaasDefaultFilter",R)):null!==$defaultFilterFromCookie&&a.removeCookie("yaasDefaultFilter"),location.search.indexOf("yaas=")>0&&(null!=localStorage.getItem("yaasMenu")&&null!=a.cookie("yaasCriteria")?_():(localYaas=localStorage.getItem("yaas"),null!==localYaas&&(yaas=jQuery.parseJSON(localYaas),g(yaas,!1)))),localMenu=localStorage.getItem("yaasMenu"),null!==localMenu&&$canInitFromCookie?(yaasMenu=jQuery.parseJSON(localMenu),r(yaasMenu,!1),null!==a.cookie("yaasCriteria")&&(B=a.cookie("yaasCriteria").split(","),d(B))):p(),null!=a.cookie("yaasGroups")&&v(),yaasBlock.find(".yaas_group_title").each(function(){a(this).click(function(){m(a(this),!0)})}),yaasBlock.find(".yaas-criterion").each(function(){var t=a(this).data("type");"link"==t?a(this).click(function(){var t=a(this).data("internal-id"),e=a(this).data("allow-multiple");0==e&&($toDeselect=a(this).closest("ul").find("[data-internal-id!="+t+"]"),$toDeselect.each(function(){if(a(this).hasClass("selected")){var t=a(this).data("internal-id");$divs=a("[data-internal-id="+t+"]"),$divs.each(function(){h(a(this),t,!1,!0)})}})),$divs=a("[data-internal-id="+t+"]"),$divs.each(function(){u(a(this),t,!0,!1)}),I=1,C()}):"slide"==t&&y(a(this))}),yaasBlock.find(".yaas-select").each(function(){a(this).change(function(){w(a(this))})}),yaasBlock.find(".yaas_reinit").click(function(){a(this).hasClass("disabled")||(yaasBlock.find(".yaas-criterion").each(function(){var t=a(this).data("type");if("link"==t){if(a(this).hasClass("selected")){var e=a(this).data("internal-id");u(a(this),e,!1,!1)}}else if("combo"==t){if(1==a(this).prop("selected")){var e=a(this).data("internal-id");u(a(this),e,!1,!1)}}else if("slide"==t){var i=a(this),n=i.data("min"),s=i.data("max");i.jslider("value",n,s)}}),B=[],q=[],C())}),B.length>0&&a(".yaas_reinit").removeClass("button_disabled")})}(jQuery);
