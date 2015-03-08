/*
 * 如何表单中出现相同的id="",错误信息无法显示，只能删除required中相同的id
 */
/* 
 *  商品信息验证
 */
$().ready(function () {
    $("#valForm").validate({
        //   debug: true,
        /* 字段认证规则 */
        rules: {
            /* 条形码 */
            barcode: {
                required: true,
                digits: true,
            },
            /* 商品 */
            product: {
                required: true,
            },
            /* 首字母 */
            letter: {
                required: true,
                ziMu: true,
            },
            /* 计量单位 */
            unit: {
                required: true,
            },
            /* 预售价格 */
            price: {
                required: true,
                numFormat: true,
            },
        },
        /* 提示信息 */
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
                numFormat: "请输入正确的数字",
            },
        },
        errorPlacement: function (error, element) {   //错误信息位置设置方法  
            error.appendTo(element.parent().next()); //这里的element是录入数据的对象,直接显示在紧随的div中  
        },
        submitHandler: function (form) {
            //alert("成功");
            form.submit();
        },
    });
});
/* 商家验证  */
$().ready(function () {
    $("#comForm").validate({
        /* 字段认证规则 */
        rules: {
            /* 商家 */
            company: {
                required: true,
            },
            /* 企业法人 */
            boss: {
                required: true,
            },
            /* 法人电话 */
            bostel: {
                required: true,
                shouJi: true,
            },
            /* 联系人 */
            people: {
                required: true,
            },
            /* 联系人电话 */
            peotel: {
                required: true,
                shouJi: true,
            },
        },
        /* 提示信息 */
        messages: {
            /* 商家 */
            company: {
                required: "请输入商家名称",
            },
            /* 企业法人 */
            boss: {
                required: "请输入法人名称",
            },
            /* 法人电话 */
            bostel: {
                required: "请输入电话号码",
                shouJi: "请输入合法的手机号码",
            },
            /* 联系人 */
            people: {
                required: "请输入联系人",
            },
            /* 联系人电话 */
            peotel: {
                required: "请输入电话号码",
                shouJi: "请输入合法的手机号码",
            },
        },
        errorPlacement: function (error, element) {   //错误信息位置设置方法  
            error.appendTo(element.parent().next()); //这里的element是录入数据的对象,直接显示在紧随的div中  
        },
        submitHandler: function (form) {
            //alert("成功");
            form.submit();
        },
    });
});

/* 老板、销售员验证  */
$().ready(function () {
    $("#userForm").validate({
        /* 字段认证规则 */
        rules: {
            /* 用户 */
            name: {
                required: true,
                userName: true,
            },
            /* 密码 6--18 */
            xcpwd: {
                required: true,
                minlength: 6,
                maxlength: 18, // 输入长度最多是5的字符串(汉字算一个字符)
            },
            /* 真实姓名 */
            real: {
                required: true,
            },
            /* 联系人 */
            IDcard: {
                required: true,
                IDcard: true,
                minlength: 16,
                maxlength: 18, // 输入长度最多是5的字符串(汉字算一个字符)
            },
            card: {
                required: true,
                IDcard: true,
                minlength: 16,
                maxlength: 18, // 输入长度最多是5的字符串(汉字算一个字符)
            },
            /* 电话 */
            tel: {
                required: true,
                shouJi: true,
            },
            /* 地址 */
            address: {
                required: true,
            },
            /* 城市 */
            city: {
                required: true,
            },
        },
        /* 提示信息 */
        messages: {
            /* 用户 */
            name: {
                required: "请输入用户名 ",
                userName: "请使用中文、英文、下划线、数字混合",
            },
            /* 密码 6--18 */
            xcpwd: {
                required: "请输入密码 6-18 位",
                minlength: "密码至少 6 位",
                maxlength: "密码最多 18 位", // 输入长度最多是5的字符串(汉字算一个字符)
            },
            /* 真实姓名 */
            real: {
                required: "请输入用户真实姓名 ",
            },
            /* 身份证 */
            IDcard: {
                required: "请输入用户身份证 ",
                IDcard: "请输入合法身份证",
                minlength: "身份证至少 16位 ",
                maxlength: "身份证至少 18位 ", // 输入长度最多是5的字符串(汉字算一个字符)
            },
            card: {
                required: "请输入用户身份证 ",
                IDcard: "请输入合法身份证",
                minlength: "身份证至少 16位 ",
                maxlength: "身份证至少 18位 ", // 输入长度最多是5的字符串(汉字算一个字符)
            },
            /* 电话 */
            tel: {
                required: "请输入用户手机号 ",
                shouJi: "请输入合法的手机号码",
            },
            /* 地址 */
            address: {
                required: "请输入用户地址 ",
            },
            /* 城市 */
            city: {
                required: "请输入身份、市区 ",
            },
        },
        errorPlacement: function (error, element) {   //错误信息位置设置方法  
            error.appendTo(element.parent().next()); //这里的element是录入数据的对象,直接显示在紧随的div中  
        },
        submitHandler: function (form) {
            //alert("成功");
            form.submit();
        },
    });
});
/*   Manager  模块结束      */

/* 店铺验证  */
$().ready(function () {
    $("#shopForm").validate({
        /* 字段认证规则 */
        rules: {
            /* 店铺名 */
            shop: {
                required: true,
            },
            /* 地址 */
            address: {
                required: true,
            },
            /* 真实姓名 */
            real: {
                required: true,
            },
            /* 负责人 */
            people: {
                required: true,
            },
            /* 电话 */
            tel: {
                required: true,
                shouJi: true,
            },
        },
        /* 提示信息 */
        messages: {
            /* 店铺名 */
            shop: {
                required: "请输入店铺名 ",
            },
            /* 地址 */
            address: {
                required: "请输入店铺地址 ",
            },
            /* 负责人 */
            people: {
                required: "请输入负责人名称 ",
            },
            /* 电话 */
            tel: {
                required: "请输入负责人电话 ",
                shouJi: "请输入合法的手机号",
            },
        },
        errorPlacement: function (error, element) {   //错误信息位置设置方法  
            error.appendTo(element.parent().next()); //这里的element是录入数据的对象,直接显示在紧随的div中  
        },
        submitHandler: function (form) {
            //alert("成功");
            form.submit();
        },
    });
});

/* 收费验证  */
$().ready(function () {
    $("#freeForm").validate({
        debug: true,
        /* 字段认证规则 */
        rules: {
            /* 使用 */
            freemonth: {
                required: true,
                feiLingZ: true
            },
            tollmonth: {
                required: true,
                feiLingZ: true
            },
            tollevery: {
                required: true,
                numFormat: true
            }
        },
        /* 提示信息 */
        messages: {
            /* 使用 */
            freemonth: {
                required: "试用期限不能为空",
                feiLingZ: "请输入非零正整数"
            },
            tollmonth: {
                required: "收费期限不能为空",
                feiLingZ: "请输入非零正整数"
            },
            tollevery: {
                required: "每月收费不能为空",
                numFormat: "请输入合法的数据"
            }
        },
        errorPlacement: function (error, element) {   //错误信息位置设置方法  
            error.appendTo(element.parent().next()); //这里的element是录入数据的对象,直接显示在紧随的div中  
        },
        submitHandler: function (form) {
            //alert("成功");
            form.submit();
        },
    });
});