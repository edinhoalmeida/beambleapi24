var inbox;
var buscando_timer = false;
var inboxClass = function ()
{
	var recipiente=null;
	var div = "#inbox_msg";
	var Tpl=null;
	var TplHtml=null;
	var beamer_id = null, client_id = null;
	var obj = {
		inicializa: function () {
			Tpl = document.getElementById("template-mensage");
			if(Tpl){
				TplHtml = Tpl.innerHTML;
			}
			recipiente = $(div);
			beamer_id = recipiente.attr('data-beamer_id');
			client_id = recipiente.attr('data-client_id');
			obj.search();
		},
		clear: function(){
			if(recipiente.length){
				recipiente.find('div').empty().remove();
			}
		},
		display: function(object){
			obj.clear();
			for(var key in object){
				var is_me_class = object[key]["is_me"]=='yes' ? 'iclient' : 'ibeamer';
				var td = TplHtml.replace(/{msg_id}/g, object[key]["id"])
									  .replace(/{status}/g, object[key]["status"])
									  .replace(/{class}/g, is_me_class)
									  .replace(/{content}/g, object[key]["message"]);
				recipiente.append(td);
		    }
		},
		search: function(){
			if(buscando_timer) return;
		    $.ajax({
		        method: 'GET',
		        dataType: "json",
		        url: '/apiweb/inbox/'+client_id+'/'+beamer_id,
		        success: function( data ) {
		            inbox.display(data.data.messages);
		        }
		    });
		    buscando_timer = true;
		    setTimeout(function(){
		        buscando_timer = false;
		    },1000);
		},

	};
	obj.inicializa();
	setInterval(function() {
		obj.search();
	}, 2000);
	return obj;
};
$(document).ready(function(){
	inbox = inboxClass();
});

