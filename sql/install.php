<?php
/**
 * Cookie Popup for GDPR Cookie Consent.
 *
 * @author    Sergei
 * @copyright 2023 LINK Company
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
$sql = ['CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'slcookie` (
    `id_slcookie` int(11) NOT NULL AUTO_INCREMENT,
                    `slc_title` varchar(128) DEFAULT NULL,
                    `slc_text` varchar(1024) DEFAULT NULL,
					`slc_text_url` varchar(1024) DEFAULT NULL,
                    `slc_url` varchar(500) DEFAULT NULL,
                    `slc_position` varchar(100) DEFAULT NULL,
                    `slc_btn_confirm` varchar(32) DEFAULT NULL,
                    `id_lang` int(10) unsigned NOT NULL,
    PRIMARY KEY  (`id_slcookie`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;'];

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
