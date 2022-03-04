<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="index.css">
</head>
<body>
  <?php

    require __DIR__.'/'.'form.php';
    require __DIR__.'/'.'form-inputs.php';
    require __DIR__.'/'.'save-classes.php';


    // To create a form you have to do the following:
    // PS [there are 10 steps]
    // 1./ create a class that extends the `Form` class (defined in form.php file)  
    class StudentRegistrationForm extends Form {
      // 2./ create the __construct function (The class constructor) that accepts two arguments:
      //    1. $data: the source of the data ($_POST or $_GET)
      //    2. $save_class: an class that extends the SaveClass (defined in `save-classes.php`)
      //        This class only needs to implement one methods which is the save method that recieves one argument
      //        that is the validated data, to invoke the save method of the class, the form has a method `save` that calls
      //        the save method of the save_class.
      //    Or use ...$args so you don't have to specify each argument individualy
      function __construct($data, $save_class=null) {
        // 3./ Now you have to add all the form inputs as`public` attributes to the class
        //    example : let' we have a form that has a firstname filed we can add that field as the following (in the __construct)
        //      $this->first_name = new TextInput("Firstname", "firstname");
        //          the fisrst argument to the `TextInput` is the field label (used to display the label for the input <label></label>)
        //    PS: all the available fields are defined in the `form-inputs.php` file
        $this->nom = new TextInput("Nom", "nom", attrs: ["class" => "form-input"]);
        $this->prenom = new TextInput("Prenom", "prenom", attrs: ["class" => "form-input"]);
        $this->addresse = new TextAreaInput("Addresse", "addresse", attrs: ["class" => "form-input form-tarea-input"]);
        $this->ville = new TextInput("Ville", "ville", attrs: ["class" => "form-input"]);
        $this->email = new EmailInput("Email", "email", attrs: ["class" => "form-input"]);
        $this->descriptionDeProjet = new TextAreaInput("Description de projet", "descriptionDeProjet", 
          attrs: ["class" => "form-input form-tarea-input"]);
        $this->nomDeFichier = new TextInput("Nom de fichier", "nomDeFichier", attrs: ["class" => "form-input"]);

        // 4./ call the parent constructor and passed it the $data and $save_file
        parent::__construct($data, $save_class);
      }
    }

    // 5./ create an instance of the form and pass it the first argument ($_POST or $_GET), and the second argument
    //     is optional which a save_class
    $form = new StudentRegistrationForm($_POST, 
      save_class: new TextFileSaver(fn($data) => $data["nomDeFichier"], exclude: ["nomDeFichier"])
    );

    // 6./ the get_fields() method of the form returns an associative array that has all the fields of the form
    //     where the key is the `name` of the fields and the value is the field itself
    $form_fields = $form->get_fields();

    if(isset($_POST['submit'])) {
      // submit button clicked

      // 7./ the `is_valid()` method of the form returns either `true` or `false` to indicate whether the form is valid or not
      if(!$form->is_valid()) {
        // form is not valid
        echo "for not valid<br>";
      } else {
        // for is valid
        try {
          // 8./ The calls the `save` method of the save_class passed as argument to the form
          $form->save();
          // 9./ we call the `reset` method of the form to reset the inputs.
          $form->reset();
        } catch(error) {
          echo "did not save<br>";
        }
      }

    } else {
      // submit button not clicked [GET]
    }

  ?>

  <div class="form-container">
    <h1 class="form-label">Student Registration</h1>
    <div class="hr-line"></div>
    <form action="" method="post">
      <div class="form-row">
        <!-- Nom -->
        <div class="form-control">
          <!-- 10./ to display a field we access the field using its name and call show() method to return the html for the field -->
          <?= $form_fields["nom"]->show() ?>
        </div>
        <!-- Prenom -->
        <div class="form-control">
          <?= $form_fields["prenom"]->show() ?>
        </div>
      </div>
      <!-- Address -->
      <div class="form-row">
        <div class="form-control form-control-full-width">
          <?= $form_fields["addresse"]->show() ?>
        </div>
      </div>
      <div class="form-row">
        <!-- Ville -->
        <div class="form-control">
          <?= $form_fields["ville"]->show() ?>
        </div>
        <!-- Email -->
        <div class="form-control">
          <?= $form_fields["email"]->show() ?>
        </div>
      </div>
      <div class="form-row">
        <div class="form-control form-control-full-width">
          <?= $form_fields["descriptionDeProjet"]->show() ?>
        </div>
      </div>
      <div class="hr-line"></div>
      <div class="form-row">
        <!-- Nom -->
        <div class="form-control">
          <?= $form_fields["nomDeFichier"]->show() ?>
        </div>
      </div>
      <div class="form-row">
        <input type="submit" name="submit" value="Submit">
      </div>
    </form>
  </div>
</body>
</html>
