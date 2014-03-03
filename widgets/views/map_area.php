<?
/**
 * @var array $area_data
 * @var Game $game_data
 */
?>
<div class="b_map"
     style="width:<?=(($area_data['area_width']+1)*16+6).'px;'; ?>; height:<?=(($area_data['area_height']+1)*16+5).'px;'; ?>;"
     data-width="<?=$area_data['area_width']?>" data-height="<?=$area_data['area_height']?>"
     data-x="<?=$area_data['x_center']?>" data-y="<?=$area_data['y_center']?>"
     data-game-id="<?=$game_data->id;?>"
     data-max-y="<?=$area_data['map_height'];?>"
     data-max-x="<?=$area_data['map_width'];?>"
    >
    <?
    $this->widget('bootstrap.widgets.TbButton',array(
        'type' => 'warning',
        'icon' => 'icon-arrow-left',
        'id' => 'map_left_5',
        'htmlOptions' => array(
            "class" => "map_button",
            "style" => "top:".(floor((($area_data['area_width']+1)*16+1)/2)-12)."px;left:-45px")
    ));
    $this->widget('bootstrap.widgets.TbButton',array(
        'type' => 'warning',
        'icon' => 'icon-arrow-right',
        'id' => 'map_right_5',
        'htmlOptions' => array(
            "class" => "map_button",
            "style" => "top:".(floor((($area_data['area_width']+1)*16+1)/2)-12)."px;right:-45px")
    ));
    $this->widget('bootstrap.widgets.TbButton',array(
        'type' => 'warning',
        'icon' => 'icon-arrow-up',
        'id' => 'map_up_5',
        'htmlOptions' => array(
            "class" => "map_button",
            "style" => "left:".(floor((($area_data['area_height']+1)*16+1)/2)-22)."px;top:-30px")
    ));
    $this->widget('bootstrap.widgets.TbButton',array(
        'type' => 'warning',
        'icon' => 'icon-arrow-down',
        'id' => 'map_down_5',
        'htmlOptions' => array(
            "class" => "map_button",
            "style" => "left:".(floor((($area_data['area_height']+1)*16+1)/2)-22)."px;bottom:-30px")
    ));
    ?>
<div class="map_coord_row">
<div class="map_x_coord_null"></div>
<?for($x = 1; $x <= $area_data['area_width']; $x++):?>
    <div class="map_x_coord" id="xcord<?=$x?>"></div>
<?endfor?>
</div>
<?for($y = 1; $y <= $area_data['area_height']; $y++):?>
<div class="map_row">
    <div class="map_y_coord" id="ycord<?=$y?>"></div>
    <?for($x = 1; $x <= $area_data['area_width']; $x++):?>
        <div class="map_cell" id="r<?=$y?>c<?=$x?>"></div>
    <?endfor?>
</div>
<?endfor?>
</div>