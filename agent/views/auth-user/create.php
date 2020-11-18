<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AuthUser */

$this->title = 'Create Auth User';
$this->params['breadcrumbs'][] = ['label' => 'Auth Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
