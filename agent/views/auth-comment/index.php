<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\AuthCommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '评论管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .editbox{width:200px;height:100px;border:1px solid #000; overflow-x:hidden; overflow-y:auto; outline:none;}
    .editbox img{ margin:0 3px; display:inline;}
    .emojibox{
        width:250px;
        height:125px;
        overflow: hidden;
    }
    .emojibox img{
        height:25px;
    }
    .editbox img{
        height:25px;
    }
</style>
<div class="auth-comment-index">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'id',
                'options' => ['width' => '80px'],
            ],
            [
                'attribute'=>'auth_item_id',
                'options' => ['width' => '10px'],
//                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return '<a target="_blank" href="/agent/auth-item/show?id='.$model->auth_item_id.'">'.$model->authItem->showCover().'</a>';
                }
            ],
            [
                'attribute'=>'auth_uid',
//                'options' => ['width' => '10px'],
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return $model->authUser?$model->authUser->showAvatar():'';
                }
            ],
            //'parent_id',
            //'open_id',
            [
                'attribute'=>'content',
                'options' => ['class' => ''],
//                'filter'=>false,
                'format'=>'html',
                'value'=>function($model){
                    return $model->showContent();
                }
            ],
            [
                'label'=>'回复评论',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return '<div contenteditable="true" class="editbox"></div><div>'.
                        Html::button('回复',['class'=>'btn btn-success','onclick'=>'reply(this,'.$model->id.')'])
                        .'</div>';
//                    return Html::textarea('titles','',['rows'=>5,'cols'=>30]).Html::hiddenInput('auth_app_ids',$model->auth_app_id);
                }
            ],
            [
                'label'=>'表情',
//                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model) use ($emojis){
                    return '<div class="emojibox">'.$emojis.'</div>';
                }
            ],
//            [
//                'label'=>'回复',
////                'filter'=>false,
//                'format'=>'raw',
//                'value'=>function($model){
//                    return Html::button('回复',['class'=>'btn btn-success','onclick'=>'reply('.$model->id.')']);
//                }
//            ],
            'is_top',
            'digg_count',
            'reply_count',
            //'create_time:datetime',
            'post_time:datetime',

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
<script>
    // $(".emojibox img").click(function(){
    //
    //     let editObj = $(this).parent().parent().find(".editbox");
    //     let img_url = "<img src='"+this.src+"'/>";
    //     /*此处如果不是插入图片可这样：
    //     var img_url = "插入测试的文字";
    //     */
    //     console.log(editObj[0]);
    //     return ;
    //     editObj[0].focus();
    //     _insertimg(img_url);
    //
    // });
    function reply(obj,comment_id) {
        let content =$(obj).parent().parent().find('.editbox').html();
        console.log(content);
        $(obj).attr('disabled',true);
        $.post('/agent/ajax/comment-reply',{comment_id:comment_id,content:content},function (res) {
            console.log(res);
            res = checkresult(res);
            $(obj).removeAttr('disabled');
            $(obj).html('再次回复');
            $(obj).removeClass('btn-success').addClass('btn-warning');
            console.log(res);
        });

    }
    function chooseEmoji(obj) {
        let editObj = $(obj.parentNode.parentNode.parentNode).find('.editbox')[0];
        console.log(editObj);
        // return ;
        // let editObj = $(this).parent().parent().parent().find(".editbox");
        let img_url = "<img src='"+obj.src+"'/>";
        /*此处如果不是插入图片可这样：
        var img_url = "插入测试的文字";
        */
        editObj.focus();
        _insertimg(img_url);

    }

    //锁定编辑器中鼠标光标位置。。
    function _insertimg(str){
        // $(".editbox").focus();
        var selection= window.getSelection ? window.getSelection() : document.selection;

        var range= selection.createRange ? selection.createRange() : selection.getRangeAt(0);

        if (!window.getSelection){


            var selection= window.getSelection ? window.getSelection() : document.selection;

            var range= selection.createRange ? selection.createRange() : selection.getRangeAt(0);

            range.pasteHTML(str);

            range.collapse(false);

            range.select();

        }else{


            range.collapse(false);

            var hasR = range.createContextualFragment(str);

            var hasR_lastChild = hasR.lastChild;

            while (hasR_lastChild && hasR_lastChild.nodeName.toLowerCase() == "br" && hasR_lastChild.previousSibling && hasR_lastChild.previousSibling.nodeName.toLowerCase() == "br") {

                var e = hasR_lastChild;

                hasR_lastChild = hasR_lastChild.previousSibling;

                hasR.removeChild(e)

            }

            range.insertNode(hasR);

            if (hasR_lastChild) {

                range.setEndAfter(hasR_lastChild);

                range.setStartAfter(hasR_lastChild)

            }

            selection.removeAllRanges();

            selection.addRange(range)

        }

    }

</script>