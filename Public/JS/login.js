
$(function () {

    $.each($("input"), function () {
        var obj = $(this);
        obj.mouseover(function () {
            obj.addClass('onFocus')
        }).mouseout(function () {
            obj.removeClass('onFocus')
        });
    });
    var checkObj = $("#checkImage");
    checkObj.mouseover(function () {
        checkObj.addClass('onFocus')
    }).mouseout(function () {
        checkObj.removeClass('onFocus')
    });
});

function hideLoginBezel() {
    $("#loginBezel").toggle();
    $("#pointerOpen").toggle();
}
function toLogin(obj) {
    location.href = "index.html";
}