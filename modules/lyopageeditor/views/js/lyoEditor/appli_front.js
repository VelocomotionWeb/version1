/**
 * 2011-2016 JUML69
 *
 *  @author    JUML69 <contact@lyondev.fr>
 *  @copyright 2011-2016 JUML69
 *  @version   Release:1
 *  @license   One Domain Licence
 */
$( document ).ready(function() {

	 renameContainer();
     $(".frame-shadow-lifted").parent().css('position','relative').css('z-index','1');
	 $( window ).resize(function() {
         fullHeight();
         fullWidth();
     });
     fullHeight();
	 fullWidth();
	 
     
	$( document).find(".carousel").each(function(){
		
		var rewindNav = $(this).attr("data-rewind-nav"),
			nbItems = $(this).attr("data-nb-items"),
			singleItem = (nbItems == 1 ? true : false),
            totalItem =$(this).children().length,
            navigationArrow = ($(this).attr('data-navigation-arrow') == 'true' ? true : false),
            paginationDot = ($(this).attr('data-pagination-dot') == 'false' || $(this).attr('data-pagination-dot') == '0' ? false : true),
            autoPlay = ($(this).attr("data-auto-play") > 0? $(this).attr("data-auto-play") : false),
            params = {
                itemsDesktop: false,
                itemsDesktop: false,
                itemsDesktopSmall:false,
                mouseDrag:false,
                touchDrag:(window.Touch ? true : false),
                singleItem:singleItem,
                items:$(this).attr("data-nb-items"),
                autoPlay:autoPlay,
                rewindNav:(rewindNav ==1 || rewindNav == 'true'? true : false),
                navigation:(navigationArrow ==1 || navigationArrow == 'true'? true : false),
                pagination:(paginationDot ==1 || paginationDot == 'true'? true : false),
                navigationText: [
                  "<i class='owl-prev fa fa-caret-left fa-5x' style='color:#869791'>&lrm;</i>",
                  "<i class='owl-next fa fa-caret-right fa-5x' style='color:#869791'>&lrm;</i>"
                ]}
		;
	
		if(totalItem > 1) $(this).owlCarousel(params);
	});
    $(window).stellar({horizontalScrolling: false,hideDistantElements:true,responsive:true});
		
	animations();
});
function animations() {


 $('[data-appear-animation]').each(function() {

		$(this).addClass('animated')
		$(this).css("animation-delay","0.5s");
		$(this).css("animation-duration","1s");
	  	$(this).appear(function() {
			
			$(this).addClass($(this).attr('data-appear-animation'));
			$(this).addClass($(this).attr('data-appear-animation') + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
				      $(this).removeClass($(this).attr('data-appear-animation') + ' animated');
				    });
 	 });
  });
};
function renameContainer(){
	$('.containering').find('.containering').removeClass('containering');
	$('.containering').addClass('container');
	
	$('.containering-fluid').addClass('container-fluid');
	$('.containering').removeClass('containering');
	$('.containering-fluid').removeClass('containering-fluid');
    $('div.widget:not(.container,.container-fluid)').addClass('container');
}
function fullHeight(){
     $('.fullHeight').css('height',$(window).height());
        $('.fullHeight').each(function(){
             var heightTotal=0;
            $(this).find('.carousel').each(function(){
   
                heightTotal += parseFloat($(this).css("height"));

            });
           
             $(this).css('padding-top',(parseFloat($(this).css('height')) - parseFloat(heightTotal))/2);
        });
}
   

function fullWidth(){
	
}