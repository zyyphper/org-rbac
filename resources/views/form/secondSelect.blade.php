<style type="text/css">
.cascader-box {
position: relative;
font-size: 15px;
color: #555;
}

.cascader-area {
    width: 100%;
    height: 172px;
    box-shadow: 0 0 13px #ccc;
    position: absolute;
    background: #fff;
    left: 7.5px;
    top: 40px;
    z-index: 10;
}

.cascader-div {
position: absolute;
top: 0px;
/*left: 0px;*/
    width: 50%;
border: 1px solid #c0c4cc;
background: #fff;
min-height: 150px;
z-index: 10;
height: 100%;
overflow: auto;
}

.cascader-item {
padding: 0 10px;
height: 35px;
line-height: 35px;
cursor: pointer;
position: relative;
}

.cascader-item:hover {
background: #e3e3e3;
}

.cascader-item-action {
background: #e3e3e3 !important;
}

.select-icon-invert {
    transform: rotate(180deg);margin-top: 5px;
}

.ci-arrow {
position: absolute;
top: 10px;
right: 10px;
border: 2px solid #999;
width: 8px;
height: 8px;
border-left: none;
border-bottom: none;
transform: rotate( 45deg);
}

.cascader-scrollbar {
scrollbar-face-color: #fcfcfc;
scrollbar-highlight-color: #6c6c90;
scrollbar-shadow-color: #fcfcfc;
scrollbar-3dlight-color: #fcfcfc;
scrollbar-arrow-color: #240024;
scrollbar-track-color: #fcfcfc;
scrollbar-darkshadow-color: #48486c;
scrollbar-base-color: #fcfcfc
}

/* 设置滚动条的样式 */
::-webkit-scrollbar {
width: 6px;
}
/* 滚动竖线 */
::-webkit-scrollbar-track {
border-radius: 6px;
}
/* 滚动条滑块 */
::-webkit-scrollbar-thumb {
border-radius: 6px;
background: rgba(0,0,0,0.1);
}
::-webkit-scrollbar-thumb:window-inactive {
background: rgba(255,0,0,0.4);
}

</style>

<div {!! admin_attrs($group_attrs) !!} >

<label for="{{$id}}" class="{{$viewClass['label']}} col-form-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">
        <input type="hidden" name="{{$name}}"/>
        <div class="{{$class}}">
            <div class="cascader-box-{{$loadData['class']}}">
                <span class="select2 select2-container select2-container--default select2-container--below select2-container--open" dir="ltr"  style="width: 100%;">
                    <span class="selection">
                        <span class="select2-selection select2-selection--single" role="combobox">
                            <span class="select2-selection__rendered select2-{{$loadData['class']}}-container" {!! $attributes !!}>
                            </span>
                            <span class="select2-selection__arrow select-icon-invert">
                                <b role="presentation"></b>
                            </span>
                        </span>
                    </span>
                </span>
                <div class="cascader-area" style="display:none;">
                    <div class="area-left" style="width: 50%;float: left;"></div>
                    <div class="area-right" style="width: 50%;float: right;"></div>
                </div>
            </div>
        </div>

        @include('admin::form.error')
        @include('admin::form.help-block')

    </div>
</div>

<script>

    $(function () {
        var jsonArr = JSON.parse(@json($loadData["data"]))

        function init(that) {
            var obj = $(that).find(".select2-{{$loadData['class']}}-container")
            var val = obj.attr('data-value')
            var chooseVal = false
            if (val && val !== 'undefined') {
                chooseVal = true
                $(that).closest('.form-group').find('input:hidden').val(val)
            } else {
                obj.empty().html('<span class="select2-selection__placeholder">请选择</span>')
            }
            var dom = $('<div></div>');
            dom.addClass("cascader-div cascader-scrollbar");
            //获取数据
            var json = jsonArr;

            for (var i = 0; i < json.length; i++) {
                var data = json[i];
                var option = $('<div><span></span></div>');
                option.addClass("cascader-item");
                option.attr('data-value', data.value);
                option.attr('data-index', i);
                option.attr('data-type', 1);
                $("span", option).eq(0).html(data.text);
                dom.append(option);
                if (chooseVal) {
                    title = loadDefaultData(that,i,val)
                    if (title !== false) {
                        chooseVal = false
                        obj.empty().html('<span class="select2-selection__rendered">'+title+'</span>')
                        option.addClass("cascader-item-action");
                    }
                }
            }
            $(".area-left").append(dom);
        }

        function loadDefaultData(that,index,val) {
            var title = '';
            var choose = false;
            var dom = $('<div></div>');
            dom.addClass("cascader-div cascader-scrollbar");
            var json = jsonArr[index].children;
            console.log(json)

            for (var i = 0; i < json.length; i++) {
                var data = json[i];
                var option = $('<div><span></span></div>');
                option.addClass("cascader-item");
                if (data.value == val) {
                    choose = true
                    option.addClass("cascader-item-action");
                    title = data.text
                }
                console.log(data.value)
                option.attr('data-value', data.value);
                option.attr('data-type', 2);
                $("span", option).eq(0).html(data.text);
                dom.append(option);
            }
            if (choose) {
                console.log(title)
                $(that).find(".area-right").append(dom);
                return title
            }
            return false
        }


        function loadData(that,index) {
            console.log(that)
            var dom = $('<div></div>');
            dom.addClass("cascader-div cascader-scrollbar");
            var json = jsonArr[index].children;

            for (var i = 0; i < json.length; i++) {
                var data = json[i];
                var option = $('<div><span></span></div>');
                option.addClass("cascader-item");
                option.attr('data-value', data.value);
                option.attr('data-type', 2);
                $("span", option).eq(0).html(data.text);
                dom.append(option);
            }
            console.log(dom)
            $(that).closest('.cascader-area').find(".area-right").append(dom);

        }

        function submit(that)
        {
            var title = $(that).find("span").text()
            var val = $(that).attr('data-value')
            var group = $(that).closest('.form-group')
            group.find(".select2-{{$loadData['class']}}-container").empty().html('<span class="select2-selection__rendered">'+title+'</span>')
            group.find(".cascader-box-{{$loadData['class']}}").attr('data-value',val)
            group.find('input:hidden').val(val)
            group.find(".select2-{{$loadData['class']}}-container").attr('data-flag','close')
            group.find(".cascader-area").hide();
            group.find(".select2-selection__arrow").removeClass("select-icon-invert").addClass("select-icon-invert");
        }



        $.admin.initialize('.duties.department_id', function () {
            $(this).addClass('initialized');
            init(this)
            var selectObj = $(this).find(".select2-{{$loadData['class']}}-container")
            selectObj.click(function () {
                var flag = $(this).attr('data-flag');
                var that = $(this).closest('.form-group')
                if (flag == undefined || flag == "close") {
                    flag = "open";
                    that.find(".cascader-area").show();
                    that.find(".select2-selection__arrow").removeClass("select-icon-invert")
                }
                else {
                    flag = "close";
                    that.find(".cascader-area").hide();
                    that.find(".select2-selection__arrow").removeClass("select-icon-invert").addClass("select-icon-invert");
                }
                $(this).attr('data-flag', flag);
            });
            $(this).on("click", ".cascader-item", function () {
                var type = parseInt($(this).attr('data-type'));
                var index = $(this).attr('data-index');
                $(this).siblings().removeClass("cascader-item-action")
                $(this).addClass("cascader-item-action");
                if (type === 1) {
                    console.log('选择左边'+index);
                    loadData(this,index)
                } else {
                    //选中值 赋值操作
                    submit(this)
                }
            });
        });



    })
</script>

