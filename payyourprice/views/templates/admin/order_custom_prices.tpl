<div class="card mt-3">
  <div class="card-header">Pay Your Price</div>
  <div class="card-body">
    <table class="table">
      <thead>
      <tr><th>Product</th><th>Attribute ID</th><th>Custom Price Applied</th></tr>
      </thead>
      <tbody>
      {foreach from=$pyp_rows item=row}
        <tr>
          <td>{$row.name|escape:'htmlall':'UTF-8'} (#{$row.id_product|intval})</td>
          <td>{$row.id_product_attribute|intval}</td>
          <td>{$row.custom_price|string_format:'%.2f'}</td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  </div>
</div>
