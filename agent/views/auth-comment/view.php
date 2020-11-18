<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AuthComment */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Auth Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auth-comment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'auth_app_id',
            'auth_account_id',
            'auth_item_id',
            'comment_id',
            'parent_id',
            'open_id',
            'content:ntext',
            'is_top',
            'digg_count',
            'reply_count',
            'create_time:datetime',
            'post_time:datetime',
        ],
    ]) ?>

</div>
