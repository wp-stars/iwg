<?php

if ( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array(
        'key' => 'group_6629164363008',
        'title' => 'Produkte',
        'fields' => array(
            array(
                'key' => 'field_662916464d436_001',
                'label' => 'Unterüberschrift',
                'name' => 'wps_sp_subheadline',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'wpml_cf_preferences' => 2,
                'default_value' => 'Subheadline',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_662916464d436',
                'label' => 'Beschreibung: Headline',
                'name' => 'wps_sp_description_title',
                'aria-label' => '',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'wpml_cf_preferences' => 2,
                'default_value' => 'Beschreibung',
                'maxlength' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
            ),
            array(
                'key' => 'field_662917d00352d',
                'label' => 'Beschreibung: Text',
                'name' => 'wps_sp_description_text',
                'aria-label' => '',
                'type' => 'wysiwyg',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'wpml_cf_preferences' => 2,
                'default_value' => '',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_662919c1b7918',
                'label' => 'Produkt PDF Downloads',
                'name' => 'wps_sp_downloads',
                'aria-label' => '',
                'type' => 'repeater',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'wpml_cf_preferences' => 2,
                'layout' => 'table',
                'pagination' => 0,
                'min' => 0,
                'max' => 0,
                'collapsed' => 'field_662919f2b7919',
                'button_label' => 'Eintrag hinzufügen',
                'rows_per_page' => 20,
                'sub_fields' => array(
                    array(
                        'key' => 'field_662919f2b7919',
                        'label' => 'PDF',
                        'name' => 'pdf',
                        'aria-label' => '',
                        'type' => 'file',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'wpml_cf_preferences' => 2,
                        'return_format' => 'array',
                        'library' => 'all',
                        'min_size' => '',
                        'max_size' => 20,
                        'mime_types' => 'pdf,PDF',
                        'parent_repeater' => 'field_662919c1b7918',
                    ),
                ),
            ),
            array(
                'key' => 'field_662918566e822',
                'label' => 'Eigenschaften & Vorteile',
                'name' => 'wps_sp_features_text',
                'aria-label' => '',
                'type' => 'wysiwyg',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'wpml_cf_preferences' => 2,
                'default_value' => '',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_66291956143de',
                'label' => 'Einsatzbereiche',
                'name' => 'wps_sp_areas_of_application_text',
                'aria-label' => '',
                'type' => 'wysiwyg',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '50',
                    'class' => '',
                    'id' => '',
                ),
                'wpml_cf_preferences' => 2,
                'default_value' => '',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'product',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
        'acfml_field_group_mode' => 'localization',
    ));

endif;