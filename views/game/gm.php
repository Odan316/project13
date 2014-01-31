<?php
/**
 * @var $this GameController
 * @var $players array Список игроков, с племенами
 * @var $game_data Game
 * @var $area_data array
 */
$this->setPageTitle('Проект13 - Кабинет Ведущего');
?>
<div id="b_gm_left">
    <?
    $this->widget('bootstrap.widgets.TbButton',array(
        'label' => 'Ход',
        'type' => 'danger',
        'size' => 'large',
        'id' => 'make_turn'
    ));
    ?>
    <div id="b_gm_players">
        <h4>Игроки</h4>
        <?
        /* @var $player Users */
        foreach($players as $user_id => $player):?>
            <div class="b_gm_player">
                <p><?=$player['name']?></p>
                <div class="b_gm_tribe_info">
                    <? if($player['tribe_tag'] !== null): ?>
                        <canvas width='25' height='25' class="tribe_flag_medium" data-color="#FF0000"></canvas>
                        <div class="b_gm_tribe_name">
                            <?=$game_data->tribes[$player['tribe_tag']]['name']?>
                            <?
                            $this->widget('bootstrap.widgets.TbButton',array(
                                'label' => 'Ред.',
                                'type' => 'secondary',
                                'size' => 'small',
                                "htmlOptions" => array('class' => "but_gm_tribe_edit")
                            ));
                            ?>
                        </div>
                    <? else: ?>
                        <div class="tribe_flag_medium_pholder"></div>
                        <div class="b_gm_tribe_name">
                            Нет
                            <?
                            $this->widget('bootstrap.widgets.TbButton',array(
                                'label' => 'Создать',
                                'type' => 'secondary',
                                'size' => 'small',
                                "htmlOptions" => array('class' => "but_gm_tribe_edit")
                            ));
                            ?>
                        </div>
                    <? endif; ?>

                </div>
            </div>
        <?endforeach?>
    </div>
</div>
<div id="b_gm_block_map">
    <h4>Карта</h4>
    <div id="gm_show_map" class="b_map"
         style="width:<?=($area_data['area_width']*16+1).'px;'; ?>; height:<?=($area_data['area_height']*16).'px;'; ?>;"
         data-width="<?=$area_data['area_width']?>" data-height="<?=$area_data['area_height']?>"
         data-x="<?=$area_data['x_center']?>" data-y="<?=$area_data['y_center']?>"
         data-game-id="<?=$game_data->id;?>"
        >
        <?if($area_data['x_center'] > $area_data['area_width']/2):?>
            <?
            $this->widget('bootstrap.widgets.TbButton',array(
                'type' => 'warning',
                'icon' => 'icon-arrow-left',
                'id' => 'map_left_5',
                'htmlOptions' => array(
                    "class" => "map_button",
                    "style" => "top:".(floor(($area_data['area_width']*16+1)/2)-12)."px;left:-45px")
            ));
            ?>
        <?endif?>
        <?if(($area_data['map_width'] - $area_data['x_center']) > $area_data['area_width']/2):?>
            <?
            $this->widget('bootstrap.widgets.TbButton',array(
                'type' => 'warning',
                'icon' => 'icon-arrow-right',
                'id' => 'map_right_5',
                'htmlOptions' => array(
                    "class" => "map_button",
                    "style" => "top:".(floor(($area_data['area_width']*16+1)/2)-12)."px;right:-45px")
            ));
            ?>
        <?endif?>
        <?if($area_data['y_center'] > $area_data['area_height']/2):?>
            <?
            $this->widget('bootstrap.widgets.TbButton',array(
                'type' => 'warning',
                'icon' => 'icon-arrow-up',
                'id' => 'map_up_5',
                'htmlOptions' => array(
                    "class" => "map_button",
                    "style" => "left:".(floor(($area_data['area_height']*16+1)/2)-22)."px;top:-30px")
            ));
            ?>
        <?endif?>
        <?if(($area_data['map_height'] - $area_data['y_center']) > $area_data['area_height']/2):?>
            <?
            $this->widget('bootstrap.widgets.TbButton',array(
                'type' => 'warning',
                'icon' => 'icon-arrow-down',
                'id' => 'map_down_5',
                'htmlOptions' => array(
                    "class" => "map_button",
                    "style" => "left:".(floor(($area_data['area_width']*16+1)/2)-22)."px;bottom:-30px")
            ));
            ?>
        <?endif?>
        <?for($y = ($area_data['y_center']-floor($area_data['area_height']/2));
              $y <= ($area_data['y_center']+floor($area_data['area_height']/2));
              $y++):?>
            <div class="map_row" id="y<?=$y?>">
                <?for($x = ($area_data['x_center']-floor($area_data['area_width']/2));
                      $x <= ($area_data['x_center']+floor($area_data['area_width']/2));
                      $x++):?>
                    <div class="map_cell" id="y<?=$y?>x<?=$x?>" data-y="<?=$y?>" data-x="<?=$x?>"></div>
                <?endfor?>
            </div>
        <?endfor?>
    </div>
</div>