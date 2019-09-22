;(function ($) {
  var shownPrice = $('[id*="submitPrice"]'),
    basePrice = $('input[name="price"]').val().split('||')[0],
    trigger = $('select.product-options');

  $('.foxycartOptionsContainer option:disabled').each(function () {
    if ($(this).prop('disabled')) {
      $(this).addClass('outOfStock').append(document.createTextNode(" (out of stock)"));
    }
  });

  trigger.bind('change', function () {
    var selected = $(this).val(),
      modifiers = $(this).val().substring(selected.lastIndexOf('{') + 1, selected.lastIndexOf('}'));

    if (getAddition(modifiers) !== undefined) {
      alteredPrice = parseInt(getAddition(modifiers)) + parseInt(basePrice);
    } else if (getSubtraction(modifiers) !== undefined) {
      alteredPrice = parseInt(basePrice) - parseInt(getSubtraction(modifiers));
    } else if (getNewPrice(modifiers)) {
      alteredPrice = parseInt(getNewPrice(modifiers));
    }

    if (alteredPrice !== undefined) {
      shownPrice.html('$' + Number.parseFloat(alteredPrice).toFixed(2));
    } else {
      shownPrice.html(basePrice);
    }
  });

  if (trigger.length > 0) {
    $(window).on('load', function () {
      trigger.change();
    });
  }

  function getAddition(variants) {
    priceModifier = variants.split('|')[0];
    priceModifier = (priceModifier.split('+').length === 2) ? priceModifier.split('+')[1] : undefined;

    return priceModifier;
  }

  function getSubtraction(variants) {
    priceModifier = variants.split('|')[0];
    priceModifier = (priceModifier.split('-').length === 2) ? priceModifier.split('-')[1] : undefined;

    return priceModifier;
  }

  function getNewPrice(variants) {
    priceModifier = variants.split('|')[0];
    priceModifier = (priceModifier.split(':').length === 2) ? priceModifier.split(':')[1] : undefined;

    return priceModifier;
  }
})(jQuery);
