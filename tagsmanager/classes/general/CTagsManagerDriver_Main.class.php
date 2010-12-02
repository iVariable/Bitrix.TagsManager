<?php
class CTagsManagerDriver_Main extends CTagsManagerDriverAbstract_AllBitrix implements ITagsManagerDriver{
	
	public function __construct(){
		parent::__construct();
		throw new Exception('Not implemented Yet. Sorry ^_^');
	}
	
	protected function getTagsFilter(){
		return array(
			'MODULE_ID' => 'main'
		);
	}
	
}
?>