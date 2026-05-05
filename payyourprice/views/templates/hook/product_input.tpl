<div id="pyp-box" class="mt-2"
     data-id-product="{$pyp_id_product|intval}"
     data-base-price="{$pyp_base_price|floatval}"
     data-min-price="{$pyp_min_price|floatval}"
     data-max-price="{$pyp_max_price|floatval}"
     data-currency="{$pyp_currency_iso|escape:'htmlall':'UTF-8'}"
     data-product-name="{$pyp_product_name|escape:'htmlall':'UTF-8'}">
  <label for="pyp_custom_price" class="form-label">Pay Your Price – Enter the amount you want to pay</label>
  <div class="input-group">
    <span class="input-group-text">{$pyp_currency_sign|escape:'htmlall':'UTF-8'}</span>
    <input type="number" class="form-control" name="pyp_custom_price" id="pyp_custom_price" min="{$pyp_min_price|floatval}" step="0.01" value="{$pyp_min_price|floatval}">
  </div>
  <small id="pyp_help" class="form-text text-muted">Minimum: {$pyp_currency_sign} {$pyp_min_price|string_format:'%.2f'}</small>
  <input type="hidden" name="pyp_custom_price" id="pyp_custom_price_hidden" value="{$pyp_min_price|floatval}">
</div>
