//@ui5-bundle sap/ui/core/designtime/library-preload.designtime.js
/*!
 * OpenUI5
 * (c) Copyright 2009-2019 SAP SE or an SAP affiliate company.
 * Licensed under the Apache License, Version 2.0 - see LICENSE.txt.
 */
sap.ui.predefine('sap/ui/core/designtime/ComponentContainer.designtime',[],function(){"use strict";return{associations:{component:{aggregationLike:true}}};},false);
sap.ui.predefine('sap/ui/core/designtime/CustomData.designtime',[],function(){"use strict";return{aggregations:{customData:{ignored:true}}};},false);
sap.ui.predefine('sap/ui/core/designtime/Icon.designtime',[],function(){"use strict";return{palette:{group:"DISPLAY",icons:{svg:"sap/ui/core/designtime/Icon.icon.svg"}}};},false);
sap.ui.predefine('sap/ui/core/designtime/UIComponent.designtime',[],function(){"use strict";return{domRef:function(u){if(u.oContainer){return u.oContainer.getDomRef("uiarea");}},aggregations:{rootControl:{ignore:false}}};},false);
sap.ui.predefine('sap/ui/core/designtime/library.designtime',[],function(){"use strict";return{};});
sap.ui.predefine('sap/ui/core/designtime/mvc/ControllerExtensionTemplate',['sap/ui/core/mvc/ControllerExtension'],function(C){"use strict";return C.extend("{{controllerExtensionName}}",{});});
sap.ui.predefine('sap/ui/core/designtime/mvc/View.designtime',[],function(){"use strict";return{controllerExtensionTemplate:"sap/ui/core/designtime/mvc/ControllerExtensionTemplate"};},false);
sap.ui.predefine('sap/ui/core/designtime/mvc/XMLView.designtime',[],function(){"use strict";return{aggregations:{content:{domRef:":sap-domref"}}};},false);
//# sourceMappingURL=library-preload.designtime.js.map