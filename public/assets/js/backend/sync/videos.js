define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sync/videos/index' + location.search,
                    // add_url: 'sync/videos/add',
                    // edit_url: 'sync/videos/edit',
                    // del_url: 'sync/videos/del',
                    // multi_url: 'sync/videos/multi',
                    // import_url: 'sync/videos/import',
                    selectsync_url: 'sync/videos/selectsync',
                    allsync_url: 'sync/videos/allsync',
                    table: 'sync_videos',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                search: false,
                showSearch: false,
                showToggle: false,
                showColumns: false,
                showExport: false,
                fixedColumns: false,
                searchFormVisible: false,
                fixedRightNumber: 1,
                pageList: [10, 15, 20, 25, 50, 100],
                columns: [
                    [
                        {checkbox: true},
                        // {field: 'id', title: __('Id')},
                        {field: 'vid', title: __('Vid')},
                        // {field: 'sn', title: __('Sn'), operate: 'LIKE'},
                        {field: 'name', title: __('Name'), operate: 'LIKE', formatter: Table.api.formatter.content},
                        // {field: 'pinyin', title: __('Pinyin'), operate: 'LIKE'},
                        {field: 'category', title: __('Category_id')},
                        // {field: 'origin_id', title: __('Origin_id')},
                        // {field: 'description', title: __('Description'), operate: 'LIKE'},
                        // {field: 'cover', title: __('Cover'), operate: 'LIKE'},
                        // {field: 'm3u8_url', title: __('M3u8_url'), operate: 'LIKE', formatter: Table.api.formatter.url},
                        // {field: 'share_url', title: __('Share_url'), operate: 'LIKE', formatter: Table.api.formatter.url},
                        {field: 'time', title: __('Time'), operate: 'LIKE'},
                        {field: 'size', title: __('Size'), operate:'BETWEEN'},
                        {field: 'resolution', title: __('Resolution'), operate: 'LIKE'},
                        {field: 'bit_rate', title: __('Bit_rate'), operate: 'LIKE'},
                        // {field: 'tags', title: __('Tags'), operate: 'LIKE', formatter: Table.api.formatter.flag},
                        // {field: 'tags_id', title: __('Tags_id'), operate: 'LIKE'},
                        // {field: 'actresses', title: __('Actresses'), operate: 'LIKE'},
                        // {field: 'actresses_id', title: __('Actresses_id'), operate: 'LIKE'},
                        // {field: 'views', title: __('Views')},
                        // {field: 'favorites', title: __('Favorites')},
                        // {field: 'like', title: __('Like')},
                        // {field: 'dislike', title: __('Dislike')},
                        // {field: 'state', title: __('State')},
                        {field: 'release_date', title: __('Release_date'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'created_at', title: __('Created_at')},
                        // {field: 'updated_at', title: __('Updated_at')},
                        // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);

            $(document).on('click', '.btn-selectsync', function () {
                var selectRows = table.bootstrapTable('getSelections');
                var currentOptions = table.bootstrapTable('getOptions');
                Layer.confirm(__('Do you confirm the %s pieces of data selected synchronously?', selectRows.length), {
                    btn: [__('Confirm'), __('Cancel')]
                }, function (index) {
                    var ids = '';
                    for (var row of selectRows) {
                        if (ids) {
                            ids += ',';
                        }
                        ids += row.id;
                    }
                    var limit = currentOptions.pageSize;
                    var offset = (currentOptions.pageNumber - 1) * currentOptions.pageSize;
                    Backend.api.ajax({
                        url: $.fn.bootstrapTable.defaults.extend.selectsync_url,
                        data: {
                            'ids': ids,
                            'limit': limit,
                            'offset': offset,
                        }
                    }, function (ret, data) {
                        Layer.close(index);
                        if (data.code === 1) {
                            $(".btn-refresh").trigger("click");
                        }
                    });
                });
            });
            $(document).on('click', '.btn-allsync', function () {
                Layer.confirm(__('Confirm that you want to start to synchronize all sources of information? When the amount of data is large, synchronization may take a long time, please wait patiently.'), {
                    btn: [__('Confirm'), __('Cancel')]
                }, function (index) {
                    Layer.close(index);
                    Fast.config.openArea = ['800px','600px'];
                    var currentOptions = table.bootstrapTable('getOptions');
                    var titles = Config.isWin ? "<span style='color: red; font-weight: bold;'>（" + __('The window cannot be closed during synchronization') + "）</span>" : "";
                    Fast.api.open($.fn.bootstrapTable.defaults.extend.allsync_url + '?total=' + currentOptions.totalRows, __('Real -time synchronization progress') + titles, $(this).data() || {});
                });
            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        allsync: function() {
            var timeout = 5000;
            sendAjax('first=true');
            var interval = setInterval(function () {
                sendAjax();
            }, timeout);
            function sendAjax(params) {
                Backend.api.ajax({
                    data: params,
                    loading: false,
                    success: function (ret) {
                        if (ret && ret.code === 1) {
                            var success = ret.data.all_sync_success;
                            if (success) {
                                clearInterval(interval);
                            }
                            addVideoName(ret.data);
                            if (success) {
                                document.getElementById('surplus').innerText = 0;
                                document.getElementById('success').innerText = document.getElementById('total').innerText;
                                parent.document.querySelector('.btn-refresh').click();
                            }
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status !== 200) {
                            Toastr.error(xhr.statusText);
                        }
                    }
                });
            }
            async function addVideoName(data) {
                var length = data.names.length;
                var ms = timeout / length;
                var messageContainer = document.querySelector('.message-container');
                for (index = 0; index < length; index++) {
                    var messageElement = document.createElement('div');
                    messageElement.classList.add('message-item');
                    messageElement.textContent = data.names[index];
                    messageContainer.appendChild(messageElement);
                    messageElement.offsetHeight;
                    messageElement.style.opacity = 1;
                    messageContainer.scrollTop += messageElement.clientHeight;
                    var messages = document.querySelectorAll('.message-item');
                    if (messages.length > 18) {
                        messageContainer.removeChild(messages[0]);
                    }
                    await sleep(ms);
                    var total = document.getElementById('total').innerText;
                    var success = document.getElementById('success').innerText;
                    document.getElementById('success').innerText = parseInt(success) + 1;
                    document.getElementById('surplus').innerText = parseInt(total) - parseInt(success) - 1;
                    if (parseInt(total) - parseInt(success) - 1 <= 0) {
                        document.getElementById('success').innerText = total;
                        document.getElementById('surplus').innerText = 0;
                        break;
                    }
                }
            }
            function sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }
            parent.document.querySelector('.layui-layer-iframe').querySelector('.layui-layer-close').addEventListener('click', () => {
                parent.document.querySelector('.btn-refresh').click();
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        },
    };
    return Controller;
});
