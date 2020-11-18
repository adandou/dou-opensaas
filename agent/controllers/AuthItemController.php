<?php

namespace app\modules\agent\controllers;

use app\models\AuthAccount;
use app\models\search\AuthAccountSearch;
use app\models\Video;
use app\modules\agent\AdminController;
use Yii;
use app\models\AuthItem;
use app\models\search\AuthItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 */
class AuthItemController extends AdminController
{
    //显示视频
    public function actionShow($id)
    {
        $obj = AuthItem::findOne($id);
        return $this->redirect($obj['show_url']);
    }
    /**
     * Lists all AuthItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $auth_account_ids = AuthAccount::find()->select('id')->where(['uid'=>Yii::$app->user->id])->column();
        $dataProvider->query->andWhere(['auth_account_id'=>$auth_account_ids]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate($video_id)
    {
        $video = Video::findOne($video_id);
        if(empty($video)){
            throw new \Exception('参数错误',1);
        }
        if(Yii::$app->user->id != $video->wx_uid){
            throw new \Exception('无权限',1);
        }
        $searchModel = new AuthAccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['uid'=>Yii::$app->user->id]);

        return $this->render('create', [
            'video' => $video,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing AuthItem model.
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
     * Deletes an existing AuthItem model.
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
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
