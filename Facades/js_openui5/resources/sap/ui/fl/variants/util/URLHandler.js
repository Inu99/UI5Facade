/*!
 * OpenUI5
 * (c) Copyright 2009-2019 SAP SE or an SAP affiliate company.
 * Licensed under the Apache License, Version 2.0 - see LICENSE.txt.
 */
sap.ui.define(["sap/ui/thirdparty/jquery","sap/ui/core/Component","sap/ui/fl/Utils","sap/ui/core/routing/History","sap/ui/core/routing/HashChanger","sap/base/Log","sap/base/util/deepEqual","sap/ui/base/ManagedObjectObserver","sap/ui/thirdparty/hasher"],function(q,C,f,H,a,L,d,M,h){"use strict";var U={initialize:function(){var u=f.getParsedURLHash().params;this._oHashRegister={hashParams:(u&&u[this.sVariantTechnicalParameterName])||[],observers:[],variantControlIds:[]};U._setOrUnsetCustomNavigationForParameter.call(this,true);},attachHandlers:function(v,u){function o(){return this._oVariantSwitchPromise.then(function(){this._oHashRegister.observers.forEach(function(O){O.destroy();});U._setOrUnsetCustomNavigationForParameter.call(this,false);this.oChangePersistence.resetVariantMap();this.destroy();this.oComponentDestroyObserver.unobserve(this.oAppComponent,{destroy:true});this.oComponentDestroyObserver.destroy();}.bind(this));}if(!this.oComponentDestroyObserver&&this.oAppComponent instanceof C){this.oComponentDestroyObserver=new M(o.bind(this));this.oComponentDestroyObserver.observe(this.oAppComponent,{destroy:true});}if(u){this._oHashRegister.variantControlIds.push(v);}},update:function(p){if(!p||!Array.isArray(p.parameters)){L.info("Variant URL parameters could not be updated since invalid parameters were received");return;}if(p.updateURL){U._setTechnicalURLParameterValues.call(this,p.component||this.oAppComponent,p.parameters);}if(p.updateHashEntry){this._oHashRegister.hashParams=p.parameters;}},_setOrUnsetCustomNavigationForParameter:function(s){var m=s?"registerNavigationFilter":"unregisterNavigationFilter";var u=f.getUshellContainer();if(u){u.getService("ShellNavigation")[m](U._navigationFilter.bind(this));}},_navigationFilter:function(n){var u=f.getUshellContainer();var s=u.getService("ShellNavigation");try{var o=u.getService("URLParsing");var N=o.parseShellHash(n);var r=N&&N.params.hasOwnProperty(this.sVariantTechnicalParameterName);if(r){var b=N.params[this.sVariantTechnicalParameterName];var m=b.reduce(function(R,v){var V=this.getVariantManagementReference(v).variantManagementReference;if(V&&this.oData[V].currentVariant!==v){R.updateRequired=true;if(this.oData[V].currentVariant!==this.oData[V].defaultVariant){R.currentVariantReferences.push(this.oData[V].currentVariant);}}else{R.currentVariantReferences.push(v);}return R;}.bind(this),{updateRequired:false,currentVariantReferences:[]});if(this._bDesignTimeMode){this.updateEntry({updateURL:false,parameters:m.currentVariantReferences,updateHashEntry:m.updateRequired});this.updateEntry({updateURL:true,parameters:[],updateHashEntry:false});}else if(m.updateRequired){this.updateEntry({updateURL:true,parameters:m.currentVariantReferences,updateHashEntry:true});}}}catch(e){L.error(e.message);}return s.NavigationFilterStatus.Continue;},getCurrentHashParamsFromRegister:function(){return Array.prototype.slice.call(this._oHashRegister.hashParams);},_setTechnicalURLParameterValues:function(c,v,s){var p=f.getParsedURLHash(this.sVariantTechnicalParameterName);if(p.params){var t=f.getTechnicalParametersForComponent(c);if(!t){L.warning("Component instance not provided, so technical parameters in component data and browser history remain unchanged");}if(v.length===0){delete p.params[this.sVariantTechnicalParameterName];t&&delete t[this.sVariantTechnicalParameterName];}else{p.params[this.sVariantTechnicalParameterName]=v;t&&(t[this.sVariantTechnicalParameterName]=v);}if(s){h.changed.active=false;h.replaceHash(f.getUshellContainer().getService("URLParsing").constructShellHash(p));h.changed.active=true;}else{var o=f.getUshellContainer().getService("CrossApplicationNavigation");o.toExternal({target:{semanticObject:p.semanticObject,action:p.action,context:p.contextRaw},params:p.params,appSpecificRoute:p.appSpecificRoute,writeHistory:false});}}},handleModelContextChange:function(v){var c="modelContextChange";function b(e){var V=this.getVariantManagementReferenceForControl(e.getSource());var g=this._oHashRegister.variantControlIds;var i=g.indexOf(V);if(i>-1){g.slice(i).forEach(function(s){if(this.getVariantIndexInURL(s).index===-1){this.switchToDefaultForVariantManagement(s);}}.bind(this));}}var o=new M(function(e){if(e.current===true&&e.old===false){e.object.attachEvent(c,b,this);}else if(e.current===false&&e.old===true){e.object.detachEvent(c,b,this);}}.bind(this));o.observe(v,{properties:["resetOnContextChange"]});this._oHashRegister.observers.push(o);if(v.getResetOnContextChange()!==false){v.attachEvent(c,b,this);}}};return U;},true);