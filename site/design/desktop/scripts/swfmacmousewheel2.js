/**
 * SWFMacMouseWheel v2.0: Mac Mouse Wheel functionality in flash - http://blog.pixelbreaker.com/
 *
 * SWFMacMouseWheel is (c) 2007 Gabriel Bucknall and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Modded by Robert M. Hall - rhall@impossibilities.com 
 * Adjusted following functionality:
 * 1. Watch for events related only to flash content and not the container page
 * 2. Fixed to dispatch mousewheel events only to specific intance ID's that were registered, this
 *    allows multiple SWFobject embeds on a page to use swfmacmousewheel and they will only respond
 *    to their specific target ID's when the event.target.is passed. This way only the currently active item will receive a dispatch event.
 * 3. Works with SWFObject 2.1
 * 4. No longer throws an error on IE/PC platforms because of a null object
 * 5. Works on Safari for PC
 *
 * Dependencies: 
 * SWFObject v2.1 <http://code.google.com/p/swfobject/>
 * Copyright (c) 2007 Geoff Stearns, Michael Williams, and Bobby van der Sluis
 * This software is released under the MIT License <http://www.opensource.org/licenses/mit-license.php>
 *
 * Requires a few lines of changes to the AS2 and AS3 code to support the PC version of Safari
 * as well as an additional Flashvar paramater, set flashvars.browser = Browser.name;
 * These changes are only required for Safari on PC - all other modifications noted above are contained solely in this JavaScript
 * Safari PC support based on code/suggestions from Richard "RaillKill" Rodney of Hypermedia - http://railkill.free.fr/
 *
 * Browser detect part from http://www.quirksmode.org/js/detect.html
 *
 */
 
var Browser = { init:function() {
this.name = this.searchString(this.dataBrowser) || "unknown" },
	searchString:function(data){
	for(var A=0;A<data.length;A++){ 
	var B=data[A].string;
	var C=data[A].prop;
	this.versionSearchString=data[A].versionSearch || data[A].identity;
	if(B){
		if(B.indexOf(data[A].subString)!=-1){ 
			return data[A].identity
			}
		} else if (C) {
				return data[A].identity 
					}
			}
		},
		dataBrowser:[
			{
				string:navigator.vendor,
				subString:"Apple",
				identity:"Safari"
			}]};
			
Browser.init();

var swfmacmousewheel = function(){
			if(!swfobject)return null;
			
			var u=navigator.userAgent.toLowerCase();
			var p=navigator.platform.toLowerCase();
			
			var d=p?/mac/.test(p):/mac/.test(u);
			// alert(Browser.name);
			if( Browser.name != "Safari" && !d) return { registerObject:function() {} };
		
		var k = [];
		
		var r = function(event){
			var o=0;
			if(event.wheelDelta){
			o=event.wheelDelta/120;
			if(window.opera)o= -o;
			if(Browser.name=="Safari")o=o*3;}
				else if(event.detail) { o= -event.detail;
			}
				if(event.preventDefault) { 
				event.preventDefault(); }
			return o;
		};
			
		var __wheel = function(event){
	
			if(event.target.id == "" || event.target.id == undefined) {
				return; 
			} else {
				var o = r(event);
				var c;
				var tmpI = null;
				for(var i=0;i<k.length;i++){
				c = swfobject.getObjectById(k[i]);
					if(typeof(c.externalMouseEvent) == 'function' && event.target.id == k[i]) {
						tmpI=i;
					}
				}
				if(tmpI !=null) {
				c = swfobject.getObjectById(k[tmpI]);
				c.externalMouseEvent(o);	
				} else {
				window.scrollBy(0,-o);
				}
				
	
			}
		};
		
		return{ 
			registerObject:function(m)
			{
				k.push(m);
				if(window.addEventListener)window.addEventListener('DOMMouseScroll',__wheel,false);
				window.onmousewheel = document.onmousewheel = __wheel;
			}
		};
}();