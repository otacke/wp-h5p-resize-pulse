( function() {

  /*
   * Polyfill for IE11, doesn't feature new Event()
   * https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/CustomEvent
   */
  if ( 'function' === typeof window.CustomEvent ) {
    return false;
  }

  /**
   * Create custom event.
   *
   * @param {string} event Event handler.
   * @param {object} params Parameters.
   * @return {Event} Event.
   */
  function CustomEvent( event, params ) {
    var evt;
    params = params || { bubbles: false, cancelable: false, detail: null };
    evt = document.createEvent( 'CustomEvent' );
    evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
    return evt;
  }

  window.CustomEvent = CustomEvent;
}() );

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

  document.onreadystatechange = function() {
    if ( 'interactive' === document.readyState ) {

      if ( ! window.H5P || ! window.H5P.externalDispatcher ) {
        return; // H5P not present, but should by now
      }

      // Resize once an H5P instance is initialized and resize might be needed
      window.H5P.externalDispatcher.once( 'initialized', function() {
        h5pResizePulse();
      });
    }
  };
}() );
