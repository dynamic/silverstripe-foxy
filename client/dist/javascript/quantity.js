!function(u){function e(t){return t.parent().find("input[name='x:visibleQuantity']").data("link")}function r(t){return t.parent().find("input[name='x:visibleQuantity']").data("code")}function d(t){return t.parent().find("input[name='x:visibleQuantity']").data("id")}function o(t){return t.parent().parent().find("input[name='x:visibleQuantity']")}function s(t){t.parent().find("button.increase, button.reduced").attr("disabled",!0).addClass("hidden")}function c(t){var n=(t=t.parents("form[id^=FoxyStripePurchaseForm_PurchaseForm_]")).attr("id");t.find("fieldset").html('<h4 id="'+n+'_unavailableText">Currently Out of Stock</h4>'),t.find("input[name=action_x\\:submit]").remove()}function l(t){t.parent().parent().parent().find(".fs-add-to-cart-button").attr("disabled",!0)}function p(){m.data("fetch",1)}function f(t,n,i,a,e){u.ajax({type:"get",url:(t={code:t,value:n,id:a,isAjax:1},n="?",-1!=(a=i).indexOf("?")&&(n="&"),a+n+u.param(t))}).done(function(t){var n,t=JSON.parse(t);t.hasOwnProperty("limit")&&(n=t.limit,e.parent().find("input[name='x:visibleQuantity']").data("limit",n),n=o(e),t.limit<n.val()&&n.val(t.limit),0==t.limit?c(e):1==t.limit?s(e):t.limit==t.quantity?e.parent().find("button.increase").attr("disabled",!0):e.parent().find("button.increase").attr("disabled",!1)),e.parent().parent().parent().parent().find("input[name='quantity']").val(t.quantityGenerated),m.data("fetch",0),e.parent().parent().parent().find(".fs-add-to-cart-button").attr("disabled",!1)}).fail(function(t){404==t.status&&"I can't handle sub-URLs on class SilverStripe\\Forms\\FormRequestHandler."==t.responseText?c(e):console.log("Error: "+t.responseText)})}var m=u("input[name='x:visibleQuantity']");u("input[name='quantity']");u(document).on("click","button.increase",function(t){var n=o(u(this)),i=n.val(),i=parseInt(i)+1;p(),l(u(this)),f(r(u(this)),i,e(u(this)),d(u(this)),u(this)),n.val(i)}),u(document).on("click","button.reduced",function(t){m.data("fetch",1);var n=o(u(this)),i=n.val(),a=parseInt(i)-1;1<i&&(p(),l(u(this)),f(r(u(this)),a,e(u(this)),d(u(this)),u(this)),n.val(a))}),u(document).ready(function(){u("button.increase").each(function(){var t=u(this).parent().find("input[name='x:visibleQuantity']").data("limit");1==t?s(u(this)):0==t&&c(u(this))})})}(jQuery);var FC=FC||{};FC.onLoad=function(n){return function(){function t(){FC.client.request("https://"+FC.settings.storedomain+"/cart?output=json").done(function(t){jQuery.each(t.items,function(t,n){var i,a,e=""===n.parent_code?n.code:n.parent_code,u=!!n.hasOwnProperty("url")&&n.url;u&&(a="",1<(i=u.split("?")).length&&(u=i[0],a="&"+i[1]),jQuery.ajax({url:u+"AddToCartForm/field/x:visibleQuantity/newvalue?code="+e+"&id="+n.id+"&value="+n.quantity+"&isAjax=1"+a,dataType:"json",success:function(t){n.quantity!=t.quantity?setTimeout(function(){FC.cart.updateItemQuantity({id:n.id,quantity:t.quantity})},150):FC.client.event("cart-quantity-updated").trigger({id:n.id,quantity:t.quantity})}}))})})}void 0!==n&&n.apply(this,arguments),FC.client.on("cart-item-quantity-update.done",function(){t()}),FC.client.on("cart-submit.done",function(){t()})}}(FC.onLoad);