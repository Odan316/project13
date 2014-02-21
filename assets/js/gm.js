$(function(){
    $('.but_gm_tribe_edit').on('click', function(){
        $(this).parents('#b_gm_players').find('.edit_tribe').detach();
        $('.but_gm_tribe_edit').show();
        $(this).hide();
        tribe_info = $(this).parents('.b_gm_tribe_info');
//        if(tribe_info.data('tribe-tag') == ""){
//            tribe_info.find(".b_new_tribe_set").hide();
//        }
        $('.edit_tribe').clone().appendTo(tribe_info).show()
            .find('input[name=player_id]').val(tribe_info.data("player-id"));
        if(tribe_info.data('tribe-tag') != ""){
            tribe_info.find(".b_new_tribe_set").hide();
        }
    });
    $(document).on('click', '.but_gm_tribe_save', function(){
        tribe_form = $(this).parent('.edit_tribe');
        saveTribe(tribe_form);
        $(this).parents('.b_gm_tribe_info').find('.but_gm_tribe_edit').show();
        tribe_form.detach();
    })
});

function saveTribe(tribe_form){
    $.ajax({
        type: "POST",
        async: false,
        url: window.url_root+"project13/game/GMSaveTribe",
        dataType: 'json',
        data: {
            "player_id": tribe_form.find('input[name=player_id]').val(),
            "tribe_tag": tribe_form.find('input[name=tribe_tag]').val(),
            "tribe_name": tribe_form.find('input[name=tribe_name]').val(),
            "tribe_color": tribe_form.find('input[name=tribe_color]').val(),
            "tribe_start_x": tribe_form.find('input[name=tribe_start_x]').val(),
            "tribe_start_y": tribe_form.find('input[name=tribe_start_y]').val()
        },
        success: function(data){
            if(data.result == true){
                location.reload();
            }
            else{
                alert("Не удалось сохранить Племя");
            }
        }
    });
}