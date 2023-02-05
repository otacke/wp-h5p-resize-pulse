( function() {

  var h5pResizePulseStarted = false;

  // h5pResizePulseParameters {object} should be passed by WordPress
  h5pResizePulseParameters = h5pResizePulseParameters || {};

  // Simple sanitizing
  h5pResizePulseParameters.timeout =
    parseInt( h5pResizePulseParameters.timeout );

  if (
    isNaN( h5pResizePulseParameters.timeout ||
    500 > h5pResizePulseParameters.timeout
  ) ) {
    h5pResizePulseParameters.timeout = 500;
  }

  /**
   * Regularly trigger H5P resizing.
   *
   * @param {number} timeout Timeout.
   */
  function h5pResizePulse( timeout ) {
    setTimeout( function() {
      try {
        window.dispatchEvent( new Event( 'resize' ) );
      } catch ( error ) {
        window.dispatchEvent( new window.CustomEvent( 'resize' ) );
      }

      h5pResizePulse( timeout );
    }, timeout );
  };

  /**
   * Add resize triggers.
   *
   * @param {string} selector CSS selector for elements.
   */
  function addH5pResizeTriggers( selector ) {
    var index;
    var triggerElements;

    try {
      triggerElements = document.querySelectorAll( selector );
    } catch ( error ) {
      triggerElements = null;
    }

    if ( ! triggerElements ) {
      return false; // No elements found
    }

    for ( index = 0; index < triggerElements.length; index++ ) {
      triggerElements[index].addEventListener( 'click', function() {
        window.requestAnimationFrame( function() {
          window.dispatchEvent( new window.CustomEvent( 'resize' ) );
        });
      });
    }

    return true;
  };

  /**
   * Try to start.
   */
  function tryToStart() {
    if (
      'selector' === h5pResizePulseParameters.mode &&
      'string' === typeof h5pResizePulseParameters.selector
    ) {
      h5pResizePulseStarted =
        addH5pResizeTriggers( h5pResizePulseParameters.selector );
    } else {
      if ( ! window.H5P || ! window.H5P.externalDispatcher ) {
        return; // H5P not present, but should by now
      }

      h5pResizePulseStarted = true;

      // Resize once an H5P instance is initialized and resize might be needed
      window.H5P.externalDispatcher.once( 'initialized', function() {
        h5pResizePulse( h5pResizePulseParameters.timeout );
      });
    }
  };

  document.addEventListener( 'readystatechange', function() {
    if ( 'interactive' === document.readyState ) {
      tryToStart();
    } else if ( 'complete' === document.readyState ) {

      // Plugin might not have rendered elements on 'interactive'
      if ( ! h5pResizePulseStarted ) {
        tryToStart();
      }
    }
  });

}() );
