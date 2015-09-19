'use strict';

var task = require('./symfony-task'),
    _ = require('underscore');

module.exports = function (config) {
    return function () {
        var data = task('werkint:frontendmapper:dump');

        var extensions = _.union(
            ['js'],
            config.es6.extensions,
            config.coffee.extensions
        ).join('|');


        return _.map(data, function (row) {
            return {
                "path":   row.path + '/**/*.+(' + extensions + ')',
                "dest":   '/js/' + row.name, // TODO: remove '/js' ?
                "prefix": row.path,
                "name":   row.name,
            };
        });
    };
};
