$(document).ready(function(){
    $("#btnreg").click(function(){
        var ajax = {
            id : $("#txtuser").val(),
            password : sha256($("#txtpass").val()),
            username : $("#txtname").val()
        }
        $.ajax({
            url: "http://epapi.test/register",
            type: "POST",
            data: ajax,
            success: function(data, textStatus, jqXHR)
            {
                console.log(data);
                $("#txtuser").val("");
                $("#txtpass").val("");
                $("#txtname").val("");
                $("#txtuser").focus();
                $(".informasi").removeClass("hidden").addClass('alert-success').html(data.message);

            },
            error: function (request, textStatus, errorThrown) {
                $(".informasi").removeClass("hidden").addClass('alert-danger').html(request.responseJSON.message);
            }
        });
    });
});