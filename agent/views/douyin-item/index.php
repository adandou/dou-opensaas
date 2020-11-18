<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DouyinItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '抖音发布';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="douyin-item-index">
    <p>
        <?= Html::a('发布', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
                'id',
            [
                'attribute'=>'douyin_account_id',
                'filter'=>false,
//                'format'=>'raw',
                'value'=>function($model){
                    return $model->douyinAccount->nickname;
                }
            ],
            [
                'attribute'=>'账号头像',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return '<img width="60" src="'.$model->douyinAccount->avatar.'"/>';
                }
            ],
//            'type',
            'title',
            [
                'attribute'=>'cover',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return '<a target="_blank" href="/agent/douyin-item/show?id='.$model->id.'"><img width="60" src="'.$model->cover.'"/></a>';
                }
            ],
            //'is_top',
            [

                'attribute'=>'comment_count',
//                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return Html::a($model->comment_count, ['douyin-comment/index','DouyinCommentSearch[item_id]'=>$model->id]);
                }
            ],
            'digg_count',
            'download_count',
            'play_count',
            'share_count',
            'forward_count',
            //'item_data:ntext',
            //'item_state',
            [

                'attribute'=>'create_time',
                'filter'=>false,
//                'format'=>'raw',
                'value'=>function($model){
                    return date('Y-m-d H:i:s',$model->create_time);
                }
            ],
            'create_time:datetime:ymdH',
            'post_time:date:ymdhis',
            [
                'label'=>'操作',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return Html::a('更新详情', '#here', ['class' => 'btn btn-success','onclick'=>'syncItem('.$model->id.')']).
                        '<br/>'.Html::a('更新评论', '#here', ['class' => 'btn btn-success','onclick'=>'syncComment('.$model->id.')']).
                        '<br/>'.Html::a('删除', '#here', ['class' => 'btn btn-danger','onclick'=>'del('.$model->id.')']);
                }
            ],
        ],
    ]); ?>


</div>
<script>
    function syncItem(id){
        if(confirm('确定要实时更新此视频信息吗')){
            $.post('/agent/ajax/sync-item',{id:id},function (req) {
                checkresult(req);
            });
        }
    }
    function syncComment(id){
        if(confirm('后台运行，可能需要几分钟时间')){
            $.post('/agent/ajax/sync-comment',{id:id},function (req) {
                checkresult(req);
            });
        }
    }
    function del(id){
        alert('暂时未开放此权限');
        return;
        if(confirm('确定删除吗？删除后不可恢复')){
            $.post('/agent/ajax/del-item',{id:id},function (req) {
                checkresult(req);
            });
        }
    }
</script>