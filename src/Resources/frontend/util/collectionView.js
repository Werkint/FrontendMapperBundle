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

        "initialize": function (options) {
            if (!this.relatedModel && this.model) {
                this.relatedModel = this.model.model;
            }

            var Model = BaseModel.extend({
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


            if (!(this.model instanceof Model)) {
                var model = this.model;
                this.model = new Model();
                this.model.set(this.relatedKey, model);
            }

            this.template = _.bind(function (supr) {
                return _.bind(function (obj, extra) {
                    return supr(_.merge(_.object([[
                        this.relatedKey,
                        this.model.get(this.relatedKey)
                    ]]), extra || {}));
                }, this);
            }, this)(this.template);

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
    });

    return View;
});