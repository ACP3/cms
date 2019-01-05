angular.module("ngAlertify",[]).factory("alertify",function(){"use strict";var t={exports:!0};!function(){function e(t){if(t){var e=function(){t&&t.parentNode&&t.parentNode.removeChild(t)};a(t,"show"),s(t,"hide"),t.addEventListener("transitionend",e),setTimeout(e,d)}}function n(t){var e=t.offsetHeight,n=window.innerHeight?window.innerHeight:document.documentElement.clientHeight?document.documentElement.clientHeight:screen.height,o=n/2-e/2;t.style.top=o+"px"}function o(t){var e=document.createElement("div");return e.innerHTML=t,e.firstChild}function i(t,e){var n="data-"+e,o=document.createElement("div");o.appendChild(t);var i=o.querySelector("["+n+"]");if(!i)throw new Error('Unable to find: "'+n+'" attribute.');return i}function a(t,e){var n=t.getAttribute("class"),o=n?n.split(" "):[],i=o.indexOf(e);i!==-1&&o.splice(i,1),t.className=o.join(" ")}function s(t,e){var n=t.getAttribute("class"),o=n?n.split(" "):[];o.push(e),t.className=o.join(" ")}function l(t){return JSON.parse(JSON.stringify(t))}function r(){var t={parent:document.body,dialogWidth:"400px",dialogPersistent:!0,dialogContainerClass:"alertify",dialogButtons:{ok:{label:"Ok",autoClose:!0,template:'<button data-alertify-btn="ok" tabindex="1"></button>'},cancel:{label:"Cancel",autoClose:!0,template:'<button data-alertify-btn="cancel" tabindex="2"></button>'},custom:{label:"Custom",autoClose:!1,template:'<button data-alertify-btn tabindex="3"></button>'}},logDelay:5e3,logMaxItems:2,logPosition:{v:"bottom",h:"left"},logContainerClass:"alertify-logs",logTemplateMethod:null,templates:{dialogButtonsHolder:"<nav data-alertify-btn-holder></nav>",dialogMessage:"<div data-alertify-msg></div>",dialogInput:'<input data-alertify-input type="text">',logMessage:"<div data-alertify-log-msg></div>"}},r=function(t){var n=this;this.type=t,this.fixed=!1,this.template=d.logTemplateMethod,this.dom={},this.createDomElements=function(t){this.dom.wrapper=o(t),this.dom.message=i(this.dom.wrapper,"alertify-log-msg"),setTimeout(function(){n.dom.wrapper.className+=" show"},10)},this.getDomElements=function(){return this.dom},this.setMessage=function(t){var e=t;this.template&&(e=this.template(t)),e instanceof HTMLElement?(this.dom.message.innerHTML="",this.dom.message.appendChild(e)):this.dom.message.innerHTML=e},this.setType=function(t){s(this.dom.message,t)},this.setClickEvent=function(t){this.dom.wrapper.addEventListener("click",function(e){t(e,n)})},this.injectHtml=function(){var t=u.elements;0===t.length||"top"===d.logPosition.v?u.container.appendChild(this.dom.wrapper):u.container.insertBefore(this.dom.wrapper,t[t.length-1].dom.wrapper),u.elements.push(this)},this.stick=function(t){this.fixed=t},this.isFixed=function(){return this.fixed},this.remove=function(){e(this.dom.wrapper);var t=u.elements.indexOf(this);t>-1&&u.elements.splice(t,1)}},d={version:"1.0.11",parent:t.parent,dialogWidth:t.dialogWidth,dialogPersistent:t.dialogPersistent,dialogContainerClass:t.dialogContainerClass,dialogButtons:l(t.dialogButtons),promptValue:"",logDelay:t.logDelay,logMaxItems:t.logMaxItems,logPosition:t.logPosition,logContainerClass:t.logContainerClass,logTemplateMethod:t.logTemplateMethod,templates:l(t.templates),build:function(t,e){var n={};if(n.container=document.createElement("div"),n.container.className=this.dialogContainerClass+" hide",n.wrapper=document.createElement("div"),n.wrapper.className="dialog",n.dialog=document.createElement("div"),n.dialog.style.width=this.dialogWidth,n.content=document.createElement("div"),n.content.className="content","dialog"===t.type?n.content.innerHTML=t.message:(n.messageWrapper=o(this.templates.dialogMessage),n.message=i(n.messageWrapper,"alertify-msg"),n.message.innerHTML=t.message,n.content.appendChild(n.messageWrapper)),n.buttonsWrapper=o(this.templates.dialogButtonsHolder),n.buttonsHolder=i(n.buttonsWrapper,"alertify-btn-holder"),"prompt"===t.type){var a=o(this.templates.dialogInput);n.input=i(a,"alertify-input"),n.content.appendChild(a)}n.container.appendChild(n.wrapper),n.wrapper.appendChild(n.dialog),n.dialog.appendChild(n.content),n.dialog.appendChild(n.buttonsWrapper),n.buttonsHolder.innerHTML="",n.buttons=[];for(var s=0;s<e.length;s++){var l=i(e[s].element,"alertify-btn");l.innerHTML=e[s].label,n.buttonsHolder.appendChild(e[s].element)}return n},prepareDialogButton:function(t,e){var n={};return!e||"object"!=typeof e||e instanceof Array||(n=e),"function"==typeof e&&(n.click=e),n.type=t,n},createButtonsDefinition:function(t){for(var e=[],n=0;n<t.buttons.length;n++){var i=this.buildButtonObject(t.buttons[n]);("dialog"===t.type||"alert"===t.type&&"ok"===i.type||["confirm","prompt"].indexOf(t.type)!==-1&&["ok","cancel"].indexOf(i.type)!==-1)&&(i.element=o(i.template),e.push(i))}return e},buildButtonObject:function(t){var e={},n=t.type||"custom",o=this.dialogButtons,i=["ok","cancel","custom"];if("undefined"!=typeof t.type&&i.indexOf(t.type)===-1)throw new Error('Wrong button type: "'+t.type+'". Valid values: "'+i.join('", "')+'"');return e.type=n,e.label="undefined"!=typeof t.label?t.label:o[n].label,e.autoClose="undefined"!=typeof t.autoClose?t.autoClose:o[n].autoClose,e.template="undefined"!=typeof t.template?t.template:o[n].template,e.click="undefined"!=typeof t.click?t.click:o[n].click,e},close:function(t,e){e=e&&!isNaN(+e)?+e:this.logDelay,e<0?t.remove():e>0&&setTimeout(function(){t.remove()},e)},dialog:function(t,e,n){return this.setup({type:e,message:t,buttons:n})},log:function(t,e,n){for(var o=u.elements,i=[],a=0,s=o.length;a<s;a++)o[a].isFixed()||i.push(o[a]);var l=i.length-this.logMaxItems;if(l>=0)for(var r=0,d=l+1;r<d;r++)this.close(i[r],-1);this.notify(t,e,n)},setLogContainerClass:function(e){this.logContainerClass=t.logContainerClass+" "+e},setLogPosition:function(t){var e=t.split(" ");if(["top","bottom"].indexOf(e[0])===-1||["left","right"].indexOf(e[1])===-1)throw new Error('Wrong value for "logPosition" parameter.');this.logPosition={v:e[0],h:e[1]}},setLogFixed:function(t){if("boolean"!=typeof t)throw new Error('Wrong value for "logFixed" parameter. Should be boolean.');this.logFixed=t},setupLogContainer:function(){var t=this.logPosition.v+" "+this.logPosition.h,n=this.logContainerClass+" "+t,o=u.container&&u.container.parentNode!==this.parent;u.container&&!o||(o&&e(u.container),u.elements=[],u.container=document.createElement("div"),u.container.className=n,this.parent.appendChild(u.container)),u.container.className!==n&&(u.container.className=n)},notify:function(t,e,n){this.setupLogContainer();var o=new r;o.createDomElements(this.templates.logMessage),o.setMessage(t),o.setType(e),"function"==typeof n&&o.setClickEvent(n),o.injectHtml(),this.close(o,this.logDelay)},setup:function(t){function o(t){"function"!=typeof t&&(t=function(){});for(var e=0;e<l.length;e++){var n=l[e],o=function(e){return function(n){s=e,e.click&&"function"==typeof e.click&&e.click(n,u),t({ui:u,event:n}),e.autoClose===!0&&u.closeDialog()}}(n);n.element.addEventListener("click",o)}d&&d.addEventListener("keyup",function(t){13===t.which&&i.click()})}for(var i,s,l=this.createButtonsDefinition(t),r=this.build(t,l),u={},d=r.input,c=0;c<l.length;c++)"ok"===l[c].type&&(i=l[c].element);d&&"string"==typeof this.promptValue&&(d.value=this.promptValue),u.dom=r,u.closeDialog=function(){e(r.container)},u.centerDialog=function(){n(r.wrapper)},u.setMessage=function(t){r.message.innerHTML=t},u.setContent=function(t){r.content.innerHTML=t},u.getInputValue=function(){if(r.input)return r.input.value},u.getButtonObject=function(){if(s)return{type:s.type,label:s.label,autoClose:s.autoClose,element:s.element}};var p;return"function"==typeof Promise?p=new Promise(o):o(),this.dialogPersistent===!1&&r.container.addEventListener("click",function(t){t.target!==this&&t.target!==r.wrapper||e(r.container)}),window.onresize=function(){u.centerDialog()},this.parent.appendChild(r.container),setTimeout(function(){a(r.container,"hide"),u.centerDialog(),d&&t.type&&"prompt"===t.type?(d.select(),d.focus()):i&&i.focus()},100),p},setLogDelay:function(e){return e=e||0,this.logDelay=isNaN(e)?t.logDelay:parseInt(e,10),this},setLogMaxItems:function(e){this.logMaxItems=parseInt(e||t.logMaxItems)},setDialogWidth:function(e){"number"==typeof e&&(e+="px"),this.dialogWidth="string"==typeof e?e:t.dialogWidth},setDialogPersistent:function(t){this.dialogPersistent=t},setDialogContainerClass:function(e){this.dialogContainerClass=t.dialogContainerClass+" "+e},setTheme:function(e){if(e){if("string"==typeof e)switch(e.toLowerCase()){case"bootstrap":this.dialogButtons.ok.template='<button data-alertify-btn="ok" class="btn btn-primary" tabindex="1"></button>',this.dialogButtons.cancel.template='<button data-alertify-btn="cancel" class="btn btn-danger" tabindex="2"></button>',this.dialogButtons.custom.template='<button data-alertify-btn="custom" class="btn btn-default" tabindex="3"></button>',this.templates.dialogInput="<input data-alertify-input class='form-control' type='text'>";break;case"purecss":this.dialogButtons.ok.template='<button data-alertify-btn="ok" class="pure-button" tabindex="1"></button>',this.dialogButtons.cancel.template='<button data-alertify-btn="cancel" class="pure-button" tabindex="2"></button>',this.dialogButtons.custom.template='<button data-alertify-btn="custom" class="pure-button" tabindex="3"></button>';break;case"mdl":case"material-design-light":this.dialogButtons.ok.template='<button data-alertify-btn="ok" class=" mdl-button mdl-js-button mdl-js-ripple-effect"  tabindex="1"></button>',this.dialogButtons.cancel.template='<button data-alertify-btn="cancel" class=" mdl-button mdl-js-button mdl-js-ripple-effect" tabindex="2"></button>',this.dialogButtons.custom.template='<button data-alertify-btn="custom" class=" mdl-button mdl-js-button mdl-js-ripple-effect" tabindex="3"></button>',this.templates.dialogInput='<div class="mdl-textfield mdl-js-textfield"><input data-alertify-input class="mdl-textfield__input"></div>';break;case"angular-material":this.dialogButtons.ok.template='<button data-alertify-btn="ok" class="md-primary md-button" tabindex="1"></button>',this.dialogButtons.cancel.template='<button data-alertify-btn="cancel" class="md-button" tabindex="2"></button>',this.dialogButtons.custom.template='<button data-alertify-btn="custom" class="md-button" tabindex="3"></button>',this.templates.dialogInput='<div layout="column"><md-input-container md-no-float><input data-alertify-input type="text"></md-input-container></div>';break;case"default":default:this.dialogButtons=l(t.dialogButtons),this.templates=l(t.templates)}if("object"==typeof e){var n=Object.keys(this.templates);for(var o in e){if(n.indexOf(o)===-1)throw new Error('Wrong template name: "'+o+'". Valid values: "'+n.join('", "')+'"');this.templates[o]=e[o]}}}},reset:function(){this.setTheme("default"),this.parent=t.parent,this.dialogWidth=t.dialogWidth,this.dialogPersistent=t.dialogPersistent,this.dialogContainerClass=t.dialogContainerClass,this.promptValue="",this.logDelay=t.logDelay,this.logMaxItems=t.logMaxItems,this.logPosition=t.logPosition,this.logContainerClass=t.logContainerClass,this.logTemplateMethod=t.logTemplateMethod},injectCSS:function(){if(!document.querySelector("#alertifyCSS")){var t=document.getElementsByTagName("head")[0],e=document.createElement("style");e.type="text/css",e.id="alertifyCSS",t.insertBefore(e,t.firstChild)}},removeCSS:function(){var t=document.querySelector("#alertifyCSS");t&&t.parentNode&&t.parentNode.removeChild(t)}};return d.injectCSS(),{_$alertify:d,_$defaults:t,reset:function(){return d.reset(),this},parent:function(t){d.parent=t},theme:function(t){return d.setTheme(t),this},dialog:function(t,e){return d.dialog(t,"dialog",e)||this},alert:function(t,e){var n=[d.prepareDialogButton("ok",e)];return d.dialog(t,"alert",n)||this},confirm:function(t,e,n){var o=[d.prepareDialogButton("ok",e),d.prepareDialogButton("cancel",n)];return d.dialog(t,"confirm",o)||this},prompt:function(t,e,n,o){var i=[d.prepareDialogButton("ok",n),d.prepareDialogButton("cancel",o)];return d.promptValue=e||"",d.dialog(t,"prompt",i)||this},dialogWidth:function(t){return d.setDialogWidth(t),this},dialogPersistent:function(t){return d.setDialogPersistent(t),this},dialogContainerClass:function(t){return d.setDialogContainerClass(t||""),this},clearDialogs:function(){for(var e;e=d.parent.querySelector(":scope > ."+t.dialogContainerClass);)d.parent.removeChild(e);return this},log:function(t,e){return d.log(t,"default",e),this},success:function(t,e){return d.log(t,"success",e),this},warning:function(t,e){return d.log(t,"warning",e),this},error:function(t,e){return d.log(t,"error",e),this},logDelay:function(t){return d.setLogDelay(t),this},logMaxItems:function(t){return d.setLogMaxItems(t),this},logPosition:function(t){return d.setLogPosition(t||""),this},logContainerClass:function(t){return d.setLogContainerClass(t||""),this},logMessageTemplate:function(t){return d.logTemplateMethod=t,this},getLogs:function(){return u.elements},clearLogs:function(){return u.container.innerHTML="",u.elements=[],this},version:d.version}}var u={container:null,elements:[]},d=500;if("undefined"!=typeof t&&t&&t.exports){t.exports=function(){return new r};var c=new r;for(var p in c)t.exports[p]=c[p]}else"function"==typeof define&&define.amd?define(function(){return new r}):window.alertify=new r}();var e=t.exports;return new e});