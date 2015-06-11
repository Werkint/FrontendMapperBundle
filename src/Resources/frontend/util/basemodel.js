define([
    'lodash',
    'backbone',
    'backbone.relational',
    'backbone.modelbinder',
], function (_, Backbone) {
    'use strict';

    // Это позволяет корректно работать с select2
    void function (obj, name) {
        var old = obj[name];
        obj[name] = function (el, convertedValue) {
            old.apply(this, arguments);
            if (el.data('select2')) {
                el.select2('val', convertedValue);
            }
        };
    }(Backbone.ModelBinder.prototype, '_setElValue');

    Backbone.Relational.store.removeModelScope();
    Backbone.Relational.store.currentScope = {};
    Backbone.Relational.store.addModelScope(
        Backbone.Relational.store.currentScope
    );

    var oldBackboneSync = Backbone.sync;
    Backbone.sync = function (method, model, options) {

        // Ensure that we have a URL.
        if (!options.url) {
            var dbg = window.$debug ? '?XDEBUG_SESSION_START=IDEA' : '';
            options.url = _.result(model, 'url') + dbg || null;
        }

        return oldBackboneSync.apply(this, [method, model, options]);
    };

    var Model = Backbone.RelationalModel;
    return Model.extend({

        get: function (attr) {
            // Call the getter if available
            if (_.isFunction(this.getters[attr])) {
                return this.getters[attr].call(this);
            }

            return Model.prototype.get.call(this, attr);
        },

        set: function (key, value, options) {
            var attrs, attr;

            // Normalize the key-value into an object
            if (_.isObject(key) || key == null) {
                attrs = key;
                options = value;
            } else {
                attrs = {};
                attrs[key] = value;
            }

            // always pass an options hash around. This allows modifying
            // the options inside the setter
            options = options || {};

            // Go over all the set attributes and call the setter if available
            for (attr in attrs) {
                if (_.isFunction(this.setters[attr])) {
                    attrs[attr] = this.setters[attr].call(this, attrs[attr], options);
                }
            }

            return Model.prototype.set.call(this, attrs, options);
        },

        getters: {},

        setters: {}

    });
});