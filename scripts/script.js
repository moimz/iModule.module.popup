/**
 * 이 파일은 iModule 팝업모듈 일부입니다. (https://www.imodules.io)
 *
 * 팝업창을 띄운다.
 * 
 * @file /modules/popup/scripts/script.js
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 9. 19.
 */
var Popup = {
	isError:false,
	lastWindowPosition:{x:0,y:0},
	maxWindowHeight:0,
	lastLayerPosition:{x:0,y:0},
	maxLayerHeight:0,
	startWindowPosition:{x:0,y:0},
	startLayerPosition:{x:0,y:0},
	get:function() {
		if (iModule.isMobile == true) return;
		
		$.send(ENV.getProcessUrl("popup","getPopups"),function(result) {
			if (result.success == true) {
				Popup.startWindowPosition.x = Popup.lastWindowPosition.x = result.window.x;
				Popup.startWindowPosition.y = Popup.lastWindowPosition.y = result.window.y;
				Popup.startLayerPosition.x = Popup.lastLayerPosition.x = result.layer.x;
				Popup.startLayerPosition.y = Popup.lastLayerPosition.y = result.layer.y;
				
				
				for (var i=0, loop=result.popups.length;i<loop;i++) {
					var popup = result.popups[i];
					if (iModule.getCookie("ModulePopupDisable-"+popup.idx) == "TRUE") continue;
					
					if (popup.position == "AUTO") {
						var position = Popup.position(popup.window,popup.width,popup.height);
					} else {
						var position = {x:popup.x,y:popup.y};
					}
					
					if (popup.window == "WINDOW") {
						Popup.open(popup.idx,position.x,position.y,popup.width,popup.height,popup.html);
					} else {
						Popup.show(popup.idx,position.x,position.y,popup.width,popup.height,popup.html);
					}
				}
			}
		});
	},
	position:function(type,width,height) {
		if (type == "WINDOW") {
			if (screen.width > Popup.lastWindowPosition.x + width) {
				var x = Popup.lastWindowPosition.x;
				Popup.lastWindowPosition.x+= width;
				var y = Popup.lastWindowPosition.y;
			} else {
				var x = Popup.startWindowPosition.x;
				Popup.lastWindowPosition.x+= width;
				Popup.lastWindowPosition.y+= Popup.maxWindowHeight;
				var y = Popup.lastWindowPosition.y;
				Popup.maxWindowHeight = 0;
			}
			
			Popup.maxWindowHeight = Math.max(Popup.maxWindowHeight,height);
		} else {
			if ($(window).width() > Popup.lastLayerPosition.x + width + 5) {
				var x = Popup.lastLayerPosition.x;
				Popup.lastLayerPosition.x+= width + 5;
				var y = Popup.lastLayerPosition.y;
			} else {
				var x = Popup.startLayerPosition.x;
				Popup.lastLayerPosition.x+= width + 5;
				Popup.lastLayerPosition.y+= Popup.maxLayerHeight;
				var y = Popup.lastLayerPosition.y;
				Popup.maxLayerHeight = 0;
			}
			
			Popup.maxLayerHeight = Math.max(Popup.maxLayerHeight + 5,height);
		}
		
		return {x:x,y:y};
	},
	open:function(idx,x,y,width,height,html) {
		if (y + height > screen.height) y = screen.height - height;
		
		var opener = window.open(ENV.getModuleUrl("popup","window",idx),"ModulePopup-"+idx,"top="+y+",left="+x+",width="+width+",height="+height+",scrollbars=0");
		
		if (!opener && Popup.isError == false) {
			if (html !== null) {
				var position = Popup.position("LAYER",width,height);
				Popup.show(idx,position.x,position.y,width,height,html);
			} else {
				iModule.alert.show("error","팝업이 차단되었습니다. 브라우저 설정에서 팝업허용을 해주시기 바랍니다.",5);
				Popup.isError = true;
			}
		}
	},
	show:function(idx,x,y,width,height,html) {
		var $html = $(html);
		
		$html.addClass("layer");
		$html.css("left",x+"px");
		$html.css("top",y+"px");
		
		$("button[data-action=close]",$html).on("click",function() {
			Popup.close($html.attr("data-idx"));
		});
		
		$("body").append($html);
	},
	resize:function(width,height) {
		var resizeWidth = width - $(window).width();
		var resizeHeight = height - $(window).height();
		
		window.resizeBy(resizeWidth,resizeHeight);
	},
	close:function(idx) {
		var $popup = $("div[data-module=popup][data-idx="+idx+"]");
		iModule.setCookie("ModulePopupDisable-"+idx,"TRUE",60 * 60 * 24);
		if ($popup.attr("data-window") == "WINDOW") {
			self.close();
		} else {
			$popup.remove();
		}
	}
};

$(document).ready(function() {
	if (parent === undefined || opener === undefined || $("div[data-module=popup]").length == 0) Popup.get();
	
	$("div[data-module=popup]").each(function() {
		var $popup = $(this);
		$("button[data-action=close]",$(this)).on("click",function() {
			Popup.close($popup.attr("data-idx"));
		});
	})
});