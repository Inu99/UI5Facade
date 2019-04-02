/*!
 * UI development toolkit for HTML5 (OpenUI5)
 * (c) Copyright 2009-2018 SAP SE or an SAP affiliate company.
 * Licensed under the Apache License, Version 2.0 - see LICENSE.txt.
 */
sap.ui.define(["sap/ui/core/Element","sap/ui/core/Icon","sap/ui/core/IconPool","sap/ui/Device"],function(E,I,a,D){"use strict";var S={};S.CSS_CLASS="sapMSelectList";S.render=function(r,l){this.writeOpenListTag(r,l,{elementData:true});this.renderItems(r,l);this.writeCloseListTag(r,l);};S.writeOpenListTag=function(r,l,s){var C=S.CSS_CLASS;r.write("<ul");if(s.elementData){r.writeControlData(l);}r.addClass(C);if(l.getShowSecondaryValues()){r.addClass(C+"TableLayout");}if(!l.getEnabled()){r.addClass(C+"Disabled");}r.addStyle("width",l.getWidth());r.addStyle("max-width",l.getMaxWidth());r.writeStyles();r.writeClasses();this.writeAccessibilityState(r,l);r.write(">");};S.writeCloseListTag=function(r,l){r.write("</ul>");};S.renderItems=function(r,l){var s=l._getNonSeparatorItemsCount(),b=l.getItems(),o=l.getSelectedItem(),c=1,d;for(var i=0;i<b.length;i++){d={selected:o===b[i],setsize:s,elementData:true};if(!(b[i]instanceof sap.ui.core.SeparatorItem)){d.posinset=c++;}this.renderItem(r,l,b[i],d);}};S.renderItem=function(r,l,i,s){if(!(i instanceof E)){return;}var e=i.getEnabled(),o=l.getSelectedItem(),C=S.CSS_CLASS,t=i.getTooltip_AsString(),b=l.getShowSecondaryValues();r.write("<li");if(s.elementData){r.writeElementData(i);}if(i instanceof sap.ui.core.SeparatorItem){r.addClass(C+"SeparatorItem");if(b){r.addClass(C+"Row");}}else{r.addClass(C+"ItemBase");if(b){r.addClass(C+"Row");}else{r.addClass(C+"Item");}if(i.bVisible===false){r.addClass(C+"ItemBaseInvisible");}if(!e){r.addClass(C+"ItemBaseDisabled");}if(e&&D.system.desktop){r.addClass(C+"ItemBaseHoverable");}if(i===o){r.addClass(C+"ItemBaseSelected");}if(e){r.writeAttribute("tabindex","0");}}r.writeClasses();if(t){r.writeAttributeEscaped("title",t);}this.writeItemAccessibilityState.apply(this,arguments);r.write(">");if(b){r.write("<span");r.addClass(C+"Cell");r.addClass(C+"FirstCell");r.writeClasses();r.writeAttribute("disabled","disabled");r.write(">");this._renderIcon(r,i);r.writeEscaped(i.getText());r.write("</span>");r.write("<span");r.addClass(C+"Cell");r.addClass(C+"LastCell");r.writeClasses();r.writeAttribute("disabled","disabled");r.write(">");if(typeof i.getAdditionalText==="function"){r.writeEscaped(i.getAdditionalText());}r.write("</span>");}else{this._renderIcon(r,i);r.writeEscaped(i.getText());}r.write("</li>");};S.writeAccessibilityState=function(r,l){r.writeAccessibilityState(l,{role:"listbox"});};S.writeItemAccessibilityState=function(r,l,i,s){var R=(i.isA("sap.ui.core.SeparatorItem"))?"separator":"option";var d;if(!i.getText()&&i.getIcon&&i.getIcon()){var o=a.getIconInfo(i.getIcon());if(o){d=o.text||o.name;}}r.writeAccessibilityState(i,{role:R,selected:s.selected,setsize:s.setsize,posinset:s.posinset,label:d});};S._renderIcon=function(r,i){if(i.getIcon&&i.getIcon()){var o=new I({src:i.getIcon()});o.addStyleClass("sapMSelectListItemIcon");r.renderControl(o);}};return S;},true);