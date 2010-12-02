<?
/**
 * Модуль управления тегами
 *
 * @author		Vladimir Savenkov
 */
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/tagsmanager/install/index.php'); //ч0ртов БУГ не понимает симлинки
if(class_exists("tagsmanager")) return;

class tagsmanager extends CModule{
	
	var $MODULE_ID = "tagsmanager";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	var $errors;

	function tagsmanager(){
		$arModuleVersion = array();

		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");

		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)){
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		}else{
			$this->MODULE_VERSION = TAGSMANAGER_VERSION;
			$this->MODULE_VERSION_DATE = TAGSMANAGER_VERSION_DATE;
		}

		$this->MODULE_NAME = GetMessage("TAGSMANAGER_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("TAGSMANAGER_MODULE_DESC");
	}

	function InstallFiles(){
				
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tagsmanager/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tagsmanager/install/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/tagsmanager/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tagsmanager/install/images/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/images/tagsmanager", true, true);
		
		return true;
	}

	function UnInstallFiles(){
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tagsmanager/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin");
		DeleteDirFilesEx("/bitrix/js/tagsmanager/");//javascript
		return true;
	}

	function DoInstall(){
		global $DB, $DOCUMENT_ROOT, $APPLICATION;
		$this->InstallFiles();
		RegisterModule("tagsmanager");
	}

	function DoUninstall(){
		global $DB, $DOCUMENT_ROOT, $APPLICATION;
		$this->UnInstallFiles();
		UnRegisterModule("tagsmanager");
	}
}
?>