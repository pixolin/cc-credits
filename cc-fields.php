<?php
/*
 * Set an array of fields for the meta box
 */

// No direct execution of plugin
defined('ABSPATH') or die();


class CC_Credits {

    private $prefix             = 'ccc_';
    public  $custom_meta_fields = array();

    function __construct() {
        $this->custom_meta_fields = array(
            array(
                'label'=> __('Title of Image', 'cc-credits'),
                'id'    => $this->prefix.'image',
                'type'  => 'text'
            ),

            array(
                'label'=> __('Year of Creation', 'cc-credits'),
                'id'    => $this->prefix.'year',
                'type'  => 'text'
            ),

            array(
                'label'=> __('Author', 'cc-credits'),
                'id'    => $this->prefix.'author',
                'type'  => 'text'
            ),

            array(
                'label'=> __('URL', 'cc-credits'),
                'id'    => $this->prefix.'url',
                'type'  => 'text'
            ),

            array(
                'label'=> __('License', 'cc-credits'),
                'id'    => $this->prefix.'license',
                'type'  => 'select',
                'options' => array (
                    'None' => array (
                        'label' => __('None/Public Domain', 'cc-credits'),
                        'value' => ''
                    ),
                    'BY' => array (
                        'label' => __('Attribution', 'cc-credits'),
                        'value' => 'BY'
                    ),
                    'BY-SA' => array (
                        'label' => __('Attribution | ShareAlike', 'cc-credits'),
                        'value' => 'BY-SA'
                    ),
                    'BY-ND' => array (
                        'label' => __('Attribution | NoDerivs', 'cc-credits'),
                        'value' => 'BY-ND'
                    ),
                    'BY-NC' => array (
                        'label' => __('Attribution | NonCommercial', 'cc-credits'),
                        'value' => 'BY-NC'
                    ),
                    'BY-NC-SA' => array (
                        'label' => __('Attribution | NonCommercial | ShareAlike', 'cc-credits'),
                        'value' => 'BY-NC-SA'
                    ),
                    'BY-NC-ND' => array (
                        'label' => __('Attribution | NonCommercial | NoDerivs', 'cc-credits'),
                        'value' => 'BY-NC-ND'
                    )
                )
            ),

            array(
                'label'=> __('Version', 'cc-credits'),
                'id'    => $this->prefix.'version',
                'type'  => 'select',
                'options' => array (

                    '2.0' => array (
                        'label' => '2.0',
                        'value' => '2.0'
                    ),
                    '3.0' => array (
                        'label' => '3.0',
                        'value' => '3.0'
                    ),
                    '4.0' => array (
                        'label' => '4.0',
                        'value' => '4.0'
                    ),
                    '1.0' => array (
                        'label' => '1.0',
                        'value' => '1.0'
                    )
                )

            )
        );

    } // ends function __construct()
} // ends class CC_Credits