<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\DouyinCommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = '抖音评论';
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/modules/agent/douyin_comment-index.js',[\app\assets\AppAsset::className(), 'depends' => ['app\assets\AppAsset']]);
?>
<div class="douyin-comment-index">

    <p>
        <?php /*Html::a('Create Douyin Comment', ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute'=>'item_id',
//                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return '<a target="_blank" href="/agent/douyin-item/show?id='.$model->douyinItem->id.'"><img width="60" src="'.$model->douyinItem->cover.'"/></a>';
                }
            ],
//            'comment_id',
            [
                'attribute'=>'parent_id',
//                'filter'=>false,
                'format'=>'html',
                'value'=>function($model){
                    return $model->parent_id?$model->parentComment->content:'';
                }
            ],
///            'open_id',
            [
                'attribute'=>'content',
//                'filter'=>false,
                'format'=>'html',
                'value'=>function($model){
                    return $model->showContent();
                }
            ],
            [
                'label'=>'回复',
//                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return Html::button('回复',['class'=>'btn btn-success','onclick'=>'reply('.$model->id.')']);
                }
            ],
            'is_top',
            'digg_count',
            'reply_count',
            'create_time:datetime',
            //'post_time:datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<!-- 模态框（Modal） -->
<div class="modal fade" id="comment_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">评论</h4>
            </div>
            <div class="modal-body" id="commennt_modal_content">在这里添加一些文本</div>
            <div class="modal-body">
                <textarea id="commennt_modal_reply" rows="10" cols="70"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="commennt_modal_reply_button">提交</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
<script>
    function reply(comment_id){
        $('#comment_modal').on('shown.bs.modal', function () {
            // 执行一些动作...
            $.get('/agent/ajax/get-comment',{comment_id:comment_id},function (res) {
                res = checkresult(res);
                console.log(res);
                $('#commennt_modal_content').text(res.content);
            });
        });
        $('#commennt_modal_reply_button').click(function () {
            $('#comment_modal').modal('hide');
            let content =$('#commennt_modal_reply').val();
            // return;
            $.post('/agent/ajax/reply-comment',{comment_id:comment_id,content:content},function (res) {
                console.log(res);
                res = checkresult(res);
                console.log(res);
            });

        });
        $('#comment_modal').on('hide.bs.modal', function () {
            $(this).unbind();
        });

        $('#comment_modal').modal();
    }
</script>