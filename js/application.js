$(function() {

  // yepnope.js
  // http://yepnopejs.com/
  // ----------------------------------------------------------------------------------------------------
  yepnope([

    // jquery.placeholder.js
    // https://github.com/serby/jquery.placeholder.js
    // ----------------------------------------------------------------------------------------------------
    {
      test: Modernizr.input.placeholder,
      nope: '/js/polyfill/vendor/jquery.placeholder.js',
      callback: function(url, result, key) {
        if (result === false) {
          $('input, textarea').placeholder();
        }
      }
    }

  ]);

  // FORMS
  // ----------------------------------------------------------------------------------------------------
  forms.init();
  
  // DIALOGS
  // ----------------------------------------------------------------------------------------------------
  /* Default settings */
  $.arcticmodal('setDefault', {
    overlay: {
      css: {
        backgroundColor: '#000',
        opacity: .66
      }
    },
    openEffect: {
      speed: 200
    },
    closeEffect: {
      speed: 200
    }
  });

  // SCROLL TO
  // ----------------------------------------------------------------------------------------------------
  $('a[data-scroll="true"]').click(function() {
    var       $this = $(this),
             anchor = $this.attr('href'),
        destination = $(anchor).offset().top;
    $('html, body').animate({scrollTop: destination}, 500);
    return false;
  });

});