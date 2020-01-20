<?php
namespace exface\UI5Facade\Facades\Elements;

use exface\Core\Facades\AbstractAjaxFacade\Elements\JqueryDisableConditionTrait;
use exface\Core\Facades\AbstractAjaxFacade\Elements\JqueryInputValidationTrait;
use exface\Core\Interfaces\Widgets\iHaveValue;
use exface\Core\Facades\AbstractAjaxFacade\Elements\JqueryContainerTrait;
use exface\Core\Interfaces\WidgetInterface;

/**
 * Generates OpenUI5 inputs
 *
 * @author Andrej Kabachnik
 *        
 */
class UI5InlineGroup extends UI5Value
{
    use JqueryContainerTrait;
    
    /**
     *
     * {@inheritDoc}
     * @see \exface\UI5Facade\Facades\Elements\UI5AbstractElement::buildJsConstructor()
     */
    public function buildJsConstructor($oControllerJs = 'oController') : string
    {
        return $this->buildJsLabelWrapper($this->buildJsConstructorForMainControl($oControllerJs));
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \exface\UI5Facade\Facades\Elements\UI5Value::buildJsConstructorForMainControl()
     */
    public function buildJsConstructorForMainControl($oControllerJs = 'oController')
    {
        return <<<JS
        new sap.m.HBox("{$this->getId()}", {
            alignItems: "Center",
            items: [
                {$this->buildJsChildrenConstructors()}
            ]
        })
        {$this->buildJsPseudoEventHandlers()}
JS;
    }
    
    /**
     *
     * @return string
     */
    public function buildJsChildrenConstructors() : string
    {
        $js = '';
        $separatorWidgets = $this->getWidget()->getSeparatorWidgets();
        foreach ($this->getWidget()->getWidgets() as $idx => $widget) {

            $element = $this->getFacade()->getElement($widget);
            if (in_array($widget, $separatorWidgets) === true) {
                $element->setAlignment("sap.ui.core.TextAlign.Center");
            }

            $element->setLayoutData($this->buildJsChildLayoutConstructor($widget));

            $widget->setWidth('100%');

            $js .= ($js ? ",\n" : '') . $element->buildJsConstructor();
        }
        
        return $js;
    }
    
    /**
     * Function for setting the layoutparameters of the InlineGroup's child widgets.
     * The settings a child gets are depending on the settings in the UXON.
     * The settings are applied by defining an instance of `FlexItemData` for every child widget.
     * 
     * If the width of a child is given, it recieves the following set of attributes:
     * ```
     *      growFactor: 0,
     *      baseSize: [width from UXON]
     * ```
     * If the width is not specified, the settings are as following:
     * ```
     *      growFactor: 1,
     *      baseSize: "0"  
     * ```
     * 
     * If there is no seperator specified, the constructor will append a small margin at the end of each item,
     * exept the last item in line, by setting its style class to UI5's `sapUiTinyMarginEnd`.
     * This is achieved by appending 
     * ```
     *      styleClass: "sapUiTinyMarginEnd"
     * ```
     * to the `FlexItemData`.
     * 
     * @param WidgetInterface $child
     * @return string
     */
    protected function buildJsChildLayoutConstructor(WidgetInterface $child) : string
    {
        //if the width of a child is undefined, it gets the following 
        if ($child->getWidth()->isUndefined()){
            $props = "growFactor: 1, baseSize: \"0\"";
        } else {
            $props = "growFactor: 0,  baseSize: \"{$child->getWidth()->getValue()}\"";
        }
        if (!$this->getWidget()->hasSeperator()){
            $widgets = $this->getWidget()->getWidgets();
            if ($widgets[sizeof($widgets)-1] !== $child){
                $props .= ", styleClass: \"sapUiTinyMarginEnd\"";
            }
        }
        
        $string = "new sap.m.FlexItemData({ $props })";
        return $string;
    }
    
    /**
     *
     * {@inheritDoc}
     * @see \exface\Core\Facades\AbstractAjaxFacade\Elements\JqueryContainerTrait::buildJsValidationError()
     */
    public function buildJsValidationError()
    {
        foreach ($this->getWidgetsToValidate() as $child) {
            $el = $this->getFacade()->getElement($child);
            $validator = $el->buildJsValidator();
            $output .= '
				if(!' . $validator . ') { ' . $el->buildJsValidationError() . '; }';
        }
        return $output;
    }
}
?>