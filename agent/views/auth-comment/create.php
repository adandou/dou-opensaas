<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AuthComment */

$this->title = 'Create Auth Comment';
$this->params['breadcrumbs'][] = ['label' => 'Auth Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-comment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
