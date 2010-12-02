<?php
/**
 * Данный файл подключается в тот момент когда речь идет о подключении модуля в коде, в нем должны находиться включения всех файлов с библиотеками функций и классов модуля
 */
$sDir = dirname( __FILE__ );
require_once( $sDir.'/classes/general/CTagsManager.class.php' );
require_once( $sDir.'/classes/general/ITagsManagerDriver.interface.php' );
require_once( $sDir.'/classes/general/CTagsManagerDriverAbstract_AllBitrix.class.php' );
?>