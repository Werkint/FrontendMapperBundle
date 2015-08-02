'use strict';

var task = require('./symfony-task'),
    _ = require('underscore');

module.exports = function (exportPath) {
    return function () {
        var data = task('werkint:frontendmapper:dump');

        return _.map(data, function (row) {
            return {
                "path":   row.path + '/**/*.+(js|coffee)', // TODO: ext change
                "dest":   '/js/' + row.name,
                "prefix": row.path,
            };
        })
    };
};
