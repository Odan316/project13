$(function(){
    redrawFlags();
});

function redrawFlags()
{
    $(".tribe_flag_medium").each(function(){
        color = $(this).data('color');
        $(this).drawRect({
            fillStyle: color,
            x: 13, y: 10,
            width: 14,
            height: 10,
            fromCenter: true
        }).drawPath({
                strokeStyle: '#000',
                strokeWidth: 2,
                p1: {
                    type: 'line',
                    x1: 6, y1: 22,
                    x2: 6, y2: 5,
                    x3: 19, y3: 5,
                    x4: 19, y4: 14,
                    x5: 6, y5: 14,
                    closed: true
                }
            });
    });
    $(".tribe_flag_small").each(function(){
        color = $(this).data('color');
        $(this).drawRect({
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
        });
    });
}
