$(function(){
    redrawFlags();

    /** Map loading */
    window.map = {};
    b_map = $('.b_map');
    window.map.game_id = b_map.data('game-id');
    if(window.map.game_id != undefined){
        window.map.area_width = b_map.data('width');
        window.map.area_height = b_map.data('height');
        window.map.center_x = b_map.data('x');
        window.map.center_y = b_map.data('y');
        window.map.max_y = b_map.data('max-y');
        window.map.max_x = b_map.data('max-x');
        loadMapArea();
    }
    /** Binding Map Events */
    $('#map_left_5').on('click', function(){
        window.map.center_x -= 5;
        loadMapArea();
    });
    $('#map_right_5').on('click', function(){
        window.map.center_x += 5;
        loadMapArea();
    });
    $('#map_up_5').on('click', function(){
        window.map.center_y -= 5;
        loadMapArea();
    });
    $('#map_down_5').on('click', function(){
        window.map.center_y += 5;
        loadMapArea();
    });
});

function loadMapArea()
{
    checkMapCoords();
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
                y_cord = 1;
                x_cord = 1;
                first_y = true;
                y_id = 0;
                for(var y in json){
                    if(first_y){
                        first_y = false;
                        y_id = y;
                    }
                    $("#ycord"+y_cord).text(y);
                    y_cord++;
                }
                for(var x in json[y_id]){
                    $("#xcord"+x_cord).text(x);
                    x_cord++;
                }
                cell_row = 1;
                cell_column = 1;
                for(var y in json){
                    for(var x in json[y]){
                        cell = $('#r'+cell_row+'c'+cell_column);
                        cell.css('background-color', json[y][x].landtype.gfx);
                        cell.data("y", y);
                        cell.data("x", x);
                        cell.html('');
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
                        cell_column++;
                    }
                    cell_column = 1;
                    cell_row++;
                }
                redrawFlags();
                checkMapButtons();
            }
        }
    });
}
function checkMapCoords()
{
    if(window.map.center_x < (window.map.area_width/2)){
        window.map.center_x = Math.floor(0 + (window.map.area_width/2));
    }
    if(window.map.center_x > (window.map.max_x - (window.map.area_width/2))){
        window.map.center_x = Math.ceil(window.map.max_x - (window.map.area_width/2));
    }
    if(window.map.center_y < (window.map.area_height/2)){
        window.map.center_y = Math.floor(0 + (window.map.area_height/2));
    }
    if(window.map.center_y > (window.map.max_y - (window.map.area_height/2))){
        window.map.center_y = Math.ceil(window.map.max_y - (window.map.area_height/2));
    }
}
function checkMapButtons()
{
    $("#map_left_5").hide();
    $("#map_right_5").hide();
    $("#map_up_5").hide();
    $("#map_down_5").hide();
    if(window.map.center_x > (window.map.area_width/2)){
        $("#map_left_5").show();
    }
    if(window.map.center_x < (window.map.max_x - (window.map.area_width/2))){
        $("#map_right_5").show();
    }
    if(window.map.center_y > (window.map.area_height/2)){
        $("#map_up_5").show();
    }
    if(window.map.center_y < (window.map.max_y - (window.map.area_height/2))){
        $("#map_down_5").show();
    }
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
    });
}
