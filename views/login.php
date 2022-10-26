<?php
$this->title = 'Login';
?>

<h1>Sign in</h1>

<div class="container">
    <?php $form=\app\core\form\Form::begin('', "post") ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'password')->passwordField() ?>
        <button type="submit" class="btn btn-primary">Sign in</button>
    <?php \app\core\form\Form::end() ?>
</div>
