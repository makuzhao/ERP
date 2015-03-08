/* 
 * IE 不兼容 required 元素
 * hmtl页面 input 控件必须写  required='true'
 * 
 */
var testElement = document.createElement('input');
/* 检测浏览器是否支持 required 属性
 * 为不支持 required 属性的浏览器写兼容代码
 */
var requiredSupported = 'required' in testElement && !/Version\/[\d\.]+\s*Safari/i.test(navigator.userAgent);
if (!requiredSupported) {
    document.getElementsByTagName("form")[0].onsubmit = function (e) {
        var inputs = document.getElementsByTagName('input');
        for (var n = 0; n < inputs.length; n++) {
            var input = inputs[n];
            var placeholder = input.placeholder ? input.placeholder : input.getAttribute('placeholder');
            if (!placeholder)
                continue;
            if (!input.value || (input.value === placeholder)) {
                alert(placeholder + ',这里不能为空 ');
                e = e || window.event;
                e.preventDefault && e.preventDefault();
                e.returnValue = false;
                break;
            }
        }
    };
}

