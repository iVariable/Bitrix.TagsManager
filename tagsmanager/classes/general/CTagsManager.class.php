<?php
class CTagsManager{
	
	/**
	 * Получение доступных драйверов
	 */
	public static function getAvailableDrivers(){
		$aDrivers = glob( dirname(__FILE__).'/CTagsManagerDriver_*.class.php' );
		$mReturn = array(
			'ALL_DRIVERS'		=> array(),
			'WORKING_DRIVERS'	=> array(),
		);
		if( !empty( $aDrivers ) ){
			foreach( $aDrivers as $sDriverName ){
				$sDriverName = substr( str_replace( '.class.php', '', basename( $sDriverName )), 19 ); // 19 - длина фрагмента CTagsManagerDriver_
				$mReturn['ALL_DRIVERS'][$sDriverName] = $sDriverName;
				try{
					self::loadDriver( $sDriverName );
					$mReturn['WORKING_DRIVERS'][ $sDriverName ] = $sDriverName;
				}catch( Exception $e ){
					$mReturn['DRIVER_LOADING_ERROR'][$sDriverName] = $e->getMessage();
					//Loading failed. Ignoring.
				}
			}
		}
		return $mReturn;
	}
	
	protected $aDrivers 		= array();
	protected $aUsableDrivers 	= array();
	protected $aIgnoreDrivers	= array();
	
	public function __construct( $aIgnoreDrivers = array(), $aOptions = array(), $aTagDrivers = array() ){
		if( empty( $aTagDrivers ) ){
			$aTagDrivers = self::getAvailableDrivers();
			$aTagDrivers = $aTagDrivers[ 'WORKING_DRIVERS' ];
		}
		
		foreach( $aTagDrivers as $sDriverName ){
			if( isset( $aIgnoreDrivers[ $sDriverName ] ) || in_array( $sDriverName , $aIgnoreDrivers ) ) continue;
			$this->aDrivers[ $sDriverName ] = self::loadDriver( $sDriverName, $aOptions );
		}
		
	}
	
	/**
	 * Установка используемых драйверов
	 * @param unknown_type $aDrivers
	 */
	public function setDrivers4Use( $aDrivers = array() ){
		if( !is_array( $aDrivers ) ) $aDrivers = (array)$aDrivers;
		$aExistingDrivers = $this->getWorkingDrivers();
		if( $aDrivers == array() ) $aDrivers = $aExistingDrivers;
		return $this->aUsableDrivers = array_intersect( $aExistingDrivers, $aDrivers );
	}
	
	public function getDrivers4Use(){
		return $this->aUsableDrivers;
	}
	
	public function getWorkingDrivers(){
		return array_keys( $this->aDrivers );
	}
		
	/**
	 * Получение списка тегов, зарегистрированных в системе
	 */
	public function getTags( $aOptions = array() ){
		$mResult = $this->proxy2Drivers(
			'getTags',
			array(
				$aOptions
			)
		);
		return $mResult;
	}
	
	/**
	 * Получение элементов инфоблока по тегам
	 */
	public function getMaterialsByTags( $aTags ){
		$mResult = $this->proxy2Drivers(
			'getMaterialsByTags',
			array(
				$aTags
			)
		);
		return $mResult;
	}
	
	
	public function addTags( $aOldTags, $aNewTags ){
		return $this->transformTags( $aOldTags, $aNewTags, true, 'addTags' );
	}
	
	public function renameTag( $aOldTags, $aNewTags ){
		return $this->transformTags( $aOldTags, $aNewTags, false, 'renameTag' );
	}
	
	public function implodeTags( $aOldTags, $aNewTags ){
		return $this->transformTags( $aOldTags, $aNewTags, false, 'implodeTags' );
	}
	
	public function explodeTags( $aOldTags, $aNewTags ){
		return $this->transformTags( $aOldTags, $aNewTags, false, 'explodeTags' );
	}
	
	public function removeTags( $aOldTags ){
		return $this->transformTags( $aOldTags, array(), false, 'removeTags' );
	}
	
	/**
	 * Трансформация тегов
	 * @param unknown_type $sOldTag
	 * @param unknown_type $aNewTags
	 * @param unknown_type $bPreserveOldTag
	 */
	public function transformTags( $aOldTags, $aNewTags, $bPreserveOldTag = false, $sOperation = false ){
		if( !is_array( $aOldTags ) ) $aOldTags = (array)$aOldTags;
		if( !is_array( $aNewTags ) ) $aNewTags = (array)$aNewTags;
		$mResult = $this->proxy2Drivers(
			'transformTags',
			array(
				$aOldTags,
				$aNewTags,
				$bPreserveOldTag,
				$sOperation
			)
		);
		return $mResult;
	}
	
	//==================PROTECTED===============//
	
	/**
	 * Подгрузка драйвера
	 * @param unknown_type $sName
	 */
	protected static function loadDriver( $sName, $aOptions = array() ){
		$mResult = null;
		$sDriverFileName = dirname( __FILE__ ).'/CTagsManagerDriver_'.$sName.'.class.php';
		$sDriverName = 'CTagsManagerDriver_'.$sName;
		if( is_readable( $sDriverFileName ) ){
			require_once $sDriverFileName;
			if( class_exists( $sDriverName ) ){
				$mResult = new $sDriverName( $aOptions, $this );
				if( !is_a( $mResult, 'ITagsManagerDriver' ) ){
					$mResult = null;
				}
			}
		}
		
		if( $mResult === null ){
			throw new Exception( 'Fatal Error: Can\'t load TagsManagerDriver - '.$sName );
		}
		
		return $mResult;
	}
	
	/**
	 * Проксирование вызова драйверам
	 * @param unknown_type $sActionName
	 * @param unknown_type $aArguments
	 * @throws Exception
	 */
	protected function proxy2Drivers( $sActionName, $aArguments = array() ){
		$mResult = array();
		$aErrors = array();
		foreach( $this->aUsableDrivers as $sDriverName ){
			$oDriver = $this->aDrivers[ $sDriverName ];
			try{
				$mResult[$sDriverName] = call_user_func_array( array( $oDriver, $sActionName ), $aArguments );
			}catch( Exception $oDriverException ){
				$aErrors[$sDriverName] = $sDriverName.': '.$oDriverException->getMessage();
			}
		}
		if( !empty( $aErrors ) ){
			throw new Exception( implode( PHP_EOL, $aErrors ) );
		}
		return $mResult;
	}
	
}
?>