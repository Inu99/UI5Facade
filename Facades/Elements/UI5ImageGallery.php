<?php
namespace exface\UI5Facade\Facades\Elements;


use exface\Core\Facades\AbstractAjaxFacade\Elements\JquerySlickGalleryTrait;
use exface\Core\Widgets\Imagegallery;

/**
 * Creates a UI5 panel with a slick image slider for a DataimageGallery widget.
 * 
 * @author Andrej Kabachnik
 * 
 * @method Imagegallery getWidget()
 *        
 */
class UI5ImageGallery extends UI5Data
{    
    use JquerySlickGalleryTrait;
    
    
}