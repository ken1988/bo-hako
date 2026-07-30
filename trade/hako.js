//======================================================================
// モバイル用グローバルメニュー（jQuery非依存）
//======================================================================
(function () {
  'use strict';

  function initializeGlobalMenu() {
    var toggle = document.querySelector('.global-menu-toggle');
    var menu = document.getElementById('global-menu');

    if (!toggle || !menu) {
      return;
    }

    function closeMenu(returnFocus) {
      toggle.setAttribute('aria-expanded', 'false');
      menu.classList.remove('is-open');

      if (returnFocus) {
        toggle.focus();
      }
    }

    toggle.addEventListener('click', function () {
      var shouldOpen = toggle.getAttribute('aria-expanded') !== 'true';
      toggle.setAttribute('aria-expanded', String(shouldOpen));
      menu.classList.toggle('is-open', shouldOpen);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        closeMenu(true);
      }
    });

    window.addEventListener('resize', function () {
      if (window.matchMedia('(min-width: 701px)').matches) {
        closeMenu(false);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeGlobalMenu);
  } else {
    initializeGlobalMenu();
  }
}());

//======================================================================
// 開発画面用
//======================================================================
function Navi(position, img, title, pos, text, exp) {
  StyElm = document.getElementById("NaviView");
  StyElm.innerHTML = "<div id='NaviTitle'>" + pos + title + "<\/div><img class='NaviImg' src=" + img + "><div class='NaviText'>" + text.replace("\n", "<br>") + "<\/div>";
  if(exp) {
    StyElm.innerHTML += "<div class='NaviText'>" + eval(exp) + "<\/div>";
  }
}
//======================================================================
$(function(){
	// モバイルではグローバルメニューを必要なときだけ開く。
	var $globalMenuToggle = $('.global-menu-toggle');
	var $globalMenu = $('#global-menu');

	$globalMenuToggle.on('click', function() {
		var isOpen = $(this).attr('aria-expanded') === 'true';
		$(this).attr('aria-expanded', String(!isOpen));
		$globalMenu.toggleClass('is-open', !isOpen);
	});

	$(document).on('keydown', function(event) {
		if (event.key === 'Escape' && $globalMenuToggle.attr('aria-expanded') === 'true') {
			$globalMenuToggle.attr('aria-expanded', 'false').trigger('focus');
			$globalMenu.removeClass('is-open');
		}
	});

	$(window).on('resize', function() {
		if (window.matchMedia('(min-width: 701px)').matches) {
			$globalMenuToggle.attr('aria-expanded', 'false');
			$globalMenu.removeClass('is-open');
		}
	});

	$('#new_reg').exValidation();
	
	$("input.limited").exValidation();
	
	$("#accordion").accordion();
	
	$("#sortable").sortable({
		placeholder: 'ui-state-highlight'
	});
	
	$("#sortable").disableSelection();
	
	$("#latestnews").liScroll();
	
	$("textarea.limited").maxlength({
          'feedback': '#charsLeft1'
      });
    
    $("input.limited").maxlength({
          'feedback': '#charsLeft2'
      });
    
 	$("textarea.limited").autoResize({
		onResize : function() {
		$(this).css({opacity:1});
		},
		animateCallback : function() {
		$(this).css({opacity:1});
		},
	    extraSpace : 10,
	    limit : 100
		});

	//make some charts
	$('#wstattable').visualize({type: 'line',width: '700px', title: '砲弾生産',colFilter: ':last-child',parseDirection: 'y'});
	$('#wstattable').visualize({type: 'line',width: '700px', title: '産業統計',colFilter: ':gt(3):not(:last-child)',parseDirection: 'y'});
	$('#wstattable').visualize({type: 'line',width: '700px', title: '人口統計',colFilter: ':lt(4)',parseDirection: 'y'});

	$('.show_table').click(function(){
	var num = Number(this.id);
	var options = {
	    title: '収支レポート',
	    width: 300,
	    height:400,
	    bar: {groupWidth: "100%"},
	    legend: { position: "none"},
	    isStacked: true
	};
	
	var drawChart = function() {
		if (!window.balanceReportData || !window.balanceReportData[num]) {
			return;
		}

		var data = google.visualization.arrayToDataTable(window.balanceReportData[num]);
		var chart = new google.visualization.ColumnChart(document.getElementById('show_graph'));
		chart.draw(data, options);
	};

	// Google Charts のパッケージ読み込みは非同期のため、完了後に描画する。
	if (google.visualization && google.visualization.ColumnChart) {
		drawChart();
	} else {
		google.charts.setOnLoadCallback(drawChart);
	}
	});


    $("#sel_val").change(function(){
   		var intunit = $('#sel_kind').val();
   		var send_num = $('#sel_val').val();
   		var units = "";

   		switch(intunit){
   			case "71":
   				units = "億Va";
   				break;
   			case "72":
  				units = "万トン";
   				break;
   			case "73":
   				units = "億Va";
   				break;
   			case "74":
	   		   	units = "ガロン";
   				break;
   			case "75":
	   		   	units = "万トン";
   				break;
   			case "76":
	   		   	units = "万トン";
   				break;
   			case "77":
	   		   	units = "万トン";
   				break;
   			case "78":
	   		   	units = "万トン";
   				break;
   			case "79":
	   		   	units = "トン";
   				break;
   			case 80:
	   		   	units = "バレル";
   				break;
   			case 81:
	   		   	units = "ガロン";
   				break;
   			case 82:
	   		   	units = "万トン";
   				break;
   			case 83:
	   		   	units = "メガトン";
   				break;
   			default:
   				break;

   		}
   		
   		if(intunit != "72"){
   		   intunit = 500;
   		}else{
   			intunit = 1000;
   		}
   		$("#conf").prop("checked",false);
   		
   		var gross_num = intunit * send_num;
    	$("#gross")
		.val($("#gross").text(gross_num+units))
		});
	
	$("#news_post").click(function(){
		var news_text = $("#news_text").val();
		if(confirm("投稿内容を確認して下さい。\n" + news_text)){
		
		}
		else {
            return false;
		}
	});
	
	$("#conf").click(function(){
   		var send_num = $('#sel_val').val();
   		var tmpe = send_num.match(/[0-9]+/g);
   		
   		if (tmpe!=send_num){
            alert("数量は半角数字で指定してください。");
  			$('#sel_val').prop("value",0);
            $("#conf").prop("checked",false);
            return false;
        }
        if (tmpe < 0 || tmpe > 600){
  			alert("数量は0-600の範囲で指定してください。");
  			$('#sel_val').prop("value",0);
  			$("#conf").prop("checked",false);
  			return false;
  		}
	});
	
	$("#regtsb").click(function(){
		if($("#conf").prop('checked')) {
		   }else{
		   	alert("確認欄にチェックを入れてください。");
		   	return false;
		   }
	});
	
	$("#islandMap table").mouseover(function(){
		if($("#NaviView").css("display")!="block"){
			$("#NaviView").show();
		}
	}).mouseout(function(){
		$("#NaviView").hide();
	}).mousemove(function(e){
		$("#NaviView").css({
			"top":e.pageY+10+"px",
			"left":e.pageX+10+"px"
		});
	});
	
	$("li.IndD").mouseover(function(){
		tid = $(this).parent().attr('id');
		$("div.tooltip[id="+tid+"]").fadeIn();
		$("div.tooltip[id="+tid+"]").html($(".databox",this).html());
	}).click(function(){
		return false;
	});
});
