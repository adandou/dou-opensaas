<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\AuthItemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '发布管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-item-index">
    <p>
        <?= Html::a('发布', ['/agent/video'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute'=>'auth_account_id',
                'filter'=>false,
//                'format'=>'raw',
                'value'=>function($model){
                    return $model->authAccount->nickname;
                }
            ],
            [
                'attribute'=>'账号头像',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return '<img width="60" src="'.$model->authAccount->avatar.'"/>';
                }
            ],
//            'type',
            'title',
            [
                'attribute'=>'cover',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return '<a target="_blank" href="/agent/auth-item/show?id='.$model->id.'">'.$model->showCover().'</a>';
                }
            ],
            //'is_top',
            [

                'attribute'=>'comment_count',
//                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
//                    return Html::a($model->comment_count, ['auth-comment/index','AuthCommentSearch[item_id]'=>$model->id]);
                    return $model->comment_count;
                }
            ],
            'digg_count',
            'play_count',
            [
                'label'=>'分享/转发/下载',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return $model->share_count.'/'.$model->forward_count.'/'.$model->download_count;
                }
            ],

            //'item_data:ntext',
            //'item_state',
            [

                'attribute'=>'create_time',
                'filter'=>false,
//                'format'=>'raw',
                'value'=>function($model){
                    return date('Y-m-d H:i',$model->create_time);
                }
            ],
            [
                'attribute'=>'timing',
                'filter'=>false,
//                'format'=>'raw',
                'value'=>function($model){
                    return $model->showTiming();
                }
            ],
            [
                'attribute'=>'post_time',
                'filter'=>false,
//                'format'=>'raw',
                'value'=>function($model){
                    return date('Y-m-d H:i',$model->post_time);
                }
            ],
            [
                'attribute'=>'state',
                'filter'=>false,
//                'format'=>'raw',
                'value'=>function($model){
                    return $model->showState();
                }
            ],
            [
                'label'=>'操作',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    $str = Html::a('更新详情', '#here', ['class' => 'btn btn-success','onclick'=>'syncItem('.$model->id.')']);
                    $str .= '<br/>'.Html::a('更新评论', '#here', ['class' => 'btn btn-success','onclick'=>'syncComment('.$model->id.')']);
//                    $str .= '<br/>'.Html::a('删除', '#here', ['class' => 'btn btn-danger','onclick'=>'del('.$model->id.')']);
                    return $str;
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