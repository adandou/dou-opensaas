<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AuthItem */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'auth_app_id')->textInput() ?>

    <?= $form->field($model, 'auth_account_id')->textInput() ?>

    <?= $form->field($model, 'video_id')->textInput() ?>

    <?= $form->field($model, 'item_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cover')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_top')->textInput() ?>

    <?= $form->field($model, 'comment_count')->textInput() ?>

    <?= $form->field($model, 'digg_count')->textInput() ?>

    <?= $form->field($model, 'download_count')->textInput() ?>

    <?= $form->field($model, 'play_count')->textInput() ?>

    <?= $form->field($model, 'share_count')->textInput() ?>

    <?= $form->field($model, 'forward_count')->textInput() ?>

    <?= $form->field($model, 'timing')->textInput() ?>

    <?= $form->field($model, 'post_time')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
