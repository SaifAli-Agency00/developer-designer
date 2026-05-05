<?php

class PayYourPriceRepository
{
    public function upsertCartPrice($idCart, $idProduct, $idProductAttribute, $idCustomization, $price)
    {
        $where = 'id_cart=' . (int) $idCart
            . ' AND id_product=' . (int) $idProduct
            . ' AND id_product_attribute=' . (int) $idProductAttribute
            . ' AND id_customization=' . (int) $idCustomization;

        $exists = (bool) Db::getInstance()->getValue('SELECT 1 FROM ' . _DB_PREFIX_ . 'pyp_cart_price WHERE ' . $where);
        $data = [
            'id_cart' => (int) $idCart,
            'id_product' => (int) $idProduct,
            'id_product_attribute' => (int) $idProductAttribute,
            'id_customization' => (int) $idCustomization,
            'custom_price' => (float) $price,
            'date_upd' => date('Y-m-d H:i:s'),
        ];

        if ($exists) {
            return Db::getInstance()->update('pyp_cart_price', $data, $where);
        }

        $data['date_add'] = date('Y-m-d H:i:s');
        return Db::getInstance()->insert('pyp_cart_price', $data);
    }

    public function getCartPrice($idCart, $idProduct, $idProductAttribute, $idCustomization)
    {
        $sql = 'SELECT custom_price FROM ' . _DB_PREFIX_ . 'pyp_cart_price
                WHERE id_cart=' . (int) $idCart . '
                AND id_product=' . (int) $idProduct . '
                AND id_product_attribute=' . (int) $idProductAttribute . '
                AND id_customization=' . (int) $idCustomization;

        $value = Db::getInstance()->getValue($sql);
        return $value !== false ? (float) $value : null;
    }

    public function linkOrderFromCart($idCart, $idOrder)
    {
        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'pyp_order_price
                (id_order, id_product, id_product_attribute, id_customization, custom_price, date_add)
                SELECT ' . (int) $idOrder . ', id_product, id_product_attribute, id_customization, custom_price, NOW()
                FROM ' . _DB_PREFIX_ . 'pyp_cart_price WHERE id_cart=' . (int) $idCart;

        return Db::getInstance()->execute($sql);
    }

    public function getOrderCustomPrices($idOrder)
    {
        $sql = 'SELECT op.id_product, op.id_product_attribute, op.custom_price, pl.name
                FROM ' . _DB_PREFIX_ . 'pyp_order_price op
                INNER JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (pl.id_product = op.id_product AND pl.id_lang=' . (int) Context::getContext()->language->id . ')
                WHERE op.id_order=' . (int) $idOrder;

        return Db::getInstance()->executeS($sql);
    }

    public function getOrderCustomPriceByOrderDetail($idOrderDetail)
    {
        $sql = 'SELECT op.custom_price
                FROM ' . _DB_PREFIX_ . 'order_detail od
                INNER JOIN ' . _DB_PREFIX_ . 'pyp_order_price op ON (
                    op.id_order = od.id_order
                    AND op.id_product = od.product_id
                    AND op.id_product_attribute = od.product_attribute_id
                )
                WHERE od.id_order_detail=' . (int) $idOrderDetail;

        $value = Db::getInstance()->getValue($sql);
        return $value !== false ? (float) $value : null;
    }
}
