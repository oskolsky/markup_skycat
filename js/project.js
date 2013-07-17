// DOM READY
// ----------------------------------------------------------------------------------------------------
$(function() {

  // SLIDERS
  // ----------------------------------------------------------------------------------------------------
  $('#cycle-2-slider').cycle({
    centerHorz: true,
    centerVert: true,
    speed: 1000,
    swipe: true,
    timeout: 3000
  });

  // DIALOGS
  // ----------------------------------------------------------------------------------------------------
  /* Open dialog */
  $('[data-dialog]').click(function() {
    var $this = $(this),
           id = $this.data('dialog');
    $.arcticmodal({
      type: 'ajax',
      url: '/views/dialogs/_' + id + '.html'
    });
    return false;
  });

  /* Close dialog */
  $(document).on('click', '.dialog_close', function() {
    $.arcticmodal('close');
    return false;
  });

  // LOAD
  // ----------------------------------------------------------------------------------------------------
  $(window).load(function() {});

  // SCROLL
  // ----------------------------------------------------------------------------------------------------
  $(window).scroll(function() {});

  // RESIZE
  // ----------------------------------------------------------------------------------------------------
  $(window).smartresize(function() {});
  
});

