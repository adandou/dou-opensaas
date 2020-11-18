<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\DouyinAccountSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="douyin-account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'douyin_app_id') ?>

    <?= $form->field($model, 'open_id') ?>

    <?= $form->field($model, 'union_id') ?>

    <?= $form->field($model, 'nickname') ?>

    <?php // echo $form->field($model, 'avatar') ?>

    <?php // echo $form->field($model, 'douyin_uid') ?>

    <?php // echo $form->field($model, 'access_token') ?>

    <?php // echo $form->field($model, 'access_expire') ?>

    <?php // echo $form->field($model, 'refresh_token') ?>

    <?php // echo $form->field($model, 'refresh_expire') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
