<?php
class CTagsManagerDriver_IBlock extends CTagsManagerDriverAbstract_AllBitrix implements ITagsManagerDriver{
	
	public function __construct( $aOptions = array(), $mParent = null ){
		parent::__construct( $aOptions, $mParent );
		if( !CModule::IncludeModule( 'iblock' ) ) throw new Exception('IBlock Module must be installed!');
	}
	
	protected function getTagsFilter(){
		return array(
			'MODULE_ID' => 'iblock'
		);
	}
	
	/**
	 * Получение элементов инфоблока по тегам
	 * @param unknown_type $aTags
	 */
	public function getMaterialsByTags( $aTags ){
		$mResult = array();
		if( !is_array( $aTags ) ) $aTags = (array)$aTags;
		
		$aFilter = $this->getTagsFilter();
		
		if( isset( $this->aOptions['FILTER'] ) ){
			$aFilter = array_merge( $this->aOptions['FILTER'], $aFilter );
		}
		
		$aTagFilter = array();
		foreach( $aTags as $sTag ){
			$aTagFilter[] = array(
				'TAGS' => '%'.$sTag.'%',
			);
		}
		
		$aFilter[] = $aTagFilter;
		
		$oElements = CIBlockElement::getList(
			array(
				'NAME' => 'ASC'
			),
			array(
				$aFilter,
			),
			false,
			false,
			array(
				'ID',
				'NAME',
				'IBLOCK_ID',
				'TAGS'
			)
		);
		while( $aElement = $oElements->Fetch() ){
			$aElemTags = array_map( 'trim', explode( ',', $aElement['TAGS'] ));
			$bGood = true;
			foreach( $aTags as $sTag ){
				$sTag = trim( $sTag );
				if( !in_array( $sTag, $aElemTags ) ) $bGood = false;
			}
			if( $bGood ){
				$aElement['FORMATTED_LINK'] = $this->getElementLink( $aElement );
				$mResult[] = $aElement;
			}
		}
		return $mResult;
	}
								 
	public function transformTags( $aOldTags, $aNewTags, $bSaveOldTags = false, $sOperation = false ){
		$aElements = $this->getMaterialsByTags( $aOldTags );
		
		$mResult = array(
			'iTotalElements' 		=> count( $aElements ),
			'iProcessedElements' 	=> 0,
			'aErrorElementID'		=> array(),
		);
		
		if( !empty($aElements) ){
			$oElement = new CIBlockElement();
			foreach( $aElements as $aElement){
				
				$aTags = explode( ',', $aElement['TAGS'] );
				
				$aNewTags = array_map( 'trim', array_unique( array_merge( $aTags, $aNewTags ) ));
				
				if( !$bSaveOldTags ){
					foreach( $aNewTags as $iKey => &$sTag ){
						$sTag = trim( $sTag );
						if( in_array( $sTag, $aOldTags ) ){
							unset( $aNewTags[ $iKey ] );
						}
					}
				}
				//var_dump($aElement['TAGS'],implode( ', ', $aNewTags ) );

				$bResult = $oElement->Update(
					$aElement['ID'],
					array(
						'TAGS' => implode( ', ', $aNewTags )
					)
				);
				if( $bResult ){
					$mResult['iProcessedElements']++;
				}else{
					$mResult['aErrorElementID'][] = array(
						'ID' 				=> $aElement['ID'],
						'IBLOCK_ID' 		=> $aElement['IBLOCK_ID'],
						'FORMATTED_LINK' 	=> $this->getElementLink( $aElement ),
					);
				}
			}
		}

		return $mResult;
	}
	
	public function getElementLink( $aElement ){
		static $aIBTypeCache = array();
		if( !isset( $aIBTypeCache[ $aElement['IBLOCK_ID'] ] ) ){
			$aIBTypeCache[ $aElement['IBLOCK_ID'] ] = CIBlock::GetByID( $aElement['IBLOCK_ID'] )->Fetch();
			$aIBTypeCache[ $aElement['IBLOCK_ID'] ] = $aIBTypeCache[ $aElement['IBLOCK_ID'] ]['IBLOCK_TYPE_ID'];
		}
		return '<a href="/bitrix/admin/iblock_element_edit.php?WF=Y&ID='.$aElement['ID'].'&type='.$aIBTypeCache[ $aElement['IBLOCK_ID'] ].'&lang=ru&IBLOCK_ID='.$aElement['IBLOCK_ID'].'&find_section_section=-1">'.@$aElement['NAME'].' </a>';
	}
	
}
?>