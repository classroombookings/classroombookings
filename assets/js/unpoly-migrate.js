(function() {
  up.framework.startExtension();

}).call(this);
(function() {
  var u,
    slice = [].slice;

  u = up.util;


  /***
  @module up.migrate
   */

  up.migrate = (function() {
    var config, deprecated, fixEventType, fixEventTypes, fixKey, formerlyAsync, renamedEvent, renamedEvents, renamedPackage, renamedProperty, reset, warn, warnedMessages;
    config = new up.Config(function() {
      return {
        logLevel: 'warn'
      };
    });
    renamedProperty = function(object, oldKey, newKey) {
      var warning;
      warning = function() {
        return warn('Property { %s } has been renamed to { %s } (found in %o)', oldKey, newKey, object);
      };
      return Object.defineProperty(object, oldKey, {
        get: function() {
          warning();
          return this[newKey];
        },
        set: function(newValue) {
          warning();
          return this[newKey] = newValue;
        }
      });
    };
    fixKey = function(object, oldKey, newKey) {
      if (u.isDefined(object[oldKey])) {
        warn('Property { %s } has been renamed to { %s } (found in %o)', oldKey, newKey, object);
        return u.renameKey(object, oldKey, newKey);
      }
    };
    renamedEvents = {};
    renamedEvent = function(oldType, newType) {
      return renamedEvents[oldType] = newType;
    };
    fixEventType = function(eventType) {
      var newEventType;
      if (newEventType = renamedEvents[eventType]) {
        warn("Event " + eventType + " has been renamed to " + newEventType);
        return newEventType;
      } else {
        return eventType;
      }
    };
    fixEventTypes = function(eventTypes) {
      return u.uniq(u.map(eventTypes, fixEventType));
    };
    renamedPackage = function(oldName, newName) {
      return Object.defineProperty(up, oldName, {
        get: function() {
          warn("up." + oldName + " has been renamed to up." + newName);
          return up[newName];
        }
      });
    };
    warnedMessages = {};
    warn = function() {
      var args, formattedMessage, message, ref;
      message = arguments[0], args = 2 <= arguments.length ? slice.call(arguments, 1) : [];
      formattedMessage = u.sprintf.apply(u, [message].concat(slice.call(args)));
      if (!warnedMessages[formattedMessage]) {
        warnedMessages[formattedMessage] = true;
        return (ref = up.log)[config.logLevel].apply(ref, ['DEPRECATION', message].concat(slice.call(args)));
      }
    };
    deprecated = function(deprecatedExpression, replacementExpression) {
      return warn(deprecatedExpression + " has been deprecated. Use " + replacementExpression + " instead.");
    };
    formerlyAsync = function(label) {
      var oldThen, promise;
      promise = Promise.resolve();
      oldThen = promise.then;
      promise.then = function() {
        warn(label + " is now a sync function");
        return oldThen.apply(this, arguments);
      };
      return promise;
    };
    reset = function() {
      return config.reset();
    };
    up.on('up:framework:reset', reset);
    return {
      deprecated: deprecated,
      renamedPackage: renamedPackage,
      renamedProperty: renamedProperty,
      formerlyAsync: formerlyAsync,
      renamedEvent: renamedEvent,
      fixEventTypes: fixEventTypes,
      fixKey: fixKey,
      warn: warn,
      loaded: true,
      config: config
    };
  })();

}).call(this);

/***
@module up.util
 */


/***
Returns a copy of the given object that only contains
the given keys.

@function up.util.only
@param {Object} object
@param {Array} ...keys
@deprecated
  Use `up.util.pick()` instead.
 */

(function() {
  var slice = [].slice;

  up.util.only = function() {
    var keys, object;
    object = arguments[0], keys = 2 <= arguments.length ? slice.call(arguments, 1) : [];
    up.migrate.deprecated('up.util.only(object, ...keys)', 'up.util.pick(object, keys)');
    return up.util.pick(object, keys);
  };


  /***
  Returns a copy of the given object that contains all except
  the given keys.
  
  @function up.util.except
  @param {Object} object
  @param {Array} ...keys
  @deprecated
    Use `up.util.omit(object, keys)` (with an array argument) instead of `up.util.object(...keys)` (with rest arguments).
   */

  up.util.except = function() {
    var keys, object;
    object = arguments[0], keys = 2 <= arguments.length ? slice.call(arguments, 1) : [];
    up.migrate.deprecated('up.util.except(object, ...keys)', 'up.util.omit(object, keys)');
    return up.util.omit(object, keys);
  };

  up.util.parseUrl = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.warn('up.util.parseUrl() has been renamed to up.util.parseURL()');
    return (ref = up.util).parseURL.apply(ref, args);
  };

  up.util.any = function() {
    var args;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.warn('up.util.any() has been renamed to up.util.some()');
    return some.apply(null, args);
  };

  up.util.all = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.warn('up.util.all() has been renamed to up.util.every()');
    return (ref = up.util).every.apply(ref, args);
  };

  up.util.detect = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.warn('up.util.detect() has been renamed to up.util.find()');
    return (ref = up.util).find.apply(ref, args);
  };

  up.util.select = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.warn('up.util.select() has been renamed to up.util.filter()');
    return (ref = up.util).filter.apply(ref, args);
  };

  up.util.setTimer = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.warn('up.util.setTimer() has been renamed to up.util.timer()');
    return (ref = up.util).timer.apply(ref, args);
  };

  up.util.escapeHtml = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.deprecated('up.util.escapeHtml', 'up.util.escapeHTML');
    return (ref = up.util).escapeHTML.apply(ref, args);
  };

  up.util.selectorForElement = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.warn('up.util.selectorForElement() has been renamed to up.fragment.toTarget()');
    return (ref = up.fragment).toTarget.apply(ref, args);
  };

  up.util.nextFrame = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.warn('up.util.nextFrame() has been renamed to up.util.task()');
    return (ref = up.util).task.apply(ref, args);
  };

}).call(this);

/***
@module up.element
 */


/***
Returns the first descendant element matching the given selector.

@function up.element.first
@param {Element} [parent=document]
  The parent element whose descendants to search.

  If omitted, all elements in the `document` will be searched.
@param {string} selector
  The CSS selector to match.
@return {Element|undefined|null}
  The first element matching the selector.

  Returns `null` or `undefined` if no element macthes.
@deprecated
  Use `up.element.get()` instead.
 */

(function() {
  var slice = [].slice;

  up.element.first = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.deprecated('up.element.first()', 'up.element.get()');
    return (ref = up.element).get.apply(ref, args);
  };

  up.element.createFromHtml = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.deprecated('up.element.createFromHtml', 'up.element.createFromHTML');
    return (ref = up.element).createFromHTML.apply(ref, args);
  };

}).call(this);

/***
@module up.event
 */

(function() {
  var slice = [].slice;

  up.migrate.renamedPackage('bus', 'event');


  /***
  [Emits an event](/up.emit) and returns whether no listener
  has prevented the default action.
  
  \#\#\# Example
  
  ```javascript
  if (up.event.nobodyPrevents('disk:erase')) {
    Disk.erase()
  })
  ```
  
  @function up.event.nobodyPrevents
  @param {string} eventType
  @param {Object} eventProps
  @return {boolean}
    whether no listener has prevented the default action
  @deprecated
    Use `!up.emit(type).defaultPrevented` instead.
   */

  up.event.nobodyPrevents = function() {
    var args, event;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.deprecated('up.event.nobodyPrevents(type)', '!up.emit(type).defaultPrevented');
    event = up.emit.apply(up, args);
    return !event.defaultPrevented;
  };

}).call(this);
(function() {
  var u;

  u = up.util;

  up.migrate.postCompile = function(elements, compiler) {
    var element, i, keepValue, len, results, value;
    if (keepValue = compiler.keep) {
      up.migrate.warn('The { keep: true } option for up.compiler() has been removed. Have the compiler set [up-keep] attribute instead.');
      value = u.isString(keepValue) ? keepValue : '';
      results = [];
      for (i = 0, len = elements.length; i < len; i++) {
        element = elements[i];
        results.push(element.setAttribute('up-keep', value));
      }
      return results;
    }
  };

  up.migrate.targetMacro = function(queryAttr, fixedResultAttrs, callback) {
    return up.macro("[" + queryAttr + "]", function(link) {
      var optionalTarget, resultAttrs;
      resultAttrs = u.copy(fixedResultAttrs);
      if (optionalTarget = link.getAttribute(queryAttr)) {
        resultAttrs['up-target'] = optionalTarget;
      } else {
        resultAttrs['up-follow'] = '';
      }
      e.setMissingAttrs(link, resultAttrs);
      link.removeAttribute(queryAttr);
      return typeof callback === "function" ? callback() : void 0;
    });
  };

}).call(this);

/***
@module up.form
 */

(function() {
  up.migrate.renamedProperty(up.form.config, 'fields', 'fieldSelectors');

  up.migrate.renamedProperty(up.form.config, 'submitButtons', 'submitButtonSelectors');

}).call(this);
(function() {
  var u,
    slice = [].slice;

  u = up.util;


  /***
  @module up.fragment
   */

  up.migrate.renamedPackage('flow', 'fragment');

  up.migrate.renamedPackage('dom', 'fragment');

  up.migrate.renamedProperty(up.fragment.config, 'fallbacks', 'mainTargets');

  up.migrate.handleResponseDocOptions = function(docOptions) {
    return up.migrate.fixKey(docOptions, 'html', 'document');
  };


  /***
  Replaces elements on the current page with corresponding elements
  from a new page fetched from the server.
  
  @function up.replace
  @param {string|Element|jQuery} target
    The CSS selector to update. You can also pass a DOM element or jQuery element
    here, in which case a selector will be inferred from the element's class and ID.
  @param {string} url
    The URL to fetch from the server.
  @param {Object} [options]
    See `options` for `up.render()`.
  @return {Promise}
    A promise that fulfills when the page has been updated.
  @deprecated
    Use `up.render()` or `up.navigate()` instead.
   */

  up.replace = function(target, url, options) {
    up.migrate.deprecated('up.replace(target, url)', 'up.navigate(target, { url })');
    return up.navigate(u.merge(options, {
      target: target,
      url: url
    }));
  };


  /***
  Updates a selector on the current page with the
  same selector from the given HTML string.
  
  \#\#\# Example
  
  Let's say your current HTML looks like this:
  
      <div class="one">old one</div>
      <div class="two">old two</div>
  
  We now replace the second `<div>`, using an HTML string
  as the source:
  
      html = '<div class="one">new one</div>' +
             '<div class="two">new two</div>';
  
      up.extract('.two', html)
  
  Unpoly looks for the selector `.two` in the strings and updates its
  contents in the current page. The current page now looks like this:
  
      <div class="one">old one</div>
      <div class="two">new two</div>
  
  Note how only `.two` has changed. The update for `.one` was
  discarded, since it didn't match the selector.
  
  @function up.extract
  @param {string|Element|jQuery} target
  @param {string} html
  @param {Object} [options]
    See options for [`up.render()`](/up.render).
  @return {Promise}
    A promise that will be fulfilled when the selector was updated.
  @deprecated
    Use `up.render()` or `up.navigate()` instead.
   */

  up.extract = function(target, document, options) {
    up.migrate.deprecated('up.extract(target, document)', 'up.navigate(target, { document })');
    return up.navigate(u.merge(options, {
      target: target,
      document: document
    }));
  };


  /***
  Returns the first element matching the given selector, but
  ignores elements that are being [destroyed](/up.destroy) or that are being
  removed by a [transition](/up.morph).
  
  Returns `undefined` if no element matches these conditions.
  
  @function up.fragment.first
  @param {Element|jQuery} [root=document]
    The root element for the search. Only the root's children will be matched.
  
    May be omitted to search through all elements in the `document`.
  @param {string} selector
    The selector to match
  @param {string} [options.layer='current']
    The the layer in which to find the element.
  
    @see layer-option
  @param {string|Element|jQuery} [options.origin]
    An second element or selector that can be referenced as `:origin` in the first selector:
  @return {Element|undefined}
    The first element that is neither a ghost or being destroyed,
    or `undefined` if no such element was found.
  @deprecated
    Use `up.fragment.get()` instead.
   */

  up.fragment.first = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.deprecated('up.fragment.first()', 'up.fragment.get()');
    return (ref = up.fragment).get.apply(ref, args);
  };

  up.first = up.fragment.first;

  up.migrate.handleScrollOptions = function(options) {
    if (u.isUndefined(options.scroll)) {
      if (u.isString(options.reveal)) {
        up.migrate.deprecated("Option { reveal: '" + options.reveal + "' }", "{ scroll: '" + options.reveal + "' }");
        options.scroll = options.reveal;
      } else if (options.reveal === true) {
        up.migrate.deprecated('Option { reveal: true }', "{ scroll: 'target' }");
        options.scroll = 'target';
      } else if (options.reveal === false) {
        up.migrate.deprecated('Option { reveal: false }', "{ scroll: false }");
        options.scroll = false;
      }
      if (u.isDefined(options.resetScroll)) {
        up.migrate.deprecated('Option { resetScroll: true }', "{ scroll: 'reset' }");
        options.scroll = 'teset';
      }
      if (u.isDefined(options.restoreScroll)) {
        up.migrate.deprecated('Option { restoreScroll: true }', "{ scroll: 'restore' }");
        return options.scroll = 'restore';
      }
    }
  };

  up.migrate.handleHistoryOption = function(options) {
    if (u.isString(options.history) && options.history !== 'auto') {
      up.migrate.warn("Passing a URL as { history } option is deprecated. Pass it as { location } instead.");
      options.location = options.history;
      return options.history = 'auto';
    }
  };

  up.migrate.handleRenderOptions = function(options) {
    var i, len, prop, ref, results;
    up.migrate.handleHistoryOption(options);
    ref = ['target', 'origin'];
    results = [];
    for (i = 0, len = ref.length; i < len; i++) {
      prop = ref[i];
      if (u.isJQuery(options[prop])) {
        up.migrate.warn('Passing a jQuery collection as { %s } is deprecated. Pass it as a native element instead.', prop);
        results.push(options[prop] = up.element.get(options[prop]));
      } else {
        results.push(void 0);
      }
    }
    return results;
  };

}).call(this);

/***
@module up.history
 */

(function() {
  up.migrate.renamedProperty(up.history.config, 'popTargets', 'restoreTargets');


  /***
  Returns a normalized URL for the current history entry.
  
  @function up.history.url
  @return {string}
  @deprecated Use the `up.history.location` property instead.
   */

  up.history.url = function() {
    up.migrate.deprecated('up.history.url()', 'up.history.location');
    return up.history.location;
  };

  up.migrate.renamedEvent('up:history:push', 'up:location:changed');

  up.migrate.renamedEvent('up:history:pushed', 'up:location:changed');

  up.migrate.renamedEvent('up:history:restore', 'up:location:changed');

  up.migrate.renamedEvent('up:history:restored', 'up:location:changed');

  up.migrate.renamedEvent('up:history:replaced', 'up:location:changed');

}).call(this);

/***
@module up.feedback
 */

(function() {
  up.migrate.renamedPackage('navigation', 'feedback');

  up.migrate.renamedProperty(up.feedback.config, 'navs', 'navSelectors');

}).call(this);

/***
@module up.link
 */

(function() {
  up.migrate.parseFollowOptions = function(parser) {
    parser.string('flavor');
    parser.string('width');
    parser.string('height');
    parser.boolean('closable');
    parser.booleanOrString('reveal');
    parser.boolean('resetScroll');
    return parser.boolean('restoreScroll');
  };


  /***
  [Follows](/up.follow) this link as fast as possible.
  
  This is done by:
  
  - [Following the link through AJAX](/a-up-follow) instead of a full page load
  - [Preloading the link's destination URL](/a-up-preload)
  - [Triggering the link on `mousedown`](/a-up-instant) instead of on `click`
  
  \#\#\# Example
  
  Use `[up-dash]` like this:
  
      <a href="/users" up-dash=".main">User list</a>
  
  This is shorthand for:
  
      <a href="/users" up-target=".main" up-instant up-preload>User list</a>
  
  @selector a[up-dash]
  @param [up-dash='body']
    The CSS selector to replace
  
    Inside the CSS selector you may refer to this link as `&` ([like in Sass](https://sass-lang.com/documentation/file.SASS_REFERENCE.html#parent-selector)).
  @deprecated
    To accelerate all links use `up.link.config.instantSelectors` and `up.link.config.preloadSelectors`.
   */

  up.migrate.targetMacro('up-dash', {
    'up-preload': '',
    'up-instant': ''
  }, function() {
    return up.migrate.deprecated('a[up-dash]', 'up.link.config.instantSelectors or up.link.config.preloadSelectors');
  });

}).call(this);

/***
@module up.layer
 */

(function() {
  up.migrate.handleLayerOptions = function(options) {
    var dimensionKey, i, len, ref;
    up.migrate.fixKey(options, 'flavor', 'mode');
    up.migrate.fixKey(options, 'closable', 'dismissable');
    up.migrate.fixKey(options, 'closeLabel', 'dismissLabel');
    ref = ['width', 'maxWidth', 'height'];
    for (i = 0, len = ref.length; i < len; i++) {
      dimensionKey = ref[i];
      if (options[dimensionKey]) {
        up.migrate.warn("Layer option { " + dimensionKey + " } has been removed. Use { size } or { class } instead.");
      }
    }
    if (options.sticky) {
      up.migrate.warn('Layer option { sticky } has been removed. Give links an [up-peel=false] attribute to prevent layer dismissal on click.');
    }
    if (options.template) {
      up.migrate.warn('Layer option { template } has been removed. Use { class } or modify the layer HTML on up:layer:open.');
    }
    if (options.layer === 'page') {
      up.migrate.warn("Option { layer: 'page' } has been renamed to { layer: 'root' }.");
      options.layer = 'root';
    }
    if (options.layer === 'modal' || options.layer === 'popup') {
      up.migrate.warn("Option { layer: '" + options.layer + "' } has been removed. Did you mean { layer: 'overlay' }?");
      return options.layer = 'overlay';
    }
  };

  up.migrate.handleTetherOptions = function(options) {
    var align, position, ref;
    ref = options.position.split('-'), position = ref[0], align = ref[1];
    if (align) {
      up.migrate.warn('The position value %o is deprecated. Use %o instead.', options.position, {
        position: position,
        align: align
      });
      options.position = position;
      return options.align = align;
    }
  };


  /***
  When this element is clicked, closes a currently open overlay.
  
  Does nothing if no overlay is currently open.
  
  To make a link that closes the current overlay, but follows to
  a fallback destination on the root layer:
  
      <a href="/fallback" up-close>Okay</a>
  
  @selector a[up-close]
  @deprecated
    Use `a[up-dismiss]` instead.
   */

  up.migrate.registerLayerCloser = function(layer) {
    return layer.registerClickCloser('up-close', (function(_this) {
      return function(value, closeOptions) {
        up.migrate.deprecated('[up-close]', '[up-dismiss]');
        return layer.dismiss(value, closeOptions);
      };
    })(this));
  };

  up.migrate.handleLayerConfig = function(config) {
    return up.migrate.fixKey(config, 'history', 'historyVisible');
  };

}).call(this);

/***
@module up.layer
 */

(function() {
  var FLAVORS_ERROR, u;

  u = up.util;

  FLAVORS_ERROR = new Error('up.modal.flavors has been removed without direct replacement. You may give new layers a { class } or modify layer elements on up:layer:open.');

  up.modal = u.literal({

    /***
    Opens a modal overlay for the given URL.
    
    @function up.modal.visit
    @param {string} url
      The URL to load.
    @param {Object} options
      See options for `up.render()`.
    @deprecated
      Use `up.layer.open({ url, mode: "modal" })` instead.
     */
    visit: function(url, options) {
      if (options == null) {
        options = {};
      }
      up.migrate.deprecated('up.modal.visit(url)', 'up.layer.open({ url, mode: "modal" })');
      return up.layer.open(u.merge(options, {
        url: url,
        mode: 'modal'
      }));
    },

    /***
    Opens the given link's destination in a modal overlay.
    
    @function up.modal.follow
    @param {Element|jQuery|string} linkOrSelector
      The link to follow.
    @param {string} [options]
      See options for `up.render()`.
    @return {Promise}
      A promise that will be fulfilled when the modal has been opened.
    @deprecated
      Use `up.follow(link, { layer: "modal" })` instead.
     */
    follow: function(link, options) {
      if (options == null) {
        options = {};
      }
      up.migrate.deprecated('up.modal.follow(link)', 'up.follow(link, { layer: "modal" })');
      return up.follow(link, u.merge(options, {
        layer: 'modal'
      }));
    },

    /***
    [Extracts](/up.extract) the given CSS selector from the given HTML string and
    opens the results in a modal overlay.
    
    @function up.modal.extract
    @param {string} selector
      The CSS selector to extract from the HTML.
    @param {string} document
      The HTML containing the modal content.
    @param {Object} options
      See options for [`up.modal.follow()`](/up.modal.follow).
    @return {Promise}
      A promise that will be fulfilled when the modal has been opened.
    @deprecated
      Use `up.layer.open({ document, mode: "modal" })` instead.
     */
    extract: function(target, html, options) {
      if (options == null) {
        options = {};
      }
      up.migrate.deprecated('up.modal.extract(target, document)', 'up.layer.open({ document, mode: "modal" })');
      return up.layer.open(u.merge(options, {
        target: target,
        html: html,
        layer: 'modal'
      }));
    },

    /***
    Closes a currently open overlay.
    
    @function up.modal.close
    @param {Object} options
    @return {Promise}
    @deprecated
      Use `up.layer.dismiss()` instead.
     */
    close: function(options) {
      if (options == null) {
        options = {};
      }
      up.migrate.deprecated('up.modal.close()', 'up.layer.dismiss()');
      up.layer.dismiss(null, options);
      return up.migrate.formerlyAsync('up.layer.dismiss()');
    },

    /***
    Returns the location URL of the fragment displayed in the current overlay.
    
    @function up.modal.url
    @return {string}
    @deprecated
      Use `up.layer.location` instead.
     */
    url: function() {
      up.migrate.deprecated('up.modal.url()', 'up.layer.location');
      return up.layer.location;
    },

    /***
    Returns the location URL of the layer behind the current overlay.
    
    @function up.modal.coveredUrl
    @return {string}
    @deprecated
      Use `up.layer.parent.location` instead.
     */
    coveredUrl: function() {
      var ref;
      up.migrate.deprecated('up.modal.coveredUrl()', 'up.layer.parent.location');
      return (ref = up.layer.parent) != null ? ref.location : void 0;
    },

    /***
    Sets default options for future modal overlays.
    
    @property up.modal.config
    @deprecated
      Use `up.layer.config.modal` instead.
     */
    get_config: function() {
      up.migrate.deprecated('up.modal.config', 'up.layer.config.modal');
      return up.layer.config.modal;
    },

    /***
    Returns whether the given element or selector is contained
    within the current layer.
    
    @function up.modal.contains
    @param {string} elementOrSelector
      The element to test
    @return {boolean}
    @deprecated
      Use `up.layer.contains()` instead.
     */
    contains: function(element) {
      up.migrate.deprecated('up.modal.contains()', 'up.layer.contains()');
      return up.layer.contains(element);
    },

    /***
    Returns whether an overlay is currently open.
    
    @function up.modal.isOpen
    @return {boolean}
    @deprecated
      Use `up.layer.isOverlay()` instead.
     */
    isOpen: function() {
      up.migrate.deprecated('up.modal.isOpen()', 'up.layer.isOverlay()');
      return up.layer.isOverlay();
    },
    get_flavors: function() {
      throw FLAVORS_ERROR;
    },
    flavor: function() {
      throw FLAVORS_ERROR;
    }
  });

  up.migrate.renamedEvent('up:modal:open', 'up:layer:open');

  up.migrate.renamedEvent('up:modal:opened', 'up:layer:opened');

  up.migrate.renamedEvent('up:modal:close', 'up:layer:dismiss');

  up.migrate.renamedEvent('up:modal:closed', 'up:layer:dismissed');


  /***
  Clicking this link will load the destination via AJAX and open
  the given selector in a modal overlay.
  
  @selector a[up-modal]
  @params-note
    All attributes for `a[up-layer=new]` may also be used.
  @param {string} up-modal
    The CSS selector that will be extracted from the response and displayed in a modal dialog.
  @deprecated
    Use `a[up-layer="new modal"]` instead.
   */

  up.migrate.targetMacro('up-modal', {
    'up-layer': 'new modal'
  }, function() {
    return up.migrate.deprecated('a[up-modal]', 'a[up-layer="new modal"]');
  });


  /***
  Clicking this link will load the destination via AJAX and open
  the given selector in a modal drawer that slides in from the edge of the screen.
  
  @selector a[up-drawer]
  @params-note
    All attributes for `a[up-layer=new]` may also be used.
  @param {string} up-drawer
    The CSS selector that will be extracted from the response and displayed in a modal dialog.
  @deprecated
    Use `a[up-layer="new drawer"]` instead.
   */

  up.migrate.targetMacro('up-drawer', {
    'up-layer': 'new drawer'
  }, function() {
    return up.migrate.deprecated('a[up-drawer]', 'a[up-layer="new drawer"]');
  });

}).call(this);

/***
@module up.layer
 */

(function() {
  var e, u;

  u = up.util;

  e = up.element;

  up.popup = u.literal({

    /***
    Attaches a popup overlay to the given element or selector.
    
    @function up.popup.attach
    @param {Element|jQuery|string} anchor
      The element to which the popup will be attached.
    @param {Object} [options]
      See options for `up.render()`.
    @return {Promise}
    @deprecated
      Use `up.layer.open({ origin, layer: 'popup' })` instead.
     */
    attach: function(origin, options) {
      if (options == null) {
        options = {};
      }
      origin = up.fragment.get(origin);
      up.migrate.deprecated('up.popup.attach(origin)', "up.layer.open({ origin, layer: 'popup' })");
      return up.layer.open(u.merge(options, {
        origin: origin,
        layer: 'popup'
      }));
    },

    /***
    Closes a currently open overlay.
    
    @function up.popup.close
    @param {Object} options
    @return {Promise}
    @deprecated
      Use `up.layer.dismiss()` instead.
     */
    close: function(options) {
      if (options == null) {
        options = {};
      }
      up.migrate.deprecated('up.popup.close()', 'up.layer.dismiss()');
      return up.layer.dismiss(null, options);
    },

    /***
    Returns the location URL of the fragment displayed in the current overlay.
    
    @function up.popup.url
    @return {string}
    @deprecated
      Use `up.layer.location` instead.
     */
    url: function() {
      up.migrate.deprecated('up.popup.url()', 'up.layer.location');
      return up.layer.location;
    },

    /***
    Returns the location URL of the layer behind the current overlay.
    
    @function up.popup.coveredUrl
    @return {string}
    @deprecated
      Use `up.layer.parent.location` instead.
     */
    coveredUrl: function() {
      var ref;
      up.migrate.deprecated('up.popup.coveredUrl()', 'up.layer.parent.location');
      return (ref = up.layer.parent) != null ? ref.location : void 0;
    },

    /***
    Sets default options for future popup overlays.
    
    @property up.popup.config
    @deprecated
      Use `up.layer.config.popup` instead.
     */
    get_config: function() {
      up.migrate.deprecated('up.popup.config', 'up.layer.config.popup');
      return up.layer.config.popup;
    },

    /***
    Returns whether the given element or selector is contained
    within the current layer.
    
    @function up.popup.contains
    @param {string} elementOrSelector
      The element to test
    @return {boolean}
    @deprecated
      Use `up.layer.contains()` instead.
     */
    contains: function(element) {
      up.migrate.deprecated('up.popup.contains()', 'up.layer.contains()');
      return up.layer.contains(element);
    },

    /***
    Returns whether an overlay is currently open.
    
    @function up.popup.isOpen
    @return {boolean}
    @deprecated
      Use `up.layer.isOverlay()` instead.
     */
    isOpen: function() {
      up.migrate.deprecated('up.popup.isOpen()', 'up.layer.isOverlay()');
      return up.layer.isOverlay();
    },
    sync: function() {
      up.migrate.deprecated('up.popup.sync()', 'up.layer.sync()');
      return up.layer.sync();
    }
  });

  up.migrate.renamedEvent('up:popup:open', 'up:layer:open');

  up.migrate.renamedEvent('up:popup:opened', 'up:layer:opened');

  up.migrate.renamedEvent('up:popup:close', 'up:layer:dismiss');

  up.migrate.renamedEvent('up:popup:closed', 'up:layer:dismissed');

  up.link.targetMacro('up-popup', {
    'up-layer': 'new popup'
  }, function() {
    return up.migrate.deprecated('[up-popup]', '[up-layer="new popup"]');
  });

}).call(this);

/***
Tooltips
========

Unpoly used to come with a basic tooltip implementation.
This feature is now deprecated.

@module up.tooltip
 */

(function() {
  up.tooltip = (function() {
    return up.macro('[up-tooltip]', function(opener) {
      up.migrate.warn('[up-tooltip] has been deprecated. A [title] was set instead.');
      return up.element.setMissingAttr(opener, 'title', opener.getAttribute('up-tooltip'));
    });
  })();

}).call(this);
(function() {
  var preloadDelayMoved, u,
    slice = [].slice;

  u = up.util;


  /***
  @module up.network
   */

  up.migrate.renamedPackage('proxy', 'network');

  up.migrate.renamedEvent('up:proxy:load', 'up:request:load');

  up.migrate.renamedEvent('up:proxy:received', 'up:request:loaded');

  up.migrate.renamedEvent('up:proxy:loaded', 'up:request:loaded');

  up.migrate.renamedEvent('up:proxy:fatal', 'up:request:fatal');

  up.migrate.renamedEvent('up:proxy:aborted', 'up:request:aborted');

  up.migrate.renamedEvent('up:proxy:slow', 'up:request:late');

  up.migrate.renamedEvent('up:proxy:recover', 'up:request:recover');

  preloadDelayMoved = function() {
    return up.migrate.deprecated('up.proxy.config.preloadDelay', 'up.link.config.preloadDelay');
  };

  Object.defineProperty(up.network.config, 'preloadDelay', {
    get: function() {
      preloadDelayMoved();
      return up.link.config.preloadDelay;
    },
    set: function(value) {
      preloadDelayMoved();
      return up.link.config.preloadDelay = value;
    }
  });

  up.migrate.renamedProperty(up.network.config, 'maxRequests', 'concurrency');

  up.migrate.renamedProperty(up.network.config, 'slowDelay', 'badResponseTime');

  up.migrate.handleRequestOptions = function(options) {
    return up.migrate.fixKey(options, 'data', 'params');
  };


  /***
  Makes an AJAX request to the given URL and caches the response.
  
  The function returns a promise that fulfills with the response text.
  
  \#\#\# Example
  
  ```
  up.ajax('/search', { params: { query: 'sunshine' } }).then(function(text) {
    console.log('The response text is %o', text)
  }).catch(function() {
    console.error('The request failed')
  })
  ```
  
  @function up.ajax
  @param {string} [url]
    The URL for the request.
  
    Instead of passing the URL as a string argument, you can also pass it as an `{ url }` option.
  @param {Object} [options]
    See options for `up.request()`.
  @return {Promise<string>}
    A promise for the response text.
  @deprecated
    Use `up.request()` instead.
   */

  up.ajax = function() {
    var args, pickResponseText;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.deprecated('up.ajax()', 'up.request()');
    pickResponseText = function(response) {
      return response.text;
    };
    return up.request.apply(up, args).then(pickResponseText);
  };


  /***
  Removes all cache entries.
  
  @function up.proxy.clear
  @deprecated
    Use `up.cache.clear()` instead.
   */

  up.network.clear = function() {
    up.migrate.deprecated('up.proxy.clear()', 'up.cache.clear()');
    return up.cache.clear();
  };

  up.network.preload = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.deprecated('up.proxy.preload(link)', 'up.link.preload(link)');
    return (ref = up.link).preload.apply(ref, args);
  };


  /***
  @class up.Request
   */

  up.Request.prototype.navigate = function() {
    up.migrate.deprecated('up.Request#navigate()', 'up.Request#loadPage()');
    return this.loadPage();
  };


  /***
  @class up.Response
   */


  /***
  Returns whether the server responded with a 2xx HTTP status.
  
  @function up.Response#isSuccess
  @return {boolean}
  @deprecated
    Use `up.Response#ok` instead.
   */

  up.Response.prototype.isSuccess = function() {
    up.migrate.deprecated('up.Response#isSuccess()', 'up.Response#ok');
    return this.ok;
  };


  /***
  Returns whether the response was not [successful](/up.Response.prototype.ok).
  
  @function up.Response#isError
  @return {boolean}
  @deprecated
    Use `!up.Response#ok` instead.
   */

  up.Response.prototype.isError = function() {
    up.migrate.deprecated('up.Response#isError()', '!up.Response#ok');
    return !this.ok;
  };

}).call(this);

/***
@module up.radio
 */

(function() {
  up.migrate.renamedProperty(up.radio.config, 'hungry', 'hungrySelectors');

}).call(this);

/***
@module up.viewport
 */

(function() {
  var slice = [].slice;

  up.migrate.renamedPackage('layout', 'viewport');

  up.migrate.renamedProperty(up.viewport.config, 'viewports', 'viewportSelectors');

  up.migrate.renamedProperty(up.viewport.config, 'snap', 'revealSnap');


  /***
  Returns the scrolling container for the given element.
  
  Returns the [document's scrolling element](/up.viewport.root)
  if no closer viewport exists.
  
  @function up.viewport.get
  @param {string|Element|jQuery} target
  @return {Element}
  @deprecated
    Use `up.viewport.get()` instead.
   */

  up.viewport.closest = function() {
    var args, ref;
    args = 1 <= arguments.length ? slice.call(arguments, 0) : [];
    up.migrate.deprecated('up.viewport.closest()', 'up.viewport.get()');
    return (ref = up.viewport).get.apply(ref, args);
  };

}).call(this);
(function() {
  up.framework.stopExtension();

}).call(this);
(function() {


}).call(this);
