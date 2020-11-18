<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

$this->title = '比赛数据录入';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page">
    <div class="weui-form">
<!--        <div class="weui-form__text-area">-->
<!--            <h2 class="weui-form__title">原生选择框</h2>-->
<!--        </div>-->
        <div class="weui-form__control-area">
            <div class="weui-cells__group weui-cells__group_form">
                <div class="weui-cells">
                    <div class="weui-cell weui-cell_select weui-cell_select-after">
                        <div class="weui-cell__hd">
                            <label for="" class="weui-label">球队</label>
                        </div>
                        <div class="weui-cell__bd">
                            <select class="weui-select" id="teams" onchange="chooseTeam(this)">
                                <option value="0">选择球队</option>

                                <?php foreach($teams as $team_id => $val){ ?>

                                <option value="<?=$team_id?>"><?=$val?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="weui-cell weui-cell_select weui-cell_select-after">
                        <div class="weui-cell__hd">
                            <label for="" class="weui-label">球员</label>
                        </div>
                        <div class="weui-cell__bd">
                            <select class="weui-select" id="players">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="2">3</option>
                            </select>
                        </div>
                    </div>
                    <div class="weui-cell weui-cell_select weui-cell_select-after">
                        <div class="weui-cell__hd">
                            <label for="" class="weui-label">事件</label>
                        </div>
                        <div class="weui-cell__bd">
                            <select class="weui-select" id="types">
                                <?php foreach($types as $type_id => $val){ ?>

                                    <option value="<?=$type_id?>"><?=$val?></option>
                                <?php }?>

                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="weui-form__opr-area">
            <a class="weui-btn weui-btn_primary" href="javascript:save()" id="save-btn">确定</a>
        </div>
    </div>
    <div class="page__bd">
        <div class="weui-cells__title">事件列表</div>
        <div class="weui-cells">
            <div class="weui-cell weui-cell_swiped">
                <div class="weui-cell__bd" id="match_datas" >
                    <div v-for=" data in datas" class="weui-cell">
                        <div class="weui-cell__bd">
                            <p>{{data.team.title}}</p>
                        </div>
                        <div class="weui-cell__bd">
                            <p>{{data.user.realname}}({{data.player.number}})</p>
                        </div>
                        <div class="weui-cell__bd">
                            <p>{{data.type}}</p>
                        </div>
                        <div class="weui-cell__ft" data-id="{{data.id}}" @click="del(data)">
                            <a class="weui-swiped-btn weui-swiped-btn_warn" href="javascript:">删除</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div id="toast" style="display: none;">
        <div class="weui-mask_transparent"></div>
        <div class="weui-toast">
            <i class="weui-icon-success-no-circle weui-icon_toast"></i>
            <p class="weui-toast__content">已完成</p>
        </div>
    </div>

<script type="text/javascript" class="searchbar js_show">
    var game_id=<?=intval($game_id)?>;
    var match_id=<?=intval($match_id)?>;
    wx.ready(function(){
        var $toast = $('#toast');
        if ($toast.css('display') != 'none') return;

        $toast.fadeIn(100);
        setTimeout(function () {
            $toast.fadeOut(100);
        }, 2000);
        $('#showToast').on('click', function(){
            if ($toast.css('display') != 'none') return;

            $toast.fadeIn(100);
            setTimeout(function () {
                $toast.fadeOut(100);
            }, 2000);
        });

    });
    var matchdatas = new Vue({
        el: '#match_datas',
        data: {
            datas: []
        },
        created:function(){
            this.loadData();
        },
        methods:{
            loadData:function () {
                var that = this;
                $.post("<?=yii::$app->urlManager->createUrl('api/schedule/match-datas')?>",{match_id:match_id},function (req) {
                    var req = checkresult(req);
                    that.datas = req;
                });
            },
            del:function (event) {
                if(confirm('确定删除吗')){
                    $.post("<?=yii::$app->urlManager->createUrl('api/game/match-data-del')?>",
                        {match_data_id:event.id,match_id:event.match_id,game_id:event.game_id,team_id:event.team_id},function (req) {
                            var req = checkresult(req);
                            if(!req) return false;
                            showTtoast();
                            matchdatas.loadData();
                        });
                }

            }
        }
    })
    function chooseTeam(obj) {
        $.post("<?=yii::$app->urlManager->createUrl('api/game/team-player')?>",{game_id:game_id,team_id:obj.value},function (req) {
            $("#players").empty();
            var req = checkresult(req);
            $.each(req.rows,function (i,n) {
                $("#players").append('<option value='+n.id+'>'+n.user.realname+'('+n.number+')'+'</option>');
            })

        });

    }
    function save() {
        var team_id = $("#teams").val();
        var player_id = $("#players").val();
        var type_id = $("#types").val();
        $.post("<?=yii::$app->urlManager->createUrl('api/game/match-data-save')?>",
            {match_id:match_id,team_id:team_id,player_id:player_id,type_id:type_id},function (req) {
            var req = checkresult(req);
            if(!req) return false;
            showTtoast();
            matchdatas.loadData();
        });

    }
    function showTtoast() {
        var $toast = $('#toast');
        if ($toast.css('display') != 'none') return;
        $toast.fadeIn(100);
        setTimeout(function () {
            $toast.fadeOut(100);
        }, 2000);
    }
</script>
