<?php
namespace exface\UI5Facade\Facades\Elements;

use exface\Core\Widgets\Form;

/**
 * Generates OpenUI5 inputs
 *
 * @author Andrej Kabachnik
 * 
 * @method Form getWidget()
 *        
 */
class UI5Form extends UI5Panel
{
    
    /**
     * 
     * {@inheritDoc}
     * @see \exface\UI5Facade\Facades\Elements\UI5Panel::buildJsConstructor()
     */
    public function buildJsConstructor($oControllerJs = 'oController') : string
    {
        $widget = $this->getWidget();
        
        if ($widget->hasButtons() === true) {
            $toolbar = $this->buildJsFloatingToolbar();
        } else {
            $toolbar = '';
        }
        
        if ($widget->hasParent() === true) {
            return $this->buildJsLayoutForm($this->buildJsChildrenConstructors(true), $toolbar, $this->getId());
        } else {
            $headerContent = $widget->getHideHelpButton() === false ? $this->buildJsHelpButtonConstructor($oControllerJs) : '';
            return $this->buildJsPageWrapper($this->buildJsLayoutForm($this->buildJsChildrenConstructors(true), '', $this->getId()), $toolbar, $headerContent);
        }
    }
    
    /**
     * Returns the constructor for an OverflowToolbar representing the main toolbar of the dialog.
     *
     * @return string
     */
    protected function buildJsFloatingToolbar()
    {
        return $this->getFacade()->getElement($this->getWidget()->getToolbarMain())->buildJsConstructor();
    }
    
}