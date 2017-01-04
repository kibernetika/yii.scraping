<?php

namespace app\controllers;

use app\models\parser\ParserNetwork;
use app\models\table\SourceReturn;
use app\models\table\SourceReturnSearch;
use app\models\table\Users;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use DateTime;

/**
 * SourceReturnController implements the CRUD actions for SourceReturn model.
 */
class SourceReturnController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SourceReturn models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SourceReturnSearch();

        $start = new DateTime('01 '.date('M').' '.date('Y'));
        $start = $start->format('Y-m-d');
        $searchModel->startDate = ( isset($_GET['start'])  ? $_GET['start'] : $start );
        $end = new DateTime('now');
        $end = $end->format('Y-m-d');
        $searchModel->endDate = ( isset($_GET['end'])  ? $_GET['end'] : $end );

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'start' => $searchModel->startDate,
            'end' => $searchModel->endDate,
            'total' => $searchModel->total,
            'average' => $searchModel->average,
        ]);
    }

    public function actionFilter(){
        $startDate = $_GET['start'];
        $endDate = $_GET['end'];
        return $this->redirect(['index', 'start' => $startDate, 'end' => $endDate]);
    }

    public function actionLoad()
    {
        $users = Users::find()->all();
        foreach ($users as $user){
            $id = $user->getAttribute('id');
            ParserNetwork::loadNewData($id);
        }
        return $this->redirect(['index']);
    }

    /**
     * Displays a single SourceReturn model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SourceReturn model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SourceReturn();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SourceReturn model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SourceReturn model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the SourceReturn model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SourceReturn the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SourceReturn::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
