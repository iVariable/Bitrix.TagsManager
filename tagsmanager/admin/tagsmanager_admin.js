function tagsManager(container, id){

	this.id = id;
	this.container = $(container);
	this.containerSelector = container;

	this.action;

	this.query;

	this.init = function(){
		
		var $container = this.container;	
		this.query = new tagsManagerQueryString( this );
		
		var manager = this;	
			
		$( '.tagsmanager_button',$container ).click(function(){return false;});
		
		$('.move_to' ,$container).click(function(){
			$('select.tagsmanager_all_tags option:selected',$container).appendTo( $('select.tagsmanager_selected_tags', $container) );
			manager.checkAction();
		})
		
		$('.move_from' ,$container).click(function(){
			$('select.tagsmanager_selected_tags option:selected',$container).appendTo( $('select.tagsmanager_all_tags', $container) );
			manager.checkAction();
		})
		
		$( '.tagsmanager_filter', $container ).keyup( function(){
			var val = $.trim( $(this).val() );
			if( val != '' ){
				$('select.tagsmanager_all_tags option', $container).hide();
				$('select.tagsmanager_all_tags option:contains('+val+')', $container).show()
			}else{
				$('select.tagsmanager_all_tags option', $container).show();
			}
		} );
		
		$( 'input:radio', $container ).change(function(){
			manager.checkAction();
		})
		
		$( 'input:text', $container ).not('.tagsmanager_filter').keyup(function(){
			manager.checkAction();
		})
		
		this.setAction('info');
		this.initCarousel();
	}
	
	this.initCarousel = function(){
		var manager = this;
		$(".tagsmanager_operations_carousel:visible", this.container ).jCarouselLite({
			btnNext: ".next",
			btnPrev: ".prev",
			vertical: true,
			visible:1,

			 btnGo:
				    [
					    this.containerSelector + " .tagsmanager_operation_info",
					    this.containerSelector + " .tagsmanager_operation_add",
					    this.containerSelector + " .tagsmanager_operation_rename",
					    this.containerSelector + " .tagsmanager_operation_implode",
					    this.containerSelector + " .tagsmanager_operation_explode",
					    this.containerSelector + " .tagsmanager_operation_remove",
				    ],
									
			circular: false,
			afterEnd: function(elem){
				manager.setAction( $(elem).attr('rel') );
			}
		});
	}
	
	this.checkAction = function(){
		return this.checkActionByName( this.action );
	}
	
	this.checkActionByName = function( action ){
		var checkResult = this.query.check( action );	
		if( checkResult.result ){
			$( '.tagsmanager_magic_string', this.container ).removeClass('tagsManager_bad_action').addClass( 'tagsManager_good_action' );
			$( 'input[name='+this.id+'_submit]', this.container).attr('disabled',false);
		}else{
			$( '.tagsmanager_magic_string', this.container ).removeClass('tagsManager_good_action').addClass( 'tagsManager_bad_action' );
			$( 'input[name='+this.id+'_submit]', this.container).attr('disabled',true);
		}
		$('.tagsmanager_magic_string',this.container).html( checkResult.verbose );	
		$('.item[rel='+this.action+'] .hint', this.container).html( checkResult.message );
	}
	
	this.setAction = function( action ){
		this.action = action;
		this.checkAction();
		$('#'+this.id+'_action').val(action);
	}
	
	this.getSelectedTags = function(){
		return $('select.tagsmanager_selected_tags option', this.container).map(
			function(){ return $(this).val() }
		);
	};
	
	this.getActionLogic = function(){
		return ( $( 'li[rel='+this.action+'] input[type=radio]:checked' ,this.container).val() == 'and' );
	}
	
	this.getAddTag = function(){
		return $( '.add_new_input' ,this.container ).val();
	}
	
	this.getRenameTag = function(){
		return $( '.rename_input' ,this.container ).val();
	}
	
	this.getExplodeTag = function(){
		return $( '.explode_input' ,this.container ).val();
	}
	
	this.getImplodeTag = function(){
		return $( '.implode_input' ,this.container ).val();
	}
}

function tagsManagerQueryString( manager ){
	
	this.manager = manager;
	
	this.operation = {
		
		checkMax: function( max ){
			var selTags = this.manager.getSelectedTags();			
			return ( selTags.length <= max );
		},
		
		checkMin: function( min ){
			var selTags = this.manager.getSelectedTags();			
			return ( selTags.length >= min );
		},
		
		info: function(){
			var result = {};
			var selTags = this.manager.getSelectedTags();
			var verbose = '<b>Получить информацию</b> о материалах <b>помеченных'+(( this.manager.getActionLogic() )?' тегами':' одним из тегов')+':</b> "'+selTags.get().join(', ')+'".';
			if( this.operation.checkMin.apply(this,[1]) ){
				result = {					
					result: true,
					message: 'Ok!',
					verbose: verbose				
				}
			}else{
				result = {					
					result: false,
					message: 'Выберите хотя бы один тег!',
					verbose: verbose				
				}
			}
			return result;
		},
		
		add_new: function(){
			var result = this.operation.info.apply(this);
			var selTags = this.manager.getSelectedTags();
			var addTags = this.manager.getAddTag();
			if( $.trim( addTags )=='' ){
				result.result = false;
				result.message = 'Введите хотя бы один новый тег!';
			};
			result.verbose = '<b>Добавить теги</b> "'+addTags+'" к материалам <b>помеченным'+(( this.manager.getActionLogic() )?' тегами':' одним из тегов')+':</b> "'+selTags.get().join(', ')+'".';
			return result;
		},
		
		remove: function(){
			var result = this.operation.info.apply(this);
			var selTags = this.manager.getSelectedTags();
			result.verbose = '<b>Удалить теги</b> "'+selTags.get().join(', ')+'" из всех материалов <b>помеченных'+(( this.manager.getActionLogic() )?' тегами':' одним из тегов')+'</b>.';
			return result;
		},
		
		rename: function(){
			var result = {};
			if( this.operation.checkMin.apply(this,[1]) && this.operation.checkMax.apply(this,[1]) ){
				result = {					
					result: true,
					message: 'Ok!'				
				}
			}else{
				result = {					
					result: false,
					message: 'Должен быть выбран один тег!'				
				}
			}			
			var selTags = this.manager.getSelectedTags();
			var renTags = this.manager.getRenameTag();
			if( $.trim( renTags )=='' ){
				result.result = false;
				result.message = 'Введите новое имя тега!';
			}else{
				var tags = renTags.split(',');
				if( tags.length != 1 ){
					result = {
						result: false,
						message: 'Тег, в который происходит переименование, должен быть один!'
					}
				}
			};
			result.verbose = '<b>Переименовать тег</b> "'+selTags.get().join( ',' )+'" в тег <b>"'+renTags+'"</b> во всех материалах.';
			return result;
		},
		
		explode: function(){
			var result = {};
			if( this.operation.checkMin.apply(this,[1]) && this.operation.checkMax.apply(this,[1]) ){
				result = {					
					result: true,
					message: 'Ok!'				
				}
			}else{
				result = {					
					result: false,
					message: 'Должен быть выбран один исходный тег!'				
				}
			}
			var selTags = this.manager.getSelectedTags();
			var expTags = this.manager.getExplodeTag();
			var tags = expTags.split(',');
			if( tags.length == 1 ){
				result = {
					result: false,
					message: 'Надо ввести минимум два тега, на которые необходимо разбить исходный!'
				}
			}
			result.verbose = '<b>Разбить тег</b> "'+selTags.get().join(', ')+'" на теги <b>"'+expTags+'"</b> во всех материалах.';
			return result;			
		},		
		
		implode: function(){
			var result = {};
			if( this.operation.checkMin.apply(this,[2]) ){
				result = {					
					result: true,
					message: 'Ok!'				
				}
			}else{
				result = {					
					result: false,
					message: 'Должно быть выбрано хотя бы два тега!'				
				}
			}
			var selTags = this.manager.getSelectedTags();
			var impTags = this.manager.getImplodeTag();
			if ($.trim(impTags) == '') {
				result.result = false;
				result.message = 'Введите новое имя тега!';
			}
			else {
				var tags = impTags.split(',');
				if (tags.length != 1) {
					result = {
						result: false,
						message: 'Тег, в который происходит объединение, должен быть один!'
					}
				}
			}
			result.verbose = '<b>Объединить теги</b> "'+selTags.get().join(', ')+'" в тег <b>"'+impTags+'"</b> во всех материалах.';
			return result;
		},
		
	}	
	
	this.check = function( actionName ){
		var result;
		if (this.operation[actionName]) {
			result = this.operation[actionName].apply( this );
		}else {
		
		}
		return result;
	}
	
}

