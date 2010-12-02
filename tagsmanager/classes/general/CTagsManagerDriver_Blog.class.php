<?php
class CTagsManagerDriver_Blog extends CTagsManagerDriverAbstract_AllBitrix implements ITagsManagerDriver{
	
	public function __construct(){
		parent::__construct();
		throw new Exception('Not implemented Yet. Sorry ^_^');
		//if( !CModule::IncludeModule( 'blog' ) ) throw new Exception('Blog Module must be installed!');
	}
	
	protected function getTagsFilter(){
		return array(
			'MODULE_ID' => 'blog'
		);
	}
	
}
?>