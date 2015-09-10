<?php
drupal_add_css(drupal_get_path('theme', 'library_basic') . '/css/style.css');
drupal_add_js(drupal_get_path('theme', 'library_basic') . '/js/behaviors.js');

function _library_basic__link_field($variables, $field_type) {
  /* Based on https://api.drupal.org/comment/24908#comment-24908 */
  $output = '';

  // Render the label, if it's not hidden.
  if (!$variables['label_hidden']) {
    $output .= '<div class="field-label"' . $variables['title_attributes'] . '>' . $variables['label'] . ':&nbsp;</div>';
  }

  // Render the items.

/**** new var to check later for empty field values ********/
  $is_empty = true;

  $output .= '<div class="field-items"' . $variables['content_attributes'] . '>';
  foreach ($variables['items'] as $delta => $item) {
    $classes = 'field-item ' . ($delta % 2 ? 'odd' : 'even');
    /* ****  */
    $rendered_item = drupal_render($item);
    if ($rendered_item) {
      if ($field_type == 'telephone') {
        /* Try to get a reasonably clean tel: URI */
        $clean = preg_replace('/[^+0-9]/', '-', $rendered_item);
        $clean = preg_replace('/-+/', '-', $clean);
        $clean = preg_replace('/^-/', '', $clean);
        $rendered_item = "<a href='tel:$clean' property='telephone'>$rendered_item</a>";
      } elseif ($field_type == 'email') {
        $rendered_item = "<a href='mailto:$rendered_item' property='email'>$rendered_item</a>";
      } elseif ($field_type == 'address') {
        /* Horrible hack until we render the nodes properly! */
        $clean = preg_replace('/<\/div>/', ' <\/div>', $rendered_item);
        $clean = urlencode(strip_tags($clean));
        $rendered_item = "<a href='https://maps.google.com?q=$clean' property=''>$rendered_item</a>";
      }
      $is_empty = false;
    }

    $output .= '<div class="' . $classes . '"' . $variables['item_attributes'][$delta] . '>' . $rendered_item . '</div>';
  }
  $output .= '</div>';

  // Render the top-level DIV.
  $output = '<div class="' . $variables['classes'] . '"' . $variables['attributes'] . '>' . $output . '</div>';

  if ($is_empty) {
    // all for nothing, but at least, we will not end up with rendered empty boxes
    $output=FALSE;
  }

  return $output;
}

function library_basic_field__field_telephone($variables) {
  return _library_basic__link_field($variables, 'telephone');
}

function library_basic_field__field_email($variables) {
  return _library_basic__link_field($variables, 'email');
}

function library_basic_field__field_address($variables) {
  return _library_basic__link_field($variables, 'address');
}
