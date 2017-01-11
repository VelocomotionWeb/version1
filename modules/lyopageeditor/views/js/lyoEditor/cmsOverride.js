/**
 * 2011-2016 JUML69link-Informations
 *
 *  @author    JUML69 <contact@lyondev.fr>
 *  @copyright 2011-2016 JUML69
 *  @version   Release:1
 *  @license   One Domain Licence
 */
lastTiny = null;
lastTinyContainer = null;
nbLoop=6;
idInterval=null;
$(window).on("load", function()
{
	/*
	 * Pour la fiche produit, la page affiché ne contient peut etre pas tiny
	 * Donc si on click sur le lien information qui contien tiny on initialise
	 */
	$("#link-Informations").bind("click", function(e,b){
       
		if($("#oldEditor_on").length) return ;
			initTime();
		});
		
	initTime();
});


function initTime(){
    if(!tinymce.EditorManager.activeEditor){
       idInterval = setTimeout(function(){ 
            init();
        }, 1000);
    }else{
        init();
    }
}
function init(){
    
    if(nbLoop <=0 && idInterval) {
        clearInterval(idInterval);
    }
    nbLoop--;
    
	//si tiny n'existe pas on initialise pas et on sort de la fonction
	 if(!tinymce.EditorManager.activeEditor){
        return ;
     }
    

	lastTiny = (lastTiny ? lastTiny : tinymce.activeEditor );
	lastTinyContainer = lastTiny.getContainer();

//	tinyMCE.activeEditor.schema.setValidElements("*[*]")
	$(lastTinyContainer).parents('.form-group').last().find('.mce-tinymce').css("display","none");
	$(lastTinyContainer).parents('.form-group').last().prepend('<div class="form-group">'+
	'<label class="control-label col-lg-3">Utiliser l"ancien éditeur</label>' +
	'<div class="col-lg-9 ">' +
		'<span class="switch prestashop-switch fixed-width-lg">' +
			'<input id="oldEditor_on" type="radio" value="1" name="oldEditor" onclick="toggleEditor()">' +
			'<label for="oldEditor_on">Oui</label>' +
			'<input id="oldEditor_off" type="radio" checked="checked" value="0" name="oldEditor" onclick="toggleEditor()">' +
			'<label for="oldEditor_off">Non</label>' +
			'<a class="slide-button btn"></a>' +
		'</span>' +
	'</div>' +
'</div>');

	$(lastTinyContainer).parents('.form-group').last().find('.mce-tinymce').parent().prepend('<img style="cursor:pointer" width="100%" class="lyoPageEditorImg" src="../modules/lyopageeditor/views/img/editor.jpg"/>')
   
    $(".lyoPageEditorImg").off('click');
    $(".lyoPageEditorImg").on('click',function(){
       var tinyId = $(this).parent().find('textarea').first().attr("id");
        openEditor(tinyId);
    });
}
function toggleEditor(){
    var goodTinys = $(lastTinyContainer).parents('.form-group').last().find('.mce-tinymce');
	if(goodTinys.css("display") == 'none'){
		goodTinys.css("display",'block');
		$(".lyoPageEditorImg").css("display",'none');
	}else{
		goodTinys.css("display",'none');
		$(".lyoPageEditorImg").css("display",'block');

	}
}

function openEditor(tinyId){
    tinyActif = tinyId;
   
   	if(! $("#dialogEditor").length ){


      var iframe = $('<iframe id="LyoPageEditorIframe" src="../modules/lyopageeditor/bootstrap.php" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="100%"></iframe>');
	  var dialogEditor = $('<div id="dialogEditor" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" aria-label="Close" class="LyoClose close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title">LyoPageEditor</h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-default LyoClose" >Close</button><button id="LyoSave" type="button" class="btn btn-primary">Save changes</button></div></div><!-- /.modal-content --></div><!-- /.modal-dialog --></div><!-- /.modal -->');
      dialogEditor.appendTo("body");
      $('#dialogEditor .modal-body').append(iframe);
      

      $("#LyoSave").on('click',function(){
        tinymce.EditorManager.get(tinyActif).setContent(LyoEditor.getHtml());
            $("#dialogEditor").modal("hide");
      });
      $(".LyoClose").on('click',function(){
          if(confirm('Are you sure you want to cancel your changes')==true){
             $("#dialogEditor").modal("hide");
          }          
      });
    
		  
        $("iframe").on('load',function(){
          
    		LyoEditor.setHtml(tinymce.EditorManager.get(tinyActif).getContent());
    
    		LyoEditor.fileManager.setFileManagerPath(ad + '/filemanager/dialog.php');
     
    	});
        
        $("#dialogEditor").on('show.bs.modal', function () {
            $(this).css("z-index",100002);
            $(this).find(".modal-dialog").css("width","100%");
            $(this).find(".modal-dialog").css("height","100%");
            $(this).find(".modal-dialog").css("top","-30px");
            $(this).find(".modal-dialog").css("left","0px");
            $(this).find(".modal-dialog").css("position","fixed");
            $(this).find(".modal-header").css("padding-top","0px");
            $(this).find(".modal-header").css("padding-bottom","0px");
            $(this).find(".modal-body").css("max-height",'none');
            $(this).find(".modal-body").css("height",$(window).height()-100);
        });
        
	}else{
		LyoEditor.setHtml(tinymce.EditorManager.get(tinyActif).getContent());
	}

	
	$("#dialogEditor").modal();
    

    
}
$( window ).resize(function(e) {
  
    $('#dialogEditor .modal-body').css("height",$(window).height()-100);
  	
});