$(document).ready(function(){
	$('.registerform').on('focus', 'input, select', function( event ){
		var rec = $(this).parent('.form-group');
		rec.find('.alert').hide( "slow", function() {
		    
		});
	});
	$('.registerform').on('submit', function( event ){
		var dados = $( this ).serialize();
		$.ajax({
			method: 'POST',
			dataType: "json",
			url: $( this ).attr('action'),
			data: dados,
			success: function( volta ) {
				console.log(volta);
				if(volta.success===false){
					for(var i in volta.data){
						var div_e = $('<div/>').addClass('alert, alert-danger');
							div_e.html(volta.data[i][0] + '<br>');
						$("#"+i).after(div_e);
					}
				}
				if(volta.success===true){
					
				}
			}
		});
		event.preventDefault();
	});
});
