;'use strict';

/**
 * Triple Bits - WooCommerce Product Colorizer Extended
 * @author Ilgıt Yıldırım <ilgityildirim@gmail.com>
 * @see https://triplebits.com
 */
var TB_ProductColorizer = function($)
{
    var that    = {},
        cache   = {elements : []};

    /**
     * Get / Set Cache for Selector
     * @param {String} selector
     * @returns {*}
     */
    cache.get           = function(selector)
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
    cache.refresh       = function(selector)
    {
        selector.elements[selector] = jQuery(selector);
    };

    /**
     * Mask Image Upload
     */
    var uploadMaskImage = function()
    {
        if ('undefined' === typeof(wp.media))
        {
            return;
        }

        // Variables
        var fileFrame       = [],
            wpMediaPostID   = wp.media.model.settings.post.id;

        cache.get('body')
        // TB WooCommerce Product Colorizer upload image button
            .on('click', '.tb_wpc_upload_image_button', function(e) {
                e.preventDefault();

                console.log('clicked');

                var $this       = $(this),
                    id          = $this.attr("id"),
                    $input      = jQuery('#' + $this.data('input').replace(/\[/g, "\\[").replace(/\]/g, "\\]")),
                    setToPostID = parseInt($input.val()) || false;

                // Media frame already exists, re-open it
                if (fileFrame[id])
                {
                    // Set the post ID
                    fileFrame[id].uploader.uploader.param('post_id', setToPostID);

                    // Open frame
                    fileFrame[id].open();

                    return;
                }

                // Set the wp.media post id so the uploader grabs the ID we want when initialised
                wp.media.model.settings.post.id = setToPostID;

                // Create the media frame
                fileFrame[id] = wp.media.frames.file_frame = wp.media({
                    title: 'Select a mask image to upload',
                    button: {
                        text: $this.data('media-button-text') || 'Use This Image Mask'
                    },
                    multiple: false,
                    library: {
                        type: 'image/svg+xml'
                    }
                });

                // When an image is selected, run a callback
                fileFrame[id].on('select', function() {
                    var selectedImage = fileFrame[id].state().get('selection').first().toJSON();

                    // Set preview image
                    jQuery('#' + $this.data('preview')).attr('src', selectedImage.url);
                    // Set attachment ID
                    $input.val(selectedImage.id);

                    // Restore the main post ID
                    wp.media.model.settings.post.id = wpMediaPostID;
                });

                // Open the modal
                fileFrame[id].open();
            })
            // Restore the post ID when the add media button is pressed
            .on('click', 'a.add_media', function() {
                wp.media.model.settings.post.id = wpMediaPostID;
            });
    };

    /**
     * Load images upon page load
     */
    that.loadImages = function()
    {
        ajax(
            {
                action  : 'tb_wpc_images',
                id      : tb_wpc.postId,
                nonce   : tb_wpc.nonce
            },
            function(response)
            {
                var $container = cache.get('#tb-wpc-templates-container');

                $container.html(response);
            }
        );
    };

    /**
     * Add a new image to the template
     */
    that.addNewImage = function()
    {
        cache.get('body').on('click', '#tb_wpc_add_new_image_template', function() {

            var row = $('#tb-wpc-templates-container > .tb-wpc-template[data-image]').length;

            ajax(
                {
                    action  : 'tb_wpc_images',
                    total   : row,
                    nonce   : tb_wpc.nonce
                },
                function(response)
                {
                    cache.get('#tb-wpc-templates-container').append(response);
                }
            );
        });
    };

    /**
     * Save Product Colorizer form
     */
    that.save = function()
    {
        var $wcProductData = cache.get('#woocommerce-product-data');

        $wcProductData.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });

        ajax(
            {
                action  : 'tb_wpc_save',
                nonce   : tb_wpc.nonce,
                id      : tb_wpc.postId,
                data    : decodeURIComponent($('.tb_wpc_form').serialize())
            },
            function(response)
            {
                $wcProductData.unblock();
            }
        );
    };

    /**
     * Generic Elements
     */
    var elements = function()
    {
        cache.get('body')
        // Save form
            .on('click', '#tb_wpc_save_top', function(e)
            {
                e.preventDefault();
                that.save();
            })
            // Remove color
            .on('click', '#product_colorizer_tab a.tb-wpc-remove-color', function(e)
            {
                e.preventDefault();

                $(this).parents('.options_group[data-color]').remove();
            })
            // Remove template
            .on('click', '#product_colorizer_tab a.tb-wpc-remove-template', function(e)
            {
                e.preventDefault();

                $(this).parents('.options_group[data-template]').remove();
            })
            // Remove Image
            .on('click', '#tb_wpc_add_new_image_template a.tb-wpc-remove-element', function(e)
            {
                e.preventDefault();

                $(this).parents('.tb-wpc-template[data-image]').remove();
            })
            // Remove discount
            .on('click', '#tb_wpc_discounts_tab a.tb-wpc-remove-discount', function(e)
            {
                e.preventDefault();

                $(this).parents('.options_group[data-discount]').remove();
            })
            // Change
            .on('change', '#tb_wpc_discounts_tab .tb_wpc_type_selector', function(e)
            {
                var $this       = $(this),
                    $container  = $this.parents('.options_group[data-discount]')
                ;

                $container.find('.tb_wpc_type_container').hide();
                $container.find('.tb_wpc_type_container[data-type="' + $this.val() + '"]').show();
            })
            // Cancel discount schedule
            .on('click', 'a.cancel_discount_schedule', function(e)
            {
                e.preventDefault();

                var $this       = $(this),
                    $container  = $this.parents('.options_group[data-discount]')
                ;

                $this.hide();

                $container.find('a.discount_schedule').show();

                $container.find('.discount_price_dates_fields').hide();
            })
            // Schedule the discount
            .on('click', 'a.discount_schedule', function(e)
            {
                e.preventDefault();

                var $this       = $(this),
                    $container  = $this.parents('.options_group[data-discount]')
                ;

                $this.hide();

                $container.find('a.cancel_discount_schedule').show();

                $container.find('.discount_price_dates_fields').show();
            })
        ;
    };

    /**
     * Load colors upon page load
     */
    that.loadColors = function()
    {
        ajax(
            {
                action  : 'tb_wpc_colors',
                id      : tb_wpc.postId,
                nonce   : tb_wpc.nonce
            },
            function(response)
            {
                var $container = cache.get('#product_colorizer_tab').find('.tb_wpc_colors_container');

                $container.html(response);

                $container.find('select.tb_wpc_palette').each(function()
                {
                    that.getPaletteColors($(this));
                });
            }
        );
    };

    /**
     * Add a new color to the product
     */
    that.addNewColor = function()
    {
        cache.get('body').on('click', '#tb_wpc_add_color_top', function() {

            var row = $('#product_colorizer_tab > .tb_wpc_colors_container > .options_group[data-color]').length;

            ajax(
                {
                    action  : 'tb_wpc_colors',
                    total   : row,
                    nonce   : tb_wpc.nonce
                },
                function(response)
                {
                    cache.get('#product_colorizer_tab')
                        .find('.tb_wpc_colors_container')
                        .append(response)
                    ;
                }
            );
        });
    };

    /**
     * Load templates upon page load
     */
    that.loadTemplates = function()
    {
        ajax(
            {
                action  : 'tb_wpc_templates',
                id      : tb_wpc.postId,
                nonce   : tb_wpc.nonce
            },
            function(response)
            {
                var $container = cache.get('#product_colorizer_tab').find('.tb_wpc_templates_container');

                $container.html(response);
            }
        );
    };

    /**
     * Add a new template to the product
     */
    that.addNewTemplate = function()
    {
        cache.get('body').on('click', '#tb_wpc_add_top', function() {

            var row = $('#product_colorizer_tab > .tb_wpc_templates_container > .options_group[data-template]').length;

            ajax(
                {
                    action  : 'tb_wpc_templates',
                    total   : row,
                    nonce   : tb_wpc.nonce
                },
                function(response)
                {
                    cache.get('#product_colorizer_tab')
                        .find('.tb_wpc_templates_container')
                        .append(response)
                    ;
                }
            );
        });
    };

    /**
     * Color Picker
     */
    var colorPicker     = function()
    {
        $('.tb_wpc_colorPicker').each(function()
        {
            var $this       = $(this);

            if (0 < $this.find('> .colorpicker[id^="collorpicker_"]').length)
            {
                $this.ColorPickerSetColor(jQuery('#' + $this.data('target')).val());
                return;
            }

            $this.ColorPicker({
                flat:true,
                color: jQuery('#' + $this.data('target')).val(),
                onSubmit: function(hsb, hex, rgb, el)
                {
                    jQuery('#' + jQuery(el).data('target')).val(hex);
                }
            });
        });
    };

    /**
     * Load discounts upon page load
     */
    that.loadDiscounts = function()
    {
        ajax(
            {
                action  : 'tb_wpc_discounts',
                id      : tb_wpc.postId,
                nonce   : tb_wpc.nonce
            },
            function(response)
            {
                var $container = cache.get('#tb_wpc_discounts_tab').find('.tb_wpc_discounts_container');

                $container.html(response);

                $('#tb_wpc_discounts_tab .tb_wpc_form.hasDatepicker').datepicker();
            }
        );
    };

    /**
     * Add a new discount to the product
     */
    that.addNewDiscount = function()
    {
        cache.get('body').on('click', '#tb_wpc_add_top', function() {

            var row = $('#tb_wpc_discounts_tab > .tb_wpc_discounts_container > .options_group[data-discount]').length;

            ajax(
                {
                    action  : 'tb_wpc_discounts',
                    total   : row,
                    nonce   : tb_wpc.nonce
                },
                function(response)
                {
                    cache.get('#tb_wpc_discounts_tab')
                        .find('.tb_wpc_discounts_container')
                        .append(response)
                    ;

                    if (isNaN(row))
                    {
                        row = 0;
                    }

                    var currentRow = row + 1;

                    cache.get('#tb_wpc_discounts_tab').find('.discount_price_dates_fields[data-target="' + currentRow + '"]').find('.tb_wpc_form.hasDatepicker').datepicker();
                }
            );
        });
    };

    /**
     * Get colors of selected palette
     */
    that.getColors = function()
    {
        cache.get('body').on('change', 'select.tb_wpc_palette', function()
        {
            that.getPaletteColors($(this));
        });
    };

    /**
     * @param {Object} $select
     */
    that.getPaletteColors = function($select)
    {
        var paletteId = parseInt($select.val());

        if (isNaN(paletteId))
        {
            return;
        }

        var $container              = $select.parents('.options_group[data-color]'),
            $defaultColorSelector   = $container.find('select.tb_wpc_defaultColor')
        ;

        ajax(
            {
                action      : 'tb_wpc_palette_colors',
                paletteId   : paletteId,
                nonce       : tb_wpc.nonce
            },
            function(response)
            {
                if (!response)
                {
                    return false;
                }

                $defaultColorSelector.append($('<option>', {value:'', text: 'Select a Default Color'}));

                for (var key in response)
                {
                    if (response.hasOwnProperty(key))
                    {
                        $defaultColorSelector.append($('<option>', {value: key, text: response[key]}));
                    }
                }

                var selected = $defaultColorSelector.data('selected-color');

                $defaultColorSelector.find('option[value="' + selected + '"]').prop('selected', true);
            }
        );
    };

    /**
     * Ajax Requests
     * @param {Object} data
     * @param {Function} callback
     * @param {String} dataType
     */
    var ajax            = function(data, callback, dataType)
    {
        if ('undefined' === typeof(dataType))
        {
            dataType = 'json';
        }

        $.ajax({
            url         : ajaxurl,
            type        : 'post',
            dataType    : dataType,
            cache       : false,
            data        : data,
            error       : function(xhr, textStatus, errorThrown) {
                console.log(xhr.status + ' ' + xhr.statusText + '---' + textStatus);
                console.log(textStatus);
            },
            success     : function(data) {
                if ('function' === typeof(callback))
                {
                    callback(data);
                }
            },
            statusCode  : {
                404: function() {
                    console.log('Something went wrong; can\'t find ajax request URL!');
                },
                500: function() {
                    console.log('Something went wrong; internal server error while processing the request!');
                }
            }
        });
    };

    /**
     * @type {Function}
     */
    that.init = (function()
    {
        that.loadImages();
        that.addNewImage();
        uploadMaskImage();

        colorPicker();

        that.loadColors();
        that.addNewColor();
        that.getColors();
        that.loadTemplates();
        that.addNewTemplate();

        that.loadDiscounts();
        that.addNewDiscount();

        elements();
    });

    return that;

}(jQuery);

jQuery(document).ready(function() {
    TB_ProductColorizer.init();
});