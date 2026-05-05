document.addEventListener('DOMContentLoaded', function () {
  const box = document.getElementById('pyp-box');
  if (!box) return;

  const input = document.getElementById('pyp_custom_price');
  const hidden = document.getElementById('pyp_custom_price_hidden');
  const help = document.getElementById('pyp_help');

  const basePrice = parseFloat(box.dataset.basePrice);
  const minPrice = parseFloat(box.dataset.minPrice);
  const maxPrice = parseFloat(box.dataset.maxPrice || '0');
  const currency = box.dataset.currency;
  const productId = parseInt(box.dataset.idProduct, 10);
  const productName = box.dataset.productName;

  function formatPrice(v) {
    return new Intl.NumberFormat(document.documentElement.lang || 'en-US', {
      style: 'currency',
      currency: currency
    }).format(v);
  }

  function syncPrice() {
    const value = parseFloat(input.value || '0');
    if (isNaN(value) || value < basePrice || value < minPrice || (maxPrice > 0 && value > maxPrice)) {
      input.setCustomValidity('Invalid custom price');
      help.classList.add('text-danger');
      help.innerText = `Allowed range: ${formatPrice(minPrice)}${maxPrice > 0 ? ` - ${formatPrice(maxPrice)}` : ''}`;
      return false;
    }

    input.setCustomValidity('');
    hidden.value = value.toFixed(2);
    help.classList.remove('text-danger');
    help.innerText = `Custom price selected: ${formatPrice(value)}`;

    const displayed = document.querySelector('.current-price .price, .product-price');
    if (displayed) {
      displayed.textContent = formatPrice(value);
    }

    return true;
  }

  input.addEventListener('input', syncPrice);
  syncPrice();

  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (!form.matches('#add-to-cart-or-refresh, form[action*="cart"]')) return;
    if (!syncPrice()) {
      e.preventDefault();
      e.stopPropagation();
      input.reportValidity();
      return;
    }

    const customPrice = parseFloat(hidden.value);
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event: 'add_to_cart',
      ecommerce: {
        currency: currency,
        value: customPrice,
        items: [{
          item_id: productId,
          item_name: productName,
          price: customPrice,
          quantity: 1,
          custom_price_enabled: true,
          original_price: basePrice
        }]
      }
    });
  }, true);
});
