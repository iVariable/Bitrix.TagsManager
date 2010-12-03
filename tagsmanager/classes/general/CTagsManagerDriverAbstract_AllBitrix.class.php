<?php
abstract class CTagsManagerDriverAbstract_AllBitrix implements ITagsManagerDriver{
	
	abstract protected function getTagsFilter();
	
	protected $aOptions;
	protected $mParent;
	
	public function __construct( $aOptions = array(), $mParent = null){
		$this->aOptions = $aOptions;
		$this->mParent 	= $mParent;
		if( !CModule::IncludeModule( 'search' ) ) throw new Exception('Search Module must be installed!');
	}
	
	public function getElementLink( $aElement ){
		throw new Exception( __METHOD__.' must be overriden in the descendant class!' );
	}
	
	public function getTags( $aOptions ){
		$mResult = array();
		
		$aFilter = $this->getTagsFilter();
		
		if( isset( $this->aOptions['FILTER'] ) ){
			$aFilter = array_merge( $this->aOptions['FILTER'], $aFilter );
		}
		
		if( isset( $aOptions['FILTER'] ) ){
			$aFilter = array_merge( $aOptions['FILTER'], $aFilter );
		}
		
		$rsTags = CSearchTags::GetList(
			array(),
			$aFilter,
			array(),
			false
		);
		while( $arTag = $rsTags->Fetch() ){
			$mResult[] = $arTag;
		}
		return $mResult;
	}
	
	public function getMaterialsByTags( $aTags ){
		throw new Exception( __METHOD__.' must be overriden in the descendant class!' );
	}
	
	public function transformTags( $aOldTags, $aNewTags, $bSaveOldTags = false, $sOperation = false ){
		throw new Exception( __METHOD__.' must be overriden in the descendant class!' );
	}
	
}

?>