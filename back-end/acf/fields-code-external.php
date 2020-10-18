<?php

$widgets = Utils::getCodes();
$fields = array();
$label_index = 'index.php';

if(function_exists('\App\template') || function_exists('\Roots\view'))
    $label_index = 'index.blade.php - <b>Utiliza o Template Engine <a href="https://laravel.com/docs/7.x/blade" target="_blank">Blade</a></b>';

$groups_fields = acf_get_field_groups();

foreach ($widgets as $key => $widget):
    $fields_code = array();
    $groups = array();

    foreach($groups_fields as $group):
        if($group['location'][0][0]['param']=='widget_acf' && $group['location'][0][0]['value'] == $key):
            $groups[admin_url('/post.php?post=' . $group['ID'] . '&action=edit')] = '<div class="option_group">'.$group['title'] . '</div><span data-group_id="' . $group['ID'] . '" class="aba_widgets_acf_delete_group_field">X</span>';

            foreach(acf_get_fields($group['key']) as $ff):
                $code = "&lt;?php var_dump(\$widget->fields[&quot;" . $ff['name'] . "_" . $key . "&quot;]); ?&gt;";
                
                if(function_exists('\App\template') || function_exists('\Roots\view'))
                    $code = "{{var_dump(\$widget->fields[&quot;" . $ff['name'] . "_" . $key . "&quot;])}}";

                $fields_code[$ff['key']] = array("title"=>$ff['label'] . ' - ' . $ff['type'], "code" => $code);
            endforeach;
        endif;
    endforeach;

    $label_index .= '<br><br>Campos personalizados:';
    $label_index .= '<ul style="width:100%; display: block;">';

    foreach($fields_code as $fc_k => $fc)
        $label_index .= '<li onclick="insert_line_codemirror(\'' . $fc['code'] . '\')" style="float: left;"><div class="acf-button-group" style="width: auto;"><label class="selected" style="padding: 3px 5px;font-size: 9px;">' . $fc['title'] . '</label></div></li>';
    
    $label_index .= '</ul><hr style="clear: left;">';

    $fields[] = array(
        'key' => 'field_aba_' . $key,
        'label' => $widget['title'].'<span class="aba_widgets_acf_delete">X</span>',
        'type' => 'tab',
        'placement' => 'left',
        'endpoint' => 0,
    );

    $fields[] = array(
        'key' => 'field_group_' . $key,
        'label' => $widget['title'] . '<div class="acf-button-group" style="width: auto;float: right;position: absolute;right: 0px;top: -10px;"><a target="_blank" href="' . admin_url('admin.php?page=acf-options-todos-os-widgets&export=' . $key) . '"><label class="selected" style="padding: 7px 10px;">Exportar .zip</label></a></div>',
        'name' => 'widget_'.$key,
        'type' => 'group',
        'instructions' => 'Cuidado para não inserir código impeditivos de funcionamento do wordpress como <b>die(); break();</b>',
        'wrapper' => array(
            'class' => 'acf-code-external',
        ),
        'layout' => 'block',
        'sub_fields' => array(
            array(
                'key' => 'field_name_' . $key,
                'label' => 'Nome do widget',
                'name' => 'name',
                'type' => 'text',
                'wrapper' => array (
                    'width' => '40%',
                ),
                'default_value' => $widget['name'],
            ),
            array(
                'key' => 'field_capa_' . $key,
                'label' => 'Imagem de capa',
                'name' => 'capa_imagem',
                'type' => 'image',
                'instructions' => '<img src="' . $widget['capa'] . '" style="height:75px; position: absolute; right: 0; top: -10px;" />',
                'wrapper' => array(
                    'width' => '60%',
                ),
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
            array(
                'key' => 'field_estilo_' . $key,
                'label' => 'Estilo',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_style_' . $key,
                'label' => 'style.scss',
                'name' => 'style',
                'type' => 'textarea',
                'instructions' => 'utilize a classe <code>.widget-acf.' . str_replace('_','-', $key) . ' {}</code> para criar somente no widget',
                'default_value' => isset($widget['style']) ? $widget['style'] : '',
            ),
            array(
                'key' => 'field_template_' . $key,
                'label' => 'Template',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_index_' . $key,
                'label' => $label_index,
                'name' => 'index',
                'type' => 'textarea',
                'default_value' => $widget['index'],
            ),
            array(
                'key' => 'field_tab_javascript_' . $key,
                'label' => 'Javascript',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_javascript_' . $key,
                'label' => 'app.js',
                'name' => 'app',
                'type' => 'textarea',
                'default_value' => isset($widget['js']) ? $widget['js'] : '',
            ),
            array(
                'key' => 'field_php_' . $key,
                'label' => 'PHP',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_functions_' . $key,
                'label' => 'functions.php',
                'name' => 'functions',
                'type' => 'textarea',
                'default_value' => $widget['functions'],
            ),
            array(
                'key' => 'field_json_aba_' . $key,
                'label' => 'Campos personalizados',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_json_' . $key,
                'label' => 'fields.json',
                'name' => 'fields_json',
                'type' => 'button_group',
                'instructions' => 'Clique em editar os campos para recriar o arquivo fields.json em seu widget automaticamente. <div class="acf-button-group" style="width: auto;float: right;margin: 15px 0;"><label class="selected" style="padding: 7px 10px;" onclick="javascript:window.open(\'' . admin_url('post-new.php?post_type=acf-field-group&location_widget_acf=' . $key) . '\',\'_self\');"">Adicionar Grupo de Campos</label></div>',
                'wrapper' => array(
                    'class' => 'choices_fields_group',
                ),
                'choices' => $groups,
                'allow_null' => 1,
                'layout' => 'vertical',
                'return_format' => 'value',
            ),
        ),
    );
endforeach;

acf_add_local_field_group(array(
    'key' => 'group_widgets_all',
    'title' => 'Todos os widgets',
    'fields' => $fields,
    'location' => array(
        array(
            array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-todos-os-widgets',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
));

acf_add_local_field_group(array(
    'key' => 'group_salvar_widget_acf',
    'title' => 'Adicionar widget',
    'fields' => array(
        array(
            'key' => 'field_salvar_widget_acf',
            'label' => 'Título do widget',
            'name' => 'salvar_widget_acf',
            'type' => 'text',
        ),
        array(
            'key' => 'field_button_salvar_widget_acf',
            'label' => 'Clique em salvar para criar um novo widget',
            'name' => 'button_salvar_widget_acf',
            'type' => 'button_group',
            'wrapper' => array(
                'class' => 'external-field-save',
            ),
            'choices' => array(
                'Salvar' => 'Salvar',
            ),
            'layout' => 'horizontal',
            'return_format' => 'value',
        ),
    ),
    'location' => array(
        array(
            array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'acf-options-todos-os-widgets',
            ),
        ),
    ),
    'menu_order' => 0,
    'position' => 'acf_after_title',
    'style' => 'default',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'active' => true,
));
