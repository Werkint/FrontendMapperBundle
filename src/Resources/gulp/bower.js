'use strict';

var gulp = require('gulp'),
    _ = require('lodash'),
    fs = require('fs'),
    glob = require('glob'),
    merge = require('merge-stream'),
    clean = require('gulp-clean'),
    mainFiles = require('main-bower-files'),
    bower = require('gulp-bower');

var getOverrides = function (bowerTarget, renamesConfig) {
    var data = {};

    var files = glob.sync(bowerTarget + '/*/' + renamesConfig);

    _.each(files, function (file) {
        _.merge(data, require(process.cwd() + '/' + file) || data);
    });

    return data;
};

module.exports = function (config) {
    _.merge(config.data, {
        dependencies: {},
    });

    _.each(config.packages, function (row) {
        config.data.dependencies[row.name] = row.path;
    });

    _.merge(config.data.overrides, getOverrides(
        config.target,
        config.renamesConfig
    ));

    fs.writeFileSync(process.cwd() + '/bower.json', JSON.stringify(config.data));

    gulp.task('bower-clearbundles', function () {
        var src = merge.apply(undefined, _.map(config.packages, function (row) {
            return gulp.src(config.target + '/' + row.name, {read: false});
        }));

        return src
            .pipe(clean());
    });

    gulp.task('bower', ['bower-clearbundles'], function () {
        return bower({
            cmd:       'install',
            directory: config.target,
        }, [
            //'-q', TODO: quietb
        ]);
    });

    return function () {
        return mainFiles({
            paths: {
                bowerDirectory: config.target,
                bowerrc:        '.bowerrc',
                bowerJson:      './bower.json',
            }
        });
    };
};