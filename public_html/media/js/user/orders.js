'use strict';

const endpoints = {
    get: '/api/order/user/get'
};
/**
 * This defines how JS code selects elements by ID
 */
const selectors = {
    table: 'table'
}

/**
 * Executes API request
 * @param {type} url Endpoint URL
 * @param {type} formData instance of FormData
 * @param {type} success Success callback
 * @param {type} fail Fail callback
 * @returns {undefined}
 */
function api(url, formData, success, fail) {
    fetch(url, {
        method: 'POST',
        body: formData
    }).then(response => response.json())
        .then(obj => {
            if (obj.status === 'success') {
                success(obj.data);
            } else {
                fail(obj.errors);
            }
        })
        .catch(e => {
            if (e.toString() == 'SyntaxError: Unexpected token < in JSON at position 0') {
                fail(['Problem is with your API controller, did not return JSON! Check Chrome->Network->XHR->Response']);
            } else {
                fail([e.toString()]);
            }
        });
}

/**
 * Table-related functionality
 */
const table = {
    getElement: function () {
        return document.getElementsByClassName(selectors.table)[0];
    },
    init: function () {
        if (this.getElement()) {
            this.data.load();

            return true;
        }

        return false;
    },
    /**
     * Data-Related functionality
     */
    data: {
        /**
         * Loads data and populates grid from API
         * @returns {undefined}
         */
        load: function () {
            console.log('Table: Calling API to get data...');
            api(endpoints.get, null, this.success, this.fail);
        },
        success: function (data) {
            Object.keys(data).forEach(i => {
                table.item.append(data[i]);
            });
        },
        fail: function (errors) {
            console.log(errors);
        }
    },
    /**
     * Operations with items
     */
    item: {
        /**
         * Builds item element from data
         *
         * @param {Object} data
         * @returns {Element}
         */
        build: function (data) {
            const item = document.createElement('tr');

            if (data.id == null) {
                throw Error('JS can`t build the item, because API data doesn`t contain its ID. Check API controller!');
            }

            item.setAttribute('data-id', data.id);
            item.className = 'data-item';

            Object.keys(data).forEach(data_id => {
                switch (data_id) {

                    default:
                        let td = document.createElement('td');
                        td.innerHTML = data[data_id];
                        td.className = data_id;
                        item.append(td);
                }
            });

            return item;
        },
        /**
         * Appends item to grid from data
         *
         * @param {Object} data
         */
        append: function (data) {
            console.log('Table: Creating item in table from ', data);
            table.getElement().append(this.build(data));
        }
    }
};


/**
 * Core page functionality
 */
const app = {
    init: function () {

        console.log('Initializing table...');
        let success = table.init();
        console.log('Table: Initialization: ' + (success ? 'PASS' : 'FAIL'));
    }
};

// Launch App
app.init();