var customTaxonomy;

(function($) {
	var $document = $( document );

	customTaxonomy = {
			idTaxonomyMetabox: "",
			modelTaxonomysTab:[],
			metaboxSufixId:"",
			init: function(idTaxonomyMetabox,modelTaxonomysTab,metaboxSufixId){
				console.log(modelTaxonomysTab);				
				this.idTaxonomyMetabox = idTaxonomyMetabox;
				this.modelTaxonomysTab = modelTaxonomysTab;
				this.metaboxSufixId = metaboxSufixId?metaboxSufixId:this.metaboxSufixId;
				
				this.rerenderMetaboxesInsideOfTaxonomiesMetabox();
				
				this.tax_metabox_resize_heights();
				this.mapEvents()
			},
			rerenderMetaboxesInsideOfTaxonomiesMetabox: function(){
				$('[data-pull]').each( function( ){

					var space = $(this),
						panel = $( '#' + space.data('pull') );
						

					panel.appendTo( space );
					panel.find("h2.hndle").removeClass("hndle ui-sortable-handle");
					panel.find("button.handlediv").hide();
				});
			},
			tax_metabox_resize_heights: function(){
				var box = $('#'+this.idTaxonomyMetabox),
					inside = box.find('.inside'),
					tabs = box.find('span.taxonomy-metabox-wrapper');

				inside.css( { minHeight : tabs.outerHeight() } );
			},
			selectTabId: function selectTabId(idTab){
				for(var i=0;i<this.modelTaxonomysTab.length;i++){
					var tab = this.modelTaxonomysTab[i];
					tab.selected = (tab.idTab == idTab);
				}
				this.setup_visible_metabox(false);
			},
			setup_visible_metabox: function(verifyIfIsShowed){				
				$(".taxonomy-metabox-tab li").removeClass('active')
						
				for(var i=0;i<this.modelTaxonomysTab.length;i++){
					var tab = this.modelTaxonomysTab[i];
					var tabObject = jQuery('#'+tab.idTab);
					var contentObject = jQuery('#'+tab.id);
					if(verifyIfIsShowed){
	    				if(tab.show){
	    					tabObject.show();
	    					contentObject.show();    										
	    				}
	    				else{				
	    					tabObject.hide();
	    					contentObject.hide();
	    				}
					}
					if(tab.selected){
						tabObject.addClass('active')    						
						contentObject.css('position','');
						contentObject.css('visibility','');
					}
					else{    						
						contentObject.css('position','absolute');
						contentObject.css('visibility','hidden');
					}
					
				}
				if(verifyIfIsShowed){
					this.tax_metabox_resize_heights()
				}
			},
			onClickPostboxes : function() {
				var $el = $(this);
				var boxId = $el.val();
				var tax_slug = boxId.substr(0, boxId.lastIndexOf(customTaxonomy.metaboxSufixId));	
				var taxonomies_tab = customTaxonomy.modelTaxonomysTab;

				for(var i=0;i<taxonomies_tab.length;i++){
					var tab = taxonomies_tab[i];
					if(tax_slug == tab.taxonomySlug){
						if(tab.show){
							tab.show = false;
							if(tab.selected){							
								tab.selected = false;
								var newSelected = customTaxonomy.getNextOndeSelected();
								if(newSelected == null){
									alert("NO ONE");								
								}
								else{
									newSelected.selected = true;
								}								
							}						
						}
						else{
							tab.show = true;
							if(!customTaxonomy.isThereAnyOneSelected()){
								tab.selected = true;
							}
						}
						break;
					}
				}
				customTaxonomy.setup_visible_metabox(true);
				setTimeout(()=>postboxes.save_state('<?php echo get_current_screen()->id;?>'), 0);
				
			},
			getNextOndeSelected:function(){
				for(var i=0;i<this.modelTaxonomysTab.length;i++){
					var tab = this.modelTaxonomysTab[i];
					if(tab.show){
						return tab;
					}
				}
				return null;
			},
			isThereAnyOneSelected: function (){
				for(var i=0;i<this.modelTaxonomysTab.length;i++){
					var tab = this.modelTaxonomysTab[i];
					if(tab.selected){
						return true;
					}
				}
				return false;
			},
			mapEvents : function(){
				$(window).on('resize', function(){
					customTaxonomy.tax_metabox_resize_heights()
				});
				$(document).on('click', '.taxonomy-metabox-tab > li > a', function(e){				
					e.preventDefault();
					var clicked = $(this);
					
					customTaxonomy.selectTabId(clicked.parent().prop('id'));								
				});
				$('.hide-postbox-tog').on('click.postboxes',this.onClickPostboxes);
			}
	}
}(jQuery))