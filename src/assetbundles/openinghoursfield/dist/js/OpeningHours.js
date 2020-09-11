/**
 * SimpleOpeningHours plugin for Craft CMS
 *
 * OpeningHours Field JS
 *
 * @author    Miroslaw Farajewicz
 * @copyright Copyright (c) 2020 Miroslaw Farajewicz
 * @link      https://www.gearrilla.com/en/
 * @package   SimpleOpeningHours
 * @since     1.0.0SimpleOpeningHoursOpeningHours
 */

 ;(function ( $, window, document, undefined ) {

    var pluginName = "SimpleOpeningHoursOpeningHours",
        defaults = {
        };

    // Plugin constructor
    function Plugin( element, options ) {
        this.element = element;

        this.options = $.extend( {}, defaults, options) ;

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {

        init: function(id) {
            var _this = this;

            $(function () {
                $(_this.element).find('select.startPicker').each(function(index, item){
                    let duration = parseInt(_this.options.openingHoursPluginSettings.openDuration);
                    if (!Number.isNaN(duration)) {
                        $(item).change(function(a, b){
                            let items = $(item).parents('.day-settings-container').find('select').filter(':not(.' + item.className +')');
                            let change = null;
                            let edgeHour = parseInt($(item).val());
                            if (item.className === 'startPicker') {
                                change = Math.min(24, edgeHour + duration);
                            } else {
                                change = Math.max(0, edgeHour - duration)
                            }
                            items.val(change);
                        })
                    }
                });

                $(_this.element).find('.closedChecker').click(function(index, item){
                    $(this).parents('.day-settings-container').find('select').val('');
                });

                $(_this.element).find('.allNightChecker').click(function(index, item){
                    $(this).parents('.day-settings-container').find('.startPicker').val(0);
                    $(this).parents('.day-settings-container').find('.endPicker').val(24);
                });

            });
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new Plugin( this, options ));
            }
        });
    };

})( jQuery, window, document );
