<?php

namespace app\modules\agent\controllers;

use app\models\DouyinApp;
use app\modules\agent\AdminController;
use Yii;
use app\models\DouyinAccount;
use app\models\search\DouyinAccountSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DouyinAccountController implements the CRUD actions for DouyinAccount model.
 */
class DouyinAccountController extends AdminController
{
    /**
     * Lists all DouyinAccount models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DouyinAccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DouyinAccount model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DouyinAccount model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $douyinApp = DouyinApp::findOne(1);
        $url = $douyinApp->buildConnectUrl(implode(',',[
            'aweme.share',//抖音分享
            'user_info',
            'video.create',
            'video.delete',
            'video.data',
            'video.list',
//            'toutiao.video.create',
//            'toutiao.video.data',
//            'xigua.video.data',
//            'xigua.video.create',
            'video.comment',//企业号评论
            'im',//企业私信
//            'following.list',
//            'fans.list',
            'micapp.is_legal',
            'renew_refresh_token',
//            'im. share',
            'item.comment',
        ]),'https://www.ttmei.vip/douyin/oauth');
        return $this->redirect($url);
    }

    /**
     * Updates an existing DouyinAccount model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DouyinAccount model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DouyinAccount model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DouyinAccount the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DouyinAccount::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
