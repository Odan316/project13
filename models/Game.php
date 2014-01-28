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
     * @var int ИД хода
     */
    public $turn;

    /**
     * Конструктор модели
     *
     * @param null|integer $game_id
     * @param null|integer $turn
     */
    public function __construct($game_id = null, $turn = null)
    {
        $this->load($game_id, $turn);
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
     */
    public function save($game_id = null, $turn = null)
    {
        $this->setPaths($game_id, $turn);

        $this->saveToFile();
    }

    /**
     * Может переопределятся в конкретной модели для загрузки сырых данных в свойства модели
     */
    protected function processRawData()
    {
        //$this->attributes = $this->raw_data;
    }

    /**
     * Может переопределятся в конкретной модели для загрузки свойства модели сырые данные
     */
    protected function parseRawData()
    {
        $this->raw_data['id'] = $this->id;
        $this->raw_data['turn'] = $this->turn;
    }

    public function createNewGame()
    {
        $this->save();
    }
} 