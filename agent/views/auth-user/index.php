<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\AuthUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '应用用户';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-user-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
//            'auth_app_id',
//            'auth_account_id',
//            'open_id',
//            'union_id',
            [
                'attribute'=> 'avatar',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return $model->showAvatar();
                }
            ],
            'nickname',
            //'gender',
            'city',
            'province',
            'country',
            //'role',
            //'utime:datetime',
        ],
    ]); ?>


</div>
