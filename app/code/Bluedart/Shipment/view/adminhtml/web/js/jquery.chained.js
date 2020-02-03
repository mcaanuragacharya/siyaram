/*
 * Chained - jQuery / Zepto chained selects plugin
 *
 * Copyright (c) 2010-2014 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   http://www.appelsiini.net/projects/chained
 *
 * Version: 0.9.10
 *
 */
/*
;(function($, window, document, undefined) {
    "use strict";

    $.fn.chained = function(parent_selector, options) {

        return this.each(function() {

            
            var child   = this;
            var backup = $(child).clone();

           
            $(parent_selector).each(function() {
                $(this).bind("change", function() {
                    updateChildren();
                });

               
                if (!$("option:selected", this).length) {
                    $("option", this).first().attr("selected", "selected");
                }

              
                updateChildren();
            });

            function updateChildren() {
                var trigger_change = true;
                var currently_selected_value = $("option:selected", child).val();

                $(child).html(backup.html());

              
                var selected = "";
                $(parent_selector).each(function() {
                    var selectedClass = $("option:selected", this).val();
                    if (selectedClass) {
                        if (selected.length > 0) {
                            if (window.Zepto) {
                               
                                selected += "\\\\";
                            } else {
                                selected += "\\";
                            }
                        }
                        selected += selectedClass;
                    }
                });

               
                var first;
                if ($.isArray(parent_selector)) {
                    first = $(parent_selector[0]).first();
                } else {
                    first = $(parent_selector).first();
                }
                var selected_first = $("option:selected", first).val();

                $("option", child).each(function() {
                   
                    if ($(this).hasClass(selected) && $(this).val() === currently_selected_value) {
                        $(this).prop("selected", true);
                        trigger_change = false;
                    } else if (!$(this).hasClass(selected) && !$(this).hasClass(selected_first) && $(this).val() !== "") {
                        $(this).remove();
                    }
                });

               
                if (1 === $("option", child).size() && $(child).val() === "") {
                    $(child).prop("disabled", true);
                } else {
                    $(child).prop("disabled", false);
                }
                if (trigger_change) {
                    $(child).trigger("change");
                }
            }
        });
    };

    
    $.fn.chainedTo = $.fn.chained;

    
    $.fn.chained.defaults = {};

})(window.jQuery || window.Zepto, window, document);
*/