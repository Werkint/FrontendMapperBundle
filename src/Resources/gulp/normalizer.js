'use strict';

var gutil = require('gulp-util'),
    merge = require('multipipe'),
    normalizer = require('gulp-bower-normalize'),
    rename = require("gulp-rename");

module.exports = function (config) {
    return function () {
        var normalizerOptions = {
            "bowerJson": './bower.json',
            "flatten":   true
        };

        var bowerJson = require(process.cwd() + '/' + normalizerOptions.bowerJson);

        var renames = bowerJson.overrides && bowerJson.overrides.renames ?
            bowerJson.overrides.renames : {};

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