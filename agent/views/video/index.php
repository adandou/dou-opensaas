<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\VideoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '视频管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="video-index">

    <p>
        <?= Html::a('上传视频', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [

                'label'=>'发布',
                'filter'=>false,
                'format'=>'raw',
                'value'=>function($model){
                    return Html::button('发布',['onclick'=>'publish('.$model->id.')','class'=>'btn btn-primary']);
                }
            ],
//            'store',
            'title',
            'cover',
//            'path',
            [

                'attribute'=>'size',
//                'filter'=>false,
//                'format'=>'raw',
                'value'=>function($model){
                    return $model->showSize();
                }
            ],
            //'width',
            //'height',
            [

                'attribute'=>'duration',
//                'filter'=>false,
//                'format'=>'raw',
                'value'=>function($model){
                    return $model->showDuration();
                }
            ],
            //'extension',
            'ctime:datetime',

            ['class' => 'yii\grid\ActionColumn','template'=>'{delete}'],
        ],
    ]); ?>


</div>
<script>
    function publish(video_id) {
        window.location = '/agent/auth-item/create?video_id='+video_id;
    }
</script>