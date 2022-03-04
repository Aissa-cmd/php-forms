<?php
// The `Form` class that all the forms has to inherit
abstract class Form {
  // the `$_called_is_valid` to make sure that we first call the `is_valid` method of the form
  // before calling the `save` method of the form
  private $_called_is_valid = false;
  private $_data;
  private $_save_class;

  public function __construct($data, $save_class = null) {
    $this->_save_class = $save_class;
    // call the `set_data` and give it the $data as the argument [look in the set_data]
    $this->set_data($data);
    $fields = $this->get_fields();
    foreach($fields as $fld) {
      $fld->set_value(isset($data[$fld->name]) ? $data[$fld->name]: null);
    }
  }

  private function set_data($data) {
    // call the `get_field_names` that returns the name of all the [Look in the get_field_names] which returns
    // the name of the fields that are defined in the child class (The class that inherits this one).
    $field_names = $this->get_field_names();
    $this->_data = array_filter($data, function($key) use($field_names) {
      if(in_array($key, $field_names)) {
        return true;
      }
      return false;
    }, ARRAY_FILTER_USE_KEY);
  }

  // the `get_field_names` return the name of the fields in the form
  private function get_field_names() {
    return array_map(function($field) {return $field->name;}, $this->get_fields());
  }

  // the `show` method of the `Form` call the show method of each field that display the html of the field.
  public function show() {
    $fields = $this->get_fields();
    foreach($fields as $fld) {
      $fld->show();
    }
  }

  // the `reset` method of the `Form` resets the value of the fields.
  public function reset() {
    foreach ($this->get_fields() as $field) {
      $field->set_value("");
    }
  }

  // [This is the most important method in the class].
  // (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC) this will return all the public attributes of the child class
  // which in our case are the form fields. ex [field1, field2, ....]
  // the indexes of the array are integers 0, 1, 2,...
  // but I wante it to be an associative array where the indexes are the names of the fields rather that integers
  // $fields[$value->getValue($this)->name] = $value->getValue($this);
  // this line add the $fields array each field where the key the field's name and the value is the field itself
  public function get_fields() {
    $fields = [];
    foreach ((new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $value) {
      $fields[$value->getValue($this)->name] = $value->getValue($this);
    }
    return $fields;
  }

  public function is_valid() {
    $this->_called_is_valid = true;
    
    $fields = $this->get_fields();
    $results = array_map(function($field) {return $field->is_valid();}, $fields);
    if(array_filter($results, function($res) {return $res == true;}) == $results) {
      return true;
    }
    return false;
  }

  public function save() {
    if(!$this->_called_is_valid) {
      throw new Error("You must call is_valid() first, before calling save()");
    } elseif(!($this->_save_class instanceof SaveClass)) {
      throw new Error("save_class must be subclass of SaveClass");
    }

    try {
      $this->_save_class->save($this->_data);
    } catch(error) {
      throw new Error("Could not save the data successfully");
    }
  }
}

