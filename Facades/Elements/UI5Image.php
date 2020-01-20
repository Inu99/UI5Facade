<?php
namespace exface\UI5Facade\Facades\Elements;

use exface\Core\Widgets\Image;

/**
 * Generates sap.m.Image
 * 
 * @method Image getWidget()
 *
 * @author Andrej Kabachnik
 *        
 */
class UI5Image extends UI5Display
{
    /**
     * 
     * {@inheritDoc}
     * @see \exface\UI5Facade\Facades\Elements\UI5AbstractElement::buildJsConstructor()
     */
    public function buildJsConstructorForMainControl($oControllerJs = 'oController')
    {      
        $alignment = $this->getWidget()->getAlign();
        $classes = '';
        switch ($alignment) {
            case EXF_ALIGN_DEFAULT: 
                break;
            case EXF_ALIGN_CENTER:
                $classes .= ' pull-center';
                break;
            case EXF_ALIGN_RIGHT:
                $classes .= ' pull-right';
                break;
        }
        $addStyleClass = $classes !== '' ? '.addStyleClass("' . $classes . '")' : '';
        return <<<JS

        new sap.m.Image("{$this->getid()}", {
    		densityAware: false,
            src: {$this->buildJsValue()},
            {$this->buildJsProperties()}
    	}){$addStyleClass}

JS;
    }
            
    public function buildJsValueBindingOptions()
    {
        $base = $this->getWidget()->getBaseUrl();
        if ($this->getWidget()->getUseProxy()) {
            $proxyFormatter = <<<JS

            var proxyUrl = "{$this->getWidget()->buildProxyUrl('xxurixx')}";
            url = proxyUrl.replace("xxurixx", url);

JS;
        }
        
            return <<<JS

        formatter: function(value) {
            var url = encodeURI('{$base}' + value);
            {$proxyFormatter}
            return url;
        },

JS;
        
            
        return parent::buildJsValueBindingOptions();
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\UI5Facade\Facades\Elements\UI5Value::buildCssHeightDefaultValue()
     */
    protected function buildCssHeightDefaultValue()
    {
        return '';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\UI5Facade\Facades\Elements\UI5Value::buildCssWidthDefaultValue()
     */
    protected function buildCssWidthDefaultValue() : string
    {
        return '';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\UI5Facade\Facades\Elements\UI5Value::buildJsValueBindingPropertyName()
     */
    public function buildJsValueBindingPropertyName() : string
    {
        return 'src';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\UI5Facade\Facades\Elements\UI5Display::buildJsPropertyAlignment()
     */
    protected function buildJsPropertyAlignment()
    {
        return '';
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\UI5Facade\Facades\Elements\UI5Display::buildJsPropertyWrapping()
     */
    protected function buildJsPropertyWrapping()
    {
        return '';
    }
}
?>