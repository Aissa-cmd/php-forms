<?php

abstract class SaveClass {
  abstract function save($data);
}

class TextFileSaver extends SaveClass {
  private $file_name = null;

  function __construct(string|callable $file_name, array $include=[], array $exclude=[]) {
    $this->file_name = $file_name;
    $this->include = $include;
    $this->exclude = $exclude;
  }

  private function get_file_name($data) {
    if(is_string($this->file_name)) {
      return $this->file_name;
    } elseif (is_callable($this->file_name)) {
      return $this->file_name->call($this, $data);
    }
  }

  private function get_fields_to_save($all_fields) {
    if(empty($this->include)) {
      $this->include = [...$all_fields];
    } else {
      $all_fields = array_filter($all_fields, function($key) {
        return in_array($key, $this->include);
      });
    }

    if(empty($this->exclude)) {
      return $all_fields;
    } else {
      return array_filter($all_fields, function($key) {
        return !in_array($key, $this->exclude);
      });
    }
  }

  function save($data) {
    $file_name = $this->get_file_name($data);
    $fields_to_save = $this->get_fields_to_save(array_keys($data));
    $data = array_intersect_key($data, array_flip($fields_to_save));
    $file;
    try{
      $file = fopen($file_name.".txt", "w");
      foreach($data as $key => $value) {
        fwrite($file, "$key: $value\n");
      }
    } catch(error) {
      echo error;
      throw new Error();
    } finally {
      fclose($file);
    }
  }
}

