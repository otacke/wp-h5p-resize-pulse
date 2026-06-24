(() => {
  let resizerIsRunning = false;

  /**
   * Retrieve resize interval value from query parameter of script URL.
   * @returns {number|null} Resize interval.
   */
  const getResizeIntervalFromScriptSource = () => {
    const scripts = Array.from(document.getElementsByTagName('script'));
    const script = scripts.find(s => s.src?.includes('h5p-resize-pulse-embed.js'));
    if (!script) {
      return null;
    }

    const query = script.src.split('?')[1];
    if (!query) {
      return null;
    }

    const resizeInterval = parseInt(new URLSearchParams(query).get('resizeInterval'));
    if (typeof resizeInterval !== 'number') {
      return null;
    }

    return resizeInterval;
  };

  /**
   * Trigger a resize event to make H5P resize - fingers crossed.
   */
  const triggerResize = () => {
    const windowHoldsH5PIFrame = window.parent === window;
    if (windowHoldsH5PIFrame) {
      window.dispatchEvent(new Event('resize'));
    }
    else {
      window.H5P.instances?.[0]?.trigger('resize');
    }
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
   * Try to start.
   */
  const tryToStart = () => {
    if (resizerIsRunning) {
      return;
    }

    if (!window.H5P || !window.H5P.externalDispatcher) {
      return; // H5P not present, but should be by now
    }

    const resizeInterval = getResizeIntervalFromScriptSource();
    if (resizeInterval === null) {
      return;
    }

    const wasInitialized = document.querySelector('.h5p-initialized');
    if (wasInitialized) {
      resizerIsRunning = true;
      scheduleResizePulse(resizeInterval);
    }
    else {
      window.H5P.externalDispatcher.once('initialized', (event) => {
        if (resizerIsRunning) {
          return;
        }

        resizerIsRunning = true;
        scheduleResizePulse(resizeInterval);
      });
    }
  };

  document.addEventListener('readystatechange', () => {
    if (document.readyState === 'interactive' || document.readyState === 'complete') {
      tryToStart();
    }
  });
})();
