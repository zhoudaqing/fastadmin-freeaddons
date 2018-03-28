define([],function(){
    var Properties={
        formatter:{
            property: function (value, row, index) {
                return '<div class="input-group" style="width:100%"><input type="text" class="form-control input-sm" value="'+value+'" ></div>';
                    },
            operate:function(value,row,index){
                return '\<div class="btn-group btn-group-justified">\
                        <span class="btn btn-default property_drag" data-index="'+index+'" data-direction="up"><i class="fa fa-arrow-up" aria-hidden="true"></i></span>\
                        <span class="btn btn-default property_drag" data-index="'+index+'" data-direction="down"><i class="fa fa-arrow-down" aria-hidden="true"></i></span>\
                        <span class="btn btn-danger property_del" data-index="'+index+'"><i class="fa fa-times" aria-hidden="true"></i></span></div>';
            }
        },
        //初始化表格
        init: function(obj) {
            obj.hide();
            obj.before("<style>\
                .swap_done{animation-name: swap;animation-duration: 1s;animation-timing-function: ease-out;animation-direction: alternate;animation-iteration-count: 1;animation-fill-mode: backwards;animation-play-state: running;}\
                @keyframes swap{0%,100%{background-color:#f1f4f6}50%{background-color:#ddd}}\
            </style>")
               .before('<table class="properties"></table>')
               .before('<button type="button" class="btn btn-success btn-embossed property_add" style="margin-top:15px;">添加参数</button>');
            var table=obj.parent().find("table.properties")

            if(obj.val()!=""){
                Properties.create(table);
                table.bootstrapTable('load',JSON.parse(obj.val()));
            }
            
            return table;
        },
        //创建表头,若存在则添加一行
        create:function(table){
            if(table.find("tbody").size()==0){
                table.bootstrapTable({
                    showHeader:true,
                    mobileResponsive:true,
                    columns: [{
                        field: 'name',
                        title: '参数名',
                        class: 'property_input',
                        formatter:Properties.formatter.property
                    }, {
                        field: 'value',
                        title: '参数值',
                        class: 'property_input',
                        formatter:Properties.formatter.property
                    }, 
                    {
                        field: 'operate',
                        title: '操作',
                        width: '150px',
                        formatter:Properties.formatter.operate
                    }],
                    data:[{name:'',value:''}]

                });
            }else{
                Properties.append(table);
            }
        },
        //添加
        append:function(table){
            var property={
                    name:'',
                    value:''
                }
            table.bootstrapTable('append',property)
        },
        //删除
        remove:function(table,index){
            var data=table.bootstrapTable('getData',true);
            data.splice(index,1),
            table.bootstrapTable('load',data)
            Properties.save(table);
        },
        //排序
        sort:function(table,index,direction){
            var data=table.bootstrapTable('getData',true);

            switch(direction){
                case 'up':
                    if(index>0){
                        table.bootstrapTable('load',Properties.swap(data,index,index-1));
                        table.find("tr[data-index="+(index-1)+"]").addClass('swap_done')
                    }else{
                        layer.tips('已经到达顶部',
                                    table.find(".property_drag[data-index="+index+"][data-direction="+direction+"]"),
                                    {
                                        tips:1
                                    });
                    }
                    break;
                case 'down':
                    if(index<data.length-1){
                        table.bootstrapTable('load',Properties.swap(data,index,index+1));
                        table.find("tr[data-index="+(index+1)+"]").addClass('swap_done')
                    }else{
                        layer.tips('已经到达底部',
                                    table.find(".property_drag[data-index="+index+"][data-direction="+direction+"]"),
                                    {
                                        tips:1
                                    });
                    }
                    break;
            }

            Properties.save(table);
        },

        //设置属性
        set:function(table,index){
            
            var property_input=table.find("tr[data-index="+index+"] td.property_input");
            var property={
                name:property_input.first().find("input").val(),
                value:property_input.last().find("input").val()
            }
            table.bootstrapTable('updateRow', {
                index: index,
                row: property
            })
            
            Properties.save(table);
        },

        //保存
        save:function(table){
            table.parents(".bootstrap-table")
                 .siblings('textarea').val(JSON.stringify(table.bootstrapTable('getData',true)))
        },

        //交换排序
        swap:function(data, old_pos, new_pos) {
            data[old_pos] = data.splice(new_pos, 1, data[old_pos])[0];
            return data;
        }
    }

    return Properties;
});