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
                <div
                    class="b_gm_tribe_info"
                    data-tribe-tag="<?=$player['tribe_tag']?>"
                    data-player-id="<?=$user_id?>"
                    >
                    <? if($player['tribe_tag'] !== null): ?>
                        <canvas width='25' height='25' class="tribe_flag_medium"
                                data-color="<?=$game_data->tribes[$player['tribe_tag']]->color?>"></canvas>
                        <div class="b_gm_tribe_name">
                            <?=$game_data->tribes[$player['tribe_tag']]->name?>
                    <? else: ?>
                        <div class="tribe_flag_medium_pholder"></div>
                        <div class="b_gm_tribe_name">
                            Нет
                    <? endif; ?>
                            <?
                            $this->widget('bootstrap.widgets.TbButton',array(
                                'label' => 'Доб./Ред.',
                                'type' => 'secondary',
                                'size' => 'small',
                                "htmlOptions" => array(
                                    'class' => "but_gm_tribe_edit"
                                )
                            ));
                            ?>
                        </div>
                </div>
            </div>
        <?endforeach?>
    </div>
</div>
<div id="b_gm_block_map">
    <h4>Карта</h4>
    <?
    $this->widget('P13MapArea', array(
        'area_data' => $area_data,
        'game_data' => $game_data
    ));
    ?>
</div>
<div class="edit_tribe">
    <?=CHtml::hiddenField("player_id")?>
    <div class="b_new_tribe_set">
        <p>Тэг:</p>
        <?=CHtml::textField("tribe_tag")?>
    </div>
    <p>Имя:</p>
    <?=CHtml::textField("tribe_name")?>
    <p>Цвет:</p>
    <?=CHtml::textField("tribe_color")?>
    <div class="b_new_tribe_set">
        <p>Координаты первой общины:</p>
        x:<?=CHtml::textField("tribe_start_x", "", array("style" => "width:60px;"))?>
        y:<?=CHtml::textField("tribe_start_y", "", array("style" => "width:60px;"))?>
    </div>
    <?
    $this->widget('bootstrap.widgets.TbButton',array(
        'label' => 'Сохранить',
        'type' => 'secondary',
        'size' => 'small',
        "htmlOptions" => array(
            'class' => "but_gm_tribe_save"
        )
    ));
    ?>
</div>