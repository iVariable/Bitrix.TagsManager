<?
IncludeModuleLangFile(__FILE__);

if($APPLICATION->GetGroupRight("tagsmanager")!="D")
{
	$aMenu = array(
		"parent_menu" => "global_menu_services",
		"section" => "tagsmanager",
		"sort" => 200,
		"text" => GetMessage("mnu_tagsmanager"),
		"title" => GetMessage("mnu_tagsmanager_title"),
		"url" => "tagsmanager_index.php",
		"icon" => "search_menu_icon",
		"page_icon" => "search_page_icon",
		"items_id" => "menu_tagsmanager",
		"items" => array(
			array(
				"text" => GetMessage("mnu_tagsmanager_all"),
				"url" => "tagsmanager_list.php?actionType=all",
				"more_url" => Array("tagsmanager_list.php"),
				"title" => GetMessage("mnu_tagsmanager_all"),
			),
			array( //info
				"text" => GetMessage("mnu_tagsmanager_info"),
				"url" => "tagsmanager_info.php?actionType=info",
				"title" => GetMessage("mnu_tagsmanager_info"),
			),
		)
	);
			
	if($APPLICATION->GetGroupRight("tagsmanager")>='W'){
		$aEditLinks = array(
			array( //add_new
				"text" => GetMessage("mnu_tagsmanager_add_new"),
				"url" => "tagsmanager_add_new.php?actionType=add_new",
				"title" => GetMessage("mnu_tagsmanager_add_new"),
			),
			
			array( //rename
				"text" => GetMessage("mnu_tagsmanager_rename"),
				"url" => "tagsmanager_rename.php?actionType=rename",
				"title" => GetMessage("mnu_tagsmanager_rename"),
			),
			
			array( //implode
				"text" => GetMessage("mnu_tagsmanager_implode"),
				"url" => "tagsmanager_implode.php?actionType=implode",
				"title" => GetMessage("mnu_tagsmanager_implode"),
			),
			
			array( //explode
				"text" => GetMessage("mnu_tagsmanager_explode"),
				"url" => "tagsmanager_explode.php?actionType=explode",
				"title" => GetMessage("mnu_tagsmanager_explode"),
			),
			
			array( //remove
				"text" => GetMessage("mnu_tagsmanager_remove"),
				"url" => "tagsmanager_remove.php?actionType=remove",
				"title" => GetMessage("mnu_tagsmanager_remove"),
			),
		);
		$aMenu['items'] = array_merge( $aMenu['items'], $aEditLinks );
	}
	return $aMenu;
}
return false;
?>
