<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

$this->title = '我的';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page">
    <div class="page__bd">
        <div class="weui-cells">
            <a class="weui-cell weui-cell_access" href="javascript:toGame();">
                <div class="weui-cell__bd">
                    <p>我的比赛</p>
                </div>
                <div class="weui-cell__ft">
                </div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript:toTeam();">
                <div class="weui-cell__bd">
                    <p>我的球队</p>
                </div>
                <div class="weui-cell__ft">
                </div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript:toInfo();">
                <div class="weui-cell__bd">
                    <p>个人资料</p>
                </div>
                <div class="weui-cell__ft">
                </div>
            </a>
            <a class="weui-cell weui-cell_access" href="javascript:;">
                <div class="weui-cell__bd">
                    <p>客服：13641234474（微信）</p>
                </div>
                <div class="weui-cell__ft">
                </div>
            </a>
        </div>
    </div>
</div>

<script type="text/javascript" class="searchbar js_show">
    wx.ready(function(){
    });
    function toGame(){
        wx.miniProgram.navigateTo({url: '/pages/my/game/index'});
    }
    function toTeam(){
        wx.miniProgram.navigateTo({url: '/pages/my/team/index'});
    }
    function toInfo(){
        wx.miniProgram.navigateTo({url: '/pages/my/data/user'});
    }
</script>
