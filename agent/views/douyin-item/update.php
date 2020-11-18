<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DouyinItem */

$this->title = 'Update Douyin Item: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Douyin Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="douyin-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
