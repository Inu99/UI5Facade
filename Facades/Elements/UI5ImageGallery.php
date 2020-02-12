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
        init as initViaTrait;
    }
    use JquerySlickGalleryTrait;
    use JqueryToolbarsTrait;
    
    protected function init()
    {
        parent::init();
        $this->setSlickGalleryId($this->getId() . "_SlickGallery");

        $this->initViaTrait();
    }
    
    public function registerExternalModules(UI5ControllerInterface $controller) : UI5AbstractElement
    {
        
        $f = $this->getFacade();
        $controller->addExternalModule('libs.exface.slick.Slick', $f->buildUrlToSource('LIBS.SLICK.SLICK_JS'), null, 'slick');
        $controller->addExternalCss($this->getFacade()->buildUrlToSource('LIBS.SLICK.SLICK_CSS'));
        

        return $this;
    }
            
    public function buildJsConstructorForControl($oControllerJs = 'oController') : string
    {
        $this->addCarouselFunctions($this->getController());
        $this->registerExternalModules($this->getController());
        $controller = $this->getController();
        
        $html = <<<HTML
     <div class="exf-grid-item exf-imagecarousel" style="width:{$this->getWidth()};height:{$this->getHeight()};box-sizing:border-box;">
        {$this->buildHtmlCarousel()}
    </div>
HTML;
        
        $html = str_replace("\"", "\\\"", $html);
        $html = str_replace("\n", "", $html);
        
        return <<<JS
         new sap.ui.core.HTML("{$this->getId()}", {
            afterRendering: function(oEvent) {
                {$controller->buildJsMethodCallFromView($this->buildJsCarouselInitFunctionName(), $this)};
                {$controller->buildJsMethodCallFromView($this->buildJsCarouselLoadFunctionName(), $this)};
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
    
    protected function addCarouselFunctions(UI5ControllerInterface $controller) : string
        
        // $controller->addProperty($this->buildJsCarouselInitFunctionName(), 'function(){ ' . $this->buildJsCarouselInitFunctionBody() . ' }');
        $controller->addMethod($this->buildJsCarouselInitFunctionName(), $this, '', $this->buildJsCarouselInitFunctionBody());
        // $controller->addProperty($this->buildJsCarouselLoadFunctionName(), 'function(){ ' . $this->buildJsCarouselLoadFunctionBody() . ' }');
        $controller->addMethod($this->buildJsCarouselLoadFunctionName(), $this, '', $this->buildJsCarouselLoadFunctionBody());

        
        return <<<JS
        

        
JS;
    }
    
    
    public function buildJsDataSource() : string
    {
        $widget = $this->getWidget();
        
        if (($urlType = $widget->getImageUrlColumn()->getDataType()) && $urlType instanceof UrlDataType) {
            $base = $urlType->getBaseUrl();
        }
        
        // TODO
        return $this->buildJsDataLoader();
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

    public function buildJsRefresh($keep_pagination_position = false)
    {
        //return $this->buildJsFunctionPrefix() . "_load();";
        
        return $this->getController()->buildJsMethodCallFromController($this->buildJsCarouselLoadFunctionName(), $this);
    }
}