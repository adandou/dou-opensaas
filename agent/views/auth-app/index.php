<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\AuthAppSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '平台应用';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-app-index">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => false,
        'columns' => [
            'title',
            'logo:image',
            [
                'attribute'=>'state',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    if($model->state==1){
                        $btn = Html::a('去授权', ['create','id'=>$model->id], ['class' => 'btn btn-success']);
                    }
                    if($model->state==2){
                        $btn = Html::a('即将上线', ['#here'], ['class' => 'btn btn-warning']);
                    }
                    if($model->state==3){
                        $btn = Html::a('开发中', ['#here'], ['class' => 'btn btn-warning']);
                        $btn = Html::a('开发中', ['#here'], ['class' => 'btn btn-info']);
//                        $btn = Html::a('开发中', ['#here'], ['class' => 'btn btn-default']);
                    }
                    return $btn;
                }
            ],
        ],
    ]); ?>


</div>
