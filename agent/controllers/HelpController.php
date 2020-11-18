<?php

namespace app\modules\agent\controllers;

use app\modules\agent\AdminController;
use Yii;
use app\models\Video;
use app\models\search\VideoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VideoController implements the CRUD actions for Video model.
 */
class HelpController extends AdminController
{
    /**
     * Lists all Video models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', []);
    }
}
