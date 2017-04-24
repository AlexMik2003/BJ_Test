$(document).ready(function () {


    $("#add").on("click", function () {
        $("#zakaz").clone().insertBefore("#add");
    });
});
