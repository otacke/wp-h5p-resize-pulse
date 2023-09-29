(() => {
  let resizeMechanismStarted = false;

  // userParams {object} should be passed by WordPress
  const userParams = window.h5pResizePulseParameters || {};

  // Simple sanitizing
  userParams.timeout = parseInt(userParams.timeout);

  if (
    isNaN(userParams.timeout) ||
    userParams.timeout < 500
  ) {
    userParams.timeout = 500;
  }

  /**
   * Trigger a resize event on the window that H5P core will recognize.
   */
  const triggerResize = () => {
    console.log('DISPATCH');

    window.dispatchEvent(new Event('resize'));
  };

  /**
   * Regularly trigger H5P resizing.
   * @param {number} timeout Timeout.
   */
  const scheduleResizePulse = (timeout) => {
    setTimeout(() => {
      triggerResize();
      scheduleResizePulse(timeout);
    }, timeout);
  };

  /**
   * Add resize pulse.
   * @returns {boolean} True, if resize mechanism could be started, else false.
   */
  const addResizePulse = () => {
    if (!window.H5P || !window.H5P.externalDispatcher) {
      return false; // H5P not present, but should be by now
    }

    // Resize once an H5P instance is initialized and resize might be needed
    window.H5P.externalDispatcher.once('initialized', () => {
      scheduleResizePulse(userParams.timeout);
    });

    return true;
  };

  /**
   * Add resize triggers.
   *
   * @param {string} selector CSS selector for elements.
   * @returns {boolean} True, if resize mechanism could be started, else false.
   */
  const addClickListeners = (selector) => {
    if (typeof userParams.selector !== 'string') {
      return false; // No valid selector
    }

    let triggerElements;
    try {
      triggerElements = Array.from(document.querySelectorAll(selector));
    } catch (error) {
      triggerElements = [];
    }

    if (!triggerElements.length) {
      return false; // No elements found
    }

    triggerElements.forEach((element) => {
      element.addEventListener('click', () => {
        // DOM will have to have re-rendered, so height can be determined
        window.requestAnimationFrame(() => {
          triggerResize();
        });
      });
    });

    return true;
  };

  /**
   * Add resize observer to each H5P iframe parent node.
   * @returns {boolean} True, if resize mechanism could be started, else false.
   */
  const addResizeObservers = () => {
    const h5pIframes = Array.from(
      document.body.querySelectorAll('iframe.h5p-iframe')
    );

    if (!h5pIframes.length) {
      return false;
    }

    h5pIframes.forEach((iframe) => {
      (new ResizeObserver(() => {
        triggerResize();
      })).observe(iframe.parentNode);
    });

    return true;
  };

  /**
   * Try to start.
   */
  const tryToStart = () => {
    if (userParams.mode === 'observer') {
      resizeMechanismStarted = addResizeObservers();
    } else if (userParams.mode === 'selector') {
      resizeMechanismStarted = addClickListeners(userParams.selector);
    } else if (userParams.mode === 'interval') {
      resizeMechanismStarted = addResizePulse();
    }
  };

  document.addEventListener('readystatechange', () => {
    if ('interactive' === document.readyState) {
      tryToStart();
    } else if ('complete' === document.readyState) {
      // Plugin might not have rendered elements on 'interactive'
      if (!resizeMechanismStarted) {
        tryToStart();
      }
    }
  });
})();
