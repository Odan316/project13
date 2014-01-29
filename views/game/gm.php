<?php
/**
 * @var $this GameController
 * @var $players array Список игроков, с племенами
 * @var $game_data Game
 */
$this->setPageTitle('Проект13 - Кабинет Ведущего');
?>
<div id="b_gm_left">
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
                            'url' => '#',
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
                            'url' => '#',
                            "htmlOptions" => array('class' => "but_gm_tribe_edit")
                        ));
                        ?>
                    </div>
                <? endif; ?>

            </div>
        </div>
    <?endforeach?>
</div>