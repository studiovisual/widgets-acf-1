(function ($) {
    if(typeof acf === 'undefined')
        return;
        
    /*
    * Init
    */
    var flexible = acf.getFieldType('flexible_content');
    var model = flexible.prototype;
    var fonts = [];
    var editorSettings = {};

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

    model.events['mouseenter .acf-fc-layout-handle'] = 'layoutMouseOver';
    model.events['mouseenter .acf-fc-layout-controls'] = 'layoutMouseOver';
    model.layoutMouseOver = function(e, $el) {
        $el.closest('.layout').addClass('layout--hover');
    }

    model.events['mouseleave .acf-fc-layout-handle'] = 'layoutMouseOut';
    model.events['mouseleave .acf-fc-layout-controls'] = 'layoutMouseOut';
    model.layoutMouseOut = function(e, $el) {
        $el.closest('.layout').removeClass('layout--hover');
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
                model.setEditorInline($layout);
            },
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
        $el2.find('.editor-initialized').removeClass('editor-initialized');
        $el2.find('.widgets-acf-editor').parents('.wp-editor-wrap').first().remove();

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

    acf.add_action('append', function($el) {
        if(!$el.parents('#acf-group_widgets_acf').length)
            return;
        
        $el.find('.widgets-acf-preview').remove();
        $el.find('.editor-initialized').removeClass('editor-initialized');

        $el.find('.widgets-acf-editor').each(function() {
            var $element = $(this);

            $element.parents('.wp-editor-wrap').first().remove();
        });
        

        if($el.parents('.widgets-acf-modal-content').length)
            model.setEditorInline($el);
    });

    model.setEditorSettings = function() { 
        var fontWeight = [];
        var fontSizes = '';
        var lineHeight = [];
        var letterSpacing = [];
        var domain = (new URL(window.location));

        for(let index = 1; index < 10; index ++) {
            fontWeight.push({
                title: index.toString() + '00',
                inline: 'span',
                styles: { 'font-weight': (index * 100).toString() },
            })
        }

        for(let index = 0.1; index < 2.1; index += 0.1) {
            lineHeight.push({
                title: index.toFixed(1).toString(),
                inline: 'span',
                styles: { 'line-height': index.toFixed(1), display: 'inline-block' }
            });
        }

        for(let index = 8; index <= 100; index++)
            fontSizes += index + 'px ';

        for(let index = -10; index <= 10; index++) {
            letterSpacing.push({
                title: index.toString() + 'px',
                inline: 'span',
                styles: { 'letter-spacing': index.toString() + 'px ' },
            });
        }

        editorSettings = {
            tinymce: {
                wpautop: false,
                forced_root_block: '',
                // skin: 'widgets-acf',
                // skin_url: `${domain.origin}/wp-content/plugins/widgets-acf/back-end/assets/tinymce/skins/widgets-acf`,
                formats: {
                    alignleft: [
                        {selector: 'p, h1, h2, h3, h4, h5, h6, td, th, div, ul, ol, li, span', inline: 'span', block: 'span', styles: {display: 'block', textAlign: 'left' }},
                    ],
                    aligncenter: [
                        {selector: 'p, h1, h2, h3, h4, h5, h6, td, th, div, ul, ol, li, span', inline: 'span', block: 'span', styles: {display: 'block', textAlign: 'center' }},
                    ],
                    alignright: [
                        {selector: 'p, h1, h2, h3, h4, h5, h6, td, th, div, ul, ol, li, span', inline: 'span', block: 'span', styles: {display: 'block', textAlign: 'right' }},
                    ],
                    alignfull: [
                        {selector: 'p, h1, h2, h3, h4, h5, h6, td, th, div, ul, ol, li, span', inline: 'span', block: 'span', styles: {display: 'block', textAlign: 'justify' }},
                    ],
                    strikethrough: {inline: 'del' }
                },
                style_formats: [
                    {
                        title: 'Altura da linha',
                        items: lineHeight,
                    },
                    {
                        title: 'Espaçamento entre letras',
                        items: letterSpacing,
                    },
                    {
                        title: 'Peso da fonte',
                        items: fontWeight,
                    },
                    {
                        title: 'Transformação',
                        items: [
                            {
                                title: 'Caixa alta',
                                inline: 'span',
                                styles: { 'text-transform': 'uppercase', display: 'inline-block' }
                            },
                            {
                                title: 'Caixa baixa',
                                inline: 'span',
                                styles: { 'text-transform': 'lowercase', display: 'inline-block' }
                            },
                            {
                                title: 'Capitalizada',
                                inline: 'span',
                                styles: { 'text-transform': 'capitalize', display: 'inline-block' }
                            }
                        ],
                    },
                ],
                fontsize_formats: fontSizes.trim(),
                relative_urls: false,
                remove_script_host: false,
                convert_urls: false,
                browser_spellcheck: true,
                fix_list_elements: false,
                entities: '38, amp, 60, lt, 62, gt',
                entity_encoding: 'raw' ,
                keep_styles: true,
                paste_webkit_styles: 'font-weight font-style color',
                preview_styles: 'font-family font-size font-weight font-style text-decoration text-transform',
                tabfocus_elements: ': prev ,: next',
                plugins: 'hr, media, paste, tabfocus, textcolor, fullscreen, wordpress, wpeditimage, wpgallery, wplink, wpdialogs, wpview, lists, colorpicker',
                resize: 'vertical' ,
                menubar: false,
                indent: false,
                toolbar1: 'forecolor backcolor, bold, italic, underline, strikethrough, subscript, superscript, removeformat, numlist, bullist, alignleft, aligncenter, alignjustify, alignright, link, unlink, hr, blockquote, fontsizeselect, styleselect',
                body_class: 'id post-type-post-status-publish post-format-standard' ,
                wpeditimage_disable_captions: false,
                wpeditimage_html5_captions: true,
                content_style: 'body { background-color: #b3b3b3; color: #000000; text-shadow: 1px 2px 3px #666; }',
            },
            quicktags: {
                buttons: 'strong,em,link,close',
            },
            mediaButtons: true,
        };

        if(fonts.length > 0) {
            var imports = '';
            editorSettings.tinymce.font_formats = '';

            for(var i = 0; i < fonts.length; i++) {
                editorSettings.tinymce.font_formats += `${fonts[i]}=${fonts[i].toLowerCase()};`;
                imports += `${i > 0 ? '&' : ''}family=${fonts[i]}:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900`;
            }
            
            editorSettings.tinymce.content_style += `@import url('https://fonts.googleapis.com/css2?${imports}&display=swap'); body { font-family: '${fonts[0]}'; }`;
        }
        if(fonts.length > 1)
            editorSettings.tinymce.toolbar1 = editorSettings.tinymce.toolbar1.replace('fontsizeselect,', 'fontsizeselect, fontselect');
    }

    model.setEditorInline = function($layout) { 
        $layout.find('textarea:not(.editor-initialized):not(.widgets-acf-editor):not([readonly="readonly"]), input[type="text"]:not(.wp-color-picker):not(.widgets-acf-flexible-control-title):not(.editor-initialized):not([readonly="readonly"])').each(function(index) {
            if(!$(this).closest('.acf-color-picker')[0] && !$(this).closest('.acf-clone')[0] && $(this).parents('.acf-relationship').length === 0 && $(this).parents('[data-name="class"]').length === 0 && $(this).parents('.no-inline-editor').length === 0) {
                var $input = $(this);
                $input.addClass('editor-initialized');
                $input.parent('.acf-input-wrap').css('overflow', 'visible');
                var $preview = $('<div class="widgets-acf-preview">' + $input.val() + '</div>');
                $preview.appendTo($input.parent());

                $preview.find('a').on('click', (event) => event.preventDefault());
                $preview.one('click', () => {
                    $preview.remove();
                    var id_div = new Date().getTime();
                    var isTextInput = $input.attr('type') == 'text';
                    var currentEditorSettings = $.extend(true, {}, editorSettings);

                    var $editor = $('<textarea id="' + id_div + '" class="widgets-acf-editor ckeditor_inline ckeditor_inline_input_text">' + $input.val() + '</textarea>');
                    $editor.appendTo($input.parent());
                    $editor.on('input paste', () => $input.val($editor.val()));

                    if(isTextInput) {
                        currentEditorSettings.mediaButtons = false;
                        currentEditorSettings.tinymce.toolbar1 = currentEditorSettings.tinymce.toolbar1.replace('numlist, bullist, ', '');
                        currentEditorSettings.tinymce.toolbar1 = currentEditorSettings.tinymce.toolbar1.replace('hr, blockquote, ', '');
                    }

                    wp.editor.initialize(id_div, currentEditorSettings);
                    setTimeout(() => $input.parents('.acf-field').first().css('min-height', 'auto'), 100);
                    
                    window.send_to_editor = function(html) {
                        tinyMCE.activeEditor.execCommand('mceInsertContent', false, html);
                    };
                });
            }
        });
    };

    $(document).on('tinymce-editor-setup', function(event, editor) {
        var $element = $(editor.targetElm);

        if(!$element.hasClass('widgets-acf-editor'))
            return;

        var $originalElement = $element.parents('.wp-editor-wrap').first().prev();

        editor.on('init', () =>editor.focus());
        editor.on('change input', () => $originalElement.val(editor.getContent()));
        // editor.on('blur', () => $element.parent().find('.mce-top-part').hide());
        // editor.on('focus', () => $element.parent().find('.mce-top-part').show());
    });

    model.modalSettings = function(e) {
        var $el = $(e.currentTarget);
        // Modal data
        var $modal = $el.parents('.acf-row').find('.widgets-acf-modal.-settings');
        
        var $layout_title = $el.attr('title');
        
        // Open modal
        modal.open($modal, {
            title: $layout_title,
            onOpen: function() {
                model.codeMirror($modal);
            },
        });
    };

    model.codeMirror = function($modal) {
        $modal.find('.code-area:not(.code-editor-initialized)').each(function() {
            var $element = $(this);
            var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                {
                    theme: 'monokai',
                    tabSize: 2,
                    lineNumbers: true,
                    styleActiveLine: true,
                    matchBrackets: true,
                    autoCloseBrackets: true,
                    mode: 'css',
                    indentWithTabs: true
                }
            );
             
            $element.addClass('code-editor-initialized');
            wp.codeEditor.initialize($element.find('textarea'), editorSettings);
        });
    };

    model.setAjaxFonts = function() {  
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

        $.ajax(settings_ajax)
            .done(function(resposta) {
                if(resposta.length == 0)
                    return;

                for(var i = 0; i < resposta['fonte'].length; i++)
                    fonts.push(resposta['fonte'][i]);

                model.setEditorSettings();

            // for(var w = 0; w < resposta['weights'].length; w++) {
            //     var w_k = 0;

            //     for(var key_w in resposta['weights'][w]) {
            //         jQuery('body').append('<input type="hidden" id="fontsweight_selected_widget_acf_' + key_w + '" value="' + resposta['weights'][w][key_w] + '" />');
            //         w_k++;
            //     }
            // }
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

    // $(document).on('click', '[data-type="flexible_content"] > .acf-label > label', model.editSectionTitle);
    // model.editSectionTitle = function(e, $el) {
    //     // Get Flexible
    //     var flexible = this;
    //     console.log($el);

    //     // // Stop propagation
    //     // e.stopPropagation();
    //     // // Toggle
    //     // flexible.editLayoutTitleToggle(e, $el);
    // }

    /*
     * Spawn
     */
    acf.addAction('new_field/type=flexible_content', function(flexible) {
        // Vars
        var $clones = flexible.$clones();
        var $layouts = flexible.$layouts();
        
        // Merge
        var $all_layouts = $.merge($layouts, $clones);
        
        // Do Actions
        $layouts.each(function() {
            var $layout = $(this);
            var $name = $layout.data('layout');
            
            acf.doAction('acfe/flexible/layouts', $layout, flexible);
            acf.doAction('acfe/flexible/layout/name=' + $name, $layout, flexible);
        });
        
        // ACFE: 1 layout available - OneClick
        if($clones.length === 1) {
            // Remove native ACF Tooltip action
            flexible.removeEvents({'click [data-name="add-layout"]': 'onClickAdd'});
            
            // Add ACF Extended Modal action
            flexible.addEvents({'click [data-name="add-layout"]': 'acfeOneClick'});
        }
        
        flexible.addEvents({'click .acfe-fc-placeholder': 'onClickCollapse'});
        flexible.addEvents({'click .acfe-flexible-opened-actions > a': 'onClickCollapse'});
        
        // Flexible: Ajax
        // if(flexible.has('acfeFlexibleAjax')) {
            flexible.add = function(args) {
                // Get Flexible
                var flexible = this;
                
                // defaults
                args = acf.parseArgs(args, {
                    layout: '',
                    before: false
                });
                
                // validate
                if(!this.allowAdd())
                    return false;

                // ajax
                $.ajax({
                    url: acf.get('ajaxurl'),
                    data: acf.prepareForAjax({
                        action: 	'widgets_acf/flexible/models',
                        field_key: 	this.get('key'),
                        layout:		args.layout,
                    }),
                    dataType: 'html',
                    type: 'post',
                    beforeSend: function() {
                        $('body').addClass('-loading');
                    },
                    success: function(html) {
                        if(html) {
                            var $layout = $(html);
                            var uniqid = acf.uniqid();
                            
                            var search = 'acf[' + flexible.get('key') + '][acfcloneindex]';
                            var replace = flexible.$control().find('> input[type=hidden]').attr('name') + '[' + uniqid + ']';
                            
                            // add row
                            var $el = acf.duplicate({
                                target: $layout,
                                search: search,
                                replace: replace,
                                append: flexible.proxy(function($el, $el2) {
                                    // append
                                    if(args.before)
                                        args.before.before($el2);
                                    else
                                        flexible.$layoutsWrap().append($el2);
                                    
                                    // enable 
                                    acf.enable($el2, flexible.cid);
                                    
                                    // render
                                    flexible.render();
                                })
                            });
                            
                            // Fix data-id
                            $el.attr('data-id', uniqid);
                            
                            // trigger change for validation errors
                            flexible.$input().trigger('change');
                            
                            // return
                            return $el;
                        }
                    },
                    'complete': function() {
                        $('body').removeClass('-loading');
                        window.lazyLoadInstance.update();
                    }
                });
            };
        // }
    });


    model.setAjaxFonts();
    $(document).on('click', '[data-action="search"]', function(e) {
        e.stopPropagation();
    });
    $(document).on('input', '[data-action="search"]', model.search);
    $(document).on('click', '[data-event="settings-layout"]', model.modalSettings);
    $(document).on('click', '[data-name="add-layout"]', function() {
        setTimeout(() => {
            window.lazyLoadInstance.update();
        }, 200);
    });

    $(document).on('click', '.widget-layout-horizontal input', function() {
        var $element = $(this);
        $element.closest('td.acf-fields').find('.values').attr('data-align-horizontal', $element.val());
    });
    $(document).on('click', '.widget-layout-vertical input', function() {
        var $element = $(this);
        $element.closest('td.acf-fields').find('.values').attr('data-align-vertical', $element.val());
    });
    $(document).on('change', '.grid-widget-settings--desktop select', function() {
        var $element = $(this);
        $element.closest('.layout').attr('data-columns-desktop', $element.val());
    });

    $(document).on('mouseenter', '.grid-widget-settings', function() {
        model.layoutMouseOver(null, $(this));
    });
    $(document).on('mouseleave', '.grid-widget-settings', function() {
        model.layoutMouseOut(null, $(this));
    });

    $(document).ready(() => {
        if($('#acf-group_widgets_acf').length)
            $('#postdivrich').hide();
    })
})(jQuery);
