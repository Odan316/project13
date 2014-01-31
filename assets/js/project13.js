$(function(){
    redrawFlags();

    window.map = {};
    b_map = $('.b_map');
    window.map.game_id = b_map.data('game-id');
    if(window.map.game_id != undefined){
        window.map.area_width = b_map.data('width');
        window.map.area_height = b_map.data('height');
        window.map.center_x = b_map.data('x');
        window.map.center_y = b_map.data('y');
        loadMapArea();
    }

});

function loadMapArea()
{
    $.ajax({
        type: "POST",
        async: false,
        url: window.url_root+"project13/game/getAreaInfo",
        dataType: 'json',
        data: {
            "width": window.map.area_width,
            "height": window.map.area_height,
            "center_x": window.map.center_x,
            "center_y": window.map.center_y
        },
        success: function(json){
            if(json && Object.size(json)){
                window.map.cells = json;
                for(var y in json){
                    for(var x in json[y]){
                        cell = $('#y'+y+'x'+x);
                        cell.css('background-color', json[y][x].landtype.gfx);
                        for(var map_object in json[y][x].objects){
                            if(json[y][x].objects[map_object].category == "landobj"){
                                $('<img class="map_object_icon type'+json[y][x].objects[map_object].type+'" />')
                                    .attr('src', json[y][x].objects[map_object].gfx)
                                    .appendTo(cell);
                            } else if(json[y][x].objects[map_object].category == "camp"){
                                $('<canvas>')
                                    .attr({"width":"15", "height":"15"})
                                    .addClass("tribe_flag_small")
                                    .addClass("map_object_canvas")
                                    .data("color", json[y][x].objects[map_object].gfx)
                                    .appendTo(cell);
                            }
                        }
                    }
                }
                redrawFlags();
            }
        }
    });
}
function redrawFlags()
{
    $(".tribe_flag_medium").each(function(){
        color = $(this).data('color');
        $(this)
            .draw({
                fn: function(ctx) {
                    ctx.fillStyle = color;
                    ctx.beginPath();
                    ctx.moveTo(6, 5);
                    ctx.lineTo(20,10);
                    ctx.lineTo(6,15);
                    ctx.fill();
                }
            })
            .drawPath({
                strokeStyle: '#000',
                strokeWidth: 2,
                p1: {
                    type: 'line',
                    x1: 6, y1: 22,
                    x2: 6, y2: 5,
                    closed: true
                }
            });
    });
    $(".tribe_flag_small").each(function(){
        color = $(this).data('color');
        $(this)
            .draw({
                fn: function(ctx) {
                    ctx.fillStyle = color;
                    ctx.beginPath();
                    ctx.moveTo(4, 2);
                    ctx.lineTo(13, 6);
                    ctx.lineTo(4, 10);
                    ctx.fill();
                }
            })
            .drawPath({
                strokeStyle: '#000',
                strokeWidth: 2,
                p1: {
                    type: 'line',
                    x1: 4, y1: 13,
                    x2: 4, y2: 2,
                    closed: true
                }
            });
        /*.drawRect({
            fillStyle: color,
            x: 8, y: 5,
            width: 6,
            height: 4,
            fromCenter: true
        }).drawPath({
            strokeStyle: '#000',
            strokeWidth: 2,
            p1: {
                type: 'line',
                x1: 4, y1: 13,
                x2: 4, y2: 3,
                x3: 11, y3: 3,
                x4: 11, y4: 8,
                x5: 4, y5: 8,
                closed: true
            }
        });*/
    });
}
