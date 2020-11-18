<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAccount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-account-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'uid')->textInput() ?>

    <?= $form->field($model, 'auth_app_id')->textInput() ?>

    <?= $form->field($model, 'app_uid')->textInput() ?>

    <?= $form->field($model, 'open_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'avatar')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token_expire_time')->textInput() ?>

    <?= $form->field($model, 'refresh_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'refresh_token_expire_time')->textInput() ?>

    <?= $form->field($model, 'refresh_token_get_nums')->textInput() ?>

    <?= $form->field($model, 'all_expire_time')->textInput() ?>

    <?= $form->field($model, 'ctime')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
