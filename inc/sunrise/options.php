<?php

 /** Plugin options */
 $options = array(
         array(
                 'name' => __('About', PLUGIN_TXT_DOMAIN),
                 'type' => 'opentab'
         ),
         array(
                 'type' => 'about'
         ),
         array(
                 'type' => 'closetab',
                 'actions' => false
         ),
         array(
                 'name' => __('Standard fields', PLUGIN_TXT_DOMAIN),
                 'type' => 'opentab'
         ),
         array(
                 'name' => __('Text input', PLUGIN_TXT_DOMAIN),
                 'desc' => __('Text input description', PLUGIN_TXT_DOMAIN),
                 'std' => 'Default value',
                 'id' => 'text',
                 'type' => 'text'
         ),
         array(
                 'name' => __('Textarea', PLUGIN_TXT_DOMAIN),
                 'desc' => __('Textarea description', PLUGIN_TXT_DOMAIN),
                 'std' => 'Default value',
                 'id' => 'textarea',
                 'type' => 'textarea',
                 'rows' => 7
         ),
         array(
                 'name' => __('Checkbox', PLUGIN_TXT_DOMAIN),
                 'desc' => __('Checkbox description', PLUGIN_TXT_DOMAIN),
                 'std' => 'on',
                 'id' => 'checkbox',
                 'type' => 'checkbox',
                 'label' => __('Checkbox label', PLUGIN_TXT_DOMAIN)
         ),
         array(
                 'name' => __('Radio buttons', PLUGIN_TXT_DOMAIN),
                 'desc' => __('Radio buttons description', PLUGIN_TXT_DOMAIN),
                 'options' => array(
                         'option1' => __('Option 1', PLUGIN_TXT_DOMAIN),
                         'option2' => __('Option 2', PLUGIN_TXT_DOMAIN),
                         'option3' => __('Option 3', PLUGIN_TXT_DOMAIN)
                 ),
                 'std' => 'option1',
                 'id' => 'radio',
                 'type' => 'radio'
         ),
         array(
                 'name' => __('Select', PLUGIN_TXT_DOMAIN),
                 'desc' => __('Select description', PLUGIN_TXT_DOMAIN),
                 'options' => array(
                         'option1' => __('Option 1', PLUGIN_TXT_DOMAIN),
                         'option2' => __('Option 2', PLUGIN_TXT_DOMAIN),
                         'option3' => __('Option 3', PLUGIN_TXT_DOMAIN)
                 ),
                 'std' => 'option1',
                 'id' => 'select',
                 'type' => 'select'
         ),
         array(
                 'type' => 'closetab'
         ),
         array(
                 'name' => __('Additional fields', PLUGIN_TXT_DOMAIN),
                 'type' => 'opentab'
         ),
         array(
                 'name' => __('Title field', PLUGIN_TXT_DOMAIN),
                 'type' => 'title'
         ),
         array(
                 'html' => '<p>Vestibulum nec quam nisl. Nulla facilisi. Etiam placerat tempor rutrum. Fusce pellentesque tellus adipiscing nulla eleifend pretium. In lacinia lectus et sapien elementum eget sollicitudin ante suscipit. Nunc eu arcu nec risus bibendum mattis. Suspendisse nisi magna, <a href="#">pretium in aliquam viverra</a>, cursus tincidunt quam. Ut nec risus elit, vel pellentesque felis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p><p>Fusce venenatis condimentum est, eget gravida erat interdum tristique. In hac habitasse platea dictumst. In hac habitasse platea dictumst. Vestibulum fringilla egestas erat, sit amet ullamcorper nisi placerat vel.</p>',
                 'type' => 'html'
         ),
         array(
                 'name' => __('Checkbox group', PLUGIN_TXT_DOMAIN),
                 'desc' => __('Checkbox group description', PLUGIN_TXT_DOMAIN),
                 'options' => array(
                         'option1' => __('Option 1', PLUGIN_TXT_DOMAIN),
                         'option2' => __('Option 2', PLUGIN_TXT_DOMAIN),
                         'option3' => __('Option 3', PLUGIN_TXT_DOMAIN)
                 ),
                 'std' => array(
                         'option1' => '',
                         'option2' => 'on',
                         'option3' => 'on',
                 ),
                 'id' => 'checkbox-group',
                 'type' => 'checkbox-group'
         ),
         array(
                 'name' => __('Number', PLUGIN_TXT_DOMAIN),
                 'desc' => __('Number field description', PLUGIN_TXT_DOMAIN),
                 'std' => 100,
                 'min' => 0,
                 'max' => 1000,
                 'units' => __('pixels', PLUGIN_TXT_DOMAIN),
                 'id' => 'number',
                 'type' => 'number'
         ),
         array(
                 'name' => __('Size', PLUGIN_TXT_DOMAIN),
                 'desc' => __('Size field description', PLUGIN_TXT_DOMAIN),
                 'std' => array(14, 'px'),
                 'min' => 1,
                 'max' => 72,
                 'units' => array('px', 'em', '%', 'pt'),
                 'id' => 'size',
                 'type' => 'size'
         ),
         array(
                 'name' => __('Upload', PLUGIN_TXT_DOMAIN),
                 'desc' => __('Upload field description', PLUGIN_TXT_DOMAIN),
                 'std' => '',
                 'id' => 'upload',
                 'type' => 'upload'
         ),
         array(
                 'name' => __('Color picker', PLUGIN_TXT_DOMAIN),
                 'desc' => __('Color picker description', PLUGIN_TXT_DOMAIN),
                 'std' => '#00bb00',
                 'id' => 'color',
                 'type' => 'color'
         ),
         array(
                 'name' => __('Code editor', PLUGIN_TXT_DOMAIN),
                 'desc' => __('Code editor description', PLUGIN_TXT_DOMAIN),
                 'std' => '',
                 'rows' => 7,
                 'id' => 'code',
                 'type' => 'code'
         ),
         array(
                 'type' => 'closetab'
         ),
 );
?>