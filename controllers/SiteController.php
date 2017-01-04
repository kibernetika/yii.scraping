<?php

namespace app\controllers;

use app\models\view\MainAgregateQuery;
use app\models\parser;
use app\models\LoginForm;
use app\models\table\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\ArrayDataProvider;
use DateTime;


class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if( Yii::$app->user->isGuest ){
            $model = new LoginForm();
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return $this->goBack();
            }
            return $this->render('login', [
                'model' => $model,
            ]);
        }else{
            $start = new DateTime('01 '.date('M').' '.date('Y'));
            $start = $start->format('Y-m-d');
            $start = ( isset($_GET['start'])  ? $_GET['start'] : $start );
            $end = new DateTime('now');
            $end = $end->format('Y-m-d');
            $end = ( isset($_GET['end'])  ? $_GET['end'] : $end );

            $searchModel = MainAgregateQuery::data($start, $end, Yii::$app->user->id);
            $dataProvider = new ArrayDataProvider([
                'allModels' => $searchModel,
                'pagination' => [
                    'pageSize' => 33,
                ],
            ]);
            return $this->render('index', [
                'dataProvider' => $dataProvider,
                'start' => $start,
                'end' => $end,
            ]);
        }
    }

    public function actionFilter(){
        $startDate = $_GET['start'];
        $endDate = $_GET['end'];
        return $this->redirect(['index', 'start' => $startDate, 'end' => $endDate]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLoad()
    {
        $users = Users::find()->all();
        foreach ($users as $user){
            $id = $user->getAttribute('id');
            parser\ParserNetwork::loadNewData($id);
            parser\ParserSource::load($id);
            parser\ParserAgregate::loadNewDataAgregate($id);
        }
        return $this->redirect(['index']);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    public function actionTest()
    {
        return $this->render('test');
    }

}
