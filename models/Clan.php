<?php
/**
 * Class Clan
 *
 * Класс для работы с отдельной общиной
 */

class Clan {
    /**
     * @var Tribe Модель племени
     */
    public  $tribe_model;
    /**
     * @var string Тэг общины
     */
    public $tag;
    public $x;
    public $y;
    public $main;
    public $kids;
    public $strata = array();
    public $stock;
    public $exchange_last_turn;
    public $looted_last_turn;

    /**
     * Конструктор класса
     *
     * @param Tribe $tribe_model
     * @param array $clan_arr
     */
    public function __construct($tribe_model, $clan_arr = array())
    {
        $this->tribe_model = $tribe_model;
        $this->tag = isset($clan_arr['tag']) ? $clan_arr['tag'] : '';
        $this->x = isset($clan_arr['x']) ? $clan_arr['x'] : 0;
        $this->y = isset($clan_arr['y']) ? $clan_arr['y'] : 0;
        $this->main = isset($clan_arr['main']) ? $clan_arr['main'] : false;
        $this->stock = isset($clan_arr['y']) ? $clan_arr['y'] : 0;
        $this->exchange_last_turn = isset($clan_arr['exchange_last_turn']) ? $clan_arr['exchange_last_turn'] : 0;
        $this->looted_last_turn = isset($clan_arr['looted_last_turn']) ? $clan_arr['looted_last_turn'] : 0;

        if(isset($clan_arr['strata'])){
            foreach($clan_arr['strata'] as $tag => $stratum){
                $this->strata[$tag] = $stratum;
            }
        }
    }

    /**
     * Возвращает обработанные данные об общине
     *
     * @return array
     */
    public function getParsedData()
    {
        $parsed_data = array();
        foreach($this as $field => &$value){
            if($field == "tribe_model"){
                // не сохраняем, т.к. будет рекурсия
            } else {
                $parsed_data[$field] = $value;
            }
        }

        return $parsed_data;
    }

    /**
     * Создание новой общины
     *
     * @param int $x
     * @param int $y
     * @param bool $is_main
     *
     * @return Clan|bool
     */
    public function createNew($x, $y, $is_main = false)
    {
        $this->tag = $this->tribe_model->tag."1";
        $this->x = $x;
        $this->y = $y;
        $this->main = $is_main;

        if(empty($this->x) || empty($this->y)){
            return false;
        }

        $strata_conf = $this->tribe_model->game_model->config->getConfigAsArray('strata');
        foreach($strata_conf as $tag => $stratum){
            $this->strata[$tag] = $stratum;
        }

        return $this;
    }

}