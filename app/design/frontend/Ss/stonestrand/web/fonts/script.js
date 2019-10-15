var Site = (function($, window) {
  var win = $(window),
      body = $('body'),
      header = $('header');

  function fixedNav() {
    var mainEle = $('main'),
        offsetTop;
    if (!mainEle.length) { return; }
    offsetTop = mainEle.offset().top;
    win.on('scroll.fixedNav', function() {
      if (win.scrollTop() > offsetTop) {
        body.addClass('nav-fixed');
        header.addClass('header-fixed');
      } else {
        body.removeClass('nav-fixed');
        header.removeClass('header-fixed');
      }
    }).trigger('scroll.fixedNav');
  }

  function hideHeaderMsg() {
    $('[data-header-info]').on('click.closeHeaderInfo', function(e) {
      e.preventDefault();
      $(this).closest('.new-info').slideUp(500);
    });
  }

  function isMobile() {
    return window.Modernizr.mq('(max-width: 991px)');
  }

  function lazyLoad() {
    $('[data-lazy-load]').Lazy({
      effect: 'fadeIn',
      effectTime: 200,
      afterLoad: function(elem) {
        elem.css('opacity', '');
        elem.trigger('imgLoaded');
      }
    });
  }

  return {
    fixedNav: fixedNav,
    hideHeaderMsg: hideHeaderMsg,
    isMobile: isMobile,
    lazyLoad: lazyLoad
  }
})(jQuery, window);

jQuery(function() {
  Site.hideHeaderMsg();
  Site.fixedNav();
  Site.lazyLoad();
});

/**
 *  @name collapse
 *  @description description
 *  @version 1.0
 *  @options
 *    option
 *  @events
 *    event
 *  @methods
 *    init
 *    destroy
 */
;(function($, window, undefined) {
  'use strict';

  var pluginName = 'collapse',
      win = $(window);

  var setDataOptions = function() {
    this.options = $.extend({}, this.options, this.element.data(pluginName));
  };

  var openContent = function(targetItem) {
    var that = this,
        options = that.options;
    if (options.isMultiOpen) {
      targetItem.addClass(options.classActive);
      targetItem.children('[data-item-body]').slideDown(options.duration);
    } else {
      targetItem
        .addClass(options.classActive)
        .children('[data-item-body]').slideDown(options.duration)
        .end()
        .siblings()
        .removeClass(options.classActive)
        .children('[data-item-body]')
        .slideUp(options.duration);
    }
  };

  var closeContent = function(targetItem) {
    var that = this;
    targetItem
      .removeClass(that.options.classActive)
      .children('[data-item-body]')
      .slideUp(that.options.duration);
  };

  var initItemOpen = function() {
    var options = this.options,
        arrIdx = options.numItemOpenDefault;
    if (!arrIdx.length) {
      return;
    }
    for (var i = 0, l = arrIdx.length; i < l; i++) {
      if (arrIdx[i] < 1) { continue; }
      this.vars.items.eq(arrIdx[i] - 1)
        .addClass(options.classActive)
        .children('[data-item-body]')
        .show();
      if (!options.isMultiOpen) {
        return;
      }
    }
    this.vars.items.not('.' + options.classActive).children('[data-item-body]').css('display', 'none');
  };

  var toggleAllContent = function() {
    this.vars.items.toggleClass(this.options.classActive)
      .children('[data-item-body]').slideToggle();
  };

  var toggleGroupContent = function(groupEle) {
    groupEle.find('[data-item]').toggleClass(this.options.classActive)
      .children('[data-item-body]').slideToggle();
  };

  function Plugin(element, options) {
    this.element = $(element);
    this.options = $.extend({}, $.fn[pluginName].defaults, this.element.data(), options);
    this.init();
  }

  Plugin.prototype = {
    init: function() {
      var that = this,
          timeout;

      that.vars = {
        items: that.element.find('[data-item]')
      };

      setDataOptions.call(that);
      initItemOpen.call(that);

      that.vars.items.off('click.' + pluginName, '[data-item-heading]')
        .on('click.' + pluginName, '[data-item-heading]', function(e) {
          e.preventDefault();
          e.stopPropagation();
          var self = $(this);

          if (that.options.notRunOn &&
              (('desktop' === that.options.notRunOn && !window.Site.isMobile()) ||
                'mobile' === that.options.notRunOn && window.Site.isMobile())
             ) {

            return;
          }

          if (!window.Site.isMobile() && self.closest('[data-group]').length) {
            toggleGroupContent.call(that, self.closest('[data-group]'));
          } else if (that.options.isToggleAll) {
            toggleAllContent.call(that);
          } else {
            that.toggle(self.closest('[data-item]'));
          }
        });

      win.on('resize.' + pluginName, function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
          var groupItems = that.element.find('[data-group]');
          if (('desktop' === that.options.notRunOn && !window.Site.isMobile()) ||
              ('mobile' === that.options.notRunOn && window.Site.isMobile())) {

            that.vars.items.find('[data-item-body]').each(function() {
              that.show($(this).closest('[data-item]'));
            });
          } else if (!window.Site.isMobile() && groupItems.length) {
            groupItems.find('[data-item]').filter('.active').each(function() {
              closeContent.call(that, $(this));
            });
          }
        }, 100);
      });
    },
    toggle: function(targetItem) {
      var options = this.options;
      if (targetItem.hasClass(options.classActive)) {
        closeContent.call(this, targetItem);
      } else {
        openContent.call(this, targetItem);
      }
    },
    show: function(targetItem) {
      openContent.call(this, targetItem);
    },
    destroy: function() {
      win.off('resize.' + pluginName);
      this.vars.items.off('click.' + pluginName, '[data-item-heading]');
      $.removeData(this.element[0], pluginName);
    }
  };

  $.fn[pluginName] = function(options, params) {
    return this.each(function() {
      var instance = $.data(this, pluginName);
      if (!instance) {
        $.data(this, pluginName, new Plugin(this, options));
      } else if (instance[options]) {
        instance[options](params);
      }
    });
  };

  $.fn[pluginName].defaults = {
    isMultiOpen: true,
    isToggleAll: false,
    numItemOpenDefault: [],
    duration: 400,
    classActive: 'active',
    notRunOn: '', // desktop/mobile
    deviation: 5
  };

  $(function() {
    $('[data-' + pluginName + ']').on('customEvent', function() {
      // to do
    });

    $('[data-' + pluginName + ']')[pluginName]();
  });

}(jQuery, window));


/**
 *  @name popup
 *  @description description
 *  @version 1.0
 *  @options
 *    option
 *  @events
 *    event
 *  @methods
 *    init
 *    destroy
 */
;(function($, window, undefined) {
  'use strict';

  var pluginName = 'popup';

  var setDataOptions = function() {
    this.options = $.extend({}, this.options, this.element.data(pluginName));
  };

  function Plugin(element, options) {
    this.element = $(element);
    this.options = $.extend({}, $.fn[pluginName].defaults, this.element.data(), options);
    this.init();
  }

  Plugin.prototype = {
    init: function() {
      var that = this;

      setDataOptions.call(that);
      that.element
        .on('click.' + pluginName, function(e) {
          e.preventDefault();
          if (that.options.autoShow) { return; }
          that.show();
        })
        .on('click.' + pluginName, '.close', function(e) {
          e.preventDefault();
          that.hide();
        });

      if (that.options.autoShow) {
        setTimeout(function() {
          that.show();
        }, that.options.delayToAutoShow);
      }
    },
    show: function() {
      var that = this;
      that.options.target.fadeIn(function() {
        // TODO somethings.
      });
    },
    hide: function() {
      var that = this;
      that.options.target.fadeOut(function() {
        // TODO somethings.
      });
    },
    destroy: function() {
      this.element.off('click.' + pluginName)
        .off('click.' + pluginName, '.close');
      $.removeData(this.element[0], pluginName);
    }
  };

  $.fn[pluginName] = function(options, params) {
    return this.each(function() {
      var instance = $.data(this, pluginName);
      if (!instance) {
        $.data(this, pluginName, new Plugin(this, options));
      } else if (instance[options]) {
        instance[options](params);
      }
    });
  };

  $.fn[pluginName].defaults = {
    autoShow: false,
    delayToAutoShow: 10000
  };

  $(function() {
    $('[data-' + pluginName + ']').on('customEvent', function() {
      // to do
    });

    $('[data-' + pluginName + ']')[pluginName]();
  });

}(jQuery, window));


/**
 *  @name slider
 *  @description description
 *  @version 1.0
 *  @options
 *    option
 *  @events
 *    event
 *  @methods
 *    init
 *    destroy
 */
;(function($, window, undefined) {
  'use strict';

  var pluginName = 'slider',
      win = $(window);

  var options = {
    services: {
      slidesToShow: 3,
      slidesToScroll: 3,
      responsive: [{
        breakpoint: 992,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }]
    },
    news: {
      autoplaySpeed: 1500
    },
    otherFavorites: {
      slidesToShow: 4,
      slidesToScroll: 4,
      responsive: [{
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      }]
    }
  };

  var initSlider = function() {
    if (window.Site.isMobile()) {
      if (!this.element.hasClass('slick-initialized')) {
        this.element.slick(this.options);
      }
    } else {

      if (this.options.isNotInitDesktop && this.element.hasClass('slick-initialized')) {
        this.element.slick('unslick');
      } else if (!this.options.isNotInitDesktop && !this.element.hasClass('slick-initialized')) {
        this.element.slick(this.options);
      }
    }
  };

  var setDataOptions = function() {
    this.options = $.extend({}, this.options, this.element.data(pluginName));
    if (typeof this.options.typeSlide !== 'undefined') {
      this.options = $.extend({}, this.options, options[this.options.typeSlide]);
    }
  };

  function Plugin(element, options) {
    this.element = $(element);
    this.options = $.extend({}, $.fn[pluginName].defaults, this.element.data(), options);
    this.init();
  }

  Plugin.prototype = {
    init: function() {
      var that = this,
          imgItems = that.element.find('[data-lazy-load]'),
          timeout;

      setDataOptions.call(that);

      that.element.on('init.' + pluginName, function() {
        // to do somethings.
      });

      imgItems.on('imgLoaded', function() {
        if (that.element.find('[data-src]').length === 0) {
          initSlider.call(that);
        }
      });

      win.on('resize.' + pluginName, function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
          initSlider.call(that);
        }, 100);
      });

    },
    destroy: function() {
      this.element.off('init.' + pluginName);
      win.off('resize.' + pluginName);
      $.removeData(this.element[0], pluginName);
    }
  };

  $.fn[pluginName] = function(options, params) {
    return this.each(function() {
      var instance = $.data(this, pluginName);
      if (!instance) {
        $.data(this, pluginName, new Plugin(this, options));
      } else if (instance[options]) {
        instance[options](params);
      }
    });
  };

  $.fn[pluginName].defaults = {
    autoplay: true,
    infinite: true,
    autoplaySpeed: 3000
  };

  $(function() {
    $('[data-' + pluginName + ']').on('customEvent', function() {
      // to do
    });

    $('[data-' + pluginName + ']')[pluginName]();
  });

}(jQuery, window));


/**
 *  @name validate-form
 *  @description description
 *  @version 1.0
 *  @options
 *    option
 *  @events
 *    event
 *  @methods
 *    init
 *    destroy
 */
;(function($, window, undefined) {
  'use strict';

  var pluginName = 'validate-form';

  var remoteValidate = function(url, data, successCB, errorCB) {
    var that = this,
        entry = {},
        i, l;

    for (i = 0, l = data.length; i < l; i++) {
      entry[data[i].name] = data[i].value;
    }

    $.ajax({
      url: url,
      type: that.element.attr('method'),
      data: entry,
    })
    .done(function(res) {
      $.isFunction(successCB) && successCB(res);
    })
    .fail(function(err) {
      $.isFunction(errorCB) && errorCB(err);
    });
  };

  var errorRemoteValidate = function(err) {
    var errors = err.errors,
        i, l;
    if (!errors.length) { return; }
    for (i = 0, l = errors.length; i < l; i++) {
      console.log(this.element.find('[name="' + errors[i].name + '"]'));
      this.element.find('[name="' + errors[i].name + '"]')
        .after(errors[i].message);
    }
  };

  var successRemoteValidate = function(res) {
    console.log(res);
  };

  var setDataOptions = function() {
    this.options = $.extend({}, this.options, this.element.data(pluginName));
  };

  function Plugin(element, options) {
    this.element = $(element);
    this.options = $.extend({}, $.fn[pluginName].defaults, this.element.data(), options);
    this.init();
  }

  Plugin.prototype = {
    init: function() {
      var that = this;

      setDataOptions.call(that);
      that.element.parsley()
        .on('form:submit', function() {
          if (that.options.urlRemoteValidate) {
            // call ajax for validate & process success or error
            remoteValidate.call(that, that.options.urlRemoteValidate, that.element.serializeArray(), function(res) {
              // success callback
              if ('success' === res.status) {
                successRemoteValidate.call(that, res);
              } else {
                errorRemoteValidate.call(that, res);
              }
            }, function(err) {
              // error callback
              console.log(err);
            });
            return false;
          }
        })
        .on('field:validate', function() {
          that.element.find('.remote-message').remove();
        });
    },
    destroy: function() {
      $.removeData(this.element[0], pluginName);
    }
  };

  $.fn[pluginName] = function(options, params) {
    return this.each(function() {
      var instance = $.data(this, pluginName);
      if (!instance) {
        $.data(this, pluginName, new Plugin(this, options));
      } else if (instance[options]) {
        instance[options](params);
      }
    });
  };

  $.fn[pluginName].defaults = {
  };

  $(function() {
    $('[data-' + pluginName + ']').on('customEvent', function() {
      // to do
    });

    $('[data-' + pluginName + ']')[pluginName]();
  });

}(jQuery, window));


/**
 *  @name video-frame
 *  @description auto play when the video upon viewing, otherwise it will pause
 *  @version 1.0
 *  @options
 *    option
 *  @events
 *    event
 *  @methods
 *    init
 *    destroy
 */
;(function($, window, undefined) {
  'use strict';

  var pluginName = 'video-frame',
        win = $(window);

  var setDataOptions = function() {
    this.options = $.extend({}, this.options, this.element.data(pluginName));
  };

  var initUtubeApi = function() {
    var tag = document.createElement('script'),
        id = 'youtubeApi';

    tag.src = 'https://www.youtube.com/iframe_api';
    tag.id = id;
    if (document.getElementById(id)) {
      return;
    }
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
  };

  var getVideoContext = function() {
    var that = this,
        utubeEvents = {
          videoId: that.options.videoId, // 'igNVdlXhKcI'
          events: {
            'onReady': function() {
              if (!window.Modernizr.touch) {
                win.trigger('scroll.' + pluginName);
              }
            },
            'onStateChange': function(e) {
              if (0 === e.data) {
                that.vars.isEnded = true;
              }
            }
          }
        };

    switch (this.options.name) {
      case 'youtube':
        initUtubeApi();
        window.onYouTubeIframeAPIReady = function() {
          that.vars.videoContext = new window.YT.Player(that.element.find('[data-frame]').get(0), utubeEvents);
        };
        break;
      default:
    }
  };

  var actionWindowScroll = function() {
    if (win.scrollTop() + window.innerHeight >
        this.element.offset().top + this.element.outerHeight() / 2 &&
        this.element.offset().top + this.element.outerHeight() / 2 >
        win.scrollTop() && !this.vars.isEnded) {

      if (!this.vars.isPlaying) {
        this.element.find('.thumb').trigger('click.' + pluginName);
        this.vars.isPlaying = true;
      }
    } else if (this.vars.isPlaying) {
      this.vars.videoContext.pauseVideo();
      this.vars.isPlaying = false;
    }
  };

  var actionWindowFocus = function() {
    if (win.scrollTop() + window.innerHeight >
        this.element.offset().top + this.element.outerHeight() / 2 &&
        this.element.offset().top + this.element.outerHeight() / 2 >
        win.scrollTop() && !this.vars.isEnded && !this.vars.isPlaying) {

      this.element.find('.thumb').trigger('click.' + pluginName);
      this.vars.isPlaying = true;
    }
  };

  function Plugin(element, options) {
    this.element = $(element);
    this.options = $.extend({}, $.fn[pluginName].defaults, this.element.data(), options);
    this.init();
  }

  Plugin.prototype = {
    init: function() {
      var that = this;

      that.vars = {
        isPlaying: false,
        isEnded: false
      };

      setDataOptions.call(that);
      getVideoContext.call(that);

      that.element.on('click.' + pluginName, '.thumb', function(e) {
        e.stopPropagation();
        $(this).fadeOut();
        if ('undefined' !== typeof that.vars.videoContext) {
          that.vars.videoContext.playVideo();
        }
      });

      if (!window.Modernizr.touch) {
        win.on('scroll.' + pluginName, function() {
          if ('undefined' === typeof that.vars.videoContext ) { return; }
          actionWindowScroll.call(that);
        })
        .on('focus.' + pluginName, function() {
          if ('undefined' === typeof that.vars.videoContext ) { return; }
          actionWindowFocus.call(that);
        })
        .on('blur.' + pluginName, function() {
          if ('undefined' === typeof that.vars.videoContext ) { return; }
          if (that.vars.isPlaying) {
            that.vars.videoContext.pauseVideo();
            that.vars.isPlaying = false;
          }
        });
      }
    },
    destroy: function() {
      win.off('scroll.' + pluginName)
          .off('focus.' + pluginName)
          .off('blur.' + pluginName);
      this.element.off('click.' + pluginName, '.thumb');
      $.removeData(this.element[0], pluginName);
    }
  };

  $.fn[pluginName] = function(options, params) {
    return this.each(function() {
      var instance = $.data(this, pluginName);
      if (!instance) {
        $.data(this, pluginName, new Plugin(this, options));
      } else if (instance[options]) {
        instance[options](params);
      }
    });
  };

  $.fn[pluginName].defaults = {
  };

  $(function() {
    $('[data-' + pluginName + ']').on('customEvent', function() {
      // to do
    });

    $('[data-' + pluginName + ']')[pluginName]({
      key: 'custom'
    });
  });

}(jQuery, window));
