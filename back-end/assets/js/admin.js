jQuery(function() {
	window.lazyLoadInstance = new LazyLoad({
		elements_selector: '[data-src]',
        use_native: true,
	});

    setTimeout(function() {
		var location_widget_acf = new RegExp('[\?&]location_widget_acf=([^&#]*)').exec(window.location.href);
        
        if(location_widget_acf) {
			jQuery("#title-prompt-text").hide();
			jQuery('input[name="post_title"]').val('Campos personalizados - ' + location_widget_acf[1]);
			jQuery("#acf_field_group-location-group_0-rule_0-param").val('widget_acf').change();
			jQuery("#acf_field_group-location-group_0-rule_0-value").val(location_widget_acf[1]).change();
        }
        
		jQuery('#acf-group_widgets_all .acf-tab-group li span.aba_widgets_acf_delete').click(function() {
			if(confirm('Tem certeza que deseja excluir o widget ?')) {
				var key_widget = jQuery(this).closest('a').data('key').replace('field_aba_', '').replaceAll('_', '-');
				window.location.href = window.location.href + '&del=' + key_widget;
			}
		});

		jQuery(".external-field-save label.selected").click(function() {
			jQuery(this).closest("form").submit();
		});

		if(jQuery('#acf-group_widgets_all .acf-code-external').length) {
			jQuery(".aba_widgets_acf_delete_group_field").click(function() {
				if(confirm("Tem certeza que deseja excluir permanentemente o Grupo de campos ?")) {
					var id_group_field = jQuery(this).data("group_id");
					window.location.href = window.location.href + '&del_group_field=' + id_group_field;
				}
			});

			jQuery('.choices_fields_group .acf-input .option_group').click(function() {
				window.location.href = jQuery(this).parent().find("input").val();
			});

			jQuery('#acf-group_widgets_all .acf-code-external .acf-tab-group li').click(function() {
				var poisition_tab = jQuery(this).index();
				var mode_codemirror = 'application/x-httpd-php';

				if(poisition_tab == 0)
					var mode_codemirror = 'css';
				else if(poisition_tab==1)
					var mode_codemirror = 'htmlmixed';
				else if(poisition_tab==2)
					var mode_codemirror = 'jsx';
				
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
						mode: mode_codemirror,
						indentWithTabs: true
					}
				);
				
				var closest = jQuery(this).closest('.acf-code-external');
				var textarea = closest.find('.acf-field-textarea').eq(poisition_tab);

				if(!textarea.find('.CodeMirror').length)
					var editor = wp.codeEditor.initialize( textarea.find('textarea'), editorSettings);
			});

			jQuery('#acf-group_widgets_all .acf-tab-group').first().find('li').click(function() {
                var poisition_tab = jQuery(this).index();
                
				jQuery('#acf-group_widgets_all .acf-code-external').eq(poisition_tab).find('.acf-tab-group li.active').click();
			});

			jQuery('#acf-group_widgets_all .acf-tab-group').first().find('li.active').click();
		}
	}, 500);
});