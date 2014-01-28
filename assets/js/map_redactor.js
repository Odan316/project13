$(function(){
    $('#create_blank_map').on('click', function(){
        $('#b_blank_map_creation').show();
    });
    $('#create_default_map').on('click', function(){
        $('#b_blank_map_creation').show();
    });

    window.map_redactor = new Object();
    window.map_redactor.game_id = $('#redactor_map').data('game-id');
    if(window.map_redactor.game_id != undefined){
        redactorMapLoad();
    }
    $(document).on('mouseenter', '.object_type_row, .object_gfx', function(){
        $(this).css('background-color', '#6495ED');
    });
    $(document).on('mouseleave', '.object_type_row, .object_gfx', function(){
        $(this).css('background-color', 'transparent');
    });

    $('.object_type_row').on('click', function(){
        $('.object_type_row').css({'border': 'none', 'margin': '1px 0', 'padding' : '1px'});
        $(this).css({'border': '2px solid #A52A2A', 'margin': '0', 'padding' : '0'});
        $('#object_gfx_list').html('');
        window.map_redactor.object_gfx_current = undefined;
        window.map_redactor.gfx_current = undefined;
        if($(this).data('category') != 'eraser'){
            window.map_redactor.eraser_current = false;
            window.map_redactor.object_type_current = $(this).data('type');
            window.map_redactor.object_category_current = $(this).data('category');
            $.ajax({
                type: "POST",
                async: false,
                url: window.url_root+"project13/game/getMapObjectGFXs",
                dataType: 'json',
                data: { 'map_object_type': window.map_redactor.object_type_current},
                success: function(json){
                    if(json){
                        if(Object.size(json)){
                            for(var key in json){
                                if(window.map_redactor.object_category_current == 'landobj'){
                                    $('<img class="object_gfx_icon" />')
                                        .attr('src', json[key]).data('gfx', key)
                                        .appendTo('#object_gfx_list');
                                } else if(window.map_redactor.object_category_current == 'landtype'){
                                    $('<div class="object_gfx_icon"></div>')
                                        .css('background-color', json[key]).data('gfx', key)
                                        .appendTo('#object_gfx_list');
                                }
                                if(Object.size(json) == 1){
                                    window.map_redactor.object_gfx_current = key;
                                    window.map_redactor.gfx_current = json[key];
                                    $('#object_gfx_list')
                                        .find('.object_gfx_icon')
                                        .css({'border': '2px solid #A52A2A', 'margin' : '1px'});
                                }
                            }
                        } else {
                            $('#object_gfx_list').html('Для такого типа вариантов нет');
                        }
                    } else{
                        $('#object_gfx_list').html('Тип не найден');
                    }
                }
            });
        } else{
            window.map_redactor.eraser_current = true;
            window.map_redactor.object_type_current = undefined;
            window.map_redactor.object_category_current = undefined;
        }

    });

    $(document).on('click', '.object_gfx_icon', function(){
        $('.object_gfx_icon').css({'border':'1px solid grey', 'margin': '2px'});
        $(this).css({'border': '2px solid #A52A2A', 'margin' : '1px'});
        window.map_redactor.object_gfx_current = $(this).data('gfx');
        if(window.map_redactor.object_category_current == 'landtype'){
            window.map_redactor.gfx_current = $(this).css('background-color');
        } else{
            window.map_redactor.gfx_current = $(this).attr('src');
        }

    });

    $(document).on('mouseenter', '.map_cell', function(){
        $(this).css({'outline': '2px solid red', 'z-index' : 10});
    });
    $(document).on('mouseleave', '.map_cell', function(){
        $(this).css({'outline': 'none', 'z-index': 1});
    });

    $(document).on('click', '.map_cell', function(){
        y = $(this).data('y');
        x = $(this).data('x');
        if(window.map_redactor != undefined &&
            window.map_redactor.object_type_current != undefined &&
            window.map_redactor.object_category_current != undefined &&
            window.map_redactor.object_gfx_current != undefined &&
            window.map_redactor.gfx_current != undefined)
        {
            if(window.map_redactor.object_category_current == 'landtype'){
                window.map_redactor.map[y][x].landtype.type = window.map_redactor.object_type_current;
                window.map_redactor.map[y][x].landtype.obj_gfx = window.map_redactor.object_gfx_current;
                window.map_redactor.map[y][x].landtype.gfx = window.map_redactor.gfx_current;
                $(this).css('background-color', window.map_redactor.gfx_current);
            } else {
                if(window.map_redactor.map[y][x].objects[window.map_redactor.object_type_current] != undefined){
                    $(this).find(".type"+window.map_redactor.object_type_current).remove();
                } else {

                }

                map_object = new Object();
                map_object.category = window.map_redactor.object_category_current;
                map_object.type = window.map_redactor.object_type_current;
                map_object.obj_gfx = window.map_redactor.object_gfx_current;
                map_object.gfx = window.map_redactor.gfx_current;
                map_object.id = null;
                window.map_redactor.map[y][x].objects[window.map_redactor.object_type_current] = map_object;
                delete map_object;

                $('<img class="map_object_icon type'+window.map_redactor.object_type_current+'" />')
                    .attr('src', window.map_redactor.gfx_current)
                    .css('z-index', window.map_redactor.object_type_current)
                    .appendTo(this);
            }

        }
        else if(window.map_redactor != undefined &&
            window.map_redactor.eraser_current != undefined &&
            window.map_redactor.eraser_current){
            $(this).find("img").remove();
            window.map_redactor.map[y][x].objects = [];
        }
    });

    $(document).on('click', '#save_map', function(){
        $.ajax({
            type: "POST",
            async: false,
            url: window.url_root+"project13/game/saveMap",
            dataType: 'json',
            data: { 'map_data': $.toJSON(window.map_redactor.map) },
            success: function(data){
                if(data == 1){
                    alert('Успешно сохранено!');
                } else {
                    alert('Сохранение не удалось!');
                }
            }
        });
    });
});

function redactorMapLoad(){
    $.ajax({
        type: "POST",
        async: false,
        url: window.url_root+"project13/game/getFullMapInfo",
        dataType: 'json',
        data: {},
        success: function(json){
            if(json && Object.size(json)){
                window.map_redactor.map = json;
                for(var y in json){
                    for(var x in json[y]){
                        cell = $('#y'+y+'x'+x);
                        cell.css('background-color', json[y][x].landtype.gfx);
                        for(var map_object in json[y][x].objects){
                            $('<img class="map_object_icon type'+json[y][x].objects[map_object].type+'" />')
                                .attr('src', json[y][x].objects[map_object].gfx)
                                .appendTo(cell);
                        }
                    }
                }
            }
        }
    });
}