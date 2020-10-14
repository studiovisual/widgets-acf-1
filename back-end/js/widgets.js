jQuery(function(){

  if(jQuery('.acf-field').length){

    set_ajax_fonts();

    add_layout();
    change_layout();
    set_layout_light_box();
  }

});

function set_ajax_fonts(){
  
  jQuery('#acf-group_widgets_acf').css({'opacity':'0.2'});

  var site_url = window.location.href.split('/wp-admin');

  var settings_ajax = {
    "async": true,
    "crossDomain": true,
    "url": site_url[0]+"/wp-admin/admin-ajax.php",
    "method": "POST",
    "headers": {
     "content-type": "application/x-www-form-urlencoded",
     "cache-control": "no-cache"},
     "data": {
      "action": "fonts_widgets_acf"
    }
  }

  var fonts_selected = '';
  jQuery.ajax(settings_ajax).done(function (resposta) {

    if(resposta.length!=0){
      
      for (var i = 0; i < resposta['fonte'].length; i++) {
        fonts_selected = resposta['fonte'][i]+';'+fonts_selected;
      }
      console.log(fonts_selected);

      for (var w = 0; w < resposta['weights'].length; w++) {

        var w_k = 0;
        for (var key_w in resposta['weights'][w]){

          jQuery('body').append('<input type="hidden" id="fontsweight_selected_widget_acf_'+key_w+'" value="'+resposta['weights'][w][key_w]+'" />');

          console.log(key_w);
          w_k++;
        }

      }

      if(!resposta['fonte']){
        fonts_selected='Arial;'
      }

      jQuery('body').append('<input type="hidden" id="fonts_selected_widget_acf" value="'+fonts_selected+'" />');

      var fonts =  jQuery('#fonts_selected_widget_acf').val().split(';');

      for (var i = 0; i < fonts.length-1; i++) {

        var name_hidden_variant = jQuery('#fontsweight_selected_widget_acf_'+fonts[i].replace(' ','_')).val();

        console.log(name_hidden_variant);
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

        for (var w = 0; w < weight_fonts.length; w++) {
          styles_weight.push({
            name: names[weight_fonts[w]],
            element: 'font',
            styles: {
              'font-weight': weight_fonts[w]
            }
          });
        }

        if(styles_weight){
          CKEDITOR.stylesSet.add( 'my_styles_'+fonts[i].replace(' ','_'), styles_weight);
        }

      }
    }
    
    set_widget_light_box();

    jQuery('#acf-group_widgets_acf').css({'opacity':'1'});

  });

}

function add_layout(){

  jQuery('div[data-key="field_the_contents"]').find('a[data-event="add-layout"], a[data-name="add-layout"]').click(function(){

    setTimeout(function(){

      jQuery(".acf-fc-popup li a").click(function(){

        jQuery('div[data-key="field_the_contents"]').find('a[data-event="add-layout"], a[data-name="add-layout"]').hide();
        setTimeout(function(){

         set_widget_light_box();

         load_new_column();
         change_new_column();

         jQuery('div[data-key="field_the_contents"]').find('a[data-event="add-layout"], a[data-name="add-layout"]').show();

       },500);
      });

    },100);

  });
}

function change_layout(){

  jQuery("#acf-group_widgets_acf").find('select[data-ajax="1"]').each(function(){
    jQuery(this).attr('data-ajax_bkp', 1);
  });

  jQuery('a[data-event="add-row"]').click(function(){
    setTimeout(function(){
     set_layout_light_box();
   },500);

  });
}
function set_ckeditor_inline(class_repeat=null, txt=null, styleset_var=null){

  if(!jQuery('#fonts_selected_widget_acf').val()){
    jQuery('body').append('<input type="hidden" id="fonts_selected_widget_acf" value=";" />');
  }

  var class_use_ckeditor = 'ckeditor_inline';
  var find_use_ck_editor = class_use_ckeditor;
  if(styleset_var){
    var toolbarText = [
      [
      'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'Anchor', 'Font','Styles','FontSize',
      'Link', 'TextColor',
      'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'lineheight',
      'BGColor']
    ];
    var toolbarTextArea = [
      ['RemoveFormat', 'Bold', 'Italic', 'Underline', 'Link', 'PasteFromWord', 'Font', 'Styles','FontSize',  'NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
    ];
  }else{
    var toolbarText = [
      [
      'Bold', 'Italic', 'Underline', 'Strike','RemoveFormat',
      'Anchor', 'Font','FontSize', 'Link', 'TextColor',
      'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'lineheight',
      'BGColor']
    ];
    var toolbarTextArea = [
      ['RemoveFormat', 'Bold', 'Italic', 'Underline', 'Link', 'PasteFromWord', 'Font','FontSize',  'NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']
    ];
  }

  if(class_repeat!=null){
    class_use_ckeditor = class_use_ckeditor+' '+class_repeat+'_ck_repeat';
    find_use_ck_editor = class_repeat+'_ck_repeat';
  }

  setTimeout(function(){
    jQuery('.acf_box_widgets_content').find('textarea, input[type="text"]:not(.wp-color-picker)').each(function() {
      if(!jQuery(this).closest('.acf-color-picker')[0] && !jQuery(this).closest('.acf-clone')[0]) {
        var id_div = jQuery(this).attr('name');
        var isTextInput = jQuery(this).attr('type') == 'text';

        jQuery("#" + id_div).remove();
        jQuery(this).parent().append('<div id="' + id_div + '" class="' + class_use_ckeditor + (isTextInput ? ' ckeditor_inline_input_text' : '') + '" contenteditable="true" >' + jQuery(this).val() + '</div>');
        jQuery(this).remove();
      }
    });

  }, 0);
    
  setTimeout(() => {
      
      jQuery('.ckeditor_inline').show();

      jQuery('.acf_box_widgets_content').find('textarea, input[type="text"]:not(.wp-color-picker)').show();
      
      if(txt){
        jQuery('.acf_box_widgets_content').append('<input type="hidden" name="txt_widgets_acf" value="'+txt+'">');
      }

      jQuery('.acf_box_widgets_content').find('div.' + find_use_ck_editor).each(function(){

        var txt = jQuery('input[name="txt_widgets_acf"]').val();
       
        if(!txt){
          var txt = jQuery(this).attr('id');
        }

        if(jQuery(this).attr('id')==txt){

          var styles_selected = jQuery(this).html().split('font-family:');
          var toolbar = jQuery(this).hasClass('ckeditor_inline_input_text') ? toolbarText : toolbarTextArea;

          if(!jQuery(this).hasClass('cke_editable_inline')) {

            CKEDITOR.disableAutoInline = true;

            if(styleset_var){

              CKEDITOR.inline(txt, {
                enterMode : CKEDITOR.ENTER_BR,
                autoParagraph : true,
                forcePasteAsPlainText: true,
                stylesSet : styleset_var,
                font_names : jQuery('#fonts_selected_widget_acf').val(),
                toolbar: toolbar
              });

            }else{

              CKEDITOR.inline(txt, {
                enterMode : CKEDITOR.ENTER_BR,
                autoParagraph : true,
                forcePasteAsPlainText: true,
                font_names : jQuery('#fonts_selected_widget_acf').val(),
                toolbar: toolbar
              });
            }
          }
          if(jQuery('input[name="txt_widgets_acf"]')){
            jQuery('input[name="txt_widgets_acf"]').remove();
          }
        destroy_ck_widget(txt);
      }
    });

  },1000);

}

function destroy_ck_widget(txt){

 CKEDITOR.instances[txt].on('change', function() {

  jQuery('.acf-table').each(function(){  
    jQuery(this).removeAttr('data-cke-table-faked-selection-table');
    jQuery(this).find('.acf-fields').removeClass('cke_table-faked-selection');
  });

  if(CKEDITOR.instances[txt].toolbar[0].items[6]['_'].value){

    var styles_selected = CKEDITOR.instances[txt].toolbar[0].items[6]['_'].value.replace(' ','_');

    CKEDITOR.instances[txt].destroy();

    console.log(styles_selected);

    set_ckeditor_inline(null, txt, 'my_styles_'+styles_selected);

  }

});

}

function set_widget_light_box(){


  jQuery("[data-key='field_linha_widgets'] .layout .acf-fc-layout-handle").click(function(){

    jQuery('#widget_acf_box_light').remove();

    var this_click = jQuery(this).parent();

    var values_input = jQuery(this).parent().find('.acf-fields');

    values_input.find('select').each(function(){
      jQuery(this).prop('disabled', false);
    });

    values_input.find('select[data-ajax_bkp="1"]').each(function(){
      jQuery(this).attr('data-ajax',1);
    });

    values_input.find('.select2-container').remove();

    jQuery('body').append('<div id="widget_acf_box_light"></div>');
    jQuery('#widget_acf_box_light').html('<div class="acf_box_widgets_content acf-fields"></div>');
    jQuery('.acf_box_widgets_content')
    .html('<div class="fixed_box_light"><h1>'+
     this_click.find('.acf-fc-layout-handle').text()+
     '</h1><div class="close button">Concluir</div></div>'+values_input.html()
     );

    jQuery('.acf_box_widgets_content div[data-conditions]').each(function(){
      var json = jQuery(this).data('conditions');
      for (let j = 0; j < json.length; j++) {

        for (let js = 0; js < json[j].length; js++) {
         
          var data_key = jQuery('.acf_box_widgets_content').find('.'+json[j][js].field).data('key');
          json[0][0].field = data_key;
          jQuery(this).attr('data-conditions', JSON.stringify(json));
        }
      }
    });

    acf.do_action('append', jQuery('#widget_acf_box_light'));

    // jQuery('.acf_box_widgets_content select[data-ui="1"]').select2();

    jQuery('.acf_box_widgets_content').find('select').change(function(){

      var value_select = jQuery(this).val();

      jQuery(this).find('option[selected="selected"]').removeAttr("selected");

      if(value_select && typeof value_select === 'object' && value_select.constructor === Array){
        for (let s_l = 0; s_l < value_select.length; s_l++) {

          jQuery(this).find('option[value="'+value_select[s_l]+'"]').attr("selected","selected");
        }
      }else{

        jQuery(this).find('option[value="'+jQuery(this).val()+'"]').attr("selected","selected");
      }

      console.log(value_select);

    });

    jQuery('.acf_box_widgets_content').find('input[type="checkbox"]:not(.acf-switch-input)').change(function(){

      if(jQuery(this).attr("checked")){

        jQuery(this).attr("checked","checked");
      }else{
        
        jQuery(this).removeAttr("checked");
      }
    });

    jQuery('.acf_box_widgets_content').find('input[type="checkbox"].acf-switch-input').each(function(){
      var parentCheck = jQuery(this).parent();

      parentCheck.change(function(){
        var check = jQuery(this).find('input');
        console.log(jQuery(this).find('.acf-switch'));

        if(jQuery(this).find('.acf-switch').hasClass('-focus') && !jQuery(this).find('.acf-switch').hasClass('-on')) {
          check.attr('checked','checked');
          jQuery(this).find('.acf-switch').addClass('-on');
        } else if (jQuery(this).find('.acf-switch').hasClass('-on')){
          check.removeAttr("checked");
          jQuery(this).find('.acf-switch').removeClass('-on');
        }
      });
    });

    jQuery('.acf_box_widgets_content').find('input[type="radio"]').change(function(){

      jQuery(this).parent().parent().parent().find('input[type="radio"]').removeAttr("checked");

      jQuery(this).attr("checked","checked");

    });

    jQuery('.mce-container-body, .mce-tinymce').remove();

    jQuery('.acf_box_widgets_content .wp-picker-container').remove();
    // jQuery('.acf_box_widgets_content .wp-picker-container .wp-picker-holder').remove();

    var myOptions = {
    		// you can declare a default color here,
    		// or in the data-default-color attribute on the input
    		defaultColor: false,
    		// a callback to fire whenever the color changes to a valid color
    		change: function(event, ui){
          // console.log(event);
          // jquery(this).val(('#'+ui.color._color));
        },
    		// a callback to fire when the input is emptied or an invalid color
    		clear: function() {},
    		// hide the color picker controls on load
    		hide: true,
    		// show a group of common colors beneath the square
    		// or, supply an array of colors to customize further
    		palettes: true
    	};

      jQuery('.wp-editor-container textarea').show();

        // editor de texto

        this_click.find('.acf-fields').html('');

        set_ckeditor_inline();

        jQuery('.acf_box_widgets_content a[data-event="add-row"]').click(function(){
          set_ckeditor_inline('ck_repeat');
        });

      // jQuery('.mce-first').first().hide();

      jQuery('.acf_box_widgets_content .acf-color_picker, .acf_box_widgets_content .acf-color-picker').each(function(){

        if(!jQuery(this).parent().parent().parent().parent().hasClass('acf-clone')){

          var_color_picker = jQuery(this).find('input').first();
          var_color_picker.wpColorPicker(myOptions);
          jQuery(this).find('input.wp-color-picker').attr('type','text');
          jQuery(this).prepend(var_color_picker);
        }

      });

      jQuery(".acf_box_widgets_content .close").click(function(){

        jQuery(".acf_box_widgets_content").find('div.ckeditor_inline').each(function(){
         var id_div = jQuery( this ).attr('id');
         if(id_div){
          CKEDITOR.instances[id_div].destroy();
          jQuery(this).parent().append('<textarea name="'+id_div+'" id="'+id_div+'" >'+jQuery( this ).html()+'</textarea>');
          jQuery( this ).remove();
         }
       });

        jQuery('.acf-table').each(function(){
          jQuery(this).removeAttr('data-cke-table-faked-selection-table');
          jQuery(this).find('.acf-fields').removeClass('cke_table-faked-selection');
        });

        jQuery('.acf_box_widgets_content').find('input, textarea').each(function(){
          jQuery(this).val(jQuery(this).val());
          jQuery(this).attr('value',jQuery(this).val());
          jQuery(this).text(jQuery(this).val());
          jQuery(this).html(jQuery(this).val());

        });

        tinymce.remove('.wp-editor-container textarea');

        jQuery('.fixed_box_light').remove();

        var html_box = jQuery('.acf_box_widgets_content').html();
        values_input.html(html_box);

        jQuery('#widget_acf_box_light').remove();

        change_new_column();

      });

    });

}

function set_layout_light_box(){

    // jQuery("[data-key='field_linha_widgets'] .layout .acf-fc-layout-handle").unbind( "click" );

    jQuery(".acf-field[data-name='ajustes_de_layout']").click(function(){

      jQuery('#widget_acf_box_light').remove();

      var this_click = jQuery(this);

      var values_input = jQuery(this).find('.acf-fields');
      
      values_input.find('select').prop('disabled', false);

      values_input.find('.select2-container').remove();

      jQuery('body').append('<div id="widget_acf_box_light" class="light_s"></div>');
      jQuery('#widget_acf_box_light').html('<div class="acf_box_widgets_content acf-fields"></div>');
      jQuery('.acf_box_widgets_content')
      .html('<div class="fixed_box_light"><h1>Ajustes de Layout</h1><div class="close button">Concluir</div></div>'+values_input.html()
       );

      jQuery('.acf_box_widgets_content').find('textarea, input[type="text"]:not(.wp-color-picker)').show();

      jQuery('.acf_box_widgets_content').find('select').change(function(){

        // console.log(jQuery(this).val());

        jQuery(this).find('option[selected="selected"]').removeAttr("selected");

        jQuery(this).find('option[value="'+jQuery(this).val()+'"]').attr("selected","selected");
        console.log(jQuery(this).html());
      });

      jQuery('.acf_box_widgets_content').find('input[type="radio"]').change(function(){

        console.log(jQuery(this).val());

        jQuery(this).parent().parent().parent().find('input[type="radio"]').removeAttr("checked");

        jQuery(this).attr("checked","checked");

      });

      jQuery('.mce-container-body,.wp-editor-tools, .mce-tinymce, .quicktags-toolbar').remove();

      jQuery('.acf_box_widgets_content .wp-picker-container').remove();
    // jQuery('.acf_box_widgets_content .wp-picker-container .wp-picker-holder').remove();

    var myOptions = {
        // you can declare a default color here,
        // or in the data-default-color attribute on the input
        defaultColor: false,
        // a callback to fire whenever the color changes to a valid color
        change: function(event, ui){
          // console.log(event);
          // jquery(this).val(('#'+ui.color._color));
        },
        // a callback to fire when the input is emptied or an invalid color
        clear: function() {},
        // hide the color picker controls on load
        hide: true,
        // show a group of common colors beneath the square
        // or, supply an array of colors to customize further
        palettes: true
      };

      jQuery('.wp-editor-container textarea').show();


      // jQuery('.mce-first').first().hide();

      jQuery('.acf-date-time-picker').find('input[type="text"]').datetimepicker({
        altFormat: jQuery('.acf-date-time-picker').attr('data-date_format'),
        timeFormat: jQuery('.acf-date-time-picker').attr('data-time_format')
      });

      jQuery('.acf_box_widgets_content .acf-color_picker, .acf_box_widgets_content .acf-color-picker').each(function(){

        if(!jQuery(this).parent().parent().parent().parent().hasClass('acf-clone')){

          var_color_picker = jQuery(this).find('input').first();

          var_color_picker.wpColorPicker(myOptions);
          jQuery(this).find('input.wp-color-picker').attr('type','text');
          jQuery(this).prepend(var_color_picker);
        }

      });

      jQuery(".acf_box_widgets_content .close").click(function(){

        console.log('close 2');

        jQuery('.acf_box_widgets_content').find('input, textarea').each(function(){
          jQuery(this).val(jQuery(this).val());
          jQuery(this).attr('value',jQuery(this).val());
          jQuery(this).text(jQuery(this).val());
          jQuery(this).html(jQuery(this).val());
        });

        tinymce.remove('.wp-editor-container textarea');

        jQuery('.fixed_box_light').remove();

        var html_box = jQuery('.acf_box_widgets_content').html();
        values_input.html(html_box);

        jQuery('#widget_acf_box_light').remove();

      });

    });

  }