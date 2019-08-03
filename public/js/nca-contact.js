$(function() {
    "use strict";

    $("#nca-form").submit(function (e) {
        e.preventDefault();
        var url = "/api/contact"; // the script where you handle the form input.

        var data = JSON.stringify(
            {
                "email": $('#email').val(),
                "newsletter": $('#newsletter').val(),
                "name": $("#name").val(),
                "phone": $("#phone").val(),
                "message": $("#message").val()
            }
        );

        $.ajax({
            type: "POST",
            url: url,
            contentType: "application/json",
            dataType: "json",
            data: data,
        }).done(function (data) {
            console.log('done', data);
            $("#nca-form").html("<div class='successMessage'>Danke wir melden uns</div>");
        }).fail(function (data) {
            let returnedData = JSON.parse(data.responseText);
            console.log('fail', data, returnedData);

            returnedData["notValidFields"].forEach(function (element) {
                console.log('element', element);
                $("#" + element).addClass('nca-contact-error');
            })
        });

        return false;
    });

    $("#hide, .show-nca, .nca-contact").click(function(e) {
        if ($(".show-nca").is(":visible")) {
            $(".show-nca").animate({
                "margin-right": "-400px"
            }, 500, function() {
                $(this).hide();
            });

            $("#switch").animate({
                "margin-right": "0px"
            }, 500).show();
        } else {
            console.log('case 2');
            $("#switch").animate({
                "margin-right": "-400px"
            }, 500, function() {
                $(this).hide();
            });
            $(".show-nca").show().animate({
                "margin-right": "0"
            }, 500);
        }
    });
});
