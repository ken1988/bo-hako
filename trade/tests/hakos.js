  $(function() {
		$("#accordion").accordion();
		
		$("#sortable").sortable({
			placeholder: 'ui-state-highlight'
		});
		$("#sortable").disableSelection();
		
		$("table").hide();
		
		$("button").click(function(){
			$("table").slideDown();
		});
});
