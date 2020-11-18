<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AuthComment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-comment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'auth_app_id')->textInput() ?>

    <?= $form->field($model, 'auth_account_id')->textInput() ?>

    <?= $form->field($model, 'auth_item_id')->textInput() ?>

    <?= $form->field($model, 'comment_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'parent_id')->textInput() ?>

    <?= $form->field($model, 'open_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'is_top')->textInput() ?>

    <?= $form->field($model, 'digg_count')->textInput() ?>

    <?= $form->field($model, 'reply_count')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'post_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
