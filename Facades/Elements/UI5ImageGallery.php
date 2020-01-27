<?php
namespace exface\UI5Facade\Facades\Elements;


use exface\Core\DataTypes\UrlDataType;
use exface\Core\Facades\AbstractAjaxFacade\Elements\JquerySlickGalleryTrait;
use exface\Core\Facades\AbstractAjaxFacade\Elements\JqueryToolbarsTrait;
use exface\Core\Widgets\Imagegallery;
use exface\UI5Facade\Facades\Elements\Traits\UI5DataElementTrait;
use exface\UI5Facade\Facades\Interfaces\UI5ControllerInterface;
use exface\Core\Interfaces\Widgets\iShowData;

/**
 * Creates a UI5 panel with a slick image slider for a DataimageGallery widget.
 * 
 * @author Andrej Kabachnik
 * 
 * @method Imagegallery getWidget()
 *        
 */
class UI5ImageGallery extends UI5AbstractElement
{   
    use UI5DataElementTrait {
        buildJsDataLoaderOnLoaded as buildJsDataOnLoadedViaTrait;  
    }
    use JquerySlickGalleryTrait;
    use JqueryToolbarsTrait;
    
    public function registerExternalModules(UI5ControllerInterface $controller) : UI5AbstractElement
    {
        $f = $this->getFacade();
        $controller->addExternalModule('libs.exface.slick.Slick', $f->buildUrlToSource('LIBS.SLICK.SLICK_JS'), null, 'slick');
        $controller->addExternalCss($this->getFacade()->buildUrlToSource('LIBS.SLICK.SLICK_CSS'));
        

        return $this;
    }
            
    public function buildJsConstructorForControl($oControllerJs = 'oController') : string
    {

        $this->registerExternalModules($this->getController());
        $this->setSlickGalleryId($this->getId() . "_SlickGallery");
        
        $html = <<<HTML
     <div class="exf-grid-item exf-imagecarousel" style="width:{$this->getWidth()};height:{$this->getHeight()};box-sizing:border-box;">
        {$this->buildHtmlCarousel()}
    </div>
HTML;
        
        $html = str_replace("\"", "\\\"", $html);
        $html = str_replace("\n", "", $html);
        
        return <<<JS
         new sap.ui.core.HTML("{$this->getId()}", {
            width: "100%",
            height: "100%",
            afterRendering: function(oEvent) {
                {$this->buildJsCarouselFunctions()}
                {$this->buildJsCarouselInit()}
            }
         }).setContent("$html")
JS;
        
//         $widget = $this->getWidget();
//         // Add Scripts for the configurator widget first as they may be needed for the others
//         $configurator_element = $this->getFacade()->getElement($widget->getConfiguratorWidget());
//         $output .= $configurator_element->buildJs();
        
//         // Add scripts for the buttons
//         $output .= $this->buildJsButtons();
        
//         $output .= <<<JS
        
//                     $('#{$configurator_element->getId()}').find('.grid').on( 'layoutComplete', function( event, items ) {
//                         setTimeout(function(){
//                             var newHeight = $('#{$this->getId()}_wrapper > .panel').height();
//                             $('#{$this->getId()}').height($('#{$this->getId()}').parent().height()-newHeight);
//                         }, 0);
//                     });
                    
// JS;
//         return $output . <<<JS
        
//                 {$this->buildJsCarouselFunctions()}
//                 {$this->buildJsCarouselInit()}

// JS;
    }
    
    public function buildJsDataSource() : string
    {
        $widget = $this->getWidget();
        
        if (($urlType = $widget->getImageUrlColumn()->getDataType()) && $urlType instanceof UrlDataType) {
            $base = $urlType->getBaseUrl();
        }
        
       
        
        return <<<JS
        
    // Don't load if already loading
    if ($('#{$this->getSlickGalleryId()}').data('_loading')) return;
    
	{$this->buildJsBusyIconShow()}
	
    $('#{$this->getSlickGalleryId()}').data('_loading', 1);
    
	var param = {
       action: '{$widget->getLazyLoadingActionAlias()}',
	   resource: "{$widget->getPage()->getAliasWithNamespace()}",
	   element: "{$widget->getId()}",
	   object: "{$widget->getMetaObject()->getId()}"
    };
    
    var checkOnBeforeLoad = function(param){
        {$this->buildJsOnBeforeLoadScript('param')}
        {$this->buildJsOnBeforeLoadAddConfiguratorData('param')}
    }(param);
    
    if (checkOnBeforeLoad === false) {
        {$this->buildJsBusyIconHide()}
        return;
    }

    {$this->buildJsBusyIconHide()}
JS;
    
// 	$.ajax({
//        url: "{$this->getAjaxUrl()}",
//        data: param,
//        method: 'POST',
//        success: function(json){
// 			try {
// 				var data = json.rows;
//                 var carousel = $('#{$this->getSlickGalleryId()}');
//                 var src = '';
//                 var title = '';
// 				for (var i in data) {
//                     src = '{$base}' + data[i]['{$widget->getImageUrlColumn()->getDataColumnName()}'];
//                     title = data[i]['{$widget->getImageTitleColumn()->getDataColumnName()}'];
//                     carousel.slick('slickAdd', '<div class="imagecarousel-item"><img src="' + src + '" title="' + title + '" alt="' + title + '" /></div>');
//                 }
// 		        {$this->buildJsBusyIconHide()}
// 		        $('#{$this->getSlickGalleryId()}').data('_loading', 0);
// 			} catch (err) {
//                 console.error(err);
// 				{$this->buildJsBusyIconHide()}
// 			}
// 		},
// 		error: function(jqXHR, textStatus,errorThrown){
// 		   {$this->buildJsBusyIconHide()}
// 		   {$this->buildJsShowError('jqXHR.responseText', 'jqXHR.status + " " + jqXHR.statusText')}
// 		}
// 	});
	
// JS;
    }

    
    /**
     * Returns a JS snippet, that empties the table (removes all rows).
     *
     * @return string
     */
    protected function buildJsDataResetter() : string
    {
        return <<<JS
        
           $('#{$this->getSlickGalleryId()} .slick-track').empty();
           
JS;
    }

    protected function buildJsOnBeforeLoadScript($js_var_param = 'param')
    {
        // Abort loading if _skipNextLoad is set - don't forget to trigger
        // resize, just as a regular load would do. Otherwise the table would
        // not fit exaclty in containers like splits.
        return <<<JS
                    // Abort immediately if next loading should be skipped
                    var jqself = $(this);
                    if (jqself.data("_skipNextLoad") == true) {
    					jqself.data("_skipNextLoad", false);
                        jqself.trigger('resize');
    					return false;
    				}
    				
                    // Scripts added programmatically
				    {$this->on_before_load}
				    
JS;
    }
    
    protected function buildJsOnBeforeLoadAddConfiguratorData(string $paramJs = 'param') : string
    {
        
        $configurator_element = $this->getFacade()->getElement($this->getWidget()->getConfiguratorWidget());
        
        $this->setElementType('datagrid');
        
        return <<<JS
        
                try {
                    if (! {$configurator_element->buildJsValidator()}) {
                        {$this->buildJsDataResetter()}
                        return false;
                    }
                } catch (e) {
                    console.warn('Could not check filter validity - ', e);
                }
                {$paramJs}['data'] = {$configurator_element->buildJsDataGetter(null, true)};
                    
                    // Enrich sorting options
                    if ({$paramJs}.sort !== undefined) {
                        var sortNames = {$paramJs}.sort.split(',');
                        var sortAttrs = [];
                        for (var i=0; i<sortNames.length; i++) {
                            colOpts = jqself.{$this->getElementType()}('getColumnOption', sortNames[i]);
                            sortAttrs.push(colOpts !== null ? colOpts['_attributeAlias'] : sortNames[i]);
                        }
                        {$paramJs}.sortAttr = sortAttrs.join(',');
                    }
                    
JS;

    }
    
    protected function buildJsDataLoaderOnLoaded(string $oModelJs = 'oModel') : string
    {
        $widget = $this->getWidget();
        
        if (($urlType = $widget->getImageUrlColumn()->getDataType()) && $urlType instanceof UrlDataType) {
            $base = $urlType->getBaseUrl();
        }
        
        return $this->buildJsDataOnLoadedViaTrait($oModelJs) . <<<JS

            try {
				var data = {$oModelJs}.getData().rows;
                var carousel = $('#{$this->getSlickGalleryId()}');
                var src = '';
                var title = '';
				for (var i in data) {
                    src = '{$base}' + data[i]['{$widget->getImageUrlColumn()->getDataColumnName()}'];
                    title = data[i]['{$widget->getImageTitleColumn()->getDataColumnName()}'];
                    carousel.slick('slickAdd', '<div class="imagecarousel-item"><img src="' + src + '" title="' + title + '" alt="' + title + '" /></div>');
                }
		        {$this->buildJsBusyIconHide()}
		        $('#{$this->getSlickGalleryId()}').data('_loading', 0);
			} catch (err) {
                console.error(err);
				{$this->buildJsBusyIconHide()}
			}
JS;
    }

}