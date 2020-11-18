<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Video */

$this->title = '上传视频';
$this->params['breadcrumbs'][] = ['label' => '视频管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('//gosspublic.alicdn.com/aliyun-oss-sdk-6.8.0.min.js',['position'=>\yii\web\View::POS_BEGIN]);
?>
<div class="video-create">

    <div class="form-group">
        <label>选择视频</label>
        <input type="file" id="file" />
    </div>
    <div class="form-group">
        <label>标题</label>
        <input type="text" class="form-control" id="file-title" value="" />
    </div>
    <div class="form-group">
        <input type="button" class="btn btn-primary" id="file-button" value="上传" />
    </div>
    <div class="form-group">
        <div class="progress">
            <div id="progress-bar"
                 class="progress-bar"
                 role="progressbar"
                 aria-valuenow="0"
                 aria-valuemin="0"
                 aria-valuemax="100" style="min-width: 2em;">
                0%
            </div>
        </div>
    </div>
</div>
<script>
    const appServer = '/agent/ajax/ali-sts';
    const bucket = 'ttmei-videos';
    // const bucket = 'ttmei-douyin-item';
    const region = 'oss-cn-beijing';

    $("#file").change(function () {
        let file = this.files[0];
        let arr = file.name.split('.');
        $("#file-title").val(arr[0]);
    });
    const applyTokenDo = function (func) {
        $.post('/agent/ajax/ali-sts',{},function (data) {
            let res = checkresult(data);
            console.log(res);
            // let file = e.target.files[0];
            // let storeAs = 'DouyinItem[cover]';
            // console.log(file.name + ' => ' + storeAs);
            let client = new OSS({
                accessKeyId: res.AccessKeyId,
                accessKeySecret: res.AccessKeySecret,
                stsToken: res.SecurityToken,
                // region表示您申请OSS服务所在的地域，例如oss-cn-hangzhou。
                region: region,
                bucket: bucket
            });
            return func(client);
        });
    };

    let currentCheckpoint;
    const progress = async function progress(p, checkpoint) {
        currentCheckpoint = checkpoint;
        const bar = document.getElementById('progress-bar');
        bar.style.width = `${Math.floor(p * 100)}%`;
        bar.innerHTML = `${Math.floor(p * 100)}%`;
    };

    let uploadFileClient;
    const uploadFile = function (client) {
        if (!uploadFileClient || Object.keys(uploadFileClient).length === 0) {
            uploadFileClient = client;
        }

        const file = document.getElementById('file').files[0];
        //获取视频或者音频时长
        var fileurl = URL.createObjectURL(file);
        //经测试，发现audio也可获取视频的时长
        var audioElement = new Audio(fileurl);
        var duration;
        audioElement.addEventListener("loadedmetadata", function (_event) {
            duration = audioElement.duration;
            //获取文件大小
            var size = file.size;//单位：字节(byte)
            console.log( "size");
            console.log( size);
            $.post('/agent/ajax/upload-video-info',{
                filename:file.name,
                title:$("#file-title").val(),
                size:file.size,
                duration:duration*1000
            },function (res) {
                res = checkresult(res);
                console.log(res);
                const options = {
                    progress,
                    partSize: 100 * 1024,
                    meta: {
                        // year: 2017,
                        // people: 'test',
                    },
                };

                return client.multipartUpload(res.filename, file, options).then( (res) => {
                    console.log('upload success: %j', res);
                    currentCheckpoint = null;
                    uploadFileClient = null;
                    window.location="/agent/video";
                }).catch((err) => {
                    if (uploadFileClient && uploadFileClient.isCancel()) {
                        console.log('stop-upload!');
                    } else {
                        console.error(err);
                    }
                });

            });
        });
    };

    window.onload = function () {
        document.getElementById('file-button').onclick = function () {
            applyTokenDo(uploadFile);
        }
    };
</script>