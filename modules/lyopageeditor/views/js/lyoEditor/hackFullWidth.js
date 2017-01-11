/**
 * 2011-2016 JUML69
 *
 *  @author    JUML69 <contact@lyondev.fr>
 *  @copyright 2011-2016 JUML69
 *  @version   Release:1
 *  @license   One Domain Licence
 */
 
function fullWidth(){
    
	var maxWidth = parseFloat($(window).width()),
	 	columnsWidth =parseFloat($('#columns').css('width')),
	 	totalMargecolumnsWidth = parseFloat($('#columns').css('margin-left')) + parseFloat($('#columns').css('margin-right')),
        TailleContainer = (totalMargecolumnsWidth + columnsWidth == maxWidth ? columnsWidth : totalMargecolumnsWidth + columnsWidth)
       ;
	  
	  //	TailleContainer = (totalMargecolumnsWidth + columnsWidth == maxWidth ? columnsWidth : totalMargecolumnsWidth + columnsWidth),
        if($('footer').width()){
            TailleContainer = $('footer').width();
        }
	  	margeTotal = maxWidth - TailleContainer,
	   	
	  	marginLeft= (margeTotal) /2
	;
	if(!isNaN(marginLeft)){
		$('.container-fluid').closest('.rte').css('margin-left',- marginLeft);
		$('.container-fluid').closest('.rte').css('width',maxWidth);
	}

    /*  $('.container-fluid > div').css("margin-left",'-100%');
    $('.container-fluid > div').css("margin-right",'-100%');
    $('.container-fluid > div').css("padding-left",'100%');
    $('.container-fluid > div').css("padding-right",'100%');*/
}