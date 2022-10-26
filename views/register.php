<?php

use app\core\form\Form;

$this->title = 'Signup';
?>

<h1>Create new account</h1>

<?php $form=Form::begin('', "post") ?>
    <div class="row">
        <div class="col">
            <?= $form->field($model, 'firstname') ?>
        </div>
        <div class="col">
            <?= $form->field($model, 'lastname') ?>
        </div>
    </div>
    <?= $form->field($model, 'email') ?>
    <?= $form->field($model, 'password')->passwordField() ?>
    <?= $form->field($model, 'passwordConfirm')->passwordField() ?>
    <button type="submit" class="btn btn-primary">Register</button>
<?php Form::end() ?>
