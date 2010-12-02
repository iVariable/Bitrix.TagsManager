<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tagsmanager/prolog.php");
require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tagsmanager/admin/main_interface.php" );

$TAGS_RIGHT = $APPLICATION->GetGroupRight("tagsmanager");
if($TAGS_RIGHT=="D") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/tagsmanager/include.php");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
IncludeModuleLangFile(__FILE__);
$err_mess = "File: ".__FILE__."<br>Line: ";

$aIgnoreDrivers = explode( ',',COption::GetOptionString('tagsmanager', 'DENIED_DRIVERS') );

$oTagsManager = new CTagsManager( $aIgnoreDrivers );
$aAvailModules = $oTagsManager->getWorkingDrivers();

$aTabs = array();
if( empty( $aAvailModules ) ){
	echo BeginNote();?>
	<?echo GetMessage("NO_WORKING_DRIVERS_FOUND")?>
	<?php
	echo EndNote();
	require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
	
}
	
$aTabs[] = array(
	'DIV' 	=> 'all_drivers',
	'TAB' 	=> GetMessage('ALL_DRIVERS_TAB'),
	'ICON' 	=> 'main_channel_edit',
	'TITLE' => GetMessage('ALL_DRIVERS_TITLE'),
	'ONSELECT' 	=> 'all_TagsManager.initCarousel()',
);

foreach( $aAvailModules as $sDriverName ){
	$aTabs[] = array(
		'DIV' 		=> $sDriverName,
		'TAB' 		=> GetMessage('DRIVER_TAB_'.$sDriverName),
		'ICON' 		=> 'main_channel_edit',
		'TITLE' 	=> GetMessage('DRIVER_TITLE_'.$sDriverName),
		'ONSELECT' 	=> $sDriverName.'_TagsManager.initCarousel()',
	);
}

$tabControl = new CAdminTabControl("tabControl", $aTabs , false);



if( isset( $_POST['tabControl_active_tab'] ) ){
	
	$sDriverName = $_POST['tabControl_active_tab'];
	$oTagsManager->setDrivers4Use( $sDriverName );
	if( $_POST['tabControl_active_tab'] == 'all_drivers' ) {
		$sDriverName = 'all';
		$oTagsManager->setDrivers4Use();
	}
	
	if( isset( $_POST[ $sDriverName.'_submit' ] ) && isset( $_POST[ $sDriverName.'_action' ] )){
		
		$aOldTags = $_POST[ $sDriverName.'_tags_selected' ];
		$aNewTags = $_POST[ $sDriverName.'_'.$_POST[ $sDriverName.'_action' ].'_tagsmanager' ];
		$sLogic = 'and';
		if( isset( $_POST[ $sDriverName.'_'.$_POST[ $sDriverName.'_action' ].'_tagsmanager_logic' ] ) ) $sLogic = $_POST[ $sDriverName.'_'.$_POST[ $sDriverName.'_action' ].'_tagsmanager_logic' ];
		
		$aOldTagsRepeater = array( $aOldTags );
		if( $sLogic == 'or' ){
			$aOldTagsRepeater = $aOldTags;
		}
		$bResult = true;
		$mResult = array();
		foreach( $aOldTagsRepeater as $aOldTags ){
			try{
				switch( $_POST[ $sDriverName.'_action' ] ){
					case 'info':
						$mTResult = $oTagsManager->getMaterialsByTags( $aOldTags );
						break;
						
					case 'add_new':
						if( $TAGS_RIGHT< 'W' ) throw new Exception( GetMessage('TAGSMANAGER_OPERATION_DENIED') );
						$mTResult = $oTagsManager->addTags( $aOldTags, $aNewTags );
						break;
						
					case 'explode':
						if( $TAGS_RIGHT< 'W' ) throw new Exception( GetMessage('TAGSMANAGER_OPERATION_DENIED') );
						$mTResult = $oTagsManager->explodeTags( $aOldTags, $aNewTags );
						break;
						
					case 'implode':
						if( $TAGS_RIGHT< 'W' ) throw new Exception( GetMessage('TAGSMANAGER_OPERATION_DENIED') );
						$mTResult = $oTagsManager->implodeTags( $aOldTags, $aNewTags );
						break;
						
					case 'rename':
						if( $TAGS_RIGHT< 'W' ) throw new Exception( GetMessage('TAGSMANAGER_OPERATION_DENIED') );
						$mTResult = $oTagsManager->implodeTags( $aOldTags, $aNewTags );
						
						break;
						
					case 'remove':
						if( $TAGS_RIGHT< 'W' ) throw new Exception( GetMessage('TAGSMANAGER_OPERATION_DENIED') );
						$mTResult = $oTagsManager->removeTags( $aOldTags );
						break;
						
					default:
						CAdminMessage::ShowMessage( 'Unknown action:'.$_POST[ $sDriverName.'_action' ] );
						break;
				}
				foreach( $mTResult as $sDriverType=>$aTResult ){
					if( isset( $aTResult['iTotalElements'] ) ){
						if( !isset($mResult[$sDriverType]) ){
							$mResult[$sDriverType] = array(
								'iTotalElements' 		=> 0,
								'iProcessedElements' 	=> 0,
								'aErrorElementID'		=> array(),
							);
						}
						
						$mResult[$sDriverType]['iTotalElements'] 		+= $aTResult['iTotalElements'];
						$mResult[$sDriverType]['iProcessedElements'] 	+= $aTResult['iProcessedElements'];
						$mResult[$sDriverType]['aErrorElementID']		 = array_merge( $mResult[$sDriverType]['aErrorElementID'], $aTResult['aErrorElementID'] );
					}else{
						if( isset( $mResult[$sDriverType] ) ){
							$mResult[$sDriverType] = array_merge( $mResult[$sDriverType], $aTResult );
						}else{
							$mResult[$sDriverType] = $aTResult;
						};
					}
				}
			}catch( Exception $e ){
				CAdminMessage::ShowMessage( $e->getMessage() );
				$bResult = false;
			}
		}
	}else{
		CAdminMessage::ShowMessage( 'Unknown action!' );
	}
	
	if( isset( $mResult['aErrorElementID'] ) && !empty( $mResult['aErrorElementID'] ) ){
		$bResult = false;
	}

	if( !$bResult ){
		CAdminMessage::ShowMessage( GetMessage('TAGSMANAGER_SHIT_HAPPENED') );
	}else{
		CAdminMessage::ShowNote( GetMessage('TAGSMANAGER_YEAAAH!!1') );
	}

	foreach( $mResult as $sDriverName => $aResult ){
		echo BeginNote();
		echo '<h3>'.GetMessage('DRIVER_TITLE_'.$sDriverName).'</h3>';
		echo '<ul>';
		if( isset( $aResult['iTotalElements'] ) ){
			$sError = '';
			if( !empty( $aResult['aErrorElementID'] ) ){
				$sError = '<li>'.GetMessage('TAGSMANAGER_ERROR_ELEMENTS').'<br />';
				foreach( $aResult['aErrorElementID'] as $aElem ){
					$sError .= $aElem['FORMATTED_LINK'].'<br />';
				}
				$sError .= '</li>';
			};
			echo '
				<li>'.GetMessage('TAGSMANAGER_TOTAL_ELEMENTS').': '.$aResult['iTotalElements'].'</li>
				<li>'.GetMessage('TAGSMANAGER_PROCESSED_ELEMENTS').': '.$aResult['iProcessedElements'].'</li>
				'.(( !empty( $sError ) )?$sError:'');
		}else{
			foreach( $aResult as $aTResult ){
				echo '<li>'.$aTResult['FORMATTED_LINK'].'</li>';
			}
		};
		echo '</ul>';
		echo EndNote();
	}
	
}

if( isset( $_GET['actionType'] ) ){
	switch( $_GET['actionType'] ){
		case 'add_new':
		case 'remove':
		case 'explode':
		case 'implode':
		case 'rename':
		case 'info':
			$sActionType = $_GET['actionType'];
			break;
			
		default:
			$sActionType = 'all';
			break;
	}
	$_SESSION['actionType'] = $sActionType;
}

if( !isset($_SESSION['actionType'] ) ){
	$_SESSION['actionType'] = 'all';
}

$sActionType = $_SESSION['actionType'];


$oTagsManager->setDrivers4Use();
$aAvailTags = $oTagsManager->getTags();

?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="/bitrix/modules/tagsmanager/admin/jc1.0.1.js"></script>
<script type="text/javascript" src="/bitrix/modules/tagsmanager/admin/tagsmanager_admin.js"></script>
<link href="/bitrix/modules/tagsmanager/admin/tagsmanager_admin.css" type="text/css" rel="stylesheet" />

<form method="POST" action="<?=$APPLICATION->GetCurPage()?>" name="post_form">
<?=bitrix_sessid_post()?>
<?
$tabControl->Begin();

$tabControl->BeginNextTab();

tagsManager_drawMainInterface( 'all',$aAvailTags, true, $sActionType );

foreach( $aAvailModules as $sDriverName ){
	$tabControl->BeginNextTab();
	tagsManager_drawMainInterface( $sDriverName, $aAvailTags[ $sDriverName ], false, $sActionType );
}

$tabControl->EndTab();
$tabControl->End();
?>

</form>
<?
$tabControl->ShowWarnings("post_form", $message);
?>
<script type="text/javascript">
	$('form[name=post_form]').submit(function(){
		$('.tagsmanager_selected_tags option').attr('selected',true);
	});
</script>
<?echo BeginNote();?>
<span class="required">*</span> - <?echo GetMessage("REQUIRED_FIELDS")?>
<?echo EndNote();?>
<?
require_once ($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>