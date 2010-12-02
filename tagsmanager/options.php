<?php
/**
 * Данный файл подключается на странице настройки параметров модулей в административном меню "Настройки"
 */
?>
<?
$TAGSMANAGER_DEFAULT_PERMISSION = 'D';
$module_id = "tagsmanager";
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/include.php");
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".$module_id."/options.php");

$TAGSMANAGER_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($TAGSMANAGER_RIGHT>="R") :
if ($REQUEST_METHOD=="GET" && strlen($RestoreDefaults)>0){
	COption::RemoveOption($module_id);
	$arGROUPS = array();
	$z = CGroup::GetList($v1, $v2, array("ACTIVE" => "Y", "ADMIN" => "N"));
	while($zr = $z->Fetch())
	{
		$ar = array();
		$ar["ID"] = intval($zr["ID"]);
		$ar["NAME"] = htmlspecialchars($zr["NAME"])." [<a title=\"".GetMessage("MAIN_USER_GROUP_TITLE")."\" href=\"/bitrix/admin/group_edit.php?ID=".intval($zr["ID"])."&lang=".LANGUAGE_ID."\">".intval($zr["ID"])."</a>]";
		$groups[$zr["ID"]] = "[".$zr["ID"]."] ".$zr["NAME"];
		$arGROUPS[] = $ar;
	}
	reset($arGROUPS);
	while (list(,$value) = each($arGROUPS))
		$APPLICATION->DelGroupRight($module_id, array($value["ID"]));
}

$aAvailDrivers = CTagsManager::getAvailableDrivers();

$aSites = array();
$rSites = CSite::GetList($by="sort", $order="desc", array() );
while( $aSite = $rSites->Fetch() ){
	$aSites[$aSite['NAME'].' '.$aSite['ID']] = $aSite['ID'];
};

$arAllOptions = array(
	array( 'SITE_ID', GetMessage('SITE_ID'), array('checkbox_group', $aSites, 'useIndex' )),
	array( 'DENIED_DRIVERS', GetMessage('DENIED_DRIVERS'), array('checkbox_group', $aAvailDrivers['WORKING_DRIVERS'] ) ),
);
if( !empty( $aAvailDrivers['DRIVER_LOADING_ERROR'] ) ){
	$arAllOptions[] = array( 'UNUSABLE_DRIVERS', GetMessage('UNUSABLE_DRIVERS'), array('notice_group', $aAvailDrivers['DRIVER_LOADING_ERROR'] ) );
}

if($REQUEST_METHOD=="POST" && strlen($Update)>0 && check_bitrix_sessid())
{
	foreach($arAllOptions as $ar)
	{
		$name = $ar[0];
		$val = $$name;
		if($ar[2][0] == "checkbox_group"){
			$val = implode( ',', $val );
		}
		if($ar[2][0] == "checkbox" && $val != "Y")
		{
			$val = "N";
		}
		COption::SetOptionString($module_id, $name, $val);
	}
	COption::SetOptionString($module_id, "TAGSMANAGER_DEFAULT_PERMISSION", $TAGSMANAGER_DEFAULT_PERMISSION);
}

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("MAIN_TAB_SET"), "ICON" => "form_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_SET")),
	array("DIV" => "edit2", "TAB" => GetMessage("MAIN_TAB_RIGHTS"), "ICON" => "form_settings", "TITLE" => GetMessage("MAIN_TAB_TITLE_RIGHTS")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>
<?
$tabControl->Begin();
?><form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>"><?=bitrix_sessid_post()?><?
$tabControl->BeginNextTab();
?>
	<?
	if (is_array($arAllOptions)):
	foreach($arAllOptions as $Option):
		$val = COption::GetOptionString($module_id, $Option[0]);
		$type = $Option[2];
	?>
	<tr>
		<td valign="top"><?	if($type[0]=="checkbox")
							echo "<label for=\"".htmlspecialchars($Option[0])."\">".$Option[1]."</label>";
						else
							echo $Option[1];?>
		</td>
		<td valign="top" nowrap><?
			if($type[0]=="checkbox"):
				?><input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>" id="<?echo htmlspecialchars($Option[0])?>" value="Y"<?if($val=="Y")echo" checked";?>><?
			elseif($type[0]=="text"):
				?><input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($Option[0])?>"><?
			elseif($type[0]=="textarea"):
				?><textarea rows="<?echo $type[1]?>" cols="<?echo $type[2]?>" name="<?echo htmlspecialchars($Option[0])?>"><?echo htmlspecialchars($val)?></textarea><?
			elseif($type[0]=="checkbox_group"):
				$aSelected = explode(',',$val);
				?>
				<?foreach( $type[1] as $iKey => $sType):?>
					<input type="checkbox" name="<?echo htmlspecialchars($Option[0])?>[]" id="<?echo htmlspecialchars($Option[0])?>_<?=$iKey?>" value="<?=$sType?>"<?if(in_array($sType, $aSelected ))echo' checked="checked"';?>> <?=GetMessage('DRIVER_TITLE_'.$sType)?> <?=((isset($type[2]))?$iKey:'')?><br />
				<?endforeach;?>
			<?elseif($type[0]=="notice_group"):
				?>
				<ul>
				<?foreach( $type[1] as $sKey => $sNotice):?>
					<li><b><?=GetMessage('DRIVER_TITLE_'.$sKey)?></b><br /><?=$sNotice?></li>
				<?endforeach;?>
				</ul>
			<?endif;
			?></td>
	</tr>
	<?
	endforeach;
	endif;
	?>
<?$tabControl->BeginNextTab();?>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");?>
<?$tabControl->Buttons();?>
<script language="JavaScript">
function RestoreDefaults()
{
	if(confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?=LANGUAGE_ID?>&mid=<?echo urlencode($mid)?>";
}
</script>
<input <?if ($TAGSMANAGER_RIGHT<"W") echo "disabled" ?> type="submit" name="Update" value="<?=GetMessage("FORM_SAVE")?>">
<input type="hidden" name="Update" value="Y">
<input type="reset" name="reset" value="<?=GetMessage("FORM_RESET")?>">
<input <?if ($TAGSMANAGER_RIGHT<"W") echo "disabled" ?> type="button" title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" OnClick="RestoreDefaults();" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
<?$tabControl->End();?>
</form>
<?endif;?>
