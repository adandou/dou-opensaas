<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AuthApp */

$this->title = 'Update Auth App: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Auth Apps', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="auth-app-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
