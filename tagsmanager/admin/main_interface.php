<?php
if( !function_exists( 'tagsmanager_drawMainInterface' ) ){
	function tagsManager_drawMainInterface( $sID, $aData, $bUseDataSections = false, $sActionType = 'all' ){
		global $APPLICATION;
		?>
<tr class="tagsmanager_container tagsmanager_container_<?=$sID?>">
	<td>&nbsp;</td>
	<td class="tagsmanager_tags">
		<input type="text" class="tagsmanager_filter" /><br />
		<select name="<?=$sID?>_tags" class="tagsmanager_all_tags" multiple="multiple">
		<?if( $bUseDataSections ):?>
		<?foreach( $aData as $sDriverName => $aTags ):?>
			<optgroup label="<?=GetMessage('DRIVER_TAB_'.$sDriverName)?>">
				<?foreach( $aTags as $aTag ):?>
					<option value="<?=$aTag['NAME']?>"><?=$aTag['NAME']?> (<?=$aTag['CNT']?>)</option>
				<?endforeach;?>
			</optgroup>
		<?endforeach;?>
		<?else:?>
			<?foreach( $aData as $aTag ):?>
				<option value="<?=$aTag['NAME']?>"><?=$aTag['NAME']?> (<?=$aTag['CNT']?>)</option>
			<?endforeach;?>
		<?endif;?>
		</select>
		<input type="hidden" id="<?=$sID?>_action" name="<?=$sID?>_action" value="info"/>
	</td>
	<td class="tagsmanager_operation">
		<input title="Выбрать теги" alt="Выбрать теги" type="image" src="/bitrix/images/tagsmanager/arrow_right.png" class="tagsmanager_button move_to" /><br /><br />
		<input title="Убрать теги" alt="Убрать теги" type="image" src="/bitrix/images/tagsmanager/arrow_left.png" class="tagsmanager_button move_from" /><br /><br />
		<hr />
		<br /><br /><br />
		<?if( $sActionType == 'all' ):?>
		<input title="Информация" alt="Информация" type="image" src="/bitrix/images/tagsmanager/magnifier.png" class="tagsmanager_button tagsmanager_operation_info" /><br /><br />
			<?if( $APPLICATION->GetGroupRight("tagsmanager")>='W' ):?>
			<input title="Добавить тег" alt="Добавить тег" type="image" src="/bitrix/images/tagsmanager/tag_blue_add.png" class="tagsmanager_button tagsmanager_operation_add" /><br /><br />
			<input title="Переименовать тег" alt="Переименовать тег" type="image" src="/bitrix/images/tagsmanager/tag_blue_edit.png" class="tagsmanager_button tagsmanager_operation_rename" /><br /><br />
			<input title="Слить теги" alt="Слить теги" type="image" src="/bitrix/images/tagsmanager/arrow_join.png" class="tagsmanager_button tagsmanager_operation_implode" /><br /><br />
			<input title="Разбить теги" alt="Разбить теги" type="image" src="/bitrix/images/tagsmanager/arrow_divide.png" class="tagsmanager_button tagsmanager_operation_explode" /><br /><br />
			<input title="Удалить тег" alt="Удалить тег" type="image" src="/bitrix/images/tagsmanager/tag_blue_delete.png" class="tagsmanager_button tagsmanager_operation_remove" />
			<?endif;?>
		<?endif;?>
	</td>
	<td class="tagsmanager_results">
		<select name="<?=$sID?>_tags_selected[]" class="tagsmanager_selected_tags" multiple="multiple">
		</select>
		<div class="tagsmanager_operations_carousel">
			<ul class="carousel">
				<?if( ( $sActionType == 'all') || ( $sActionType == 'info' ) ):?>
				<li class="item" rel="info">
					<input type="radio" name="<?=$sID?>_info_tagsmanager_logic" value="and" checked="checked"> все теги / <input type="radio" name="<?=$sID?>_info_tagsmanager_logic" value="or"> хоть один <br />
					<input type="submit" name="<?=$sID?>_submit"  class="tagsmanager_getinfo" value="Получить протегированные материалы" /><br />
					<?echo BeginNote();?>
					<div class="hint"></div>
					<?echo EndNote();?>
				</li>
				<?endif;?>
				<?if( ( $sActionType == 'all') || ( $sActionType == 'add_new' ) ):?>
				<li class="item" rel="add_new">
					<label for="<?=$sID?>_add_new_tagsmanager">Добавить новый тег: <input type="text" class="add_new_input" name="<?=$sID?>_add_new_tagsmanager" id="<?=$sID?>_add_new_tagsmanager" /></label><br />
					<input type="radio" name="<?=$sID?>_add_new_tagsmanager_logic" value="and" checked="checked"> все теги / <input type="radio" name="<?=$sID?>_add_new_tagsmanager_logic" value="or"> хоть один <br />
					<input type="submit" name="<?=$sID?>_submit" class="remove_input" value="Добавить новый тег" />
					<?echo BeginNote();?>
					<div class="hint"></div>
					<?echo EndNote();?>
				</li>
				<?endif;?>
				<?if( $APPLICATION->GetGroupRight("tagsmanager")>='W' ):?>
					<?if( ( $sActionType == 'all') || ( $sActionType == 'rename' ) ):?>
					<li class="item" rel="rename">
						<label for="<?=$sID?>_rename_tagsmanager">Новое имя тега: <input type="text" class="rename_input" name="<?=$sID?>_rename_tagsmanager" id="<?=$sID?>_rename_tagsmanager" /></label><br />
						<input type="submit" name="<?=$sID?>_submit" class="remove_input" value="Переименовать" />
						<?echo BeginNote();?>
						<div class="hint"></div>
						<?echo EndNote();?>
					</li>
					<?endif;?>
					<?if( ( $sActionType == 'all') || ( $sActionType == 'implode' ) ):?>
					<li class="item" rel="implode">
						<label for="<?=$sID?>_implode_tagsmanager">Объединить в тег: <input type="text" class="implode_input" name="<?=$sID?>_implode_tagsmanager" id="<?=$sID?>_implode_tagsmanager" /></label><br />
						<input type="submit" name="<?=$sID?>_submit" class="remove_input" value="Объединить" />
						<?echo BeginNote();?>
						<div class="hint"></div>
						<?echo EndNote();?>
					</li>
					<?endif;?>
					<?if( ( $sActionType == 'all') || ( $sActionType == 'explode' ) ):?>
					<li class="item" rel="explode">
						<label for="<?=$sID?>_explode_tagsmanager">Разбить на теги: <input type="text" class="explode_input" name="<?=$sID?>_explode_tagsmanager" id="<?=$sID?>_explode_tagsmanager" /></label><br />
						<input type="submit" name="<?=$sID?>_submit" class="remove_input" value="Разбить" />
						<?echo BeginNote();?>
						<div class="hint"></div>
						<?echo EndNote();?>
					</li>
					<?endif;?>
					<?if( ( $sActionType == 'all') || ( $sActionType == 'remove' ) ):?>
					<li class="item" rel="remove">
						<input type="radio" name="<?=$sID?>_remove_tagsmanager_logic" value="and" checked="checked"> все теги / <input type="radio" name="<?=$sID?>_remove_tagsmanager_logic" value="or"> хоть один <br />
						<input type="submit" name="<?=$sID?>_submit" class="remove_input" value="Удалить" />
						<?echo BeginNote();?>
						<div class="hint"></div>
						<?echo EndNote();?>
					</li>
					<?endif;?>
				<?endif;?>
			</ul>
		</div>
		<div class="tagsmanager_magic_string"></div>
		<script type="text/javascript">
			$(function(){
				<?=$sID?>_TagsManager = new tagsManager( '.tagsmanager_container_<?=$sID?>', '<?=$sID?>' );
				<?=$sID?>_TagsManager.init();
				<?if( ( $sActionType != 'all')):?><?=$sID?>_TagsManager.setAction('<?=$sActionType?>');<?endif;?>
			})
		</script>
	</td>
	<td>&nbsp;</td>
</tr>
		<?php
	}
}
?>