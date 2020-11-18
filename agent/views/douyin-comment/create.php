<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DouyinComment */

$this->title = 'Create Douyin Comment';
$this->params['breadcrumbs'][] = ['label' => 'Douyin Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="douyin-comment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
