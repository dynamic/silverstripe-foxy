/**
 * Quantity Field Handler - Vanilla JavaScript
 * Handles cart quantity increase/decrease with inventory validation
 */
(function () {
  'use strict';

  /**
   * Get the link URL for AJAX requests
   * @param {HTMLElement} element - The button element
   * @returns {string} - The AJAX endpoint URL
   */
  function getLink(element) {
    const input = element.parentElement.querySelector("input[name='x:visibleQuantity']");
    return input ? input.dataset.link : '';
  }

  /**
   * Build URL with query parameters
   * @param {string} link - Base URL
   * @param {Object} data - Query parameters
   * @returns {string} - Full URL with query string
   */
  function getLinkURL(link, data) {
    const delimiter = link.indexOf('?') !== -1 ? '&' : '?';
    return link + delimiter + new URLSearchParams(data).toString();
  }

  /**
   * Get the product code from the quantity field
   * @param {HTMLElement} element - The button element
   * @returns {string} - The product code
   */
  function getCode(element) {
    const input = element.parentElement.querySelector("input[name='x:visibleQuantity']");
    return input ? input.dataset.code : '';
  }

  /**
   * Get the product ID from the quantity field
   * @param {HTMLElement} element - The button element
   * @returns {string} - The product ID
   */
  function getId(element) {
    const input = element.parentElement.querySelector("input[name='x:visibleQuantity']");
    return input ? input.dataset.id : '';
  }

  /**
   * Get the quantity limit from the quantity field
   * @param {HTMLElement} element - The button element
   * @returns {number} - The quantity limit
   */
  function getLimit(element) {
    const input = element.parentElement.querySelector("input[name='x:visibleQuantity']");
    return input ? parseInt(input.dataset.limit) || 0 : 0;
  }

  /**
   * Update the quantity limit on the field
   * @param {HTMLElement} element - The button element
   * @param {number} newLimit - The new limit value
   */
  function updateLimit(element, newLimit) {
    const input = element.parentElement.querySelector("input[name='x:visibleQuantity']");
    if (input) {
      input.dataset.limit = newLimit;
    }
  }

  /**
   * Get the visible quantity input field
   * @param {HTMLElement} element - The button element
   * @returns {HTMLElement|null} - The quantity input field
   */
  function getVisibleQuantityField(element) {
    return element.parentElement.parentElement.querySelector("input[name='x:visibleQuantity']");
  }

  /**
   * Disable the increase button
   * @param {HTMLElement} element - The button element
   */
  function disableIncreaseButton(element) {
    const btn = element.parentElement.querySelector('button.increase');
    if (btn) btn.disabled = true;
  }

  /**
   * Enable the increase button
   * @param {HTMLElement} element - The button element
   */
  function enableIncreaseButton(element) {
    const btn = element.parentElement.querySelector('button.increase');
    if (btn) btn.disabled = false;
  }

  /**
   * Hide both increase and decrease buttons
   * @param {HTMLElement} element - The button element
   */
  function hideButtons(element) {
    const buttons = element.parentElement.querySelectorAll('button.increase, button.reduced');
    buttons.forEach(function (btn) {
      btn.disabled = true;
      btn.classList.add('hidden');
    });
  }

  /**
   * Display out of stock message and remove submit button
   * @param {HTMLElement} element - The button element
   */
  function outOfStock(element) {
    const form = element.closest('form[id^=FoxyStripePurchaseForm_PurchaseForm_]');
    if (!form) return;

    const id = form.id;
    const fieldset = form.querySelector('fieldset');
    if (fieldset) {
      fieldset.innerHTML = '<h4 id="' + id + '_unavailableText">Currently Out of Stock</h4>';
    }

    const submitBtn = form.querySelector('input[name="action_x:submit"]');
    if (submitBtn) submitBtn.remove();
  }

  /**
   * Disable the add to cart submit button
   * @param {HTMLElement} element - The button element
   */
  function disableSubmit(element) {
    const submitBtn = element.closest('.product__form, form')?.querySelector('.fs-add-to-cart-button');
    if (submitBtn) submitBtn.disabled = true;
  }

  /**
   * Enable the add to cart submit button
   * @param {HTMLElement} element - The button element
   */
  function enableSubmit(element) {
    const submitBtn = element.closest('.product__form, form')?.querySelector('.fs-add-to-cart-button');
    if (submitBtn) submitBtn.disabled = false;
  }

  /**
   * Query new quantity value from server
   * @param {string} code - Product code
   * @param {number} newValue - New quantity value
   * @param {string} link - AJAX endpoint URL
   * @param {string} id - Product ID
   * @param {HTMLElement} clicked - The clicked button element
   */
  function queryNewValue(code, newValue, link, id, clicked) {
    const quantData = {
      'code': code,
      'value': newValue,
      'id': id,
      'isAjax': 1
    };

    fetch(getLinkURL(link, quantData))
      .then(function (response) {
        if (!response.ok) {
          throw new Error(response.statusText);
        }
        return response.json();
      })
      .then(function (data) {
        if (data.hasOwnProperty('limit')) {
          updateLimit(clicked, data.limit);

          const visibleQuantity = getVisibleQuantityField(clicked);
          if (visibleQuantity && data.limit < parseInt(visibleQuantity.value)) {
            visibleQuantity.value = data.limit;
          }

          if (data.limit === 0) {
            outOfStock(clicked);
          } else if (data.limit === 1) {
            hideButtons(clicked);
          } else if (data.limit === data.quantity) {
            disableIncreaseButton(clicked);
          } else {
            enableIncreaseButton(clicked);
          }
        }

        // Update hidden quantity field with generated value
        const form = clicked.closest('form');
        if (form) {
          const hiddenQty = form.querySelector("input[name='quantity']");
          if (hiddenQty) {
            hiddenQty.value = data.quantityGenerated;
          }
        }

        enableSubmit(clicked);
      })
      .catch(function (error) {
        if (error.message.includes("404") || error.message.includes("sub-URLs")) {
          outOfStock(clicked);
        } else {
          console.error('Error:', error.message);
        }
      });
  }

  /**
   * Initialize event listeners
   */
  function init() {
    // Event delegation for increase button
    document.addEventListener('click', function (event) {
      if (!event.target.matches('button.increase')) return;

      const btn = event.target;
      const visibleQuantity = getVisibleQuantityField(btn);
      if (!visibleQuantity) return;

      const currentVal = parseInt(visibleQuantity.value) || 0;
      const newValue = currentVal + 1;

      disableSubmit(btn);
      queryNewValue(getCode(btn), newValue, getLink(btn), getId(btn), btn);
      visibleQuantity.value = newValue;
    });

    // Event delegation for decrease button
    document.addEventListener('click', function (event) {
      if (!event.target.matches('button.reduced')) return;

      const btn = event.target;
      const visibleQuantity = getVisibleQuantityField(btn);
      if (!visibleQuantity) return;

      const currentVal = parseInt(visibleQuantity.value) || 0;
      const newValue = currentVal - 1;

      if (currentVal > 1) {
        disableSubmit(btn);
        queryNewValue(getCode(btn), newValue, getLink(btn), getId(btn), btn);
        visibleQuantity.value = newValue;
      }
    });

    // Check initial limits on page load
    document.querySelectorAll('button.increase').forEach(function (btn) {
      const limit = getLimit(btn);
      if (limit === 1) {
        hideButtons(btn);
      } else if (limit === 0) {
        outOfStock(btn);
      }
    });
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

/**
 * Foxy Cart Integration
 * Syncs quantity with Foxy cart when items are added/updated
 */
var FC = FC || {};
FC.onLoad = (function (_super) {
  return function () {
    if (typeof _super !== 'undefined') {
      _super.apply(this, arguments);
    }

    /**
     * Update quantity in local inventory tracking
     */
    function updateQuantity() {
      FC.client.request('https://' + FC.settings.storedomain + '/cart?output=json')
        .done(function (dataJSON) {
          dataJSON.items.forEach(function (product) {
            const code = product.parent_code === '' ? product.code : product.parent_code;
            const link = product.hasOwnProperty('url') ? product.url : false;

            if (!link) return;

            const parts = link.split('?');
            let baseLink = parts[0];
            let extra = parts.length > 1 ? '&' + parts[1] : '';

            const url = baseLink + 'AddToCartForm/field/x:visibleQuantity/newvalue?code=' + code +
              '&id=' + product.id + '&value=' + product.quantity + '&isAjax=1' + extra;

            fetch(url)
              .then(function (response) {
                return response.json();
              })
              .then(function (data) {
                if (product.quantity !== data.quantity) {
                  setTimeout(function () {
                    FC.cart.updateItemQuantity({
                      id: product.id,
                      quantity: data.quantity
                    });
                  }, 150);
                  return;
                }
                FC.client.event('cart-quantity-updated').trigger({
                  id: product.id,
                  quantity: data.quantity
                });
              })
              .catch(function (error) {
                console.error('Quantity sync error:', error);
              });
          });
        });
    }

    FC.client.on('cart-item-quantity-update.done', function () {
      updateQuantity();
    });

    FC.client.on('cart-submit.done', function () {
      updateQuantity();
    });
  };
})(FC.onLoad);
