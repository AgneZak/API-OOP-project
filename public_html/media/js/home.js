'use strict';

const endpoints = {
    get: 'api/pizza/get',
    create: 'api/pizza/create',
    edit: 'api/pizza/edit',
    update: 'api/pizza/update',
    delete: 'api/pizza/delete',
    order: 'api/order/create'
};

/**
 * This defines how JS code selects elements by ID
 */
const selectors = {
    forms: {
        create: 'pizza-create-form',
        update: 'pizza-update-form'
    },
    modal: 'update-modal',
    grid: 'pizza-grid-container'
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
 * Form array
 * Contains all form-related functionality
 *
 * Object forms
 */
const forms = {
    /**
     * Create Form
     */
    create: {
        init: function () {
            if (this.getElement()) {
                this.getElement().addEventListener('submit', this.onSubmitListener);
                return true;
            }

            return false;
        },
        getElement: function () {
            return document.getElementById(selectors.forms.create);
        }
        ,
        onSubmitListener: function (e) {
            e.preventDefault();
            let formData = new FormData(e.target);
            formData.append('action', 'create');
            api(endpoints.create, formData, forms.create.success, forms.create.fail);
        }
        ,
        success: function (data) {
            const element = forms.create.getElement();

            grid.item.append(data);
            forms.ui.errors.hide(element);
            forms.ui.clear(element);
            forms.ui.flash.class(element, 'success');
        }
        ,
        fail: function (errors) {
            forms.ui.errors.show(forms.create.getElement(), errors);
        }
    },
    /**
     * Update Form
     */
    update: {
        init: function () {
            if (this.elements.form()) {
                this.elements.form().addEventListener('submit', this.onSubmitListener);

                const closeBtn = forms.update.elements.modal().querySelector('.close');
                closeBtn.addEventListener('click', forms.update.onCloseListener);
                return true;
            }

            return false;
        }
        ,
        elements: {
            form: function () {
                return document.getElementById(selectors.forms.update);
            }
            ,
            modal: function () {
                let modal = document.getElementById(selectors.modal);

                if (!modal) {
                    throw Error('Update modal was not found, check selector: ' + selectors.modal);
                }

                return modal;
            }
        }
        ,
        onSubmitListener: function (e) {
            e.preventDefault();
            let formData = new FormData(e.target);
            let id = forms.update.elements.form().getAttribute('data-id');
            formData.append('id', id);
            formData.append('action', 'update');

            api(endpoints.update, formData, forms.update.success, forms.update.fail);
        }
        ,
        success: function (data) {
            grid.item.update(data);
            forms.update.hide();
        }
        ,
        fail: function (errors) {
            forms.ui.errors.show(forms.update.elements.form(), errors);
        }
        ,
        fill: function (data) {
            forms.ui.fill(forms.update.elements.form(), data);
        }
        ,
        onCloseListener: function (e) {
            forms.update.hide();
        }
        ,
        show: function () {
            this.elements.modal().style.display = 'block';
        }
        ,
        hide: function () {
            this.elements.modal().style.display = 'none';
        }
    }
    ,
    /**
     * Common/Universal Form UI Functions
     */
    ui: {
        init: function () {
            // Function has to exist
            // since we're calling init() for
            // all elements withing forms object
            return true;
        }
        ,
        /**
         * Fills form fields with data
         * Each data index corelates with input name attribute
         *
         * @param {Element} form
         * @param {Object} data
         */
        fill: function (form, data) {
            console.log('Filling form fields with:', data);
            form.setAttribute('data-id', data.id);

            Object.keys(data).forEach(data_id => {
                if (form[data_id]) {
                    const input = form.querySelector('input[name="' + data_id + '"]');
                    if (input) {
                        input.value = data[data_id];
                    } else {
                        console.log('Could not fill field ' + data_id + 'because it wasn`t found in form');
                    }
                }
            });
        }
        ,
        clear: function (form) {
            let fields = form.querySelectorAll('[name]')
            fields.forEach(field => {
                field.value = '';
            });
        }
        ,
        flash:
            function (element, class_name) {
                const prev = element.className;

                element.className += class_name;
                setTimeout(function () {
                    element.className = prev;
                }, 1000);
            },
        /**
         * Form-error related functionality
         */
        errors: {
            /**
             * Shows errors in form
             * Each error index correlates with input name attribute
             *
             * @param {Element} form
             * @param {Object} errors
             */
            show: function (form, errors) {
                this.hide(form);

                console.log('Form errors received', errors);

                Object.keys(errors).forEach(function (error_id) {
                    const field = form.querySelector('input[name="' + error_id + '"]');
                    if (field) {
                        const span = document.createElement("span");
                        span.className = 'field-error';
                        span.innerHTML = errors[error_id];
                        field.parentNode.append(span);

                        console.log('Form error in field: ' + error_id + ':' + errors[error_id]);
                    }
                });
            }
            ,
            /**
             * Hides (destroys) all errors in form
             * @param {type} form
             */
            hide: function (form) {
                const errors = form.querySelectorAll('.field-error');
                if (errors) {
                    errors.forEach(node => {
                        node.remove();
                    });
                }
            }
        }
    }
};

/**
 * Table-related functionality
 */
const grid = {
    getElement: function () {
        return document.getElementById(selectors.grid);
    },
    init: function () {
        if (this.getElement()) {
            this.data.load();

            Object.keys(this.buttons).forEach(buttonId => {
                let success = grid.buttons[buttonId].init();
                console.log('Setting up button listeners "' + buttonId + '": ' + (success ? 'PASS' : 'FAIL'));
            });

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
            console.log('Grid: Calling API to get data...');
            api(endpoints.get, null, this.success, this.fail);
        },
        success: function (data) {
            Object.keys(data).forEach(i => {
                grid.item.append(data[i]);
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
            const item = document.createElement('div');

            if (data.id == null) {
                throw Error('JS can`t build the item, because API data doesn`t contain its ID. Check API controller!');
            }

            item.setAttribute('data-id', data.id);
            item.className = 'data-item';

            Object.keys(data).forEach(data_id => {
                switch (data_id) {
                    case 'image':
                        let img = document.createElement('img');
                        img.src = data[data_id];
                        item.append(img);
                        break;

                    case 'buttons':
                        let buttons = data[data_id];
                        Object.keys(buttons).forEach(button_id => {
                            let btn = document.createElement('button');
                            btn.innerHTML = buttons[button_id];
                            btn.className = button_id;
                            item.append(btn);
                        });
                        break;

                    default:
                        let span = document.createElement('span');
                        span.innerHTML = data[data_id];
                        span.className = data_id;
                        item.append(span);
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
            console.log('Grid: Creating item in grid container from ', data);
            grid.getElement().append(this.build(data));
        },
        /**
         * Updates existing item in grid from data
         * Row is selected via "id" index in data
         *
         * @param {Object} data
         */
        update: function (data) {
            let item = grid.getElement().querySelector('.data-item[data-id="' + data.id + '"]');
            item.replaceWith(this.build(data));
            //row = this.build(data);
        },
        /**
         * Deletes existing item
         * @param {Integer} id
         */
        delete: function (id) {
            const item = grid.getElement().querySelector('.data-item[data-id="' + id + '"]');
            item.remove();
        }
    },
    // Buttons are declared on whole grid, not on each item individually, so
    // onClickListeners dont duplicate
    buttons: {
        delete: {
            init: function () {
                if (grid.getElement()) {
                    grid.getElement().addEventListener('click', this.onClickListener);
                    return true;
                }

                return false;
            },
            onClickListener: function (e) {
                // Listener is set on whole grid, so we listen for which class button
                // has been pressed
                if (e.target.className === 'delete') {
                    let formData = new FormData();

                    // Find container of the button, which has ID
                    let item = e.target.closest('.data-item');
                    console.log('Delete button clicked on', item);

                    formData.append('id', item.getAttribute('data-id'));
                    api(endpoints.delete, formData, grid.buttons.delete.success, grid.buttons.delete.fail);
                }
            },
            success: function (data) {
                grid.item.delete(data.id);
            },
            fail: function (errors) {
                alert(errors[0]);
            }
        },
        edit: {
            init: function () {
                if (grid.getElement()) {
                    grid.getElement().addEventListener('click', this.onClickListener);
                    return true;
                }

                return false;
            },
            onClickListener: function (e) {
                if (e.target.className === 'edit') {
                    let formData = new FormData();

                    let item = e.target.closest('.data-item');
                    console.log('Edit button clicked on', item);

                    formData.append('id', item.getAttribute('data-id'));
                    api(endpoints.edit, formData, grid.buttons.edit.success, grid.buttons.edit.fail);
                }
            },
            success: function (api_data) {
                forms.update.show();
                forms.update.fill(api_data);
            },
            fail: function (errors) {
                alert(errors[0]);
            }
        },
        order: {
            init: function () {
                if (grid.getElement()) {
                    grid.getElement().addEventListener('click', this.onClickListener);
                    return true;
                }

                return false;
            },
            onClickListener: function (e) {
                // Listener is set on whole grid, so we listen for which class button
                // has been pressed
                if (e.target.className === 'order') {
                    let formData = new FormData();

                    // Find container of the button, which has ID
                    let item = e.target.closest('.data-item');
                    console.log('Order button clicked on', item);

                    formData.append('id', item.getAttribute('data-id'));
                    api(endpoints.order, formData, grid.buttons.order.success, grid.buttons.order.fail);
                }
            },
            success: function (data) {
                alert('Created order');
            },
            fail: function (errors) {
                alert(errors[0]);
            }
        }
    }
};

/**
 * Core page functionality
 */
const app = {
    init: function () {
        // Initialize all forms
        Object.keys(forms).forEach(formId => {
            let success = forms[formId].init();
            console.log('Initializing form "' + formId + '": ' + (success ? 'SUCCESS' : 'FAIL'));
        });

        console.log('Initializing grid...');
        let success = grid.init();
        console.log('Grid: Initialization: ' + (success ? 'PASS' : 'FAIL'));
    }
};

// Launch App
app.init();