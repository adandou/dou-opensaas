<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DouyinAccount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="douyin-account-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'douyin_app_id')->textInput() ?>

    <?= $form->field($model, 'open_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'union_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'avatar')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'douyin_uid')->textInput() ?>

    <?= $form->field($model, 'access_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'access_expire')->textInput() ?>

    <?= $form->field($model, 'refresh_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'refresh_expire')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
