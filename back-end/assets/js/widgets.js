(function ($) {
    if(typeof acf === 'undefined')
        return;
        
    /*
    * Init
    */
    var flexible = acf.getFieldType('flexible_content');
    var model = flexible.prototype;

    /*
    * Actions
    */
    model.events['click .acf-fc-layout-handle'] = 'editLayoutTitleToggleHandle';
    model.editLayoutTitleToggleHandle = function(e, $el) {
        // Vars
        var $layout = $el.closest('.layout');
        
        if($layout.hasClass('widgets-acf-flexible-title-edition'))
            $layout.find('> .acf-fc-layout-handle > .widgets-acf-layout-title > input.widgets-acf-flexible-control-title').trigger('blur');
    }
    
    model.events['click .widgets-acf-layout-title-text'] = 'editLayoutTitle';
    model.editLayoutTitle = function(e, $el) {
        // Get Flexible
        var flexible = this;

        // Stop propagation
        e.stopPropagation();
        // Toggle
        flexible.editLayoutTitleToggle(e, $el);
    }
    
    model.events['blur input.widgets-acf-flexible-control-title'] = 'editLayoutTitleToggle';
    model.editLayoutTitleToggle = function(e, $el) {
        // Vars
        var $layout = $el.closest('.layout');
        var $handle = $layout.find('> .acf-fc-layout-handle');
        var $title = $handle.find('.widgets-acf-layout-title');
        
        if($layout.hasClass('widgets-acf-flexible-title-edition')) {            
            var $input = $title.find('> input[data-widgets-acf-flexible-control-title-input]');
            
            if($input.val() === '')
                $input.val($input.attr('placeholder')).trigger('input');
            
            $layout.removeClass('widgets-acf-flexible-title-edition');
            $input.insertAfter($handle);
        }
        else{   
            var $input = $layout.find('> input[data-widgets-acf-flexible-control-title-input]');
            var $input = $input.appendTo($title);

            $layout.addClass('widgets-acf-flexible-title-edition');
            $input.focus().attr('size', $input.val().length);
        }
    }
    
    // Layout: Edit Title
    model.events['click input.widgets-acf-flexible-control-title'] = 'editLayoutTitlePropagation';
    model.editLayoutTitlePropagation = function(e) {
        e.stopPropagation();
    }
    
    // Layout: Edit Title Input
    model.events['input [data-widgets-acf-flexible-control-title-input]'] = 'editLayoutTitleInput';
    model.editLayoutTitleInput = function(e, $el) {
        // Vars
        var $layout = $el.closest('.layout');
        var $title = $layout.find('> .acf-fc-layout-handle .widgets-acf-layout-title .widgets-acf-layout-title-text');
        var val = $el.val();
        
        if(val.length == 0)
            return;

        $el.attr('size', val.length);
        $title.html(val);
    }
    
    // Layout: Edit Title Input Enter
    model.events['keypress [data-widgets-acf-flexible-control-title-input]'] = 'editLayoutTitleInputEnter';
    model.editLayoutTitleInputEnter = function(e, $el) {
        // Enter Key
        if(e.keyCode !== 13)
            return;
        
        e.preventDefault();
        $el.blur();
    }
    
    /*
    * Actions
    */
    model.events['click [data-action="widgets-acf-flexible-modal-edit"]'] = 'modalEdit';
    model.modalEdit = function(e, $el) {
        var flexible = this;
        // Layout
        var $layout = $el.closest('.layout');
        // Modal data
        var $modal = $layout.find('> .widgets-acf-modal.-fields');
        var $handle = $layout.find('> .acf-fc-layout-handle');
        var $layout_order = $handle.find('> .acf-fc-layout-order').outerHTML();
        var $layout_title = $handle.find('.widgets-acf-layout-title-text').text();
        
        // Open modal
        modal.open($modal, {
            title: $layout_order + ' ' + $layout_title,
            // footer: close,
            onOpen: function() {
                flexible.openLayout($layout);
                model.setCkeditorInline($layout);
      
                $(document).on('click', '.acf-field-widgets a[data-event="add-row"]', function() {
                    model.setCkeditorInline($layout, 'ck_repeat');
                });
            },
            onClose: function() {
                flexible.closeLayout($layout);
                
                $(document).off('click', '.acf-field-widgets a[data-event="add-row"]', model.setCkeditorInline($layout, 'ck_repeat'));
            }
        });
    };

    // Layout: Clone
    model.events['click [data-widgets-acf-flexible-control-clone]'] = 'cloneLayout';
    model.cloneLayout = function(e, $el) {
        // Get Flexible
        var flexible = this;
        // Vars
        var $layout = $el.closest('.layout');
        var layout_name = $layout.data('layout');
        // Popup min/max
        var $popup = $(flexible.$popup().html());
        var $layouts = flexible.$layouts();
        var countLayouts = function(name) {
            return $layouts.filter(function() {
                return $(this).data('layout') === name;
            }).length;
        };
         // vars
        var $a = $popup.find('[data-layout="' + layout_name + '"]');
        var max = $a.data('max') || 0;
        var count = countLayouts(layout_name);
        
        // max
        if(max && count >= max) {
            $el.addClass('disabled');
            return false;
        }
        else
            $el.removeClass('disabled');
            
        // Fix inputs
        flexible.fixInputs($layout);
        
        var $_layout = $layout.clone();
        
        // Clean Layout
        flexible.cleanLayouts($_layout);
        
        var parent = $el.closest('.acf-flexible-content').find('> input[type=hidden]').attr('name');
        
        // Clone
        flexible.duplicate({
            layout: $_layout,
            before: $layout,
            parent: parent
        });
    }

    // Flexible: Fix Inputs
    model.fixInputs = function($layout) {
        $layout.find('input').each(function() {
            $(this).attr('value', this.value);
        });
        
        $layout.find('textarea').each(function() {
            $(this).html(this.value);
        });
        
        $layout.find('input:radio,input:checkbox').each(function() {
            if(this.checked)
                $(this).attr('checked', 'checked');
            else
                $(this).attr('checked', false);
        });
        
        $layout.find('option').each(function() {
            if(this.selected)
                $(this).attr('selected', 'selected');
            else
                $(this).attr('selected', false);
        });
    }

    // Flexible: Clean Layout
    model.cleanLayouts = function($layout) {      
        // Clean WP Editor
        $layout.find('.acf-editor-wrap').each(function() {
            var $input = $(this);
            
            $input.find('.wp-editor-container div').remove();
            $input.find('.wp-editor-container textarea').css('display', '');
        });
        
        // Clean Date
        $layout.find('.acf-date-picker').each(function() {
            var $input = $(this);
            
            $input.find('input.input').removeClass('hasDatepicker').removeAttr('id');
        });
        
        // Clean Time
        $layout.find('.acf-time-picker').each(function() {
            var $input = $(this);
            
            $input.find('input.input').removeClass('hasDatepicker').removeAttr('id');
        });
        
        // Clean DateTime
        $layout.find('.acf-date-time-picker').each(function() {
            var $input = $(this);
            
            $input.find('input.input').removeClass('hasDatepicker').removeAttr('id');
        });

        // Clean Code Editor
        $layout.find('.widgets-acf-field-code-editor').each(function() {
            var $input = $(this);

            $input.find('.CodeMirror').remove();
        });
        
        // Clean Color Picker
        $layout.find('.acf-color-picker').each(function() {
            var $input = $(this);
            var $color_picker = $input.find('> input');
            var $color_picker_proxy = $input.find('.wp-picker-container input.wp-color-picker').clone();
            
            $color_picker.after($color_picker_proxy);
            $input.find('.wp-picker-container').remove();
        });
        
        // Clean Post Object
        $layout.find('.acf-field-post-object').each(function() {
            var $input = $(this);
            
            $input.find('> .acf-input span').remove();
            $input.find('> .acf-input select').removeAttr('tabindex aria-hidden').removeClass();
        });
        
        // Clean Page Link
        $layout.find('.acf-field-page-link').each(function() {
            var $input = $(this);
            
            $input.find('> .acf-input span').remove();
            $input.find('> .acf-input select').removeAttr('tabindex aria-hidden').removeClass();
        });
        
        // Clean Select2
        $layout.find('.acf-field-select').each(function() {
            var $input = $(this);
            
            $input.find('> .acf-input span').remove();
            $input.find('> .acf-input select').removeAttr('tabindex aria-hidden').removeClass();
        });
        
        // Clean FontAwesome
        $layout.find('.acf-field-font-awesome').each(function() {
            var $input = $(this);
            
            $input.find('> .acf-input span').remove();
            $input.find('> .acf-input select').removeAttr('tabindex aria-hidden');
        });

        // Clean Tab
        $layout.find('.acf-tab-wrap').each(function() {
            var $wrap = $(this);
            var $content = $wrap.closest('.acf-fields');
            
            var tabs = [];
            $.each($wrap.find('li a'), function() {
                tabs.push($(this));
            });
            
            $content.find('> .acf-field-tab').each(function() {
                $current_tab = $(this);
                
                $.each(tabs, function() {
                    var $this = $(this);
                    
                    if($this.attr('data-key') !== $current_tab.attr('data-key'))
                        return;
                    
                    $current_tab.find('> .acf-input').append($this);
                });
            });
            
            $wrap.remove();
        });
        
        // Clean Accordion
        $layout.find('.acf-field-accordion').each(function() {
            var $input = $(this);
            
            $input.find('> .acf-accordion-title > .acf-accordion-icon').remove();
            // Append virtual endpoint after each accordion
            $input.after('<div class="acf-field acf-field-accordion" data-type="accordion"><div class="acf-input"><div class="acf-fields" data-endpoint="1"></div></div></div>');
        });
    }

    // Flexible: Duplicate
    model.duplicate = function(args) {
        // Arguments
        args = acf.parseArgs(args, {
            layout: '',
            before: false,
            parent: false,
            search: '',
            replace: '',
        });
        
        // Validate
        if(!this.allowAdd())
            return false;
        
        var uniqid = acf.uniqid();
        
        if(args.parent) {
            if(!args.search)
                args.search = args.parent + '[' + args.layout.attr('data-id') + ']';
                
            args.replace = args.parent + '[' + uniqid + ']';
        }

        var duplicate_args = {
            target: args.layout,
            search: args.search,
            replace: args.replace,
            append: this.proxy(function($el, $el2) {
                // Add class to duplicated layout
                $el2.addClass('widgets-acf-layout-duplicated');
                // Reset UniqID
                $el2.attr('data-id', uniqid);

                // append before
                if(args.before)
                    // Fix clone: Use after() instead of native before()
                    args.before.after($el2);
                // append end
                else
                    this.$layoutsWrap().append($el2);

                // enable
                acf.enable($el2, this.cid);
                // render
                this.render();
            })
        }

        var acfVersion = parseFloat(acf.get('acf_version'));

        if(acfVersion < 5.9)
            // Add row
            var $el = acf.duplicate(duplicate_args);
        // Hotfix for ACF Pro 5.9
        else
            // Add row
            var $el = model.newAcfDuplicate(duplicate_args);
        
        // trigger change for validation errors
        this.$input().trigger('change');

        // Fix tabs conditionally hidden
        var tabs = acf.getFields({
            type: 'tab',
            parent: $el,
        });

        if(tabs.length) {
            $.each(tabs, function() {
                if(this.$el.hasClass('acf-hidden'))
                    this.tab.$el.addClass('acf-hidden');
            });
        }

        // return
        return $el;        
    }

    /*
     * Based on acf.duplicate (5.9)
     *
     * doAction('duplicate) has been commented out
     * This fix an issue with the WYSIWYG editor field during copy/paste since ACF 5.9
     */
    model.newAcfDuplicate = function(args) {
        // allow jQuery
        if(args instanceof jQuery) {
            args = {
                target: args
            };
        }

        // defaults
        args = acf.parseArgs(args, {
            target: false,
            search: '',
            replace: '',
            rename: true,
            before: function($el) {},
            after: function($el, $el2) {},
            append: function($el, $el2) {
                $el.after($el2);
            }
        });

        // compatibility
        args.target = args.target || args.$el;

        // vars
        var $el = args.target;

        // search
        args.search = args.search || $el.attr('data-id');
        args.replace = args.replace || acf.uniqid();

        // before
        // - allow acf to modify DOM
        // - fixes bug where select field option is not selected
        args.before($el);
        acf.doAction('before_duplicate', $el);

        // clone
        var $el2 = $el.clone();

        // rename
        if(args.rename) {
            acf.rename({
                target:		$el2,
                search:		args.search,
                replace:	args.replace,
                replacer:	(typeof args.rename === 'function' ? args.rename : null)
            });
        }

        // remove classes
        $el2.removeClass('acf-clone');
        $el2.find('.ui-sortable').removeClass('ui-sortable');

        // after
        // - allow acf to modify DOM
        args.after($el, $el2);
        acf.doAction('after_duplicate', $el, $el2);

        // append
        args.append($el, $el2);

        /**
         * Fires after an element has been duplicated and appended to the DOM.
         *
         * @date	30/10/19
         * @since	5.8.7
         *
         * @param	jQuery $el The original element.
         * @param	jQuery $el2 The duplicated element.
         */
        //acf.doAction('duplicate', $el, $el2 );

        // append
        acf.doAction('append', $el2);

        // return
        return $el2;
    };

    model.setCkeditorInline = function($layout, class_repeat = null, txt = null, styleset_var = null) { 
        if(!jQuery('#fonts_selected_widget_acf').val())
            jQuery('body').append('<input type="hidden" id="fonts_selected_widget_acf" value=";" />');
        
        var class_use_ckeditor = 'ckeditor_inline';
        // var find_use_ck_editor = class_use_ckeditor;
    
        // if(styleset_var) {
        //     var toolbarText = [
        //     [
        //         'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'Anchor', 'Font','Styles','FontSize',
        //         'Link', 'TextColor',
        //         'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'lineheight',
        //         'BGColor']
        //     ];
        //     var toolbarTextArea = [
        //         ['RemoveFormat', 'Bold', 'Italic', 'Underline', 'Link', 'PasteFromWord', 'Font', 'Styles','FontSize',  'NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
        //     ];
        // }
        // else {
            var toolbarText = [
                [
                    'RemoveFormat', 'Bold', 'Italic', 'Underline', 'Strike',
                    'Anchor', 'Font','FontSize', 'Link', 'TextColor', 'BGColor',
                    'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'
                ]
            ];
            var toolbarTextArea = [
                [
                    'RemoveFormat', 'Bold', 'Italic', 'Underline', 'Strike',
                    'Anchor', 'Font','FontSize', 'Link', 'TextColor', 'BGColor',
                    'NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'
                ]
            ];
        // }
        
        if(class_repeat != null) {
            class_use_ckeditor = class_use_ckeditor + ' ' + class_repeat + '_ck_repeat';
            find_use_ck_editor = class_repeat + '_ck_repeat';
        }
        
        setTimeout(function() {
            $layout.find('textarea, input[type="text"]:not(.wp-color-picker):not(.widgets-acf-flexible-control-title):not(.editor-initialized)').each(function() {
                if(!jQuery(this).closest('.acf-color-picker')[0] && !jQuery(this).closest('.acf-clone')[0]) {
                    var $input = $(this);
                    var id_div = $input.attr('name') + (new Date().getTime());
                    var isTextInput = $input.attr('type') == 'text';
            
                    $input.addClass('editor-initialized');

                    $('<div id="' + id_div + '" class="' + class_use_ckeditor + (isTextInput ? ' ckeditor_inline_input_text' : '') + '" contenteditable="true" >' + $input.val() + '</div>')
                        .appendTo($input.parent());
                    var editor;
                    var toolbar = isTextInput ? toolbarText : toolbarTextArea;

                    CKEDITOR.disableAutoInline = true;
        
                    // if(styleset_var) {
                    //     editor = CKEDITOR.inline(id_div + '_editor', {
                    //         enterMode : CKEDITOR.ENTER_BR,
                    //         autoParagraph : true,
                    //         forcePasteAsPlainText: true,
                    //         stylesSet : styleset_var,
                    //         font_names : jQuery('#fonts_selected_widget_acf').val(),
                    //         toolbar: toolbar
                    //     });
                    // }
                    // else{
                        editor = CKEDITOR.inline(id_div, {
                            enterMode : CKEDITOR.ENTER_BR,
                            autoParagraph : true,
                            forcePasteAsPlainText: true,
                            font_names : jQuery('#fonts_selected_widget_acf').val(),
                            toolbar: toolbar
                        });
                    // }

                    editor.on('change', function() {
                        $input.val(editor.getData());
                    });
                }
            });
        }, 100);
    };

    model.modalSettings = function(e) {
        var $el = $(e.currentTarget);
        // Modal data
        var $modal = $el.parents('.acf-fields').first().find('.widgets-acf-modal.-settings');
        
        var $layout_title = $el.attr('title');
        
        // Open modal
        modal.open($modal, {
            title: $layout_title,
        });
    };

    model.setAjaxFonts = function() {
        jQuery('#acf-group_widgets_acf').css({'opacity': '0.2'});
  
        var site_url = window.location.href.split('/wp-admin');
        var settings_ajax = {
            "async": true,
            "crossDomain": true,
            "url": site_url[0] + "/wp-admin/admin-ajax.php",
            "method": "POST",
            "headers": {
                "content-type": "application/x-www-form-urlencoded",
                "cache-control": "no-cache"
            },
            "data": {
                "action": "fonts_widgets_acf"
            }
        };

        var fonts_selected = '';

        jQuery.ajax(settings_ajax).done(function(resposta) {
            if(resposta.length != 0) {
                for(var i = 0; i < resposta['fonte'].length; i++)
                    fonts_selected = resposta['fonte'][i] + ';' + fonts_selected;
    
                for(var w = 0; w < resposta['weights'].length; w++) {
                    var w_k = 0;

                    for(var key_w in resposta['weights'][w]) {
                        jQuery('body').append('<input type="hidden" id="fontsweight_selected_widget_acf_' + key_w + '" value="' + resposta['weights'][w][key_w] + '" />');
                        w_k++;
                    }
                }
        
                if(!resposta['fonte'])
                    fonts_selected = 'Arial;';
        
                jQuery('body').append('<input type="hidden" id="fonts_selected_widget_acf" value="' + fonts_selected + '" />');
        
                var fonts = jQuery('#fonts_selected_widget_acf').val().split(';');
        
                for(var i = 0; i < fonts.length - 1; i++) {
                    var name_hidden_variant = jQuery('#fontsweight_selected_widget_acf_' + fonts[i].replace(' ', '_')).val();
                    var weight_fonts = name_hidden_variant.split(';');
                    var styles_weight = [];
                    var names = {
                        '100':'Thin 100',
                        '100italic':'Thin 100 Italic',
                        '200':'Thin 200',
                        '200italic':'Thin 200 Italic',
                        '300':'Thin 300',
                        '300italic':'Thin 300 Italic',
                        'regular':'Regular',
                        'italic':'Italic',
                        '400':'Regular 400',
                        '400italic':'Regular 400 Italic',
                        '500':'Medium 500',
                        '500italic':'Medium 500 Italic',
                        '600':'Semi-bold 600',
                        '600italic':'Semi-bold 600 Italic',
                        '700':'Bold 700',
                        '700italic':'Bold 700 Italic',
                        '800':'Bold 800',
                        '800italic':'Bold 800 Italic',
                        '900':'Bold 900',
                        '900italic':'Bold 900 Italic',
                    };
        
                for(var w = 0; w < weight_fonts.length; w++) {
                        styles_weight.push({
                            name: names[weight_fonts[w]],
                            element: 'font',
                            styles: {
                                'font-weight': weight_fonts[w],
                            },
                        });
                }
        
                if(styles_weight)
                    CKEDITOR.stylesSet.add('my_styles_' + fonts[i].replace(' ', '_'), styles_weight);
                }
            }
        });
    };

    model.search = function(el) {
        var $input = $(el.currentTarget);
        var filter = $input.val().toUpperCase();
        var $items = $('[data-action="results"]').find('li').not('.search');

        $items.each(function(index, element) {
            var $element = $(element);
            var $title = $element.find('a');

            if($title.text().toUpperCase().indexOf(filter) > -1)
                $element.show();
            else
                $element.hide();
        });
    };

    model.setAjaxFonts();
    $(document).on('click', '[data-action="search"]', function(e) {
        e.stopPropagation();
    });
    $(document).on('input', '[data-action="search"]', model.search);
    $(document).on('click', '[data-name="settings-layout"]', model.modalSettings);
    $(document).on('change', '.grid-widget-settings--desktop select', function() {
        var $element = $(this);
        $element.closest('.layout').attr('data-columns-desktop', $element.val());
    });
})(jQuery);