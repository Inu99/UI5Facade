/*!
 * OpenUI5
 * (c) Copyright 2009-2019 SAP SE or an SAP affiliate company.
 * Licensed under the Apache License, Version 2.0 - see LICENSE.txt.
 */
sap.ui.define(["sap/ui/base/ManagedObject","sap/base/Log"],function(M,L){"use strict";var S=M.extend("sap.f.cards.util.SimpleControl",{metadata:{properties:{resolved:{type:"any"}}}});var s=new S();var B={};function p(v,m,P,c,i){var R=c===i;if(R){L.warning("BindingResolver maximum level processing reached. Please check for circular dependencies.");}if(!v||R){return v;}if(Array.isArray(v)){v.forEach(function(I,b,A){if(typeof I==="object"){p(I,m,P,c+1,i);}else if(typeof I==="string"){A[b]=r(I,m,P);}},this);return v;}else if(typeof v==="object"){for(var a in v){if(typeof v[a]==="object"){p(v[a],m,P,c+1,i);}else if(typeof v[a]==="string"){v[a]=r(v[a],m,P);}}return v;}else if(typeof v==="string"){return r(v,m,P);}else{return v;}}function r(b,m,P){if(!b){return b;}var o=M.bindingParser(b);if(!o){return b;}if(!P){P="/";}s.setModel(m);s.bindObject(P);s.bindProperty("resolved",o);var v=s.getResolved();s.unbindProperty("resolved");s.unbindObject();s.setModel(null);return v;}B.resolveValue=function(v,m,P){var c,a,C=0,i=30;if(v&&typeof v==="object"){c=jQuery.extend(true,Array.isArray(v)?[]:{},v);}a=c||v;if(m){a=p(a,m,P,C,i);}return a;};return B;});