<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\DouyinItem */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Douyin Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="douyin-item-view">

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
            'type',
            'item_id',
            'title',
            'cover',
            'is_top',
            'comment_count',
            'digg_count',
            'download_count',
            'play_count',
            'share_count',
            'forward_count',
            'item_data:ntext',
            'item_state',
            'create_time:datetime',
            'post_time:datetime',
        ],
    ]) ?>

</div>
