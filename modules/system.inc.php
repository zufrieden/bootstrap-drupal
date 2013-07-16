<?php
/**
 * @file form.inc.php
 *
 * Form alter and theme preprocess functions.
 */

use Drupal\Core\Template\Attribute;

/**
 * Implements hook_form_alter().
 *
 * @TODO: bootstrap_form_alter() - Needs refactoring. Move to form.inc.php.
 */
function bootstrap_form_alter(&$form) {
  // Id's of forms that should be ignored
  // Make this configurable?
  $form_ids = array(
    'node_form',
    'system_site_information_settings',
    'user_profile_form',
    'node_delete_confirm',
  );
  // Only wrap in container for certain form
  if (isset($form['#form_id']) && !in_array($form['#form_id'], $form_ids) && !isset($form['#node_edit_form']) && isset($form['actions']) && ($form['actions']['#type'] == 'actions')) {
    $form['actions']['#theme_wrappers'] = array();
  }
}

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * @TODO: bootstrap_form_search_form_alter() - Needs refactoring. Move to form.inc.php.
 */
function bootstrap_form_search_form_alter(&$form) {
  $form['#attributes']['class'][] = 'form-search';
  $form['#attributes']['class'][] = 'pull-left';

  $form['basic']['keys']['#title'] = '';
  $form['basic']['keys']['#attributes']['class'][] = 'search-query';
  $form['basic']['keys']['#attributes']['class'][] = 'span2';
  $form['basic']['keys']['#attributes']['placeholder'] = t('Search');

  // Hide the default button from display and implement a theme wrapper to add
  // a submit button containing a search icon directly after the input element.
  $form['basic']['submit']['#attributes']['class'][] = 'visually-hidden';
  $form['basic']['keys']['#theme_wrappers'][] = 'bootstrap_search_form_wrapper';

  // Apply a clearfix so the results don't overflow onto the form.
  $form['#suffix'] = '<div class="clearfix"></div>';
  $form['#attributes']['class'][] = 'content-search';
}

/**
 * Implements hook_form_FORM_ID_alter().
 *
 * @TODO: bootstrap_form_search_block_form_alter() - Needs refactoring. Move to form.inc.php.
 */
function bootstrap_form_search_block_form_alter(&$form) {
  $form['#attributes']['class'][] = 'form-search';

  $form['search_block_form']['#title'] = '';
  $form['search_block_form']['#attributes']['class'][] = 'search-query';
  $form['search_block_form']['#attributes']['class'][] = 'span2';
  $form['search_block_form']['#attributes']['placeholder'] = t('Search');

  // Hide the default button from display and implement a theme wrapper to add
  // a submit button containing a search icon directly after the input element.
  $form['actions']['submit']['#attributes']['class'][] = 'visually-hidden';
  $form['search_block_form']['#theme_wrappers'][] = 'bootstrap_search_form_wrapper';

  // Apply a clearfix so the results don't overflow onto the form.
  $form['#attributes']['class'][] = 'content-search';
}

/**
 * Legacy function.
 *
 * @TODO: _bootstrap_element_whitelist() - Needs refactoring. Move to form.inc.php.
 */
function _bootstrap_element_whitelist() {
  /**
   * Why whitelist an element?
   * The reason is to provide a list of elements we wish to exclude
   * from certain modifications made by the bootstrap theme which
   * break core functionality - e.g. ajax.
   */
  return array(
    'edit-refresh',
    'edit-pass-pass1',
    'edit-pass-pass2',
    'panels-ipe-cancel',
    'panels-ipe-customize-page',
    'panels-ipe-save',
  );
}

/**
 * Implements hook_preprocess_HOOK() for bootstrap-search-form-wrapper.html.twig
 *
 * @TODO: bootstrap_preprocess_bootstrap_search_form_wrapper() - Needs refactoring. Move to theme.inc.php.
 *
 * The following could probably be moved to bootstrap-search-form-wrapper.html.twig:
 *   1 call(s) to t(); can also use t as a filter in Twig
 *   approximately 10 strings of markup
 */
function bootstrap_preprocess_bootstrap_search_form_wrapper(&$variables) {
  $output = '<div class="input-append">';
  $output .= $variables['element']['#children'];
  $output .= '<button type="submit" class="btn">';
  $output .= '<i class="icon-search"></i>';
  $output .= '<span class="visually-hidden">' . t('Search') . '</span>';
  $output .= '</button>';
  $output .= '</div>';
  $variables['bootstrap_preprocess_bootstrap_search_form_wrapper'] = $output;
}

/**
 * Implements hook_preprocess_HOOK() for bootstrap-append-element.html.twig
 *
 * @TODO: bootstrap_preprocess_bootstrap_append_element() - Needs refactoring. Move to theme.inc.php.
 *
 * The following could probably be moved to bootstrap-append-element.html.twig:
 *   approximately 91 strings of markup
 *   2 call(s) to theme() found.  The function call should be removed and changed to simple arrays:
array(
'#theme' => 'form_element_label',
0# => ERROR: could not find T_ARRAY,
)
array(
'#theme' => 'form_element_label',
0# => ERROR: could not find T_ARRAY,
)
 */
function bootstrap_preprocess_bootstrap_append_element(&$variables) {
  $element = &$variables['element'];

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }

  $exclude_control = FALSE;
  $control_wrapper = '<div class="controls">';
  // Add bootstrap class
  if ($element['#type'] == "radio" || $element['#type'] == "checkbox" || isset($element['#exclude_control'])) {
    $exclude_control = TRUE;
  }
  else {
    $attributes['class'] = array('control-group');
  }

  // Check for errors and set correct error class
  if (isset($element['#parents']) && form_get_error($element)) {
    $attributes['class'][] = 'error';
  }

  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
  }
  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }


  if (isset($element['#field_prefix'])) {
    $attributes['class'][] = 'input-prepend';
  }

  if (isset($element['#field_suffix'])) {
    $attributes['class'][] = 'input-append';
  }

  $attributes['class'][] = 'form-item';
  $output = '<div' . new Drupal\Core\Template\Attribute($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? $element['#field_prefix'] : '';
  $suffix = isset($element['#field_suffix']) ? $element['#field_suffix'] : '';

  // Prepare input whitelist - added to ensure ajax functions don't break
  $whitelist = _bootstrap_element_whitelist();

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      // Check if item exists in element whitelist
      if (isset($element['#id']) && in_array($element['#id'], $whitelist)) {
        $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
        $exclude_control = TRUE;
      }
      else {
        $output = $exclude_control ? $output : $output.$control_wrapper;
        $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      }
      break;

    case 'after':
      $output = $exclude_control ? $output : $output.$control_wrapper;
      $variables['#children'] = ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output = $exclude_control ? $output : $output.$control_wrapper;
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if ( !empty($element['#description']) ) {
    $output .= '<p class="help-block">' . $element['#description'] . "</p>\n";
  }

  // Check if control wrapper was added to ensure we close div correctly
  if ($exclude_control) {
    $output .= "</div>\n";
  }
  else {
    $output .= "</div></div>\n";
  }
  $variables['bootstrap_preprocess_bootstrap_append_element'] = $output;
}

/**
 * Implements hook_preprocess_HOOK() for form-element.html.twig.
 *
 * @TODO bootstrap_preprocess_form_element() - Needs refactoring. Move to theme.inc.php.
 *
 * The following could probably be moved to form-element.html.twig:
 *   approximately 89 strings of markup
 *   2 call(s) to theme() found.  The function call should be removed and changed to simple arrays:
 */
function bootstrap_preprocess_form_element(&$variables) {
  $element = &$variables['element'];

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }

  $exclude_control = FALSE;
  $control_wrapper = '<div class="controls">';
  // Add bootstrap class
  if (isset($element['#type']) && ($element['#type'] == "radio" || $element['#type'] == "checkbox")){
    $exclude_control = TRUE;
  }
  else {
    $attributes['class'] = array('control-group');
  }

  // Check for errors and set correct error class
  if (isset($element['#parents']) && form_get_error($element)) {
    $attributes['class'][] = 'error';
  }

  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
  }
  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }
  $attributes['class'][] = 'form-item';
  $output = '<div' . new Drupal\Core\Template\Attribute($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

  // Prepare input whitelist - added to ensure ajax functions don't break
  $whitelist = _bootstrap_element_whitelist();

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      // Check if item exists in element whitelist
      if (isset($element['#id']) && in_array($element['#id'], $whitelist)) {
        $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
        $exclude_control = TRUE;
      }
      else {
        $output = $exclude_control ? $output : $output.$control_wrapper;
        $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      }
      break;

    case 'after':
      $output = $exclude_control ? $output : $output.$control_wrapper;
      $variables['#children'] = ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output = $exclude_control ? $output : $output.$control_wrapper;
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if ( !empty($element['#description']) ) {
    $output .= '<p class="help-block">' . $element['#description'] . "</p>\n";
  }

  // Check if control wrapper was added to ensure we close div correctly
  if ($exclude_control) {
    $output .= "</div>\n";
  }
  else {
    $output .= "</div></div>\n";
  }
  $variables['bootstrap_preprocess_form_element'] = $output;
}

/**
 * Implements hook_preprocess_HOOK() for form-element-label.html.twig.
 *
 * @TODO bootstrap_preprocess_form_element_label() - Needs refactoring. Move to theme.inc.php.
 *
 * The following could probably be moved to form-element-label.html.twig:
 *   approximately 37 strings of markup
 *   1 call(s) to theme() found.  The function call should be removed and changed to simple arrays:
array(
'#theme' => 'form_required_marker',
'#element' => $element,
)
 */
function bootstrap_preprocess_form_element_label(&$variables) {
  $element = $variables['element'];

  // If title and required marker are both empty, output no label.
  if ((!isset($element['#title']) || $element['#title'] === '') && empty($element['#required'])) {
    $variables['bootstrap_preprocess_form_element_label'] = '';
  }

  // If the element is required, a required marker is appended to the label.
  $required = !empty($element['#required']) ? theme('form_required_marker', array('element' => $element)) : '';

  $title = filter_xss_admin($element['#title']);

  $attributes = array();
  // Style the label as class option to display inline with the element.
  if ($element['#title_display'] == 'after') {
    $attributes['class'][] = 'option';
    $attributes['class'][] = $element['#type'];
  }
  // Show label only to screen readers to avoid disruption in visual flows.
  elseif ($element['#title_display'] == 'invisible') {
    $attributes['class'][] = 'visually-hidden';
  }

  if (!empty($element['#id'])) {
    $attributes['for'] = $element['#id'];
  }

  // @Bootstrap: Add Bootstrap control-label class except for radio.
  if ($element['#type'] != 'radio') {
    $attributes['class'][] = 'control-label';
  }
  // @Bootstrap: Insert radio and checkboxes inside label elements.
  $output = '';
  if ( isset($variables['#children']) ) {
    $output .= $variables['#children'];
  }

  // @Bootstrap: Append label
  $output .= t('!title !required', array('!title' => $title, '!required' => $required));

  // The leading whitespace helps visually separate fields from inline labels.
  $variables['bootstrap_preprocess_form_element_label'] = ' <label' . new Drupal\Core\Template\Attribute($attributes) . '>' . $output . "</label>\n";
}

/**
 * Implements hook_preprocess_HOOK().
 */
function bootstrap_preprocess_input(&$variables) {
  // Process buttons, adding classes if necessary.
  if (in_array($variables['theme_hook_original'], array('input__submit', 'input__button'))) {
    _bootstrap_button($variables);
  }
}

/**
 * Adds necessary button classes for Bootstrap.
 */
function _bootstrap_button(&$variables) {
  // Skip white-listed elements.
  if (isset($element['#id']) && in_array($element['#id'], _bootstrap_element_whitelist())) {
    return;
  }

  // Reference the attributes array.
  $attributes = &$variables['element']['#attributes'];

  // Unset core added classes.
  foreach (array_keys($attributes['class'], 'button') as $key) {
    unset($attributes['class'][$key]);
  }
  foreach (array_keys($attributes['class'], 'button-primary') as $key) {
    unset($attributes['class'][$key]);
  }

  // Add default button class.
  $attributes['class'][] = 'btn';

  // Add additional classes for buttons that contain certain text.
  if (!empty($variables['element']['#value'])) {
    $values = array(
      // Specific values.
      t('Save and add')       => 'btn-info',
      t('Add another item')   => 'btn-info',
      t('Add effect')         => 'btn-primary',
      t('Add and configure')  => 'btn-primary',
      t('Update style')       => 'btn-primary',
      t('Download feature')   => 'btn-primary',
      // General values.
      t('Save')     => 'btn-primary',
      t('Apply')    => 'btn-primary',
      t('Create')   => 'btn-primary',
      t('Confirm')  => 'btn-primary',
      t('Submit')   => 'btn-primary',
      t('Export')   => 'btn-primary',
      t('Import')   => 'btn-primary',
      t('Restore')  => 'btn-primary',
      t('Rebuild')  => 'btn-primary',
      t('Search')   => 'btn-primary',
      t('Add')      => 'btn-info',
      t('Update')   => 'btn-info',
      t('Delete')   => 'btn-danger',
      t('Remove')   => 'btn-danger',
    );
    foreach ($values as $value => $class) {
      if (strpos($variables['element']['#value'], $value) !== FALSE) {
        $attributes['class'][] = $class;
        break;
      }
    }
  }
  $variables['attributes'] = new Attribute($attributes);
}
