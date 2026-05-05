CREATE TABLE IF NOT EXISTS `PREFIX_pyp_cart_price` (
  `id_pyp_cart_price` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_cart` INT UNSIGNED NOT NULL,
  `id_product` INT UNSIGNED NOT NULL,
  `id_product_attribute` INT UNSIGNED NOT NULL DEFAULT 0,
  `id_customization` INT UNSIGNED NOT NULL DEFAULT 0,
  `custom_price` DECIMAL(20,6) NOT NULL,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_pyp_cart_price`),
  UNIQUE KEY `uniq_cart_product` (`id_cart`,`id_product`,`id_product_attribute`,`id_customization`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_pyp_order_price` (
  `id_pyp_order_price` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_order` INT UNSIGNED NOT NULL,
  `id_product` INT UNSIGNED NOT NULL,
  `id_product_attribute` INT UNSIGNED NOT NULL DEFAULT 0,
  `id_customization` INT UNSIGNED NOT NULL DEFAULT 0,
  `custom_price` DECIMAL(20,6) NOT NULL,
  `date_add` DATETIME NOT NULL,
  PRIMARY KEY (`id_pyp_order_price`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;
