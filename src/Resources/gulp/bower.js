'use strict';

var gulp = require('gulp'),
    bower = require('gulp-bower'),
    mainFiles = require('main-bower-files');

module.exports = function (config) {
    var packages = [
        './bower.json',
    ].concat(config.packages);

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