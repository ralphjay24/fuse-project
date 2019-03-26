jQuery(document).ready(function($) {
   
   $('.popup').click(function() {
     var NWin = window.open($(this).prop('href'), '', 'scrollbars=1,height=600,width=400,top=30,left=120');
      if (window.focus)
     {
       NWin.focus();
     }
      return false;
    });

   $('.popup2').click(function() {
     var NWin = window.open($(this).prop('href'), '', 'scrollbars=1,height=400,width=1000,top=120,left=120');
     if (window.focus)
     {
       NWin.focus();
     }
     return false;
    });

   $('.popup3').click(function() {
     var NWin = window.open($(this).prop('href'), '', 'scrollbars=1,height=400,width=800,top=120,left=120');
     if (window.focus)
     {
       NWin.focus();
     }
     return false;
    });
  


});