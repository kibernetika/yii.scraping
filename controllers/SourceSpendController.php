<?php

namespace app\controllers;

use app\models\parser\ParserSource;
use app\models\parser\ParserUtils;
use app\models\table\Users;
use app\models\table\SourceSpend;
use app\models\table\SourceSpendSearch;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use DateTime;

/**
 * SourceSpendController implements the CRUD actions for SourceSpend model.
 */
class SourceSpendController extends Controller
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
     * Lists all SourceSpend models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SourceSpendSearch();
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
            ParserSource::load($id);
        }
        return $this->redirect(['index']);
    }

    /**
     * Displays a single SourceSpend model.
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
     * Creates a new SourceSpend model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SourceSpend();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SourceSpend model.
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
     * Deletes an existing SourceSpend model.
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
     * Finds the SourceSpend model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SourceSpend the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SourceSpend::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
