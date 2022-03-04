<?php

// ==================================================================
// [No]- button
// x- checkbox
// color
// date
// datetile-local
// x- email
// file
// hidden
// image
// month
// x- number
// x- password
// x- radio
// range
// [No]- reset
// search
// [No]- submit
// tel
// x- text
// time
// x- url
// week
// x- select
// ==================================================================


require __DIR__.'/'.'validators.php';

// label 
// name
// required
// attrs = { class: "", id: "", placeholder: "", ... }
abstract class FormInput {
  public $error;
  public $type;
  public $value;
  public $validators = [];
  
  public function __construct() {
    global $required_validator;
    if($this->required) {
      array_push($this->validators, [$required_validator, "This field is required"]);
    }
  }

  public function is_valid() {
    if(!$this->required && empty($this->value)) { return true; }
    return $this->_is_valid();
  }

  public function _is_valid() {
    foreach($this->validators as $validator) {
      if(!$validator[0]($this->value)) {
        $this->error = $validator[1];
        return false;
      }
    }
    return true;
  }
 
  abstract public function show();
  
  public function set_value($value = '') {
    $this->value = $value;
  }
}

class TextInput extends FormInput {
  public function __construct($label, $name, $required=true, $validators= [], $attrs = []) {
    $attrs = array_merge(['class' => '', 'id' => $name], $attrs);

    $this->label = $label;
    $this->name = $name;
    $this->required = $required;
    $this->attrs = $attrs;
    $this->type = "text";
    $this->validators = $validators;

    parent::__construct();
  }

  public function show() {
    echo "
      <label class='input-label' for='".$this->attrs["id"]."'>$this->label".($this->required ? '<span>*</span>': '')."</label>
      <input type='$this->type' name='$this->name' id='".$this->attrs['id']."' 
        class='".$this->attrs['class']."' 
        value='$this->value' ".($this->required ? 'required': '')." />
      <div>".$this->error."</div>
    ";
  }  
}

class TextAreaInput extends TextInput {
  public function show() {
    echo "
      <label class='input-label' for='".$this->attrs["id"]."'>$this->label".($this->required ? '<span>*</span>': '')."</label>
      <textarea name='$this->name' id='".$this->attrs['id']."' 
        class='".$this->attrs['class']."' ".($this->required ? 'required': '')." >$this->value</textarea>
      <div>".$this->error."</div>
    ";
  }
}

class EmailInput extends TextInput {
  public function __construct(...$args) {
    global $email_validator;
    parent::__construct(...$args);
    $this->type = 'email';
    array_push($this->validators, [$email_validator, 'Please enter a valid email']);
  }
}

class URLInput extends TextInput {
  public function __construct(...$args) {
    global $url_validator;
    parent::__construct(...$args);
    $this->type = 'url';
    array_push($this->validators, [$url_validator, 'Please enter a URL']);
  }
}

class PasswordInput extends TextInput {
  public function __construct(...$args) {
    parent::__construct(...$args);
    $this->type = 'password';
  }
}

class NumberInput extends FormInput {
  public function __construct($label, $name, $required=true, $min = null, $max = null, $validators= [], $attrs = []) {
    global $min_validator;
    global $max_validator;

    $attrs = array_merge(['class' => '', 'id' => $name], $attrs);

    $this->label = $label;
    $this->name = $name;
    $this->required = $required;
    try {
      $this->min = (int)$min;
      $this->max = (int)$max;
    } catch(error) {
      throw new Error("`min` and `max` arguments has to be of type int");
    }
    $this->attrs = $attrs;
    $this->type = "number";
    $this->validators = $validators;

    array_push($this->validators, [$min_validator, "Please enter a number greater than $this->min"]);
    array_push($this->validators, [$max_validator, "Please enter a number less than $this->max"]);

    parent::__construct();
  }

  public function show() {
    echo "
      <label class='input-label' for='".$this->attrs["id"]."'>$this->label".($this->required ? '<span>*</span>': '')."</label>
      <input type='$this->type' id='".$this->attrs['id']."' name='$this->name'
        class='".$this->attrs['class']."' value='$this->value' ".($this->required ? 'required': '')." 
        min='$this->min' max='$this->max' />
      <div>".$this->error."</div>
    ";
  }

  public function _is_valid() {
    foreach($this->validators as $validator) {
      if(!$validator[0]($this->value, $this->min, $this->max)) {
        $this->error = $validator[1];
        return false;
      }
    }
    return true;
  }
}

class RadioInput extends FormInput {
  public function __construct($label, $name, $required=true, $options = [], $validators= [], $attrs = []) {
    global $radio_required_validator;

    $attrs = array_merge(['class' => '', 'id' => $name], $attrs);

    $this->label = $label;
    $this->name = $name;
    $this->required = $required;
    $this->attrs = $attrs;
    $this->type = "radio";
    $this->validators = $validators;
    $this->options = $options;

    array_push($this->validators, [$radio_required_validator, "This field is required"]);
  }

  public function show() {
    echo "<label class='input-label' for='".$this->attrs["id"]."'>$this->label".($this->required ? '<span>*</span>': '')."</label>";
    echo "<div class='".$this->attrs['class']."' >";
    foreach($this->options as $opt) {
      echo "<input type='$this->type' id='".$this->attrs['id']."' 
        name='$this->name' value='$opt' ".($this->required ? 'required': '')."/>
        <label>".ucfirst($opt)."</label>
      ";
    }
    echo "</div>";
    echo "<div>".$this->error."</div>";
  }

  public function _is_valid() {
    foreach($this->validators as $validator) {
      if(!$validator[0]($this->value, $this->options)) {
        $this->error = $validator[1];
        return false;
      }
    }
    return true;
  }
}

class CheckboxInput extends FormInput {
  public function __construct($label, $name, $required=true, $options = [], $validators= [], $attrs = []) {
    global $checkbox_required_validator;

    $attrs = array_merge(['class' => '', 'id' => $name], $attrs);

    $this->label = $label;
    $this->name = $name;
    $this->required = $required;
    $this->attrs = $attrs;
    $this->type = "checkbox";
    $this->validators = $validators;
    $this->options = $options;

    array_push($this->validators, [$checkbox_required_validator, "This field is required"]);
  }

  public function show() {
    echo "<label class='input-label' for='".$this->attrs["id"]."'>$this->label".($this->required ? '<span>*</span>': '')."</label>";
    echo "<div class='".$this->attrs['class']."' >";
    foreach($this->options as $opt) {
      echo "<input type='$this->type' id='".$this->attrs['id']."' 
        name='$this->name[]' value='$opt' />
        <label>".ucfirst($opt)."</label>
      ";
    }
    echo "</div>";
    echo "<div>".$this->error."</div>";
  }

  public function _is_valid() {
    if(!is_array($this->value)) {
      $this->error = "This field is required";
      return false;
    }
    foreach($this->validators as $validator) {
      if(!$validator[0]($this->value, $this->options)) {
        $this->error = $validator[1];
        return false;
      }
    }
    return true;
  }
}

class SelectInput extends FormInput {
  public function __construct($label, $name, $required=true, $options = [], $validators= [], $attrs = []) {
    global $select_required_validator;

    $attrs = array_merge(['class' => '', 'id' => $name], $attrs);

    $this->label = $label;
    $this->name = $name;
    $this->required = $required;
    $this->attrs = $attrs;
    $this->type = "checkbox";
    $this->validators = $validators;
    $this->options = $options;

    array_push($this->validators, [$select_required_validator, "This field is required"]);
  }

  public function show() {
    echo "<label class='input-label' for='".$this->attrs["id"]."'>$this->label".($this->required ? '<span>*</span>': '')."</label>";
    echo "<select name='$this->name' id='".$this->attrs['id']."' class='".$this->attrs['class']."'>";
    foreach($this->options as $opt) {
      echo "<option value='$opt'>".ucfirst($opt)."</option>";
    }
    echo "</select>";
    echo "<div>".$this->error."</div>";
  }

  public function _is_valid() {
    if(empty($this->value)) {
      $this->error = "This field is required";
      return false;
    }
    foreach($this->validators as $validator) {
      if(!$validator[0]($this->value, $this->options)) {
        $this->error = $validator[1];
        return false;
      }
    }
    return true;
  }
}

class DateInput extends FormInput {
  public function __construct($label, $name, $required=true, $validators= [], $attrs = []
  ) {
    $attrs = array_merge(['class' => '', 'id' => $name], $attrs);

    $this->label = $label;
    $this->name = $name;
    $this->required = $required;
    $this->attrs = $attrs;
    $this->type = "date";
    $this->validators = $validators;
  }

  public function show() {
    echo "
      <label class='input-label' for='".$this->attrs["id"]."'>$this->label".($this->required ? '<span>*</span>': '')."</label>
      <input type='$this->type' name='$this->name' id='".$this->attrs['id']."' 
        class='".$this->attrs['class']."' 
        value='$this->value' ".($this->required ? 'required': '')." />
      <div>".$this->error."</div>
    ";
  }

  public function _is_valid() {
    if(empty($this->value)) {
      $this->error = "This field is required";
      return false;
    }

    foreach($this->validators as $validator) {
      if(!$validator[0]($this->value)) {
        $this->error = $validator[1];
        return false;
      }
    }
    return true;
  }
}
