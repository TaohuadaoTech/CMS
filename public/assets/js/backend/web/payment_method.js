define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'web/payment_method/index' + location.search,
                    add_url: 'web/payment_method/add',
                    edit_url: 'web/payment_method/edit',
                    del_url: 'web/payment_method/del',
                    multi_url: 'web/payment_method/multi',
                    import_url: 'web/payment_method/import',
                    table: 'web_payment_method',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'id', title: __('Id')},
                        {field: 'index', title: __('Index')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        // {field: 'image', title: __('Image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'channel', title: __('Channel'), searchList: Config.channelList, formatter: Table.api.formatter.normal},
                        // {field: 'pay_param_one', title: __('Pay_param_one'), operate: 'LIKE'},
                        // {field: 'pay_param_two', title: __('Pay_param_two'), operate: 'LIKE'},
                        // {field: 'pay_param_three', title: __('Pay_param_three'), operate: 'LIKE'},
                        // {field: 'qrcode_image', title: __('Qrcode_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status_Enable'),"0":__('Status_Disable')}, formatter: Table.api.formatter.toggle},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $("form[role=form]").data('validator-options', {
                    ignore: ':hidden'
                });
                $(document).on('change', 'select.channel-select', function (event) {
                    var form = $(event.currentTarget).parents('form.form-horizontal');
                    var channel = $(event.currentTarget).val();
                    var param_one = param_two = param_three = '';
                    if (channel === 'Alipay') {
                        param_one = __('Pay_param_one_Alipay');
                        param_two = __('Pay_param_two_Alipay');
                        param_three = __('Pay_param_three_Alipay');
                    } else if (channel === 'Wechatpay') {
                        param_one = __('Pay_param_one_Wechatpay');
                        param_two = __('Pay_param_two_Wechatpay');
                        param_three = __('Pay_param_three_Wechatpay');
                    } else if (channel === 'Epay') {
                        param_one = __('Pay_param_one_EPay');
                        param_two = __('Pay_param_two_EPay');
                        param_three = __('Pay_param_three_EPay');
                    } else {
                        form.find('div.auto-pay').addClass('hidden');
                        form.find('div.manually-pay').removeClass('hidden');
                        return true;
                    }
                    form.find('div.auto-pay').removeClass('hidden');
                    form.find('div.manually-pay').addClass('hidden');
                    form.find('label.param-one').text(param_one + ':');
                    form.find('label.param-two').text(param_two + ':');
                    form.find('label.param-three').text(param_three + ':');
                });
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
