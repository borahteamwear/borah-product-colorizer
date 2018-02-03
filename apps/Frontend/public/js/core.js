;'use strict';

/**
 * Triple Bits - WooCommerce Product Colorizer
 * @author Ilgıt Yıldırım <ilgityildirim@gmail.com>
 * @see https://triplebits.com
 */
var TB_ProductColorizer = function($)
{

    var that                = {},
        cache               = {elements: []},
        listenedElements    = {}
    ;

    /**
     * Get / Set Cache for Selector
     * @param {String} selector
     * @returns {*}
     */
    cache.get = function (selector)
    {
        // It is already cached!
        if ($.inArray(selector, cache.elements) !== -1)
        {
            return cache.elements[selector];
        }

        // Create cache and return
        cache.elements[selector] = jQuery(selector);

        return cache.elements[selector];
    };

    /**
     * Refreshes given cache
     * @param {String} selector
     */
    cache.refresh = function (selector)
    {
        selector.elements[selector] = jQuery(selector);
    };

    /**
     * @type {Function}
     * @deprecated
     */
    var getListenedElements = function()
    {
        var $container = cache.get('#tb_svg_container');

        cache.get('#tb_svg_container').find('img.tb_wpc_svg_convert').each(function()
        {
            var $element    = $(this),
                $target     = $(this.getAttribute('data-listen'))
            ;

            if (!$target || 1 > $target.length)
            {
                return true;
            }

            // console.log('Adding', $element);
            if (!$.isArray(listenedElements[$target.attr('id')]))
            {
                listenedElements[$target.attr('id')] = [];
            }

            listenedElements[$target.attr('id')].push($element);

            $target.data('old-value', $target.val());

            $target.bind('propertychange change click keyup input paste', function()
            {
                if ($target.data('old-value') === $target.val())
                {
                    return;
                }

                // Update old value
                $target.data('old-value', $target.val());

                if ($container.is(':hidden'))
                {
                    $container.show();
                }

                for (var i = 0; i < listenedElements[$target.attr('id')].length; i++)
                {
                    var $currentElement = listenedElements[$target.attr('id')][i];

                    $currentElement.find('path').css('fill', that.hex2rgb(this.value));
                }
            });
        });
    };

    /**
     * @param {string} hex
     * @returns {string}
     */
    that.hex2rgb = function(hex)
    {
        var h = '0123456789ABCDEF',
            r = h.indexOf(hex[1]) * 16 + h.indexOf(hex[2]),
            g = h.indexOf(hex[3]) * 16 + h.indexOf(hex[4]),
            b = h.indexOf(hex[5]) * 16 + h.indexOf(hex[6])
        ;

        return 'rgba(' + r + ', ' + g + ', ' + b  + ', 1)';
    };

    /**
     * @type {Function}
     */
    var convertImagesToSVG = function()
    {
        var $container      = cache.get('#tb_svg_container'),
            $moveContainer  = cache.get('.tb_wpc_place_svg_here > .tm-collapse > .tm-collapse-wrap')
        ;

        $container.find('img.tb_wpc_svg_convert')
            .each(function()
            {
                that.convertImageToSVG($(this));
            });

        // Move
        if ($moveContainer && $moveContainer.length > 0)
        {
            $container.detach().appendTo($moveContainer);
        }
    };

    /**
     * @param {Object} $image
     */
    that.convertImageToSVG = function($image)
    {
        var imgID       = $image.attr('id'),
            imgClass    = $image.attr('class'),
            imgURL      = $image.attr('src'),
            $container  = cache.get('#tb_svg_container'),
            height;

        jQuery.get(imgURL, function(data)
        {
            // Get the SVG tag, ignore the rest
            var $svg = jQuery(data).find('svg');

            // Add replaced image's ID to the new SVG
            if (typeof imgID !== 'undefined')
            {
                $svg = $svg.attr('id', imgID);
            }

            // Add replaced image's classes to the new SVG
            if (typeof imgClass !== 'undefined')
            {
                $svg = $svg.attr('class', imgClass+' replaced-svg');
            }

            if (!$svg.attr('viewBox'))
            {
                $svg.attr('viewBox', ('0 0 '
                    + $svg.attr('width').match(/[0-9]+\.[0-9]*/) + ' '
                    + $svg.attr('height').match(/[0-9]+\.[0-9]*/)));
            }

            // Remove any invalid XML tags as per http://validator.w3.org
            $svg = $svg.removeAttr('xmlns:a');

            // Replace image with new SVG
            $image.replaceWith($svg);

            height = $svg.outerHeight();

            if ($container.outerHeight() < height)
            {
                $container.height(height);
            }

        }, 'xml');
    };

    /**
     * @type {Function}
     */
    var elements = function()
    {
        var $container = cache.get('#tb_svg_container');

        $container.hide();
        $container.find('.tb_wpc_template_container').hide();

        selectStyle();
        rebuildSpectrum();
        clickableTemplates();
    };

    /**
     * Select Style / Template
     */
    var selectStyle = function()
    {
        $(tb_wpc.templates.selector).on('change', function()
        {
            var selectedTemplate    = $(this).val(),
                templateId          = parseInt(tb_wpc.templates.values[selectedTemplate]),
                $svgContainer       = cache.get('#tb_svg_container'),
                $templateContainer  = cache.get('#template_' + templateId),
                totalSvg            = $templateContainer.find('svg').length - 1
            ;

            if (isNaN(templateId))
            {
                $svgContainer.hide();
                $svgContainer.find('.tb_wpc_template_container').hide();
                return false;
            }

            if ($svgContainer.is(':hidden'))
            {
                $svgContainer.show();
            }

            $svgContainer.find('> .tb_wpc_template_container').not($templateContainer).hide();

            if ($templateContainer.is(':hidden'))
            {
                $templateContainer.show();
                setTimeout(function() {fixHeight($templateContainer)}, 50);
            }

            // Hide additional colors / apply colors
            for (var i = 0; i < tb_wpc.colors.length; i++)
            {
                var currentIndex    = i + 1,
                    $element        = $(tb_wpc.colors[i].selector),
                    $container      = $element.parents('.tm-cell.cpf-type-color'),
                    $svg            = $templateContainer.find('svg').eq(currentIndex)
                ;

                if (currentIndex > totalSvg)
                {
                    $container.hide();

                    $svg.find('path').css('fill', '');
                }
                else
                {
                    $container.show();

                    if (null !== $element.val() && $element.val().length > 0)
                    {
                        $svg.find('path').css('fill', $element.spectrum('get').toRgbString());
                    }
                }
            }

        });
    };

    /**
     * @type {Function}
     */
    var rebuildSpectrum = function()
    {
        if (!$.isArray(tb_wpc.colors) || 'undefined' === typeof(tb_wpc.templates.selector) || !$(tb_wpc.templates.selector) || $(tb_wpc.templates.selector).length < 1)
        {
            return;
        }

        var spectrumOptions         = {
            // preferredFormat         : 'rgb',
            showPaletteOnly         : true,
            showPalette             : true,
            hideAfterPaletteSelect  : true,
            allowEmpty              : true,
            palette                 : [],
            change                  : function(color)
            {
                var hexCode = color.toHexString();

            },
            show                    : function(color)
            {
                if ('undefined' === typeof(tb_wpc.colorNames))
                {
                    return true;
                }

                var $span = $('.sp-container > .sp-palette-container > .sp-palette > .sp-palette-row > span.sp-thumb-el[title][data-color]');

                $span.each(function()
                {
                    var $this   = $(this),
                        hexCode = $this.attr('title')//.replace('#', '')
                    ;

                    if (tb_wpc.colorNames[hexCode])
                    {
                        $this.attr('title', tb_wpc.colorNames[hexCode]);
                    }
                });
            }
        };

        for (var i = 0; i < tb_wpc.colors.length; i++)
        {
            var $element = $(tb_wpc.colors[i].selector);

            if (!$element || $element.length < 1)
            {
                continue;
            }

            $element.spectrum('destroy');

            if (isDefaultColorSet(tb_wpc.colors[i]))
            {
                $element.val('#' + tb_wpc.colors[i].defaultColor);

                spectrumOptions.color = '#' + tb_wpc.colors[i].defaultColor;
            }
            else if ('undefined' !== typeof(spectrumOptions.color))
            {
                delete spectrumOptions.color;
            }

            spectrumOptions.palette = tb_wpc.colors[i].palette;
            spectrumOptions.change  = function(color)
            {
                var $this               = $(this),
                    selectedTemplate    = $(tb_wpc.templates.selector).val(),
                    templateId          = parseInt(tb_wpc.templates.values[selectedTemplate]),
                    // $svgContainer       = cache.get('#tb_svg_container'),
                    $templateContainer  = cache.get('#template_' + templateId),
                    index               = findIndex($this) + 1,
                    $svg                = $templateContainer.find('svg').eq(index),
                    hexCode             = color.toHexString();
                ;

                $svg.find('path').css('fill', color.toRgbString());

                if (0 < hexCode.length)
                {
                    hexCode = hexCode.replace('#', '');
                }

                that.overridePrices();

                $this.val(tb_wpc.colorNames['#' + hexCode]);
            };

            $element.spectrum(spectrumOptions);
        }

        function findIndex($element)
        {
            var id = '#' + $element.attr('id')
            ;

            for (var i = 0; i < tb_wpc.colors.length; i++)
            {
                if ($(tb_wpc.colors[i].selector).is($(id)))
                {
                    return i;
                }
            }

            return 0;
        }

        function isDefaultColorSet(element)
        {
            return (
                'undefined' !== typeof(tb_wpc.colors[i].defaultColor) &&
                null !== tb_wpc.colors[i].defaultColor &&
                tb_wpc.colors[i].defaultColor.length > 0
            );
        }
    };

    /**
     * Fix height of template contianer
     * @param {Object} $templateContainer
     */
    var fixHeight = function($templateContainer)
    {
        var maxHeight   = 0,
            $svgs       = $templateContainer.find('> svg'),
            total       = $svgs.length,
            i           = 1;
        ;

        $svgs.each(function()
        {
            var height = $(this).outerHeight();

            if (height > maxHeight)
            {
                maxHeight = height;
            }

            if (i >= total)
            {
                $templateContainer.css('height', maxHeight + 'px');
            }

            i++;
        });
    };

    /**
     * @type {Function}
     * @returns {boolean}
     */
    var clickableTemplates = function()
    {
        if (!isClickAbleTemplatesActive())
        {
            console.log('not clickable templates');
            return false;
        }

        var $select     = $(tb_wpc.clickAbleTemplates.select),
            $products   = $(tb_wpc.clickAbleTemplates.products)
        ;

        if (!$select || $select.length < 1 || !$products || $products.length < 1)
        {
            return false;
        }

        $products.on('click', function(e)
        {
            e.preventDefault();

            var value = $(this).attr('data-value');

            if ('undefined' === typeof(value) || value.length < 1)
            {
                return;
            }

            $select.val(value).change();
        });
    };

    var isClickAbleTemplatesActive = function()
    {
        return (
            'undefined' !== typeof(tb_wpc.clickAbleTemplates.select) ||
            'undefined' !== typeof(tb_wpc.clickAbleTemplates.products) ||
            tb_wpc.clickAbleTemplates.select.length < 1 || tb_wpc.clickAbleTemplates.products.length < 1
        );
    };

    that.getProductPrice = function()
    {
        var quantity = parseInt(cache.get('form.cart .quantity > input[type="number"][name="quantity"].qty').val()),
            price    = parseFloat(tb_wpc.productPrice),
            discount = findMatchingDiscount()
        ;

        /**
         * @returns {Object|null}
         */
        function findMatchingDiscount()
        {

            /**
             * @param {Object} discount
             * @returns {boolean}
             */
            function isMatch(discount)
            {
                return (
                    quantity >= discount.minQuantity &&
                    (discount.maxQuantity <= 0 || quantity <= discount.maxQuantity)
                );
            }

            var discount = null;

            for(var i = 0; i < tb_wpc.discounts.length; i++)
            {
                if (isMatch(tb_wpc.discounts[i]))
                {
                    discount = tb_wpc.discounts[i];
                    break;
                }
            }

            return discount;
        }

        if (isNaN(price))
        {
            price = 0;
        }

        if (null !== discount && discount.price > 0)
        {
            price = discount.price;
        }
        else if (null !== discount && discount.percentage > 0)
        {
            price = price - ((price / 100) * discount.percentage);
        }

        return price;
    };

    /**
     * @return {String}
     */
    that.calculateNewTotalPrice = function(productPrice)
    {
        if ('undefined' === typeof(newTotalPrice))
        {
            productPrice = that.getProductPrice();
        }

        var quantity        = parseInt(cache.get('form.cart .quantity > input[type="number"][name="quantity"].qty').val()),
            newPrice        = 0
        ;

        if (isNaN(quantity) || 0 >= quantity)
        {
            quantity = 1;
        }

        for (var i = 0; i < tb_wpc.colors.length; i++)
        {
            var $element = $(tb_wpc.colors[i].selector);

            if (!$element || $element.length < 1)
            {
                continue;
            }

            var color = $element.spectrum('get');

            if (!color)
            {
                continue;
            }

            var hexCode = color.toHexString();

            if (!hexCode || 0 >= hexCode.length)
            {
                continue;
            }

            hexCode = hexCode.replace('#', '');

            var price = parseFloat(tb_wpc.colorPrices[hexCode]);

            if (isNaN(price))
            {
                continue;
            }

            newPrice = price;

            break;
        }

        newPrice += productPrice;
        newPrice += that.additionalTMPrices();
        newPrice *= quantity;
        newPrice += that.getTMQuantityPrices();
        newPrice = newPrice.toFixed(2);

        return newPrice;
    };

    /**
     * @returns {int|float}
     */
    that.additionalTMPrices = function()
    {
        var additionalPrices = 0;

        $('input[type="radio"].tm-epo-field:checked').each(function()
        {
            var $this   = $(this),
                type    = $this.attr('type'),
                price   = that.getTMPrice($this.attr('data-original-rules'))
            ;

            additionalPrices += price;
        });

        return additionalPrices;
    };

    /**
     * @returns {number}
     */
    that.getTMQuantityPrices = function()
    {
        var additionalPrices = 0;

        $('input[type="number"].tm-epo-field').each(function()
        {
            var $this   = $(this),
                qty     = parseInt($this.val()),
                $parent = $this.parents('ul[data-original-rules]')
            ;

            if (isNaN(qty) || !$parent || $parent.length < 1)
            {
                return;
            }

            var price = that.getTMPrice($parent.attr('data-original-rules'));

            price = price * parseInt($this.val());

            additionalPrices += price;
        });

        return additionalPrices;
    };

    /**
     * @param {string} price
     * @returns {number}
     */
    that.getTMPrice = function(price)
    {
        try
        {
            price = JSON.parse(price);

            if (typeof(price[0]) !== 'undefined')
            {
                price = parseFloat(price[0]);

                if (isNaN(price))
                {
                    price = 0;
                }
            }
        }
        catch (error)
        {
            price = 0;
        }

        return price;
    };

    that.overridePrices = function()
    {
        function overridePrices()
        {
            var productPrice    = that.getProductPrice(),
                totalPrice      = that.calculateNewTotalPrice(productPrice)
            ;

            cache.get('p.price span.amount').html('<span class="woocommerce-Price-currencySymbol">$</span>' + totalPrice);
            cache.get('dd.tm-final-totals > span.price.amount.final').text('$' + totalPrice);

            cache.get('input.tb_wpc_qty[type="number"]').each(function()
            {
                that.calculateNewTotalPrice(productPrice);
            });
        }

        setTimeout(overridePrices, 250);
    };

    var tmChanges = function()
    {
        // Disable fadeInDown effect from accordions
        $('.tb-pc-tm-disable-animation .tm-collapse > .tm-collapse-wrap').removeClass('tm-collapse-wrap');

        // Add an empty value to template selector
        $(tb_wpc.templates.selector).prepend('<option value="" selected="selected">Choose a Style</option>');

        var $inputQty   = cache.get('form.cart .quantity > input[type="number"][name="quantity"].qty'),
            $qtyInputs  = $('input.tb_wpc_qty'),
            $cartForm   = $('form[class="cart"][enctype="multipart/form-data"]')
        ;

        $inputQty.val('0');

        $qtyInputs.each(function() {
            var $this = $(this);

            $this.attr('type', 'number');

            $this.removeAttr('data-price data-rules data-original-rules data-rulestype data-freechars');
        });

        // When an input change happens that should effect total quantity
        $qtyInputs.off().on('input', function()
        {
            var totalQty        = 0,
                totalQtyInputs  = $qtyInputs.length,
                i               = 1
            ;

            $qtyInputs.each(function() {
                var $this   = $(this),
                    qty     = parseInt($(this).val());

                if (!isNaN(qty) && qty > 0)
                {
                    totalQty += qty;
                }
                else
                {
                    $this.val('0');
                }

                if (i >= totalQtyInputs)
                {
                    $inputQty.val(totalQty);
                    that.overridePrices();
                }

                i++;
            });
        });

        $inputQty.off().on('input', function()
        {
            that.overridePrices();
        });

        $cartForm.on('submit', function(e)
        {
            var uploadedFile    = $(tb_wpc.fileUploadInput).val(),
                extension       = getFileExtension(uploadedFile)
            ;

            if (uploadedFile.length === 0 || extension ===  tb_wpc.allowedFile)
            {
                return true;
            }

            e.preventDefault();

            setTimeout(function()
            {
                $('#flasho, .fl-overlay').remove();
                $cartForm.find('button[type="submit"]').removeClass('disabled');
            }, 2000);

            return false;
        });
    };

    var getFileExtension = function(fileName)
    {
        var regex       = /(?:\.([^.]+))?$/;

        return regex.exec(fileName)[1];
    };

    var hideAddToCartButtonForQty = function()
    {
        var $qtyInputs = $('input.tb_wpc_qty'),
            $btn       = $('button[type="submit"][name="add-to-cart"][value]')
        ;

        if (!$qtyInputs || $qtyInputs.length < 1 || !tb_wpc.isCustomTeamProduct)
        {
            return;
        }


        if (getTotal() < 5)
        {
            $btn.attr('disabled', 'disabled');
            $btn.hide();
        }

        $('form[class="cart"]').on('submit', function(e)
        {
            total = getTotal();

            if (total >= 5)
            {
                return true;
            }

            e.preventDefault();

            return false;
        });

        $qtyInputs.on('keyup keydown click', function()
        {

            if (getTotal() >= 5)
            {
                $btn.removeAttr('disabled');
                $btn.show();
            }
            else
            {
                $btn.attr('disabled', 'disabled');
                $btn.hide();
            }
        });

        function getTotal()
        {
            var total = 0;

            $qtyInputs.each(function()
            {
                var value = parseInt(this.value);

                if (isNaN(value))
                {
                    return true;
                }

                total += value;
            });

            return total;
        }
    };

    /**
     * @type {Function}
     */
    that.init = (function()
    {
        elements();
        convertImagesToSVG();
        tmChanges();
        hideAddToCartButtonForQty();
    });

    return that;

}(jQuery);

jQuery(document).ready(function()
{
    TB_ProductColorizer.init();
});