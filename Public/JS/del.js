/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// onclick="javascript:del_sure()"
function del_sure() {

    var del = window.confirm("是否要删除?");

    if (del) {
        // window.location.href = '';
        return true;

    } else {

        return false;

    }
}

function checkSub() {
    /*  密码  */
    var len = $("#ck").val().length;
    if (len < 6 || len <= 0) {
        alert("密码至少 6 位");
        $("#ck").focus($("#ck").val());
        return false;
    }
    if (len > 12) {
        alert("密码至多 12 位");
        $("#ck").focus($("#ck").val());
        return false;
    }

    /*  身份证  */
    var IDcard = $("#IDcard").val().length;
    if (IDcard < 16) {
        alert("身份证号码至少 16 位");
        $("#IDcard").focus($("#IDcard").val());
        return  false;
    }
    if (IDcard > 18) {
        alert("身份证号码至多 18 位");
        $("#IDcard").focus($("#IDcard").val());
        return  false;
    }
    /*  手机电话 */
    var tel = $("#tel").val().length;
    if (tel != 11) {
        alert("手机号码必须是 11 位");
        $("#tel").focus($("#tel").val());
        return false;
    }
}
/* 首字母  */
function xianZM() {
    var zimu = /^[a-zA-Z]+$/;
    if ($('#zimu').val() == "") {
        alert('首字母必须填写');
        return false;
    }
    if (zimu.test($('#zimu').val())) {
        return  true;
    } else {
        alert('首字母只能是字母');
        $('#zimu').focus($('#zimu').val());
        return  false;
    }
}
/*  条形码 */
function xianSZ() {
    var shuzi = /^[0-9]*[1-9][0-9]*$/;
    if ($('#bar').val() == "") {
        alert('条形码必须填写');
        return false;
    }
    if (shuzi.test($('#bar').val())) {
        return  true;
    } else {
        alert('条形码只能是正整数');
        $('#bar').focus($('#bar').val());
        return  false;
    }
}
/* 价格 */
function xianJG() {
    //alert(jia);
    var jiage = /^\d+$/;
    if ($('#jia').val() == "") {
        alert('价格必须填写');
        return false;
    }
    if (jiage.test($('#jia').val())) {
        return  true;
    } else {
        alert('价格只能是整数');
        $('#' + jia).focus($('#' + jia).val());
        return  false;
    }
}

/* 提交验证  */
/* 首字母  */
function xian() {
    var zimu = /^[a-zA-Z]+$/;
    if ($('#zimu').val() == "") {
        alert('首字母必须填写');
        return false;
    }
    if (zimu.test($('#zimu').val())) {
        return  true;
    } else {
        alert('首字母只能是字母');
        $('#zimu').focus($('#zimu').val());
        return  false;
    }


    var shuzi = /^[0-9]*[1-9][0-9]*$/;
    if ($('#bar').val() == "") {
        alert('条形码必须填写');
        return false;
    }
    if (shuzi.test($('#bar').val())) {
        return  true;
    } else {
        alert('条形码只能是正整数');
        $('#bar').focus($('#bar').val());
        return  false;
    }
    return false;

}

/* 
 *  表单验证 
 *  

$().ready(function () {
    $("#valForm").validate({
        debug: true,
        rules: {
            barcode: {
                required: true,
                digits: true,
            },
            product: {
                required: true,
            },
            letter: {
                required: true,
                ziMu: true,
            },
            unit: {
                required: true,
            },
            price: {
                required: true,
                intFloat3: true,
            }
        },
        messages: {
            barcode: {
                required: "请输入条形码",
                maxlength: "长度最长为20位",
                digits: "条形码只能是整数",
            },
            product: {
                required: "请输入商品名称",
            },
            letter: {
                required: "请输入字母",
                ziMu: "请填写英文字母，且字母不分大小写",
            },
            unit: {
                required: "请输入单位",
            },
            price: {
                required: "请输入售价",
                intFloat3: "请输入正确的数字",
            }
        },
        errorPlacement: function (error, element) {   //错误信息位置设置方法  
            error.appendTo(element.parent().next()); //这里的element是录入数据的对象,直接显示在紧随的div中  
        },
    });
});
 */