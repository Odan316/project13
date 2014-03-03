<?php
/**
 * Виджет для вывода участка карты с навигацией
 */
class P13MapArea extends CWidget {
    /**
     * @var array
     */
    public $area_data;

    /**
     * @var Game
     */
    public $game_data;

    public function init()
    {
    }

    public function run()
    {
        $this->render('map_area', array(
            'area_data' => $this->area_data,
            'game_data' => $this->game_data
        ));
    }
} 