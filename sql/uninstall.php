<?php
/**
 * Cookie Popup for GDPR Cookie Consent.
 *
 * @author    Sergei
 * @copyright 2023 LINK Company
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * In some cases you should not drop the tables.
 * Maybe the merchant will just try to reset the module
 * but does not want to loose all of the data associated to the module.
 */
$sql = ['DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'slcookie`;'];

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
