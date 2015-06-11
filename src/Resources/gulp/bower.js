'use strict';

var gulp = require('gulp'),
    _ = require('underscore'),
    bower = require('gulp-bower'),
    mainFiles = require('main-bower-files');

module.exports = function (config) {
    var packages = [
        './bower.json',
    ].concat(_.map(config.packages, function (row) {
            return row.path;
        }));

    gulp.task('bower', function () {
        return bower({
            cmd:       'install',
            directory: config.target,
        }, [
            packages,
        ]);
    });

    return mainFiles;
};