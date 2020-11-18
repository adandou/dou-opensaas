<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AuthItem */

$this->title = '发布';
$this->params['breadcrumbs'][] = ['label' => '发布管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="douyin-item-create">
    <table class="table table-striped table-bordered">
        <tbody>
        <tr>
            <td><?=$video->title?></td>
            <td><?=$video->showSize()?></td>
            <td><?=$video->showDuration()?></td>
        </tr>
        </tbody>
    </table>
</div>

<div class="douyin-account-index">
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label'=>'选择',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model)use($video){
                    return Html::checkbox('account_ids',false,['value'=>$model->id]);
                }
            ],
//            [
//                'label'=>'发布账号ID',
//                'attribute'=>'id',
//            ],
            [
                'label'=>'发布账号头像',
                'attribute'=>'avatar',
                'options'=>['wdith'=>'100px'],
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return $model->authUser->showAvatar();
                    return '<img src="'.$model->avatar.'"/>';
                }
            ],
//            'douyin_app_id',
            [
                'label'=>'发布账号昵称',
                'attribute'=>'nickname',
            ],
            //'avatar',
            //'douyin_uid',
            //'access_token',
            [
                'label'=>'发布标题',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model)use($video){
                    return Html::textarea('titles',$video->title,['rows'=>5,'cols'=>30]).Html::hiddenInput('auth_app_ids',$model->auth_app_id);
                }
            ],
            [
                'label'=>'发布时间',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return Html::radioList('timings['.$model->id.']',2,\app\models\AuthItem::$timing_arr).Html::input('datetime-local','post_times');
                }
            ],
        ],
    ]); ?>


</div>
<div class="form-group">
    <?= Html::submitButton('提交', ['onclick'=>'publish()','class' => 'btn btn-success']) ?>
</div>
<script>
    let video_id = <?=$video->id?>;
    function publish() {
        let data_arr = new Array();
        $('input[name="account_ids"]:checked').each(function () {
            let auth_account_id = $(this).val();
            let auth_app_id = $(this).parent().parent().find('*[name="auth_app_ids"]').first().val();
            let title = $(this).parent().parent().find('*[name="titles"]').first().val();
            let timing = $(this).parent().parent().find('*[name^="timings"]:checked').val();
            let post_time = $(this).parent().parent().find('*[name="post_times"]').first().val();
            console.log(auth_account_id);
            data_arr.push({video_id:video_id,auth_account_id:auth_account_id,auth_app_id:auth_app_id,title:title,timing:timing,post_time:post_time});
        });
        if(data_arr.length <1){
            alert('请选择');
            return ;
        }
        // console.log(data_arr);
        // return ;
        if(confirm("后台运行,需要几分钟时间,视频越大需要的时间越长")){
            $.ajax({
                contentType: 'application/json',
                type: 'POST',
                url:'/agent/ajax/publish',
                data:JSON.stringify(data_arr),
                success:function (res) {
                    console.log(res)
                    window.location.href = '/agent/auth-item';
                }
            });
        }
    }
</script>
