<?php

namespace app\controllers;

use Yii;
use app\models\table\Balance;
use app\models\table\BalanceSearch;
use app\models\table\Users;
use app\models\parser\ParserSource;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use DateTime;


/**
 * BalanceController implements the CRUD actions for Balance model.
 */
class BalanceController extends Controller
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
     * Lists all Balance models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BalanceSearch();
//        $start = new DateTime('01 ' . date('M') . ' ' . date('Y'));
//        $start = $start->format('Y-m-d');
        $end = new DateTime('now');
        date_add($end, date_interval_create_from_date_string('-1 days'));
        $end = $end->format('Y-m-d');
        $searchModel->startDate = (isset($_GET['start']) ? $_GET['start'] : $end);
        $searchModel->endDate = (isset($_GET['end']) ? $_GET['end'] : $end);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'start' => $searchModel->startDate,
            'end' => $searchModel->endDate,
        ]);
    }

    public function actionLoad()
    {
        $users = Users::find()->all();
        foreach ($users as $user){
            $id = $user->getAttribute('id');
            ParserSource::load($id);
        }
        return $this->redirect(['index']);
    }

    public function actionFilter(){
        $startDate = $_GET['start'];
        $endDate = $_GET['end'];
        return $this->redirect(['index', 'start' => $startDate, 'end' => $endDate]);
    }

    /**
     * Displays a single Balance model.
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
     * Creates a new Balance model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Balance();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Balance model.
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
     * Deletes an existing Balance model.
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
     * Finds the Balance model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Balance the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Balance::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
