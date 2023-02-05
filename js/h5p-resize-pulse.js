( function() {

  // Default timeout in ms
  var timeoutDefault = 500;

  // timeout {string[]} should be passed by WordPress
  if ( ! Array.isArray( timeout ) || 0 === timeout.length ) {
    timeout = timeoutDefault;
  }

  // Parse value
  timeout = parseInt( timeout );
  if ( isNaN( timeout ) ) {
    timeout = timeoutDefault;
  }

  /**
   * Regularly trigger H5P resizing.
   */
  function h5pResizePulse() {
    setTimeout( function() {

      try {
        window.dispatchEvent( new Event( 'resize' ) );
      } catch ( error ) {
        window.dispatchEvent( new window.CustomEvent( 'resize' ) );
      }

      h5pResizePulse();
    }, timeout );
  }

  document.addEventListener( 'readystatechange', function() {
    if ( 'interactive' === document.readyState ) {

      if ( ! window.H5P || ! window.H5P.externalDispatcher ) {
        return; // H5P not present, but should by now
      }

      // Resize once an H5P instance is initialized and resize might be needed
      window.H5P.externalDispatcher.once( 'initialized', function() {
        h5pResizePulse();
      });
    }
  });

}() );
