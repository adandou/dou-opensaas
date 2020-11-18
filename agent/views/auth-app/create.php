<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AuthApp */

$this->title = 'Create Auth App';
$this->params['breadcrumbs'][] = ['label' => 'Auth Apps', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-app-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
