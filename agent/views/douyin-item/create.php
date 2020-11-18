<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DouyinItem */

$this->title = '发布';
$this->params['breadcrumbs'][] = ['label' => '发布管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('//gosspublic.alicdn.com/aliyun-oss-sdk-6.8.0.min.js',['position'=>\yii\web\View::POS_BEGIN]);

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
                    return Html::checkbox('account_ids',false,['onchange'=>'choose(this,'.$video->id.','.$model->id.')']);
                }
            ],
            [
                'label'=>'发布账号ID',
                'attribute'=>'id',
            ],
            [
                'label'=>'发布账号头像',
                'attribute'=>'avatar',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
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
                    return Html::textarea('title',$video->title,['rows'=>5,'cols'=>30]);
                }
            ],
        ],
    ]); ?>


</div>
<div class="form-group">
    <?= Html::submitButton('发布', ['onclick'=>'publish()','class' => 'btn btn-success']) ?>
</div>
<script>
    let account_arr = new Array();
    function choose(obj,video_id,account_id) {
        if(obj.checked){
            let title = $(obj).parent().parent().find('*[name="title"]').first();
            account_arr[account_id] = {"video_id":video_id,"account_id":account_id,"title":title.val()};
        }else{
            delete(account_arr[account_id]);
        }
        console.log(account_arr);
        //account_arr[account_id] =
    }
    function publish() {
        if(confirm("后台运行,需要几分钟时间")){
            $.ajax({
                contentType: 'application/json',
                type: 'POST',
                url:'/agent/ajax/publish',
                data:JSON.stringify(account_arr),
                success:function (res) {
                    console.log(res)
                }
            });

        }
    }
</script>