/**
 * @author Bogdan Yurov
 *
 * Это вспомогательный вид для коллекции, нужен для того,
 * чтобы ModelBinder мог корректно подцепиться к дочерним элементам.
 * Работает просто - оборачивает коллекцию в еще одну модель.
 */
define([
    'lodash',
    'backbone',
    'util/basemodel',
], function (_, Backbone, BaseModel) {
    'use strict';

    var View = Backbone.View.extend({
        "relatedModel": null,
        "relatedKey":   'list',

        "initialize": function () {
            if (!this.relatedModel && this.model) {
                this.relatedModel = this.model.model;
            }

            this.ensureModel();
            this.ensureTemplate();
        },

        "ensureModel": function () {
            if (!(this.model && this.model.collectionPatched)) {
                var Model = BaseModel.extend({
                    "initialize": function () {
                        this.collectionPatched = true;
                    },

                    "relations": [{
                        "type":         Backbone.HasMany,
                        "key":          this.relatedKey,
                        "relatedModel": this.relatedModel
                    }],

                    "fetch": function () {
                        this.get('list').fetch.apply(this.get('list'), arguments);
                    },

                    "save": function () {
                        var list = this.get('list');
                        list.save(list);
                    }
                });

                var model = this.model;
                this.model = new Model();
                this.model.set(this.relatedKey, model);
            }
        },

        "ensureTemplate": function () {
            if (this.template.collectionPatched) {
                return;
            }
            this.template = _.bind(function (supr) {
                return _.bind(function (obj, extra) {
                    return supr(_.merge(_.object([[
                        this.relatedKey,
                        this.model.get(this.relatedKey)
                    ]]), extra || {}));
                }, this);
            }, this)(this.template);
            this.template.collectionPatched = true;
        },

        "addItemEmpty": function () {
            var el = new this.relatedModel();
            this.model.get('list').add(el);
            return el;
        },

        "save": function (option) {
            var list = this.model.get('list');
            list.save = function () {
                this.sync("update", this, option);
            };
            list.save();
        },

        "setModel": function (model) {
            this.model = model;
            this.ensureModel();
        },
    });

    return View;
});