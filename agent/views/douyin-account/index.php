<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DouyinAccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '抖音授权账号';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="douyin-account-index">
    <p>
        <?= Html::a('添加抖音授权账号', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute'=>'avatar',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return '<img src="'.$model->avatar.'"/>';
                }
            ],
//            'douyin_app_id',
            'nickname',
            //'avatar',
            //'douyin_uid',
            //'access_token',
            [

                'label'=>'授权过期',
                'attribute'=>'access_expire',
                'filter'=>false,
//                'format'=>'raw',
                'value'=>function($model){
                    return date('Y-m-d H:i:s',$model->access_expire);
                }
            ],
            [

                'label'=>'授权',
                'attribute'=>'access_expire',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return Html::a('重新授权', ['create'], ['class' => 'btn btn-success']);
                }
            ],
            [

                'label'=>'同步',
                'attribute'=>'access_expire',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return Html::a('拉取我的所有发布', '#here', ['class' => 'btn btn-success','onclick'=>'syncMyItem('.$model->id.')']);
                }
            ],
            //'access_expire',
            //'refresh_token',
            //'refresh_expire',

        ],
    ]); ?>


</div>
<script>
    function syncMyItem(aid){
        if(confirm('后台运行，可能需要几分钟时间')){
            $.post('/agent/ajax/sync-user-item',{aid:aid},function (req) {
                checkresult(req);
            });
        }
    }
</script>