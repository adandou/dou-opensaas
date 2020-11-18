<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AuthAccount */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Auth Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="auth-account-view">

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
            'uid',
            'auth_app_id',
            'app_uid',
            'open_id',
            'nickname',
            'avatar',
            'token',
            'token_expire_time:datetime',
            'refresh_token',
            'refresh_token_expire_time:datetime',
            'refresh_token_get_nums',
            'all_expire_time:datetime',
            'ctime:datetime',
        ],
    ]) ?>

</div>
