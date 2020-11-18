<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\VideoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '帮助&客服';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="video-index">
    <br/>
    <br/>
    <h3>
        QQ交流群:608677746
    </h3>
    <br/>
    <br/>
    <h3>
        客服微信:moguidaxing
    </h3>
</div>
<script>
    function publish(video_id) {
        window.location = '/agent/auth-item/create?video_id='+video_id;
    }
</script>