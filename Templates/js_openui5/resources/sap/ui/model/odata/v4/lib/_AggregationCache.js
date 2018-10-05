/*!
 * UI development toolkit for HTML5 (OpenUI5)
 * (c) Copyright 2009-2018 SAP SE or an SAP affiliate company.
 * Licensed under the Apache License, Version 2.0 - see LICENSE.txt.
 */
sap.ui.define(["jquery.sap.global","sap/ui/base/SyncPromise","./_Cache","./_Helper","./_Parser"],function(q,S,_,a,b){"use strict";var r=/,|%2C|%2c/,c=new RegExp("^"+b.sODataIdentifier+"(?:"+b.sWhitespace+"+(?:asc|desc))?$"),d=new RegExp(b.sWhitespace+"+");function e(R,s,A,Q,f){var m={},g,F,M;_.call(this,R,s,Q,f);if(Q.$filter){throw new Error("Unsupported system query option: $filter");}if(a.hasMinOrMax(A.aggregate)){if(Q.$orderby){throw new Error("Unsupported system query option: $orderby");}if(A.groupLevels.length){throw new Error("Unsupported group levels together with min/max");}this.oMeasureRangePromise=new Promise(function(h,i){M=h;});g=a.buildApply(A,m);this.oFirstLevel=_.create(R,s,q.extend({},Q,{$apply:g}),f);this.oFirstLevel.getResourcePath=e.getResourcePath.bind(this.oFirstLevel,A,this.oFirstLevel.getResourcePath);this.oFirstLevel.handleResponse=e.handleResponse.bind(this.oFirstLevel,m,M,this.oFirstLevel.handleResponse);}else{if(Q.$count){throw new Error("Unsupported system query option: $count");}F=e.filterAggregationForFirstLevel(A);this.oFirstLevel=_.create(R,s,q.extend({},Q,{$apply:a.buildApply(F),$count:true,$orderby:e.filterOrderby(Q.$orderby,F)}),f);this.oFirstLevel.calculateKeyPredicates=e.calculateKeyPredicate.bind(null,F,this.oFirstLevel.sMetaPath,this.oFirstLevel.aElements.$byPredicate);}}e.prototype=Object.create(_.prototype);e.prototype.fetchValue=function(g,p,D,l){if(!this.oMeasureRangePromise&&p==="$count"){q.sap.log.error("Failed to drill-down into $count, invalid segment: $count",this.oFirstLevel.toString(),"sap.ui.model.odata.v4.lib._Cache");return S.resolve();}return this.oFirstLevel.fetchValue(g,p,D,l);};e.prototype.getMeasureRangePromise=function(){return this.oMeasureRangePromise;};e.prototype.read=function(i,l,p,g,D){var R=this.oFirstLevel.read(i,l,p,g,D);if(!this.oMeasureRangePromise){return R.then(function(o){o.value.forEach(function(E){E["@$ui5.node.isExpanded"]=false;E["@$ui5.node.isTotal"]=true;E["@$ui5.node.level"]=1;});return o;});}return R;};e.calculateKeyPredicate=function(A,m,B,g,t){var f=A.groupLevels[0],l=a.formatLiteral(g[f],t[m][f].$Type),p="("+encodeURIComponent(f)+"="+encodeURIComponent(l)+")";function s(o){return JSON.stringify(a.publicClone(o));}if(p in B){throw new Error("Multi-unit situation detected: "+s(g)+" vs. "+s(B[p]));}a.setPrivateAnnotation(g,"predicate",p);};e.create=function(R,s,A,Q,f){return new e(R,s,A,Q,f);};e.filterAggregationForFirstLevel=function(A){function f(t,k){t[k]=this[k];return t;}function g(m,F){return Object.keys(m).filter(F).reduce(f.bind(m),{});}function h(s){return A.aggregate[s].subtotals;}function i(G){return A.groupLevels.indexOf(G)>=0;}return{aggregate:g(A.aggregate,h),group:g(A.group,i),groupLevels:A.groupLevels};};e.filterOrderby=function(o,A){if(o){return o.split(r).filter(function(O){var n;if(c.test(O)){n=O.split(d)[0];return n in A.aggregate||n in A.group||A.groupLevels.indexOf(n)>=0;}return true;}).join(",");}};e.getResourcePath=function(A,g,s,E){var o,R;if(s!==0){throw new Error("First request needs to start at index 0");}R=g.call(this,s,E+1);o=a.clone(A);Object.keys(o.aggregate).forEach(function(f){var D=o.aggregate[f];delete D.min;delete D.max;});this.mQueryOptions.$apply=a.buildApply(o);this.sQueryString=this.oRequestor.buildQueryString(this.sMetaPath,this.mQueryOptions,false,this.bSortExpandSelect);this.getResourcePath=g;return R;};e.handleResponse=function(A,m,h,s,E,R,t){var f,M={},o;function g(i){M[i]=M[i]||{};return M[i];}if("@odata.count"in R){R["@odata.count"]-=1;}o=R.value.splice(0,1)[0];for(f in A){g(A[f].measure)[A[f].method]=o[f];}m(M);this.handleResponse=h;this.handleResponse(s,E,R,t);};return e;},false);