<?php
/**
 * Class Game
 *
 * Модель для работы с игрой
 */

class Game extends P13Model {
    /**
     * @var int ИД игры
     */
    public $id;

    /**
     * @var P13Config
     */
    public $config;

    /**
     * @var int ИД хода
     */
    public $turn;

    /**
     * @var P13Map Карта
     */
    public $map;

    /**
     * @var array Массив с объектами Племен
     */
    public $tribes = array();

    /**
     * Конструктор модели
     *
     * @param null|integer $game_id
     * @param null|integer $turn
     */
    public function __construct($game_id = null, $turn = null)
    {
        $this->load($game_id, $turn);
        $this->config = new P13Config($this->id);
        $this->map = new P13Map($this->id, $this->turn);
    }

    /**
     * Условие для получения конкретной игры
     *
     * @param integer $game_id
     *
     * @return $this
     */
    public function setId($game_id)
    {
        ($game_id !== null) ? $this->id = $game_id : null;

        return $this;
    }

    /**
     * Условие для получения конкретного хода
     *
     * @param integer $turn
     *
     * @return $this
     */
    public function setTurn($turn)
    {
        ($turn !== null) ? $this->turn = $turn : null;

        return $this;
    }

    /**
     * Установка путей к папке и файлу
     *
     * @param null|integer $game_id
     * @param null|integer $turn
     */
    protected function setPaths($game_id = null, $turn = null)
    {
        $this->setId($game_id);
        $this->setTurn($turn);

        if($this->id !== null && $this->turn !== null){
            $this->model_path = Yii::app()->getModulePath()."/project13/data/games/".$this->id."/turns/".$this->turn."/";
            $this->model_file = "main_save.json";
        }
    }

    /**
     * Загрузка игрового файла в модель
     *
     * @param null|integer $game_id
     * @param null|integer $turn
     */
    public function load($game_id = null, $turn = null)
    {
        $this->setPaths($game_id, $turn);

        $this->loadFromFile();
    }

    /**
     * Сохранение модели в файл
     *
     * @param null|integer $game_id
     * @param null|integer $turn
     *
     * @return bool
     */
    public function save($game_id = null, $turn = null)
    {
        $this->setPaths($game_id, $turn);

        return $this->saveToFile();
    }

    /**
     * Загрузка сырых данных в свойства модели
     */
    protected function processRawData()
    {
        if(isset($this->raw_data['tribes'])){
            foreach($this->raw_data['tribes'] as $tribe){
                //CVarDumper::dump($tribe, 1, 1);
                $this->tribes[$tribe['tag']] = new Tribe($this, $tribe);
            }
        }
    }

    /**
     * Выгрузка свойств модели в сырые данные
     */
    protected function parseRawData()
    {
        $this->raw_data['id'] = $this->id;
        $this->raw_data['turn'] = $this->turn;
        /** @var Tribe $tribe */
        foreach($this->tribes as $tribe){
            $this->raw_data['tribes'][$tribe->tag] = $tribe->getParsedData();
        }
    }

    /**
     *
     */
    public function createNewGame()
    {
        $this->save();
    }

    public function comparePlayers($players_model){
        $players = array();
        /** @var Persons $player */
        foreach($players_model as $player){
            $players[$player->user_id] = array(
                'name' => $player->nickname,
                'tribe_tag' => null
            );
        }
        /** @var Tribe $tribe */
        foreach($this->tribes as $tribe){
            if(isset($players[$tribe->player_id])){
                $players[$tribe->player_id]['tribe_tag'] = $tribe->tag;
            }
        }
        return $players;
    }

    /**
     * Возвращает массив с информацией о племени по ИД игрока
     * или false если у такого игрока нет еще племени
     *
     * @param $player_id
     *
     * @return Tribe|bool
     */
    public function getTribeByPlayer($player_id)
    {
        foreach($this->tribes as $tribe){
            if($tribe->player_id == $player_id){
                return $tribe;
            }
        }
        return false;
    }

    /**
     * Добавление нового племени в игре
     * @param $player_id
     * @param $tag
     * @param $color
     * @param $name
     * @param $start_x
     * @param $start_y
     * @return bool
     */
    public function addNewTribe($player_id, $tag, $color, $name, $start_x, $start_y)
    {
        if($start_x > $this->map->getWidth() || $start_y > $this->map->getHeight()){
            return false;
        }
        $this->tribes[$tag] = (new Tribe($this))->createNew($tag, $player_id, $name, $color, $start_x, $start_y);
        return $this->save();
    }

    /**
     * Сохраняет новую инофрмацию о племени
     *
     * @param $tag
     * @param $tribe
     *
     * @return bool
     */
    public function saveTribe($tag, $tribe)
    {
        $this->tribes[$tag] = $tribe;
        return $this->save();
    }

    /**
     * @param $tribe_tag
     *
     * @return array
     */
    public function createNewClan($tribe_tag)
    {
        return array(

        );
    }
} 