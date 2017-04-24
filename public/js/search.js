$(document).ready(function () {



    $("#s_id").on("click", function () {
        $("#date").hide();
        $("#s_price").hide();
        $("#simple_search").show();
    });

    $("#s_name").on("click", function () {
        $("#date").hide();
        $("#s_price").hide();
        $("#simple_search").show();
    });

    $("#s_surname").on("click", function () {
        $("#date").hide();
        $("#s_price").hide();
        $("#simple_search").show();
    });

    $("#s_mail").on("click", function () {
        $("#date").hide();
        $("#s_price").hide();
        $("#simple_search").show();
    });

    $("#s_date").on("click", function () {
        $("#simple_search").hide();
        $("#s_price").hide();
        $("#date").show();
    });

    $("#s_sum").on("click", function () {
        $("#simple_search").hide();
        $("#date").hide();
        $("#s_price").show();
    });


});

