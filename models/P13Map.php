<?php
/**
 * Class P13Map
 *
 * Класс для работы с экземпляром карты Проекта13
 */

class P13Map extends P13Model{

    /**
     * @var P13Map Переменная для хранения статического экземпляра класса для работы со статическими функциями
     */
    private static $instance;

    /**
     * @var int ИД игры
     */
    public $game_id;

    /**
     * @var int ИД хода
     */
    public $turn;

    /**
     * @var array Ячейки карты
     */
    private $_cells = false;

    /**
     * @var string Путь к папке данных игры TODO: удалить
     */
    private $_common_path;

    /**
     * Создает пустой экземпляр карты для доступа к статическим методам, не требующим загруженной карты
     *
     * @return P13Map
     */
    public static function stat()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self(false, false);
        }
        return self::$instance;
    }

    /**
     * Конструктор экземпляра карты
     *
     * @param null|integer $game_id
     * @param null|integer $turn
     */
    public function __construct($game_id = null, $turn = null)
    {
        $this->loadMap($game_id, $turn);

        /** TODO: delete */
        $this->_common_path = Yii::app()->params['rootPath']."/protected/modules/project13/data/common";
    }

    /**
     * Условие для получения конкретной игры
     *
     * @param integer $game_id
     *
     * @return $this
     */
    public function setGameId($game_id)
    {
        ($game_id !== null) ? $this->game_id = $game_id : null;

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
        $this->setGameId($game_id);
        $this->setTurn($turn);

        if($this->game_id !== null && $this->turn !== null){
            $this->model_path = Yii::app()->getModulePath()."/project13/data/games/".$this->game_id."/turns/".$this->turn."/";
            $this->model_file = "map.json";
        }
    }

    /**
     * Загрузка карты из файла в свойство класса
     *
     * @param null|integer $game_id
     * @param null|integer $turn
     */
    private function loadMap($game_id = null, $turn = null)
    {
        $this->setPaths($game_id, $turn);

        $this->loadFromFile();
    }

    /**
     * Загрузка сырых данных в массив клеток
     */
    protected function processRawData()
    {
        $this->_cells = $this->raw_data;
    }

    /**
     * Выгрузка массив клеток в сырые данные
     */
    protected function parseRawData()
    {
        $this->raw_data = $this->_cells;
    }

    /**
     * Проверка на то, существует ли файл карты для сочетания игра-ход
     *
     * @return bool
     */
    public function exists()
    {
        return $this->fileExists();
    }

    /**
     * Создание чистой карты с заданными размерами
     *
     * @param integer $width
     * @param integer $height
     */
    public function createBlankMap($width, $height)
    {
        $this->_cells = array();
        $map_array = array();
        for($y = 1; $y <= $height; $y++) {
            for($x = 1; $x <= $width; $x++) {
                $cell = array(
                    'x' => $x,
                    'y' => $y,
                    'objects' => array(array(
                        'object_type' =>  1,
                        'object_gfx' => 1
                    ))
                );
                $map_array[] = $cell;
                $this->_cells[$y][$x] = $cell;
            }
        }
        $this->saveMap();
    }

    /**
     * Загружает в модель дефолтную карту
     */
    public function loadDefaultMap()
    {
        $this->model_path = Yii::app()->getModulePath()."/project13/data/common/";
        $this->model_file = "default_map.json";

        $this->loadMap();
    }

    /**
     * Возвращает массив с информацией о карте
     *
     * @return array
     */
    public function getMapInfo()
    {
        if(is_array($this->_cells)){
            return array('width' => count($this->_cells[1]), 'height' => count($this->_cells));
        } else {
            return array();
        }
    }

    /**
     * Возвращает массив с полной информацией о всей карте в формате для отсылки на фронт
     *
     * @return array
     */
    public function getFullMapArray()
    {
        return $this->buildMapArray($this->_cells);
    }

    /**
     * Возвращает массив с полной информацией об участке карты в формате для отсылки на фронт
     *
     * @param $width
     * @param $height
     * @param $center_x
     * @param $center_y
     *
     * @return array
     */
    public function getAreaArray($width, $height, $center_x, $center_y)
    {
        $cell_data = array();
        for($y = ($center_y-floor($height/2)); $y <= ($center_y+ceil($height/2)); $y++){
            for($x = ($center_x-floor($width/2)); $x <= ($center_x+ceil($width/2)); $x++){
                if(isset($this->_cells[$y][$x])){
                    $cell_data[$y][$x] = $this->_cells[$y][$x];
                }
            }
        }
        return array(
            "cells" => $this->buildMapArray($cell_data),
            "map_width" => count($this->_cells[1]),
            "map_height" => count($this->_cells),
            "area_width" => $width,
            "area_height" => $height,
            "x_center" => $center_x,
            "y_center" => $center_y
        );
    }


    /**
     * Возвращает массив с общей информацией об участке карты в формате для отсылки на фронт
     *
     * @param $width
     * @param $height
     * @param $center_x
     * @param $center_y
     *
     * @return array
     */
    public function getAreaInfo($width, $height, $center_x, $center_y)
    {
        return array(
            "map_width" => count($this->_cells[1]),
            "map_height" => count($this->_cells),
            "area_width" => $width,
            "area_height" => $height,
            "x_center" => $center_x,
            "y_center" => $center_y
        );
    }

    /**
     * Формирует массив с полной информацией о выбранных клетках в формате для отсылки на фронт
     *
     * @param $cell_data array()
     *
     * @return array()
     */
    private function buildMapArray($cell_data)
    {
        $map_array = array();
        $map_objects = (new P13Config($this->game_id))->getConfigAsArray('land_obj');
        foreach($cell_data as $row){
            foreach($row as $cell){
                $map_array[$cell['y']][$cell['x']]['objects'] = array();
                $map_array[$cell['y']][$cell['x']]['landtype'] = array();
                foreach($cell['objects'] as $object) {
                    $object_type = $map_objects[$object['object_type']];
                    if($object_type['category'] == 'landtype'){
                        $map_array[$cell['y']][$cell['x']]['landtype'] = array(
                            'name' => $object_type['name_rus'],
                            'type' => $object['object_type'],
                            'obj_gfx' => $object['object_gfx'],
                            'gfx' => $object_type['gfx'][$object['object_gfx']]
                        );
                    } elseif($object_type['category'] == "landobj") {
                        $map_array[$cell['y']][$cell['x']]['objects'][$object['object_type']] = array(
                            'name' => $object_type['name_rus'],
                            'category' => $object_type['category'],
                            'type' => $object['object_type'],
                            'obj_gfx' => $object['object_gfx'],
                            'gfx' => Yii::app()->controller->module->assetsBase.'/images/map_icons/'.$object_type['gfx'][$object['object_gfx']].'.png',
                        );
                    } elseif($object_type['category'] == "camp") {
                        $map_array[$cell['y']][$cell['x']]['objects'][$object['object_type']] = array(
                            'name' => $object['name'],
                            'category' => $object_type['category'],
                            'type' => $object['object_type'],
                            'gfx' => $object['gfx'],
                        );
                    }
                }
            }
        }

        return $map_array;
    }

    /**
     * Загружает данные карты в модель
     *
     * @param $map_data
     */
    public function setData($map_data)
    {
        $this->_cells = array();
        foreach($map_data as $y => $row){
            foreach($row as $x => $cell){
                $objects = array();
                if(isset($cell->landtype->type)){
                    $objects[] = array(
                        'object_type' =>  $cell->landtype->type,
                        'object_gfx' => $cell->landtype->obj_gfx,
                    );
                }
                if(!empty($cell->objects)){
                    foreach($cell->objects as $map_object){
                        if(!empty($map_object)){
                            $objects[] = array(
                                'object_type' => $map_object->type,
                                'object_gfx' => $map_object->obj_gfx
                            );
                        }
                    }
                }
                $this->_cells[$y][$x] = array(
                    'x' => $x,
                    'y' => $y,
                    'objects' => $objects
                );
            }
        }
    }

    /**
     * Сохраняет карту в файл
     *
     * @param null|integer $game_id
     * @param null|integer $turn
     *
     * @return bool
     */
    public function saveMap($game_id = null, $turn = null)
    {
        $this->setPaths($game_id, $turn);
        return $this->saveToFile();
    }

    /**
     * Возвращает информацию о типе объекта
     *
     * @param int $object_type_id ИД типа объекта
     *
     * @return array()
     *
     * TODO: Переписать всю цепочку так, что бы передавать полную инфу а не только соответствие ИД - ссылка на графику
     */
    public function makeObjectTypeGFX($object_type_id)
    {
        $map_objects = (new P13Config($this->game_id))->getConfigAsArray('land_obj');
        $object_type = $map_objects[$object_type_id];
        $return_list = array();
        foreach($object_type['gfx'] as $gfx_id => $gfx){
            if($object_type['category'] == 'landtype'){
                $return_list[$gfx_id] = $gfx;
            } elseif($object_type['category'] == 'landobj'){
                $return_list[$gfx_id] = Yii::app()->controller->module->assetsBase.'/images/map_icons/'.$gfx.'.png';
            }
        }

        return $return_list;
    }

    /**
     * Возвращает ширину карты
     * @return int
     */
    public function getWidth(){
        return count($this->_cells[1]);
    }

    /**
     * Возвращает высоту карты
     * @return int
     */
    public function getHeight(){
        return count($this->_cells);
    }

    /**
     * Добавление на карту объектов общин
     *
     * @param array $tribes Модель игры
     */
    public function addTribes($tribes)
    {
        /** @var Tribe $tribe */
        foreach($tribes as $tribe){
            /** @var Clan $clan */
            foreach($tribe->clans as $clan){
                $this->_cells[$clan->y][$clan->x]['objects'][] = array(
                    'name' => $clan->tribe_model->name.($clan->main ? "(*)" : ""),
                    'object_type' => "camp",
                    'gfx' => $clan->tribe_model->color
                );
            }
        }
    }



} 