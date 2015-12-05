var PatchTester = {
    orderTable: function () {
        var table = document.getElementById('sortTable'),
            direction = document.getElementById('directionTable'),
            order = table.options[table.selectedIndex].value,
            currentOrder = document.getElementById('adminForm').getAttribute('data-order').valueOf();

        if (order != currentOrder) {
            var dirn = 'asc';
        } else {
            var dirn = direction.options[direction.selectedIndex].value;
        }

        Joomla.tableOrdering(order, dirn, '');
    },

    submitpatch: function (task, id) {
        var idField = document.getElementById('pull_id');
        idField.value = id;

        return Joomla.submitbutton(task);
    }
}
