function init(){

}

$(document).ready(function() {
			
});
/* CONDICIÓN CAMBIO DE ACCESO USUARIO A ACCESO SOPORTE */
$(document).on("click","#btnsoporte",function(){
    if($('#rol_id').val()=='1'){
        $('#lbltitulo').html("Acceso Soporte");
        $('#btnsoporte').html("Acceso Usuario");
        $('#rol_id').val('2');
        $('#imgtipo').attr("src","/PERSONAL_HelpDesk/public/1.png");
    }else{
        $('#lbltitulo').html("Acceso Usuario");
        $('#btnsoporte').html("Acceso Soporte");
        $('#rol_id').val('1');
        $('#imgtipo').attr("src","/PERSONAL_HelpDesk/public/2.png");
    }   

});

init();