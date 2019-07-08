function send(url_var) {
    var DSLScript = document.createElement("script");
    DSLScript.src = url_var;
    DSLScript.type = "text/javascript";
    document.body.appendChild(DSLScript);
    document.body.removeChild(DSLScript);
}

function changementPouce(id_pouce, id) {
    var url = document.getElementById(id_pouce).src;
	
	if(url.includes("vert_rempli")){
		document.getElementById(id_pouce).src="res/img/pouce_vert.png";
		changementProgressBar(id,"-","");
	}
	else if(url.includes("vert")){
		if(document.getElementById("dislike"+id).src.includes("rouge_rempli")){
			document.getElementById("dislike"+id).src = "res/img/pouce_rouge.png";
			changementProgressBar(id,"+","-");
		}
		else{
			changementProgressBar(id,"+","");
		}
		document.getElementById(id_pouce).src="res/img/pouce_vert_rempli.png";
	}
	else if(url.includes("rouge_rempli")){
		document.getElementById(id_pouce).src="res/img/pouce_rouge.png";
		changementProgressBar(id,"","-");
	}
	else if(url.includes("rouge")){
		if(document.getElementById("like"+id).src.includes("vert_rempli")){
			document.getElementById("like"+id).src = "res/img/pouce_vert.png";
			changementProgressBar(id,"-","+");
		}
		else{
			changementProgressBar(id,"","+");
		}
		document.getElementById(id_pouce).src="res/img/pouce_rouge_rempli.png";
	}
}

function changementProgressBar(id,cptLike,cptDislike){

	//Changement compteur
	var id_progress_bar_like = "cptlike" + id;
	var id_progress_bar_dislike = "cptdislike" + id;

	if(cptLike == "+"){
		document.getElementById(id_progress_bar_like).innerHTML = parseInt(document.getElementById(id_progress_bar_like).innerHTML) + 1;
		if(cptDislike == "-"){
			document.getElementById(id_progress_bar_dislike).innerHTML = parseInt(document.getElementById(id_progress_bar_dislike).innerHTML) - 1;
		}
	}
	else if(cptLike == "-"){
		document.getElementById(id_progress_bar_like).innerHTML = parseInt(document.getElementById(id_progress_bar_like).innerHTML) - 1;
		if(cptDislike == "+"){
			document.getElementById(id_progress_bar_dislike).innerHTML = parseInt(document.getElementById(id_progress_bar_dislike).innerHTML) + 1;
		}
	}
	else if(cptLike == "" && cptDislike == "+"){
		document.getElementById(id_progress_bar_dislike).innerHTML = parseInt(document.getElementById(id_progress_bar_dislike).innerHTML) + 1;
	}
	else if(cptLike == "" && cptDislike == "-"){
		document.getElementById(id_progress_bar_dislike).innerHTML = parseInt(document.getElementById(id_progress_bar_dislike).innerHTML) - 1;
	}

	//Changement Barre
	var nb_like = parseInt(document.getElementById(id_progress_bar_like).innerHTML)
	var nb_dislike = parseInt(document.getElementById(id_progress_bar_dislike).innerHTML);
	if((nb_like + nb_dislike) == 0){
		document.getElementById("progressbar" + id).style.width = "50%";
	}
	else{
		document.getElementById("progressbar" + id).style.width = (100*nb_like/(nb_like+nb_dislike))+"%";
	}

}

function traitementlike(url_var,id_pouce,id){
	send(url_var);
	changementPouce(id_pouce,id);
}

function alertNonConnecte(){
	alert("Veuillez vous connecter pour accéder à cette fonctionnalité");
}

function callback(message){
	alert(message);
}