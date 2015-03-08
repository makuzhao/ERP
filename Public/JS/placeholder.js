/*
 * jQuery placeholder, fix for IE6,7,8,9
 * @author JENA
 * @since 20131115.1504
 * @website ishere.cn
 */

/*
 $(function () {
 // -- Constants --
 var PLACE_HOLDER_COLOR = "#847d7a"; // "darkGrey" does not work in IE6
 var PLACE_HOLDER_DATA_NAME = "original-font-color";
 
 // -- Util Methods --  
 var getContent = function (element) {
 return $(element).val();
 }
 
 var setContent = function (element, content) {
 $(element).val(content);
 }
 
 var getPlaceholder = function (element) {
 return $(element).attr("placeholder");
 }
 
 var isContentEmpty = function (element) {
 var content = getContent(element);
 return (content.length === 0) || content == getPlaceholder(element);
 }
 
 var setPlaceholderStyle = function (element) {
 $(element).data(PLACE_HOLDER_DATA_NAME, $(element).css("color"));
 $(element).css("color", PLACE_HOLDER_COLOR);
 }
 
 var clearPlaceholderStyle = function (element) {
 $(element).css("color", $(element).data(PLACE_HOLDER_DATA_NAME));
 $(element).removeData(PLACE_HOLDER_DATA_NAME);
 }
 
 var showPlaceholder = function (element) {
 setContent(element, getPlaceholder(element));
 setPlaceholderStyle(element);
 }
 
 var hidePlaceholder = function (element) {
 if ($(element).data(PLACE_HOLDER_DATA_NAME)) {
 setContent(element, "");
 clearPlaceholderStyle(element);
 }
 }
 
 // -- Event Handlers --
 var inputFocused = function () {
 if (isContentEmpty(this)) {
 hidePlaceholder(this);
 }
 }
 
 var inputBlurred = function () {
 if (isContentEmpty(this)) {
 showPlaceholder(this);
 }
 }
 
 var parentFormSubmitted = function () {
 if (isContentEmpty(this)) {
 hidePlaceholder(this);
 }
 }
 
 // -- Bind event to components --
 $("textarea, input[type='text']").each(function (index, element) {
 if ($(element).attr("placeholder")) {
 $(element).focus(inputFocused);
 $(element).blur(inputBlurred);
 $(element).bind("parentformsubmitted", parentFormSubmitted);
 
 // triggers show place holder on page load
 $(element).trigger("blur");
 // triggers form submitted event on parent form submit
 $(element).parents("form").submit(function () {
 $(element).trigger("parentformsubmitted");
 });
 }
 });
 });
 
 */

$(function () {
    var yanse = "#847d7a";
    //判断浏览器是否支持placeholder属性
    supportPlaceholder = 'placeholder'in document.createElement('input'),
            placeholder = function (input) {

                var text = input.attr('placeholder');
                var defaultValue = input.defaultValue;

                if (!defaultValue) {
                    input.val(text).css("color", yanse);
                }

                input.focus(function () {

                    if (input.val() == text) {

                        $(this).val("");
                    }
                });


                input.blur(function () {

                    if (input.val() == "") {

                        //  $(this).val(text).addClass("phcolor");
                        $(this).val(text).css("color", yanse);
                    }
                });

                //输入的字符不为灰色
                input.keydown(function () {
                    // input.css("color", "#000");
                });
            };

    //当浏览器不支持placeholder属性时，调用placeholder函数
    if (!supportPlaceholder) {

        $('input').each(function () {

            text = $(this).attr("placeholder");

            if ($(this).attr("type") == "text") {
                placeholder($(this));
            }
            if ($(this).attr("type") == "password") {
                $(this).attr("type", "text");
                placeholder($(this));
            }
        });
    }

});