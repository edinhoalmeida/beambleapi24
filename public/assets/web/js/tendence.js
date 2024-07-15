var tendence;
function tendencessssssss(){

    const tendence = {
	  user_id: '',
	  name: '',
	  surname: '',
	  city: '',
	  country: '',
	  beamer_type: 'classic',
	  beamer_cost: ''
	};
	const getChangeHandler = (name) => (e) => formState[name] = e.target.value
}
var tendenceClass = function ()
{
	var recipiente=null;
	var div = "#tendence_rec";
	var tendenceTpl=null;
	var tendenceTplHtml=null;
	var obj = {
		inicializa: function () {

			tendenceTpl = document.getElementById("template-tendence");
			if(tendenceTpl){
				tendenceTplHtml = tendenceTpl.innerHTML;
			}
			recipiente = $(div);
		},
		clear: function(){
			if(recipiente.length){
				recipiente.find('div').empty().remove();
			}
		},
		display: function(tendences){
			obj.clear();
			for(var key in tendences){
				var td = tendenceTplHtml.replace(/{user_id}/g, tendences[key]["id"])
									  .replace(/{name}/g, tendences[key]["name"])
									  .replace(/{surname}/g, tendences[key]["surname"])
									  .replace(/{beamer_type}/g, tendences[key]["beamer_type"])
									  .replace(/{beamer_cost}/g, tendences[key]["beamer_cost"])
									  .replace(/{city}/g, tendences[key]["city"])
									  .replace(/{country}/g, tendences[key]["country"]);
				recipiente.append(td);
		    }
		}
	};
	obj.inicializa();
	return obj;
};
$(document).ready(function(){
	tendence = tendenceClass();
});

