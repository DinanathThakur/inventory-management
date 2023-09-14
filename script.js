class App {
    constructor() {
        this.requestTable = null;
        this.allItems = null;
        this.init();
    }

    init() {

        this.prepareRequestTable();
        this.prepareItemSelection();
        this.handleAddRequest();
        this.handleUpdateRequest();
        this.handleActionButton();
        this.handleOrderRequest();
    }

    prepareRequestTable() {
        this.requestTable = new DataTable('#request-table', {
                ajax: '/app.php?action=getRequests',
                columns: [
                    {data: 'id'},
                    {data: 'user'},
                    {data: 'items'},
                    {data: 'type'}
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, row) {
                            return '<div class="dropdown action-dropwdown">\n' +
                                '  <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">\n' +
                                '   <i class="fa fa-gear"></i>' +
                                '  </button>\n' +
                                '  <ul class="dropdown-menu">\n' +
                                '    <li><a class="dropdown-item action-button" data-id="' + data + '" data-action="edit" href="javascript:;">Edit</a></li>\n' +
                                '    <li><a class="dropdown-item action-button" data-id="' + data + '" data-action="delete" href="javascript:;">Delete</a></li>\n' +
                                '  </ul>\n' +
                                '</div>';
                        }
                    },
                ]
            }
        );
    }

    prepareItemSelection() {
        let _this = this;

        $.ajax({
            url: "/app.php?action=getItems",
            cache: false,
            success: function (data) {
                _this.allItems = data;
                let itemsElement = '';
                let items = data['items'];
                let itemTypes = data['itemTypes'];

                items.forEach(function (item) {
                    itemsElement += '<option value="' + item['id'] + '">' + item['item'] + ' (' + itemTypes[item['item_type']] + ')</option>';
                });

                $('#requested-items').html(itemsElement);
            }
        });
    }

    handleAddRequest() {
        let _this = this;

        $(document).on('click', '#add-request-button', function () {
            let user = $('#user').val();
            let items = $('#requested-items').val();

            if (user.length > 0 && items.length > 0) {
                let formData = {
                    'user': user,
                    'items': items
                };

                $.ajax({
                    url: "/app.php?action=addRequest",
                    type: "post",
                    data: formData,
                    success: function (response) {
                        alert(response.message);
                        bootstrap.Modal.getInstance($('#add-request-modal')).hide();
                        _this.requestTable.ajax.reload();
                    }
                });
            } else {
                alert('Please provide details to add');
            }
        });
    }


    handleActionButton() {
        let _this = this;

        $(document).on('click', '.action-dropwdown .action-button', function () {
            let action = $(this).data('action');
            let id = $(this).data('id');

            if (id > 0) {
                if (action == 'edit') {
                    $.ajax({
                        url: "/app.php?action=getRequestDetails",
                        type: "get",
                        data: {'id': id},
                        success: function (response) {
                            let data = response.data;

                            if (data.id != undefined) {
                                let itemsElement = '';
                                let items = _this.allItems['items'];
                                let itemTypes = _this.allItems['itemTypes'];
                                let selectedItems = JSON.parse(data.items).map(function (x) {
                                    return x[0];
                                });

                                items.forEach(function (item) {
                                    itemsElement += '<option value="' + item['id'] + '" ' + (selectedItems.includes(item['id']) ? 'selected' : '') + '>' + item['item'] + ' (' + itemTypes[item['item_type']] + ')</option>';
                                });

                                $('#request-id').val(data.id);
                                $('#updated-user').val(data.requested_by);
                                $('#updated-request-items').html(itemsElement);

                                (new bootstrap.Modal($('#edit-request-modal'))).show()
                            } else {
                                alert('Invalid request');
                            }
                        }
                    });
                } else if (action == 'delete') {
                    if (confirm('Are you sure you want to delete?')) {
                        $.ajax({
                            url: "/app.php?action=deleteRequest",
                            type: "post",
                            data: {'id': id},
                            success: function (response) {
                                alert(response.message);
                                if (response.data > 0) {
                                    _this.requestTable.ajax.reload();
                                }
                            }
                        });
                    }
                }
            } else {
                alert('Invalid action');
            }
        });
    }

    handleUpdateRequest() {
        let _this = this;

        $(document).on('click', '#update-request-button', function () {
            let id = $('#request-id').val();
            let items = $('#updated-request-items').val();

            if (id > 0 && items.length > 0) {
                let formData = {
                    'id': id,
                    'items': items
                };

                $.ajax({
                    url: "/app.php?action=updateRequest",
                    type: "post",
                    data: formData,
                    success: function (response) {
                        alert(response.message);
                        if (response.data > 0) {
                            bootstrap.Modal.getInstance($('#edit-request-modal')).hide();
                            _this.requestTable.ajax.reload();
                        }
                    }
                });
            } else {
                alert('Please provide details to add');
            }
        });
    }

    handleOrderRequest() {
        $(document).on('click', '#order-requests', function () {
            if (confirm('Are you sure you want to order requests')) {
                $.ajax({
                    url: "/app.php?action=orderRequests",
                    type: "post",
                    success: function (d) {
                        alert('Ordered requests');
                    }
                });
            } else {
                alert('Okay, not ordering');
            }
        });
    }

}

// Initialize the App class
const app = new App();
