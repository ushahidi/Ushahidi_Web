/*
 * Copyright (c) 2009 Cameron Zemek
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */
;(function($) {
    function Hovertip(elem, conf) {
        // Create tooltip
        var tooltip = $('<div></div>')
            .addClass(conf.className)
            .html(elem.attr('title'))
            .insertAfter(elem);
        tooltip.hide();

        // Remove the browser tooltip
        elem.removeAttr('title');

        function setPosition(posX, posY) {
            tooltip.css({ left: posX, top: posY });
        }

        function updatePosition(event) {
            var tooltipWidth = tooltip.outerWidth();
            var tooltipHeight = tooltip.outerHeight();
            var $window = $(window);
            var windowWidth = $window.width() + $window.scrollLeft();
            var windowHeight = $window.height() + $window.scrollTop();
            var posX = event.pageX + conf.offset[0];
            var posY = event.pageY + conf.offset[1];
            if (posX + tooltipWidth > windowWidth) {
                // Move left
                posX = windowWidth - tooltipWidth;
            }
            if (posY + tooltipHeight > windowHeight) {
                // Move tooltip to above cursor
                posY = event.pageY - conf.offset[1] - tooltipHeight;
            }
            setPosition(posX, posY);
        }

        elem.hover(
            // Show
            function(event) {
                updatePosition(event);
                conf.show(tooltip);
            },
            // Hide
            function() {
                conf.hide(tooltip);
            }
        );
    }

    $.fn.hovertip = function(conf) {
        var defaultConf = {
            offset: [10, 10],
            className: 'hovertip',
            show: function(tooltip) {
                tooltip.fadeIn(150);
            },
            hide: function(tooltip) {
                tooltip.fadeOut(150);
            }
        };
        $.extend(defaultConf, conf);

        this.each(function() {
            var el = new Hovertip($(this), defaultConf);
            $(this).data("hovertip", el);
        });
    }
})(jQuery);
