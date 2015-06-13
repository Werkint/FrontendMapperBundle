'use strict';

var gutil = require('gulp-util'),
    merge = require('multipipe'),
    normalizer = require('gulp-bower-normalize'),
    rename = require('gulp-rename');

module.exports = function (config) {
    return function () {
        // TODO: Мерджить конфиг
        var normalizerOptions = {
            "bowerJson": './' + config.bower.mainFile,
            "flatten":   true,
        };

        // TODO: проверка на существование
        var renames = config.bower.data.overrides.renames || {};

        return merge(
            normalizer(normalizerOptions),
            rename(function (path) {
                if (renames[path.basename]) {
                    path.basename = renames[path.basename];
                }
            })
        );
    };
};