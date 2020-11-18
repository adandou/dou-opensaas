<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\AuthAccountSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'uid') ?>

    <?= $form->field($model, 'auth_app_id') ?>

    <?= $form->field($model, 'app_uid') ?>

    <?= $form->field($model, 'open_id') ?>

    <?php // echo $form->field($model, 'nickname') ?>

    <?php // echo $form->field($model, 'avatar') ?>

    <?php // echo $form->field($model, 'token') ?>

    <?php // echo $form->field($model, 'token_expire_time') ?>

    <?php // echo $form->field($model, 'refresh_token') ?>

    <?php // echo $form->field($model, 'refresh_token_expire_time') ?>

    <?php // echo $form->field($model, 'refresh_token_get_nums') ?>

    <?php // echo $form->field($model, 'all_expire_time') ?>

    <?php // echo $form->field($model, 'ctime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
