<?php
/**
 * @var $this GameController
 * @var $players array Список игроков, с племенами
 */
$this->setPageTitle('Проект13 - Кабинет Ведущего');
?>
<div id="b_gm_left">
    <h3>Игроки</h3>
    <?
    /* @var $player Users */
    foreach($players as $player):?>
        <div class="b_gm_player">
            <p><?=$player->person->nickname?></p>
        </div>
    <?endforeach?>
</div>