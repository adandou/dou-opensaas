<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\DouyinItemSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="douyin-item-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'item_id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'cover') ?>

    <?php // echo $form->field($model, 'is_top') ?>

    <?php // echo $form->field($model, 'comment_count') ?>

    <?php // echo $form->field($model, 'digg_count') ?>

    <?php // echo $form->field($model, 'download_count') ?>

    <?php // echo $form->field($model, 'play_count') ?>

    <?php // echo $form->field($model, 'share_count') ?>

    <?php // echo $form->field($model, 'forward_count') ?>

    <?php // echo $form->field($model, 'item_data') ?>

    <?php // echo $form->field($model, 'item_state') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'post_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
