<?php
interface ITagsManagerDriver{
				
	public function getTags( $aOptions );
	
	public function getMaterialsByTags( $aTags );
	
	public function getElementLink( $aElement );
	
	public function transformTags( $aOldTags, $aNewTags, $bSaveOldTags = false, $sOperation = false );
}
?>