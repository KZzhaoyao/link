define("arale-validator/0.10.0/index",["jquery/2.1.1/jquery","arale-widget/1.2.0/widget","arale-base/1.2.0/base","arale-class/1.2.0/class","arale-events/1.2.0/events"],function(e,t,r){r.exports=e("arale-validator/0.10.0/src/validator")}),define("arale-validator/0.10.0/src/validator",["jquery/2.1.1/jquery","arale-widget/1.2.0/widget","arale-base/1.2.0/base","arale-class/1.2.0/class","arale-events/1.2.0/events"],function(e,t,r){var a=e("arale-validator/0.10.0/src/core"),n=e("jquery/2.1.1/jquery"),i=a.extend({events:{"mouseenter .{{attrs.inputClass}}":"mouseenter","mouseleave .{{attrs.inputClass}}":"mouseleave","mouseenter .{{attrs.textareaClass}}":"mouseenter","mouseleave .{{attrs.textareaClass}}":"mouseleave","focus .{{attrs.itemClass}} input,textarea,select":"focus","blur .{{attrs.itemClass}} input,textarea,select":"blur"},attrs:{explainClass:"ui-form-explain",itemClass:"ui-form-item",itemHoverClass:"ui-form-item-hover",itemFocusClass:"ui-form-item-focus",itemErrorClass:"ui-form-item-error",inputClass:"ui-input",textareaClass:"ui-textarea",showMessage:function(e,t){this.getExplain(t).html(e),this.getItem(t).addClass(this.get("itemErrorClass"))},hideMessage:function(e,t){this.getExplain(t).html(t.attr("data-explain")||" "),this.getItem(t).removeClass(this.get("itemErrorClass"))}},setup:function(){i.superclass.setup.call(this);var e=this;this.on("autoFocus",function(t){e.set("autoFocusEle",t)})},addItem:function(e){i.superclass.addItem.apply(this,[].slice.call(arguments));var t=this.query(e.element);return t&&this._saveExplainMessage(t),this},_saveExplainMessage:function(e){var t=e.element,r=t.attr("data-explain");void 0!==r||this.getItem(t).hasClass(this.get("itemErrorClass"))||t.attr("data-explain",this.getExplain(t).html())},getExplain:function(e){var t=this.getItem(e),r=t.find("."+this.get("explainClass"));return 0==r.length&&(r=n('<div class="'+this.get("explainClass")+'"></div>').appendTo(t)),r},getItem:function(e){e=n(e);var t=e.parents("."+this.get("itemClass"));return t},mouseenter:function(e){this.getItem(e.target).addClass(this.get("itemHoverClass"))},mouseleave:function(e){this.getItem(e.target).removeClass(this.get("itemHoverClass"))},focus:function(e){var t=e.target,r=this.get("autoFocusEle");if(r&&r.has(t)){var a=this;return void n(t).keyup(function(){a.set("autoFocusEle",null),a.focus({target:t})})}this.getItem(t).removeClass(this.get("itemErrorClass")),this.getItem(t).addClass(this.get("itemFocusClass")),this.getExplain(t).html(n(t).attr("data-explain")||"")},blur:function(e){this.getItem(e.target).removeClass(this.get("itemFocusClass"))}});r.exports=i}),define("arale-validator/0.10.0/src/core",["jquery/2.1.1/jquery","arale-widget/1.2.0/widget","arale-base/1.2.0/base","arale-class/1.2.0/class","arale-events/1.2.0/events"],function(e,t,r){function a(e,t){for(var r=0;r<t.length;r++)if(e===t[r])return t.splice(r,1),t}function n(e,t){var r;return i.each(t,function(t,a){return e.get(0)===a.element.get(0)?(r=a,!1):void 0}),r}var i=e("jquery/2.1.1/jquery"),s=e("arale-validator/0.10.0/src/async"),l=e("arale-widget/1.2.0/widget"),u=e("arale-validator/0.10.0/src/utils"),o=e("arale-validator/0.10.0/src/item"),c=[],f={value:i.noop,setter:function(e){return i.isFunction(e)?e:u.helper(e)}},d=l.extend({attrs:{triggerType:"blur",checkOnSubmit:!0,stopOnError:!1,autoSubmit:!0,checkNull:!0,onItemValidate:f,onItemValidated:f,onFormValidate:f,onFormValidated:f,displayHelper:function(e){var t,r,a=e.element.attr("id");return a&&(t=i('label[for="'+a+'"]').text(),t&&(t=t.replace(/^[\*\s\:\：]*/,"").replace(/[\*\s\:\：]*$/,""))),r=e.element.attr("name"),t||r},showMessage:f,hideMessage:f,autoFocus:!0,failSilently:!1,skipHidden:!1},setup:function(){var e=this;if(e.items=[],e.element.is("form")){e._novalidate_old=e.element.attr("novalidate");try{e.element.attr("novalidate","novalidate")}catch(t){}e.get("checkOnSubmit")&&e.element.on("submit.validator",function(t){t.preventDefault(),e.execute(function(t){!t&&e.get("autoSubmit")&&e.element.get(0).submit()})})}e.on("itemValidated",function(e,t,r,a){this.query(r).get(e?"showMessage":"hideMessage").call(this,t,r,a)}),c.push(e)},Statics:i.extend({helper:u.helper},e("arale-validator/0.10.0/src/rule"),{autoRender:function(e){var t=new this(e);i("input, textarea, select",t.element).each(function(e,r){r=i(r);var a=r.attr("type");if("button"==a||"submit"==a||"reset"==a)return!0;var n={};if(n.element="radio"==a||"checkbox"==a?i("[type="+a+"][name="+r.attr("name")+"]",t.element):r,!t.query(n.element)){var s=u.parseDom(r);if(!s.rule)return!0;i.extend(n,s),t.addItem(n)}})},query:function(e){return l.query(e)},validate:function(e){var t=i(e.element),r=new d({element:t.parents()});r.addItem(e),r.query(t).execute(),r.destroy()}}),addItem:function(e){var t=this;if(i.isArray(e))return i.each(e,function(e,r){t.addItem(r)}),this;if(e=i.extend({triggerType:t.get("triggerType"),checkNull:t.get("checkNull"),displayHelper:t.get("displayHelper"),showMessage:t.get("showMessage"),hideMessage:t.get("hideMessage"),failSilently:t.get("failSilently"),skipHidden:t.get("skipHidden")},e),"string"==typeof e.element&&(e.element=this.$(e.element)),!i(e.element).length){if(e.failSilently)return t;throw new Error("element does not exist")}var r=new o(e);return t.items.push(r),r._validator=t,r.delegateEvents(r.get("triggerType"),function(e){(this.get("checkNull")||this.element.val())&&this.execute(null,{event:e})}),r.on("all",function(){this.trigger.apply(this,[].slice.call(arguments))},t),t},removeItem:function(e){var t=this,r=e instanceof o?e:t.query(e);return r&&(r.get("hideMessage").call(t,null,r.element),a(r,t.items),r.destroy()),t},execute:function(e){var t=this,r=[],a=!1,n=null;return i.each(t.items,function(e,r){r.get("hideMessage").call(t,null,r.element)}),t.trigger("formValidate",t.element),s[t.get("stopOnError")?"forEachSeries":"forEach"](t.items,function(e,i){e.execute(function(e,s,l){e&&!a&&(a=!0,n=l),r.push([].slice.call(arguments,0)),i(t.get("stopOnError")?e:null)})},function(){t.get("autoFocus")&&a&&(t.trigger("autoFocus",n),n.focus()),t.trigger("formValidated",a,r,t.element),e&&e(a,r,t.element)}),t},destroy:function(){var e=this,t=e.items.length;if(e.element.is("form")){try{void 0==e._novalidate_old?e.element.removeAttr("novalidate"):e.element.attr("novalidate",e._novalidate_old)}catch(r){}e.element.off("submit.validator")}for(var n=t-1;n>=0;n--)e.removeItem(e.items[n]);a(e,c),d.superclass.destroy.call(this)},query:function(e){return n(this.$(e),this.items)}});r.exports=d}),define("arale-validator/0.10.0/src/async",[],function(e,t,r){var a={};r.exports=a;var n=function(e,t){if(e.forEach)return e.forEach(t);for(var r=0;r<e.length;r+=1)t(e[r],r,e)},i=function(e,t){if(e.map)return e.map(t);var r=[];return n(e,function(e,a,n){r.push(t(e,a,n))}),r},s=function(e){if(Object.keys)return Object.keys(e);var t=[];for(var r in e)e.hasOwnProperty(r)&&t.push(r);return t};a.forEach=function(e,t,r){if(r=r||function(){},!e.length)return r();var a=0;n(e,function(n){t(n,function(t){t?(r(t),r=function(){}):(a+=1,a===e.length&&r(null))})})},a.forEachSeries=function(e,t,r){if(r=r||function(){},!e.length)return r();var a=0,n=function(){t(e[a],function(t){t?(r(t),r=function(){}):(a+=1,a===e.length?r(null):n())})};n()};var l=function(e){return function(){var t=Array.prototype.slice.call(arguments);return e.apply(null,[a.forEach].concat(t))}},u=function(e){return function(){var t=Array.prototype.slice.call(arguments);return e.apply(null,[a.forEachSeries].concat(t))}},o=function(e,t,r,a){var n=[];t=i(t,function(e,t){return{index:t,value:e}}),e(t,function(e,t){r(e.value,function(r,a){n[e.index]=a,t(r)})},function(e){a(e,n)})};a.map=l(o),a.mapSeries=u(o),a.series=function(e,t){if(t=t||function(){},e.constructor===Array)a.mapSeries(e,function(e,t){e&&e(function(e){var r=Array.prototype.slice.call(arguments,1);r.length<=1&&(r=r[0]),t.call(null,e,r)})},t);else{var r={};a.forEachSeries(s(e),function(t,a){e[t](function(e){var n=Array.prototype.slice.call(arguments,1);n.length<=1&&(n=n[0]),r[t]=n,a(e)})},function(e){t(e,r)})}}}),define("arale-validator/0.10.0/src/utils",["jquery/2.1.1/jquery"],function(require,exports,module){function unique(){return"__anonymous__"+u_count++}function parseRules(e){return e?e.match(/[a-zA-Z0-9\-\_]+(\{[^\{\}]*\})?/g):null}function parseDom(e){var e=$(e),t={},r=[],a=e.attr("required");a&&(r.push("required"),t.required=!0);var n=e.attr("type");if(n&&"submit"!=n&&"cancel"!=n&&"checkbox"!=n&&"radio"!=n&&"select"!=n&&"select-one"!=n&&"file"!=n&&"hidden"!=n&&"textarea"!=n){if(!Rule.getRule(n))throw new Error('Form field with type "'+n+'" not supported!');r.push(n)}var i=e.attr("min");i&&r.push('min{"min":"'+i+'"}');var s=e.attr("max");s&&r.push("max{max:"+s+"}");var l=e.attr("minlength");l&&r.push("minlength{min:"+l+"}");var u=e.attr("maxlength");u&&r.push("maxlength{max:"+u+"}");var o=e.attr("pattern");if(o){var c=new RegExp(o),f=unique();Rule.addRule(f,c),r.push(f)}var d=e.attr("data-rule");return d=d&&parseRules(d),d&&(r=r.concat(d)),t.rule=0==r.length?null:r.join(" "),t}function parseJSON(str){function getValue(str){return'"'==str.charAt(0)&&'"'==str.charAt(str.length-1)||"'"==str.charAt(0)&&"'"==str.charAt(str.length-1)?eval(str):str}if(!str)return null;var NOTICE='Invalid option object "'+str+'".';str=str.slice(1,-1);var result={},arr=str.split(",");return $.each(arr,function(e,t){if(arr[e]=$.trim(t),!arr[e])throw new Error(NOTICE);var r=arr[e].split(":"),a=$.trim(r[0]),n=$.trim(r[1]);if(!a||!n)throw new Error(NOTICE);result[getValue(a)]=$.trim(getValue(n))}),result}function isHidden(e){var t=e[0].offsetWidth,r=e[0].offsetHeight,a="TR"===e.prop("tagName");return 0!==t||0!==r||a?0===t||0===r||a?"none"===e.css("display"):!1:!0}var $=require("jquery/2.1.1/jquery"),Rule=require("arale-validator/0.10.0/src/rule"),u_count=0,helpers={};module.exports={parseRule:function(e){var t=e.match(/([^{}:\s]*)(\{[^\{\}]*\})?/);return{name:t[1],param:parseJSON(t[2])}},parseRules:parseRules,parseDom:parseDom,isHidden:isHidden,helper:function(e,t){return t?(helpers[e]=t,this):helpers[e]}}}),define("arale-validator/0.10.0/src/rule",["jquery/2.1.1/jquery"],function(e,t,r){function a(e,t){var r=this;if(r.name=e,t instanceof RegExp)r.operator=function(e,r){var a=t.test(o(e.element).val());r(a?null:e.rule,i(e,a))};else{if(!o.isFunction(t))throw new Error("The second argument must be a regexp or a function.");r.operator=function(e,r){var a=t.call(this,e,function(t,a){r(t?null:e.rule,a||i(e,t))});void 0!==a&&r(a?null:e.rule,i(e,a))}}}function n(e,t,r){return o.isPlainObject(e)?(o.each(e,function(e,t){o.isArray(t)?n(e,t[0],t[1]):n(e,t)}),this):(c[e]=t instanceof a?new a(e,t.operator):new a(e,t),s(e,r),this)}function i(e,t){var r,a=e.rule;return e.message?o.isPlainObject(e.message)?(r=e.message[t?"success":"failure"],"undefined"==typeof r&&(r=f[a][t?"success":"failure"])):r=t?"":e.message:r=f[a][t?"success":"failure"],r?u(e,r):r}function s(e,t){return o.isPlainObject(e)?(o.each(e,function(e,t){s(e,t)}),this):(f[e]=o.isPlainObject(t)?t:{failure:t},this)}function l(e,t){if(t){var r=c[e];return new a(null,function(e,a){r.operator(o.extend(null,e,t),a)})}return c[e]}function u(e,t){var r=t,a=/\{\{[^\{\}]*\}\}/g,n=/\{\{(.*)\}\}/,i=t.match(a);return i&&o.each(i,function(t,a){var i=a.match(n)[1],s=e[o.trim(i)];r=r.replace(a,s)}),r}var o=e("jquery/2.1.1/jquery"),c={},f={};a.prototype.and=function(e,t){var r=e instanceof a?e:l(e,t);if(!r)throw new Error('No rule with name "'+e+'" found.');var n=this,s=function(e,t){n.operator.call(this,e,function(a){a?t(a,i(e,!a)):r.operator.call(this,e,t)})};return new a(null,s)},a.prototype.or=function(e,t){var r=e instanceof a?e:l(e,t);if(!r)throw new Error('No rule with name "'+e+'" found.');var n=this,s=function(e,t){n.operator.call(this,e,function(a){a?r.operator.call(this,e,t):t(null,i(e,!0))})};return new a(null,s)},a.prototype.not=function(e){var t=l(this.name,e),r=function(e,r){t.operator.call(this,e,function(t){t?r(null,i(e,!0)):r(!0,i(e,!1))})};return new a(null,r)},n("required",function(e){var t=o(e.element),r=t.attr("type");switch(r){case"checkbox":case"radio":var a=!1;return t.each(function(e,t){return o(t).prop("checked")?(a=!0,!1):void 0}),a;default:return Boolean(o.trim(t.val()))}},"请输入{{display}}"),n("email",/^\s*([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,20})\s*$/,"{{display}}的格式不正确"),n("text",/.*/),n("password",/.*/),n("radio",/.*/),n("checkbox",/.*/),n("url",/^(http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?$/,"{{display}}的格式不正确"),n("number",/^[+-]?[1-9][0-9]*(\.[0-9]+)?([eE][+-][1-9][0-9]*)?$|^[+-]?0?\.[0-9]+([eE][+-][1-9][0-9]*)?$/,"{{display}}的格式不正确"),n("digits",/^\s*\d+\s*$/,"{{display}}的格式不正确"),n("date",/^\d{4}\-[01]?\d\-[0-3]?\d$|^[01]\d\/[0-3]\d\/\d{4}$|^\d{4}年[01]?\d月[0-3]?\d[日号]$/,"{{display}}的格式不正确"),n("min",function(e){var t=e.element,r=e.min;return Number(t.val())>=Number(r)},"{{display}}必须大于或者等于{{min}}"),n("max",function(e){var t=e.element,r=e.max;return Number(t.val())<=Number(r)},"{{display}}必须小于或者等于{{max}}"),n("minlength",function(e){var t=e.element,r=t.val().length;return r>=Number(e.min)},"{{display}}的长度必须大于或等于{{min}}"),n("maxlength",function(e){var t=e.element,r=t.val().length;return r<=Number(e.max)},"{{display}}的长度必须小于或等于{{max}}"),n("mobile",/^1\d{10}$/,"请输入正确的{{display}}"),n("confirmation",function(e){var t=e.element,r=o(e.target);return t.val()==r.val()},"两次输入的{{display}}不一致，请重新输入"),r.exports={addRule:n,setMessage:s,getMessage:function(e,t){return i(e,t)},getRule:l,getOperator:function(e){return c[e].operator}}}),define("arale-validator/0.10.0/src/item",["jquery/2.1.1/jquery","arale-widget/1.2.0/widget","arale-base/1.2.0/base","arale-class/1.2.0/class","arale-events/1.2.0/events"],function(e,t,r){function a(e){return(" "+e+" ").indexOf(" required ")>=0}function n(e,t,r){var a=l.extend({},e,{element:r.element,display:e&&e.display||r.get("display"),rule:t}),n=r.get("errormessage")||r.get("errormessage"+i(t));return n&&!a.message&&(a.message={failure:n}),a}function i(e){return e+="",e.charAt(0).toUpperCase()+e.slice(1)}function s(e,t,r){var a=e.element;if(!e.get("required")){var i=!1,s=a.attr("type");switch(s){case"checkbox":case"radio":var o=!1;a.each(function(e,t){return l(t).prop("checked")?(o=!0,!1):void 0}),i=o;break;default:i=!!a.val()}if(!i)return void(r&&r(null,null))}if(!l.isArray(t))throw new Error("No validation rule specified or not specified as an array.");var d=[];l.each(t,function(t,r){var a=u.parseRule(r),i=a.name,s=a.param,l=f.getOperator(i);if(!l)throw new Error('Validation rule with name "'+i+'" cannot be found.');var o=n(s,i,e);d.push(function(t){l.call(e._validator,o,t)})}),c.series(d,function(e,t){r&&r(e,t[t.length-1])})}var l=e("jquery/2.1.1/jquery"),u=e("arale-validator/0.10.0/src/utils"),o=e("arale-widget/1.2.0/widget"),c=e("arale-validator/0.10.0/src/async"),f=e("arale-validator/0.10.0/src/rule"),d={value:l.noop,setter:function(e){return l.isFunction(e)?e:u.helper(e)}},m=o.extend({attrs:{rule:{value:"",getter:function(e){return e=l.trim(e),this.get("required")?e&&a(e)||(e=l.trim("required "+e)):a(e)&&(e=l.trim((" "+e+" ").replace(" required "," "))),e}},display:null,displayHelper:null,triggerType:{getter:function(e){if(!e)return e;var t=this.element,r=t.attr("type"),a=t.is("select")||"radio"==r||"checkbox"==r;return a&&(e.indexOf("blur")>-1||e.indexOf("key")>-1)?"change":e}},required:{value:!1,getter:function(e){return l.isFunction(e)?e():e}},checkNull:!0,errormessage:null,onItemValidate:d,onItemValidated:d,showMessage:d,hideMessage:d},setup:function(){!this.get("display")&&l.isFunction(this.get("displayHelper"))&&this.set("display",this.get("displayHelper")(this))},execute:function(e,t){var r=this,a=!!r.element.attr("disabled");if(t=t||{},r.get("skipHidden")&&u.isHidden(r.element)||a)return e&&e(null,"",r.element),r;r.trigger("itemValidate",r.element,t.event);var n=u.parseRules(r.get("rule"));return n?s(r,n,function(a,n){r.trigger("itemValidated",a,n,r.element,t.event),e&&e(a,n,r.element)}):e&&e(null,"",r.element),r},getMessage:function(e,t,r){var a="",i=this,s=u.parseRules(i.get("rule"));return t=!!t,l.each(s,function(s,o){var c=u.parseRule(o),d=c.name,m=c.param;e===d&&(a=f.getMessage(l.extend(r||{},n(m,d,i)),t))}),a}});r.exports=m});