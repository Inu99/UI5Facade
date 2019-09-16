/*
 * ! OpenUI5
 * (c) Copyright 2009-2019 SAP SE or an SAP affiliate company.
 * Licensed under the Apache License, Version 2.0 - see LICENSE.txt.
 */
sap.ui.define(["sap/ui/fl/apply/_internal/connectors/StaticFileConnector","sap/base/security/encodeURLParameters","sap/base/Log"],function(S,e,L){"use strict";var A="sap/ui/fl/apply/_internal/connectors/";function a(s,t,k){if(!t[k]){t[k]=s[k];return;}if(Array.isArray(t[k])){t[k]=t[k].concat(s[k]);return;}if(typeof t[k]==='object'){Object.keys(s[k]).forEach(function(i){a(s[k],t[k],i);});}t[k]=s[k];}return{getConnectors:function(n,i){var c=sap.ui.getCore().getConfiguration().getFlexibilityServices();var C=[];if(i){C=[S.CONFIGURATION];}C=C.concat(c);return new Promise(function(r){var b=C.map(function(m){var s=m.connectorName;return m.custom?s:n+s;});sap.ui.require(b,function(){Array.from(arguments).forEach(function(o,I){C[I].connector=o;});r(C);});});},getApplyConnectors:function(){return this.getConnectors(A,true);},logAndResolveDefault:function(r,c,f,E){L.error("Connector ("+c.connectorName+") failed call '"+f+"': "+E);return r;},mergeResults:function(r){var R={};r.forEach(function(o){Object.keys(o).forEach(function(k){a(o,R,k);});});return R;},getSubsetOfObject:function(s,k){var t={};if(Array.isArray(k)){k.forEach(function(K){if(s[K]){t[K]=s[K];}});}return t;},getUrl:function(r,p,P){if(!r||!p.url){throw new Error("Not all necessary parameters were passed");}var u=p.url+r;if(p.cacheKey){u+="~"+p.cacheKey+"~/";}if(p.reference){u+=p.reference;}if(P){var q=e(P);if(q.length>0){u+="?"+q;}}return u;},sendRequest:function(u,m,p){m=m||"GET";m=m.toUpperCase();return new Promise(function(r,b){var x=new XMLHttpRequest();x.open(m,u);if((m==="GET"||m==="HEAD")&&(!p||!p.token)){x.setRequestHeader("X-CSRF-Token","fetch");}if((m==="POST"||m==="PUT"||m==="DELETE")&&p&&p.token){x.setRequestHeader("X-CSRF-Token",p.token);}if(p&&p.contentType){x.setRequestHeader("Content-Type",p.contentType);}if(p&&p.dataType){x.responseType=p.dataType;}if(p&&p.payload){x.send(p.payload);}else{x.send();}x.onload=function(){if(x.status>=200&&x.status<400){var R={};R.response=JSON.parse(x.response);R.status=x.status;R.token=x.getResponseHeader("X-CSRF-Token");r(R);}else{b({status:x.status,message:x.statusText});}};});}};});