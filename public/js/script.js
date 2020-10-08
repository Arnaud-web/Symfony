$(document).ready(function(){
    var article = $('#article');
    cacher = $('#cacher'); 
    afficher = $('#afficher');
    // var test = $('.test');

    $(cacher).on('click', function(){
        $(article).hide();
        $(cacher).hide();
        $(afficher).show();
    })

    $(afficher).on('click', function(){
        $(article).show();
        $(cacher).show();
        $(afficher).hide();
    })

    $('.action').each(function(){
        $(this).on('click',function(){
            var a = $(this).parent('.par');
                $(a).children('.test').hide();
                $(a).children('.action_a').show();
                $(this).hide();
            var b = $(a).children('.action_a');
            $(b).on('click',function(){
                $(a).children('.test').show();
                $(a).children('.action').show();
                $(a).children('.action_a').hide();

            })
        })
    })
})