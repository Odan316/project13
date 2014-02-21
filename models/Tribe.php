<?php
/**
 * Class Tribe
 *
 * Модель для работы с племенем
 */

class Tribe {
    /**
     * @var Game Модель игры
     */
    public $game_model;

    /**
     * @var string Тэг племени
     */
    public $tag;

    /**
     * @var int ИД игрока (соответствует user_id)
     */
    public $player_id;

    /**
     * @var string Цвет флага и территории
     */
    public $color;

    /**
     * @var string Имя племени
     */
    public $name;
    /**
     * @var array Список кланов (экземпляры класса Clan)
     */
    public $clans = array();

    /**
     * @var array Прогресс техов
     */
    public $tech_levels = array();

    /**
     * @var array Список открытых технологий
     */
    public $technologies = array();

    /**
     * @var array Список принятых решений
     */
    public $decisions = array();

    /**
     * @var array Список особых отношений с другими племенами
     */
    public $relations = array();

    /**
     * @var bool Флаг миграции
     */
    public $migration;

    /**
     * @var bool Флаг изоляции
     */
    public $isolation;

    /**
     * Конструктор класса
     *
     * @param Game $game_model
     * @param array $tribe_arr
     */
    public function __construct($game_model, $tribe_arr = array())
    {
        $this->game_model = $game_model;

        $this->tag = isset($tribe_arr['tag']) ? $tribe_arr['tag'] : '';
        $this->player_id = isset($tribe_arr['player_id']) ? $tribe_arr['player_id'] : 0;
        $this->color = isset($tribe_arr['color']) ? $tribe_arr['color'] : '#808080';
        $this->name = isset($tribe_arr['name']) ? $tribe_arr['name'] : '';

        if(isset($tribe_arr['clans'])){
            foreach($tribe_arr['clans'] as $clan){
                $this->clans[$clan['tag']] = new Clan($this, $clan);
            }
        }

        $this->tech_levels = isset($tribe_arr['tech_levels']) ? $tribe_arr['tech_levels'] : array();
        $this->technologies = isset($tribe_arr['technologies']) ? $tribe_arr['technologies'] : array();
        $this->decisions = isset($tribe_arr['decisions']) ? $tribe_arr['decisions'] : array();
        $this->relations = isset($tribe_arr['relations']) ? $tribe_arr['relations'] : array();
        $this->migration = isset($tribe_arr['migration']) ? $tribe_arr['migration'] : false;
        $this->isolation = isset($tribe_arr['isolation']) ? $tribe_arr['isolation'] : false;

    }

    /**
     * Возвращает обработанные данные о племени
     *
     * @return array
     */
    public function getParsedData()
    {
        $parsed_data = array();
        foreach($this as $field => &$value){
            if($field == "game_model"){
                // не сохраняем, т.к. будет рекурсия
            } elseif($field == "clans"){
                /** @var Clan $clan */
                foreach($value as $clan_tag => &$clan){
                    $parsed_data[$field][$clan_tag] = $clan->getParsedData();
                }
            } else {
                $parsed_data[$field] = $value;
            }
        }

        return $parsed_data;
    }

    /**
     * Создание нового племени
     *
     * @param $tag
     * @param $player_id
     * @param $name
     * @param string $color
     * @param int $start_x
     * @param int $start_y
     *
     * @return Tribe|bool
     */
    public function createNew($tag, $player_id, $name, $color = "#808080", $start_x, $start_y)
    {
        $this->tag = $tag;
        $this->player_id = $player_id;
        $this->name = $name;
        $this->color = $color;

        if(empty($this->tag) || empty($this->player_id) || empty($this->name)){
            return false;
        }

        $tech_spheres = $this->game_model->config->getConfigAsArray('tech_spheres');
        foreach($tech_spheres as $sphere => $name){
            $this->tech_levels[$sphere] = 0;
        }

        $this->clans[$tag."1"] = (new Clan($this))->createNew($start_x, $start_y);

        return $this;
    }
}
?>