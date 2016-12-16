/* =============================================================
 * fy-rolling.js v1.0.2
 * =============================================================
 * 图片列表滚动插件
 * 作者: 许剑
 * 最后编辑时间: 2016-03-02
 * =============================================================
 * 控制参数
 * stretch: 是否拉伸图片占满显示区域
 * num: 显示的滚动个数
 * start: 开始时的图片位置
 * interval: 自动滚动间隔事件
 * speed: 动画速度
 * auto: 自动滚动
 * rollto: 有大图显示的情况下，接管轮换图片点击事件，变为滚动到被点击的图片上
 * mode: 滚动模式: roll 无缝连续滚动; page 翻页模式
 * autoStyle: 自动处理样式
 * autoIndicators: 自动生成当前进度指示器
 * autoWidth: 自动宽度, 占满整个父容器
 * 事件
 * setwhole: 设置大图之后触发
 * ============================================================ */

!function ($) {

    "use strict"; // jshint ;_;

    var Rolling = function (element, options) {
        var i, j, $itemImages

        this.options     = options
        this.$element    = $(element)
        this.$whole      = this.$element.find('.f-rolling-whole')
        this.$indicators = this.$element.find('.f-rolling-indicators')
        this.$imgList    = this.$element.find('.f-rolling-images')
        this.$items      = this.$imgList.find('li')
        this.$button     = this.$element.find('.f-rolling-btn')

        // 自动宽度
        if(this.options.autoWidth) {
            i = this.$element.parent().width()
            this.$element.width(i)
            this.$items.width(i)
            this.$whole.width(i)
        }

        this.itemNum         = this.$items.length
        this.itemWidth       = this.$items.width()
        this.itemHeight      = this.$items.height()
        this.itemOuterWidth  = this.$items.outerWidth(true)
        this.itemOuterHeight = this.$items.outerHeight(true)
        this.wholeWidth      = this.$whole.length ? this.$whole.width() : 0
        this.wholeHeight     = this.$whole.length ? this.$whole.height() : 0
        this.active          = 0
        this.page            = 0
        this.totalPage       = Math.floor(this.itemNum / this.options.num)

        this.rollOffset = 1 // 滚动偏移量

        if(this.options.mode == 'roll') { // 无缝滚动模式
            // 补齐无缝滚动所需的元素
            i = j = 0
            this.$imgList.prepend(this.$items.eq(this.itemNum - 1).clone())
            while(i++ <= this.options.num - 1) {
                this.$imgList.append(this.$items.eq(j++).clone())
                if(j >= this.itemNum) j = 0
            }
            this.activeItemWidth = this.itemOuterWidth
            this.activeItemHeight = this.itemOuterHeight
        } else if (this.options.mode == 'page') {
            // 设置当前活动元素
            this.$active = this.$items.eq(this.active).addClass('active')
            this.activeItemWidth = this.$active.outerWidth(true)
            this.activeItemHeight = this.$active.outerHeight(true)
            this.rollOffset = 0
        }

        if(this.options.autoStyle) {
            // 处理图片缩放、居中
            $itemImages = this.$imgList.find('img')
            for(i = 0; i < $itemImages.length; i++) {
                $itemImages.eq(i).css(this.centerImg(this.itemWidth, this.itemHeight, $itemImages[i], this.options.stretch))
                if(this.$whole.length != 0) {
                    $itemImages.eq(i).data('whole', this.centerImg(this.wholeWidth, this.wholeHeight, $itemImages[i], this.options.stretch))
                }
            }
            // 未加载完成的图片居中
            $itemImages.on(
                'load'
              , {width: this.itemWidth, height: this.itemHeight, wWidth: this.wholeWidth, wHeight: this.wholeHeight, rolling: this}
              , function (event) {
                    var $this = $(this)
                    if(event.data.rolling.options.autoStyle) {
                        $this.css(event.data.rolling.centerImg(event.data.width, event.data.height, this, event.data.rolling.options.stretch))
                        if (event.data.wWidth && event.data.wHeight) {
                            $this.data('whole', event.data.rolling.centerImg(event.data.wWidth, event.data.wHeight, this, event.data.rolling.options.stretch))
                        }
                    }
            })
        }

        // 设置滚动元素的宽度
        this.$element.find('.f-rolling-mask').width((this.options.num - 1) * this.itemOuterWidth + this.activeItemWidth)
        this.$imgList.width((this.options.num + this.itemNum) * this.itemOuterWidth + this.activeItemWidth)

        // 处理当前进度指示器
        if(this.$indicators.length == 0 && this.options.autoIndicators) {
            var htmlIndicators = '<ul class="f-rolling-indicators">'
            for(i = 0; i < this.itemNum; i++) {
                htmlIndicators += '<li>' + (i + 1) + '</li>'
            }
            htmlIndicators += '</ul>'
            this.$element.append(htmlIndicators)
            this.$indicators = this.$element.find('.f-rolling-indicators')
        }

        // 点击指示位置的事件
        if(this.$indicators.length) {
            this.$indicators.children().on('click', {rolling: this}, function (event) {
                var index = $(this).index()

                event.preventDefault()

                if(index > event.data.rolling.itemNum - 1 || index < 0) { return }

                if(index != event.data.rolling.active) {
                    event.data.rolling.active = index
                    event.data.rolling.roll()
                }
            })
        }

        // 点击轮换图片事件
        if(this.$whole.length && this.options.rollto) {
            this.$imgList.children().find('*').on('click', {rolling: this}, function (event) {
                var index = ($(this).closest('li').index() - event.data.rolling.rollOffset) % event.data.rolling.itemNum

                event.preventDefault()

                if(index != event.data.rolling.active) {
                    event.data.rolling.active = index
                    if (event.data.rolling.options.mode == 'roll') {
                        event.data.rolling.roll()
                    } else if (event.data.rolling.options.mode == 'page'){
                        event.data.rolling.setActive()
                    }
                }
            })
        }

        // 处理按钮隐藏
        if(this.options.hideButton) {
            this.$element
                .mouseover($.proxy(function () {
                    this.$button.show()
                }, this))
                .mouseout($.proxy(function () {
                    this.$button.hide()
                }, this))
        }

        // 初始化滚动元素
        this.roll(false)
        this.setActive()

        if(this.options.auto) {
            this.run()
            this.$element.find('.f-rolling-images, .f-rolling-btn, .f-rolling-indicators').mouseover($.proxy(this.pause, this))
                .mouseout($.proxy(this.run, this))
        }
    }

    Rolling.prototype = {
        prev: function () {
            if(--this.active < 0) {
                switch(this.options.mode) {
                    case 'roll':
                        this.active = this.itemNum - 1
                        this.$imgList.css({left: -(this.itemNum + this.rollOffset)* this.itemOuterWidth})
                        break;
                    case 'page':
                        this.active = 0
                        break;
                    default:
                        break;
                }
            }
            this.roll()
        }

      , next: function () {
            if(++this.active >= this.itemNum) {
                switch(this.options.mode) {
                    case 'roll':
                        this.active = 0
                        this.$imgList.css({left: 0})
                        break;
                    case 'page':
                        this.active = this.itemNum - 1
                        break;
                    default:
                        break;
                }
            }
            this.roll()
        }

      , pagePrev: function () {
            if(--this.page >= 0) {
                this.turnPage()
            } else {
                this.page = 0;
            }
        }

      , pageNext: function () {
            if(++this.page <= this.totalPage) {
                this.turnPage()
            } else {
                this.page = this.totalPage
            }
        }

      , roll: function (animate) {
            animate = animate === undefined ? true : animate
            switch(this.options.mode) {
                case 'roll':
                    this.$imgList.stop().animate({left: -(this.active + this.rollOffset) * this.itemOuterWidth}, (animate ? this.options.speed : 0))
                    break;
                case 'page':
                    if (this.active >= ((this.page + 1) * this.options.num) || this.active < (this.page * this.options.num)) {
                        this.page = Math.floor(this.active / this.options.num)
                        this.turnPage(false)
                    }
                    break;
                default:
                    break;
            }
            this.setActive()
        }

      , turnPage: function (changeActive) {
            this.$imgList.stop().animate({left: -(this.page * this.options.num + this.rollOffset) * this.itemOuterWidth}, this.options.speed)
            if(changeActive === undefined || changeActive) {
                this.active = this.page * this.options.num
            }
            this.setActive()
      }

      , centerImg: function (dWidth, dHeight, imgObj, stretch) {
            var img, ratio, newWidth, newHeight, marginLeft, marginTop

            img = new Image()
            img.src = imgObj.src

            ratio = stretch ?
                Math.max(dWidth / img.width, dHeight / img.height) // 填满模式选择大的比例
              : Math.min(dWidth / img.width, dHeight / img.height) // 非填满模式选择小的比例

            newWidth = Math.round(img.width * ratio)
            newHeight = Math.round(img.height * ratio)

            if(stretch) {
                marginLeft = newWidth > dWidth ? Math.round((dWidth - newWidth) / 2) : 0
                marginTop = newHeight > dHeight ? Math.round((dHeight - newHeight) / 2) : 0
            } else {
                marginLeft = newWidth < dWidth ? Math.round((dWidth - newWidth) / 2) : 0
                marginTop = newHeight < dHeight ? Math.round((dHeight - newHeight) / 2) : 0
            }

            return {
                width: newWidth
              , height: newHeight
              , 'margin-left': marginLeft
              , 'margin-top': marginTop
            }
        }

      , setActive: function () {
            this.$indicators
                .find('li').removeClass('active')
                .eq(this.active).addClass('active')

            if(this.options.mode == 'page') {
                this.$items.eq(this.active % this.itemNum).addClass('active').siblings().removeClass('active')
            }
            this.setWhole();
        }

      , setWhole: function () {
            if(this.$whole.length && this.$items.length) {
                var $active = this.$items.eq(this.active % this.itemNum)
                  , $img = $active.find('img')
                  , $old = this.$whole.children()
                  , $new = $('<div style="display:none; position:absolute; top:0px; left:0px; width: ' + this.wholeWidth + 'px; height: ' + this.wholeHeight + 'px;">' + $active[0].innerHTML + '</div>')
                  , wholeCss = $img.data('whole')

                this.$whole.append($new);
                if(this.options.autoStyle) { // 自动设置样式
                    // 老 ie 的 bug: data('whole') 有时会消失不见
                    if(wholeCss === undefined) {
                        $img.data('whole', (wholeCss = this.centerImg(this.wholeWidth, this.wholeHeight, $img[0], this.options.stretch)))
                    }
                    $new.find('img').css(wholeCss)
                }

                if($old.length) {
                    $old.stop().fadeOut(this.options.speed, function () { $(this).remove() })
                    $new.stop().fadeIn(this.options.speed)
                } else {
                    $new.show();
                }
                this.$element.trigger('setwhole')
            }
        }

      , pause: function () {
            clearInterval(this.interval)
        }

      , run: function () {
            this.interval = setInterval($.proxy(this.next, this), this.options.interval)
        }
    }

    var old = $.fn.rolling

    $.fn.rolling = function (option) {
        return this.each(function () {
            var $this = $(this)
              , data = $this.data('rolling')
              , options = $.extend({}, $.fn.rolling.defaults, $this.data(), typeof option == 'object' && option)

            if(!data) $this.data('rolling', (data = new Rolling(this, options)))
            if(typeof option == 'string') data[option]()
        })
    }

    $.fn.rolling.defaults = {
        stretch: true // 是否拉伸图片占满显示区域
      , num: 4 // 显示的滚动个数
      , start: 0 // 开始时的图片位置
      , interval: 4000 // 自动滚动间隔事件
      , speed: 500 // 动画速度
      , auto: true // 自动滚动
      , rollto: true // 有大图显示的情况下，接管轮换图片点击事件，变为滚动到被点击的图片上
      , mode: 'roll' // 滚动模式: roll 无缝连续滚动; page 翻页模式
      , autoStyle: true // 自动处理样式
      , hideButton: false // 自动隐藏按钮
      , autoIndicators: true // 自动生成当前进度指示器
      , autoWidth: false // 自动宽度, 占满整个父容器
    }

    $.fn.rolling.noConflict = function () {
        $.fn.rolling = old
        return this
    }

    $(document).on('click.rolling.data-api', '.f-rolling-btn', function (e) {
        var $this = $(this)
          , $target = $this.data('target') ? $($this.data('target')) : $this.closest('.f-rolling')
          , action = false

        if ($this.is('.f-rolling-btn-prev')) { action = 'prev' }
        else if ($this.is('.f-rolling-btn-next')) { action = 'next' }
        else if ($this.is('.f-rolling-btn-page-prev')) { action = 'pagePrev' }
        else if ($this.is('.f-rolling-btn-page-next')) { action = 'pageNext'}

        action && $target.rolling(action)

        e.preventDefault()
    })

    $(window).on('load', function () {
        $('[data-ride="rolling"]').each(function () {
            $(this).rolling()
        })
    })

}(window.jQuery);

/* =============================================================
 * tab.js v1.1
 * =============================================================
 * 选项卡插件
 * 作者: 许剑
 * 最后编辑时间: 2014-10-28
 * ============================================================
 * 控制参数
 * delay: 延时, 毫秒为单位
 * on: 触发事件, 默认为 mouseover
 * ============================================================ */

!function ($) {

    "use strict"; // jshint ;_;

    var Tab = function (element, options) {
        this.$element = $(element)
        this.options = options
        this.timeId = null

        this.$element.find('.f-tab-nav').children().on(this.options.on, {'tab': this}, function(event) {
            event.data.tab.timeid = setTimeout($.proxy(function () {
                var $this = $(this)
                $this.addClass('active')
                    .siblings().removeClass('active')
                event.data.tab.$element.find('.f-tab-content').children(':eq(' + $this.index() + ')').addClass('active')
                    .siblings().removeClass('active')
            }, this), event.data.tab.options.delay)
        })
        .on('mouseout', {'tab': this}, function(event) {
            clearTimeout(event.data.tab.timeid)
        })
    }

    var old = $.fn.tab

    $.fn.tab = function (option) {
        return this.each(function () {
            var $this = $(this)
              , data = $this.data('tab')
              , options = $.extend({}, $.fn.tab.defaults, $this.data(), typeof option == 'object' && option)
            if(!data) $this.data('tab', (data = new Tab(this, options)))
            if(typeof option == 'string') data[option]()
        })
    }

    $.fn.tab.defaults = {
        delay: 0 // 延时
      , on: 'mouseover' // 触发事件
    }

    $.fn.tab.noConflict = function () {
        $.fn.tab = old
        return this
    }

    $(window).on('load', function () {
        $('[data-ride="tab"]').each(function () {
            $(this).tab()
        })
    })

}(window.jQuery);

/* =============================================================
 * stretch.js v1.1
 * =============================================================
 * 图片拉伸填充插件
 * 作者: 许剑
 * 最后编辑时间: 2016-03-27
 * ============================================================
 * 控制参数
 * fill: 图片是否填满整个容器
 * zoomEffect: 放大效果
 * ============================================================ */
!function ($) {

    "use strict"; // jshint ;_;

    var Stretch = function (element, options) {
        this.$element = $(element)
        this.options = options
        this.$images = this.$element.find('img')

        var $this, $container, i
        for(i = 0; i < this.$images.length; i++) {
            $this = $(this.$images[i])
            $container = $this.parent()
            $this.css(this.centerImg($container.width(), $container.height(), this.$images[i], this.options.fill)).show()
            if(this.options.zoomEffect) {
                $this.data('originalPosition', {width: $this.width() + 'px', 'height': $this.height() + 'px', 'margin-left': $this.css('margin-left'), 'margin-top': $this.css('margin-top')})
                $this.on('mouseover', this.zoom).on('mouseout', this.resetPosition)
            }
        }
    }

    Stretch.prototype = {
        centerImg: function (dWidth, dHeight, imgObj, fill) {
            var img, ratio, newWidth, newHeight, marginLeft, marginTop

            img = new Image()
            img.src = imgObj.src

            ratio = fill ?
                Math.max(dWidth / img.width, dHeight / img.height) // 填满模式选择大的比例
              : Math.min(dWidth / img.width, dHeight / img.height) // 非填满模式选择小的比例

            newWidth = Math.round(img.width * ratio)
            newHeight = Math.round(img.height * ratio)

            if(fill) {
                marginLeft = newWidth > dWidth ? Math.round((dWidth - newWidth) / 2) : 0
                marginTop = newHeight > dHeight ? Math.round((dHeight - newHeight) / 2) : 0
            } else {
                marginLeft = newWidth < dWidth ? Math.round((dWidth - newWidth) / 2) : 0
                marginTop = newHeight < dHeight ? Math.round((dHeight - newHeight) / 2) : 0
            }

            return {
                width: newWidth
              , height: newHeight
              , 'margin-left': marginLeft
              , 'margin-top': marginTop
            }
        }
      , zoom: function () {
            var $this, originalPosition, width, height, newWidth, newHeight, newTop, newLeft
            $this = $(this)
            originalPosition = $this.data('originalPosition')
            width = $this.width()
            height = $this.height()
            newWidth = width * 1.1
            newHeight = height * 1.1
            newTop = (height - newHeight) / 2
            newLeft = (width - newWidth) / 2

            $this.css(originalPosition).stop().animate({'width': newWidth + 'px', 'height': newHeight + 'px', 'margin-top': newTop + 'px', 'margin-left': newLeft + 'px'}, 200)
        }
      , resetPosition: function () {
            var $this = $(this)
            // $this.css($this.data('originalPosition'))
            // $this.stop().animate({'width': '240px', 'height': '150px', 'margin-top': '0px', 'margin-left': '0px'}, 200)
            $this.stop().animate($this.data('originalPosition'), 200)
        }
    }

    var old = $.fn.stretch

    $.fn.stretch = function (option) {
        return this.each(function () {
            var $this = $(this)
              , data = $this.data('stretch')
              , options = $.extend({}, $.fn.stretch.defaults, $this.data(), typeof option == 'object' && option)
            if(!data) $this.data('stretch', (data = new Stretch(this, options)))
            if(typeof option == 'string') data[option]()
        })
    }

    $.fn.stretch.defaults = {
        fill: true // 是否填满整个容器
      , zoomEffect: false // 放大效果
    }

    $.fn.stretch.noConflict = function () {
        $.fn.stretch = old
        return this
    }

    $(window).on('load', function () {
        $('[data-ride="stretch"').each(function () {
            $(this).stretch()
        })
    })
}(window.jQuery);