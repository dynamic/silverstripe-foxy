!function(n){var s=n('[id*="submitPrice"]'),t=n(".product-options"),e=!!n('[name="x:submit"]').length,i=t.closest("form").find('[id*="_unavailableText"]');n("option:disabled").each(function(){n(this).prop("disabled")&&n(this).addClass("outOfStock").append(document.createTextNode(" (out of stock)"))}),t.on("change",function(){var e=[],t=n(this).val();0<t.length&&(t=t.substring(t.lastIndexOf("{")+1,t.lastIndexOf("}")).split("|")[0].split(":")[1]),n(this).each(function(){var t=n(this).val();(t=t.substring(t.lastIndexOf("{")+1,t.lastIndexOf("}")).split("|")).length&&n.each(t,function(t,n){""!==n&&(e[n.split(":")[1]]=n.split(":")[1])})}),t in e&&void 0!==e[t]&&s.html("$"+Number.parseFloat(e[t]).toFixed(2))}),!1==e?(s.addClass("d-none"),i.removeClass("d-none")):0<t.length&&t.change()}(jQuery);