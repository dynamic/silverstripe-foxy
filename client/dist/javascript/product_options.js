/**
 * Product Options Handler - Vanilla JavaScript
 * Handles price modifier display when product variations are selected
 */
(function() {
  'use strict';

  /**
   * Initialize product options functionality
   */
  function init() {
    const shownPrice = document.querySelector('[id*="submitPrice"]');
    const priceInput = document.querySelector('input[name="price"]');
    const triggers = document.querySelectorAll('select.product-options');

    if (!priceInput || !shownPrice) {
      return;
    }

    const basePrice = priceInput.value.split('||')[0];

    // Mark disabled options as out of stock
    document.querySelectorAll('.foxycartOptionsContainer option:disabled').forEach(function(option) {
      if (option.disabled) {
        option.classList.add('outOfStock');
        option.textContent += ' (out of stock)';
      }
    });

    // Bind change event to each trigger
    triggers.forEach(function(trigger) {
      trigger.addEventListener('change', function(event) {
        const selected = event.target.value;
        const modifiers = selected.substring(
          selected.lastIndexOf('{') + 1,
          selected.lastIndexOf('}')
        );

        let alteredPrice;

        const addition = getAddition(modifiers);
        const subtraction = getSubtraction(modifiers);
        const newPrice = getNewPrice(modifiers);

        if (addition !== undefined) {
          alteredPrice = parseFloat(addition) + parseFloat(basePrice);
        } else if (subtraction !== undefined) {
          alteredPrice = parseFloat(basePrice) - parseFloat(subtraction);
        } else if (newPrice !== undefined) {
          alteredPrice = parseFloat(newPrice);
        }

        if (alteredPrice !== undefined) {
          shownPrice.innerHTML = '$' + Number.parseFloat(alteredPrice).toFixed(2);
        } else {
          shownPrice.innerHTML = '$' + Number.parseFloat(basePrice).toFixed(2);
        }
      });
    });

    // Trigger change on load if triggers exist
    if (triggers.length > 0) {
      window.addEventListener('load', function() {
        triggers.forEach(function(trigger) {
          trigger.dispatchEvent(new Event('change'));
        });
      });
    }
  }

  /**
   * Extract price addition from modifiers string
   * @param {string} variants - The modifiers string
   * @returns {string|undefined} - The price modifier or undefined
   */
  function getAddition(variants) {
    const parts = variants.split('|')[0];
    const splitParts = parts.split('+');
    return splitParts.length === 2 ? splitParts[1] : undefined;
  }

  /**
   * Extract price subtraction from modifiers string
   * @param {string} variants - The modifiers string
   * @returns {string|undefined} - The price modifier or undefined
   */
  function getSubtraction(variants) {
    const parts = variants.split('|')[0];
    const splitParts = parts.split('-');
    return splitParts.length === 2 ? splitParts[1] : undefined;
  }

  /**
   * Extract new fixed price from modifiers string
   * @param {string} variants - The modifiers string
   * @returns {string|undefined} - The new price or undefined
   */
  function getNewPrice(variants) {
    const parts = variants.split('|')[0];
    const splitParts = parts.split(':');
    return splitParts.length === 2 ? splitParts[1] : undefined;
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
