<?php

namespace app\modules\agent\controllers;

use app\models\DouyinAccount;
use app\models\search\DouyinAccountSearch;
use app\models\Video;
use app\modules\agent\AdminController;
use Yii;
use app\models\DouyinItem;
use app\models\search\DouyinItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DouyinItemController implements the CRUD actions for DouyinItem model.
 */
class DouyinItemController extends AdminController
{
    /**
     * Lists all DouyinItem models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DouyinItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $douyin_account_ids = DouyinAccount::find()->select('id')->where(['wx_uid'=>Yii::$app->user->id])->column();
        $dataProvider->query->andWhere(['douyin_account_id'=>$douyin_account_ids]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DouyinItem model.
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
    public function actionShow($id)
    {
        $obj = DouyinItem::findOne($id);
        $arr = json_decode($obj->item_data,1);
        return $this->redirect($arr['share_url']);
    }

    /**
     * Creates a new DouyinItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($video_id)
    {
        $video = Video::findOne($video_id);
        if(empty($video)){
            throw new \Exception('参数错误',1);
        }
        if(Yii::$app->user->id != $video->wx_uid){
            throw new \Exception('无权限',1);
        }
        $searchModel = new DouyinAccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('create', [
            'video' => $video,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
   }

    /**
     * Updates an existing DouyinItem model.
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
     * Deletes an existing DouyinItem model.
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
     * Finds the DouyinItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DouyinItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DouyinItem::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
