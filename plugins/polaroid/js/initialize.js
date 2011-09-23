$(function(){
  
  /*--------When you click on a report, load it in a modal iframe----------*/
  $("a.modal-that-junk, .search_block h3 a").colorbox({
    width:"820px", 
    height:"80%", 
    iframe:true,
    current: "{current} of {total}"
    });
    
    var $container = $('.polaroids');

     // filter buttons
     $('#filters a').click(function(){
       var selector = $(this).attr('data-filter');
       $container.isotope({ filter: selector });
       var $this = $(this);

       // don't proceed if already selected
       if ( !$this.hasClass('selected') ) {
         $this.parents('.option-set').find('.selected').removeClass('selected');
         $this.addClass('selected');
       }
       return false;
     });


     // switches selected class on buttons
     $('#options').find('.option-set a').click(function(){
       

     });

     $container.isotope({
       itemSelector : '.polaroid'
     });


});