  INSERT INTO configuration
  ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, use_function, set_function)
VALUES
  ( "Enable product reviews by guests?", "REVIEWS_BY_GUESTS", "1", "Identifies whether (1) or not (0) your store allows guests to write reviews.", 18, 63, NOW(), NULL, "zen_cfg_select_option(array('1', '0')," ),
  ( "Product Review Write - Guest Reviewer Name", "REVIEW_NAME_MIN_LENGTH", 5, "Minimum length of a guest reviewer's name", 2, 14, NOW(), NULL, NULL );