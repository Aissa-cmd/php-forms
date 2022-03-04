<?php

$required_validator = function($value) {
  if(empty($value)) {
    return false;
  }
  return true;
};

$email_validator = function ($value) {
  if(filter_var($value, FILTER_VALIDATE_EMAIL) !== false) {
    return true;
  }
  return false;
};

$url_validator = function ($value) {
  if(filter_var($value, FILTER_VALIDATE_URL) !== false) {
    return true;
  }
  return false;
};

$min_validator = function($value, $min, $max) {
  if($value < $min) {
    return false;
  }
  return true;
};

$max_validator = function($value, $min, $max) {
  if($value > $max) {
    return false;
  }
  return true;
};

$radio_required_validator = function($value, $options) {
  if(empty($value)) {
    return false;
  }
  if(!in_array($value, $options)) {
    return false;
  }
  return true;
};

$checkbox_required_validator = function($value, $options) {
  if(count($options) <= 0) {
    return false;
  }
  foreach($value as $opt) {
    if(!in_array($opt, $options)) {
      return false;
    }
    return true;
  }
};

$select_required_validator = function($value, $options) {
  if(count($options) < 0 || !in_array($value, $options)) {
    return false;
  }
  return true;
};
