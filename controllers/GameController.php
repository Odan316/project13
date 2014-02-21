<?php
/**
 * Class GameController
 *
 * Контроллер для работы в Кабинете (Ведущего или Игрока)
 */
class GameController extends Controller
{
    /**
     * Модель пользователя (из базового движка)
     * @var $user_model Users
     */
    private $user_model;

    /**
     * Модель игры (из базового движка)
     * @var $game_model Games
     */
    private $game_model;

    /**
     * Перед загрузкой контроллера необходимо
     * - установить общий layout модуля
     * - подключить общие стили и JS
     */
    public function init()
    {
        $this->layout = 'main';
        /** @var $ClientScript CClientScript */
        $ClientScript = Yii::app()->clientScript;
        $ClientScript->registerCssFile($this->module->assetsBase.'/css/styles.css');
        $ClientScript->registerScriptFile($this->module->assetsBase.'/js/jcanvas.js');
        $ClientScript->registerScriptFile($this->module->assetsBase.'/js/project13.js');

        parent::init();
    }

    /**
     * Перед загрузкой действия необходимо
     * - проверить наличие или попытаться установить ИД игры в куки
     * - проверить права пользователя на доступ к игре
     * - загрузить базовые модели пользователя и игры     *
     *
     * @param CAction $action
     *
     * @return bool
     */
    public function beforeAction($action)
    {
        $game_id = false;
        /** @var $user CWebUser */
        $user = Yii::app()->user;
        if(isset($this->actionParams['id'])){
            $game_id = $this->actionParams['id'];
            $cookie = new CHttpCookie('game_id', $game_id);
            $cookie->expire = time()+60*60*24*30;
            Yii::app()->request->cookies['game_id'] = $cookie;
        } elseif(isset(Yii::app()->request->cookies['game_id'])){
            $game_id = Yii::app()->request->cookies['game_id']->value;
        } elseif (!$user->getState('game_id')) {
            $this->redirect($this->createUrl('cabinet/no_such_game'));
        }

        if(!$user->getState('game_role')){
            $user_role = Users2games::model()->findByAttributes(array(
                'user_id' => $user->getState('uid'), 'game_id' => $game_id));
            if(!$user_role){
                $this->redirect($this->createUrl('cabinet/game_access_denied'));
            } else{
                $user->setState('game_role', $user_role->role_id);
            }
        }
        $this->user_model = Users::model()->with('person')->findByPk($user->getState('uid'));
        $this->game_model = Games::model()
            ->with('master_user', 'players_users')
            ->findByPk($game_id);

        return parent::beforeAction($action);
    }

    /**
     * По умолчанию, в зависимости от роли мы грузим
     * - либо страницу ГМа (для ГМа),
     * - либо страницу племени (для остальных)
     */
    public function actionIndex()
    {
        if(Yii::app()->user->getState('game_role') == Game_roles::GM_ROLE){
            $this->actionGM();
        } else {
            $this->actionTribe();
        }
    }

    /**
     * Страница ГМа (только для ГМа)
     */
    public function actionGM()
    {
        // Сначала проверяем роль
        if(Yii::app()->user->getState('game_role') == Game_roles::GM_ROLE){
            /** @var $ClientScript CClientScript */
            $ClientScript = Yii::app()->clientScript;
            $ClientScript->registerScriptFile($this->module->assetsBase.'/js/gm.js');

            $players_ids = CHtml::listData($this->game_model->players_users, 'id', 'id');
            $players = Persons::model()->id_in($players_ids)->findAll();
            $game_data = new Game($this->game_model->id, $this->game_model->last_turn);
            $players = $game_data->comparePlayers($players);

            $map = new P13Map($this->game_model->id, $this->game_model->last_turn);

            $area_data = $map->getAreaInfo(35, 35, 80, 40);
            $this->render('gm', array(
                'players' => $players,
                'game_data' => $game_data,
                "area_data" => $area_data
            ));
        } else {
            $this->actionNoAccess();
        }
    }

    /**
     * Страница с редактором карт (только для ГМа)
     */
    public function actionMap_redactor()
    {
        // Сначала проверяем роль
        if(Yii::app()->user->getState('game_role') == Game_roles::GM_ROLE){
            /** @var $ClientScript CClientScript */
            $ClientScript = Yii::app()->clientScript;
            $ClientScript->registerScriptFile($this->module->assetsBase.'/js/jquery.json-2.4.js');
            $ClientScript->registerScriptFile($this->module->assetsBase.'/js/map_redactor.js');

            $game_id = $this->game_model->id;
            $turn = $this->game_model->last_turn;
            $map = new P13Map($game_id, $turn);

            if(!$map->exists() && isset($_POST['create_map'])){
                $map->createBlankMap(
                    htmlspecialchars($_POST['map_width']),
                    htmlspecialchars($_POST['map_height'])
                );
            }
            $map_info = $map->getMapInfo();
            $map_object_types = (new P13Config($game_id))->getConfigAsArray('land_obj');

            $this->render('map_redactor', array(
                'map_info' => $map_info,
                'map_object_types' => $map_object_types
            ));
        } else {
            $this->actionNoAccess();
        }

    }

    public function actionTribe()
    {
        $this->render('tribe', array(
        ));
    }

    public function actionTech()
    {
        $this->render('index', array(
            'user_model' => $this->user_model,
            'game_model' => $this->game_model,
        ));
    }

    public function actionRequest()
    {
        $this->render('index', array(
            'user_model' => $this->user_model,
            'game_model' => $this->game_model,
        ));
    }

    public function actionMap()
    {
        $this->render('index', array(
            'user_model' => $this->user_model,
            'game_model' => $this->game_model,
        ));
    }

    public function actionStatistic()
    {
        $this->render('index', array(
            'user_model' => $this->user_model,
            'game_model' => $this->game_model,
        ));
    }

    /**
     * Создание карты на основе дефолтной (только для ГМа)
     */
    public function actionCreate_default_map()
    {
        // Сначала проверяем роль
        if(Yii::app()->user->getState('game_role') == Game_roles::GM_ROLE){
            $map = new P13Map();
            $map->loadDefaultMap();

            $map->saveMap($this->game_model->id, 0);

            $this->redirect($this->createUrl("game/map_redactor"));
        } else {
            $this->actionNoAccess();
        }
    }

    /**
     * Отображение заглушки, говорящей что страница не доступна этой роли
     */
    public function actionNoAccess()
    {
        $this->render('no_access');
    }

    /**
     * (AJAX) Возвращает массив с полной информацией о карте
     */
    public function actionGetFullMapInfo()
    {
        $map = new P13Map($this->game_model->id, $this->game_model->last_turn);
        $map_array = $map->getFullMapArray();

        echo json_encode($map_array);
    }

    /**
     * (AJAX) Возвращает массив с информацией об участке карты
     */
    public function actionGetAreaInfo()
    {
        $map = new P13Map($this->game_model->id, $this->game_model->last_turn);
        $game_data = new Game($this->game_model->id, $this->game_model->last_turn);
        $map->addTribes($game_data->tribes);
        $area_data = $map->getAreaArray(
            htmlspecialchars($_POST['width']),
            htmlspecialchars($_POST['height']),
            htmlspecialchars($_POST['center_x']),
            htmlspecialchars($_POST['center_y'])
        );

        echo json_encode($area_data['cells']);
    }

    /**
     * (AJAX) Возвращает массив с информацией о графическом отображении объектов карты
     */
    public function actionGetMapObjectGFXs()
    {
        $map = new P13Map();
        $object_type_id = htmlspecialchars($_POST['map_object_type']);

        echo json_encode($map->makeObjectTypeGFX($object_type_id));
    }

    /**
     * (AJAX) Сохраняет карту
     */
    public function actionSaveMap()
    {
        $map = new P13Map($this->game_model->id, $this->game_model->last_turn);
        $map_data = json_decode($_POST['map_data']);
        $map->setData($map_data);

        echo $map->saveMap();
    }

    /**
     * (AJAX) Сохраняет информацию о племени
     */
    public function actionGMSaveTribe()
    {
        $player_id = htmlspecialchars($_POST['player_id']);
        $game = new Game($this->game_model->id, $this->game_model->last_turn);
        //CVarDumper::dump($game, 10, 1);
        $tribe = $game->getTribeByPlayer($player_id);

        $tag = htmlspecialchars($_POST['tribe_tag']);
        $color = htmlspecialchars($_POST['tribe_color']);
        $name = htmlspecialchars($_POST['tribe_name']);

        if($tribe !== false) {
            $tribe->name = ($name ? $name : $tribe->name);
            $tribe->color = ($color ? $color : $tribe->color);
            $result = $game->saveTribe($tribe->tag, $tribe);
        } else {
            $start_x = htmlspecialchars($_POST['tribe_start_x']);
            $start_y = htmlspecialchars($_POST['tribe_start_y']);
            $result = $game->addNewTribe($player_id, $tag, $color, $name, $start_x, $start_y);
        }

        echo json_encode(array("result" => $result));
    }
}