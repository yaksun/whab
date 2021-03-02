$(document).ready(function(){

var $tab_li = $('.lib_Menubox_sx ul li');

$tab_li.hover(function(){

$(this).addClass('hover').siblings().removeClass('hover');

   var index = $tab_li.index(this);

$('div.lib_Contentbox_sx > div').eq(index).show().siblings().hide();

});

});


/**
 * 交流园地
 * **/
$(document).ready(function(){

var $tab_li = $('.lib_Menubox_sx_4 ul li');

$tab_li.hover(function(){

$(this).addClass('hover').siblings().removeClass('hover');

   var index = $tab_li.index(this);

$('div.lib_Contentbox_sx_4 > div').eq(index).show().siblings().hide();

});

});

/**第1位置竖型选项卡
function setTab1(name,cursel,n){
	for(i=1;i<=n;i++){
		var menu=document.getElementById(name+i);
		var con=document.getElementById("con_"+name+"_"+i);
		
		var aa=document.getElementById("a"+i);
		aa.className=i==cursel?"hover":"";
		menu.className=i==cursel?"hover":"";
		con.style.display=i==cursel?"block":"none";
	}
}

/**第2位置竖型选项卡
function setTab2(name,cursel,n){
	for(i=1;i<=n;i++){
		var menu=document.getElementById(name+i);
		var con=document.getElementById("con_"+name+"_"+i);
		var aa=document.getElementById("aTwo"+i);
		aa.className=i==cursel?"hover":"";
		menu.className=i==cursel?"hover":"";
		con.style.display=i==cursel?"block":"none";
	}
}

/**第3位置竖型选项卡
function setTab3(name,cursel,n){
	for(i=1;i<=n;i++){
		var menu=document.getElementById(name+i);
		var con=document.getElementById("con_"+name+"_"+i);
		var aa=document.getElementById("aThree"+i);
		aa.className=i==cursel?"hover":"";
		menu.className=i==cursel?"hover":"";
		con.style.display=i==cursel?"block":"none";
	}
}
*/
/**第4位置竖型选项卡
function setTab4(name,cursel,n){
	for(i=1;i<=n;i++){
		var menu=document.getElementById(name+i);
		var con=document.getElementById("con_"+name+"_"+i);
		var aa=document.getElementById("aFour"+i);
		aa.className=i==cursel?"hover":"";
		menu.className=i==cursel?"hover":"";
		con.style.display=i==cursel?"block":"none";
	}
}*/