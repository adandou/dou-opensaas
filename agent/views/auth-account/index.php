<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\AuthAccountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '授权账号';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-account-index">

    <p>
        <?= Html::a('添加授权账号', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
//            'uid',
            [
                'attribute'=>'auth_app_id',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return Html::img($model->authApp->logo);
                }
            ],
//            'app_uid',
//            'open_id',
            'nickname',
            [
                'attribute'=> 'avatar',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return Html::img($model->avatar,['width'=>60]);
                }
            ],
            //'token',
            //'token_expire_time:datetime',
            //'refresh_token',
            //'refresh_token_expire_time:datetime',
            //'refresh_token_get_nums',
            'all_expire_time:datetime',
            //'ctime:datetime',
            [

                'label'=>'授权',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return Html::a('重新授权', ['/agent/auth-app/create','id'=>$model->auth_app_id], ['class' => 'btn btn-success']);
                }
            ],
            [

                'label'=>'同步',
                'attribute'=>'access_expire',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return Html::a('拉取我的所有发布', '#here', ['class' => 'btn btn-success','onclick'=>'syncMyItem('.$model->id.')'])
                        .'<br/><br/>'.Html::a('拉取关注/粉丝', '#here', ['class' => 'btn btn-success','onclick'=>'syncFollowFans('.$model->id.')'])
                        ;
                }
            ],

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
<script>
    function syncMyItem(aid){
        if(confirm('后台运行，可能需要几分钟时间')){
            $.post('/agent/ajax/sync-items',{aid:aid},function (req) {
                checkresult(req);
            });
        }
    }
    function syncFollowFans(aid){
        if(confirm('后台运行，可能需要几分钟时间')){
            $.post('/agent/ajax/sync-follow-fans',{aid:aid},function (req) {
                checkresult(req);
            });
        }
    }
</script>