'use strict';

// global process variable. поставь в баш рц или в файле который запускает таск
require('events').EventEmitter.prototype._maxListeners = process.env.NODE_MAX_LISTENER || 400;

var gulp = require('gulp'),
    _ = require('lodash'),
    merge = require('merge-stream'),
    gutil = require('gulp-util'),
    multipipe = require('multipipe'),
    change = require('gulp-change'),
    mark = require('gulp-mark'),
    marker = require('./marker'),
    rename = require('./rename2'),
    watch = require('gulp-watch'),
    globule = require('globule'),
    clean = require('gulp-clean'),
    notify = require("gulp-notify"),
    gulpIgnore = require('gulp-ignore'),
    plumber = require('gulp-plumber'),
    config = require('./symfony-task')('werkint:frontendmapper:config'),
    concat = require('gulp-concat'),
    gulpif = require('gulp-if'),
    coffee = require('gulp-coffee'),
    Path = require('path'),
    babel = require('gulp-babel'),
    requirejs = require('requirejs'),
    parseRjs = require('./rjs-parser/parse');

// Task-helpers
var symfonyMapper = require('./symfony-mapper')(config),
    bower = require('./bower')(config.bower),
    normalizer = require('./normalizer')(config),
    minify = require('./minify')(config);

// Список источников
var streams = {
    bower:   function () {
        var src = gulp.src(bower(), {
            base: config.bower.target,
        });

        return src
            .pipe(gulpIgnore.exclude('**/' + config.bower.renamesConfig))
            .pipe(gulpIgnore.exclude('**/bower.json'));
    },
    bundles: function () {
        var bundles = symfonyMapper();

        var list = _.map(bundles, function (resource) {
            return gulp.src(resource.path)
                .pipe(marker(function (file) {
                    file.resource = resource;
                }));
        });

        return merge.apply(undefined, list);
    },
};

// Меняет dest в зависимости от бандла
var bundleRename = function (path, file) {
    // TODO: Path.join
    path.dirname = config.path + '/' + file.resource.dest + '/' + path.dirname;
};

module.exports = function () {
    var getPipe = function (pipeName) {
        var list = streams;
        if (pipeName) {
            list = _.object(_.filter(_.pairs(streams), function (row) {
                return row[0] === pipeName;
            }));
        }

        var src = merge.apply(undefined, _.map(list, function (source, name) {
            return source().pipe(mark.set(name));
        }));

        /** es6 support */
        var exts = _.map(config.es6.extensions, function (ext) {
            return '.' + ext;
        }), options = _.pick(config.es6, ['modules']);
        src.pipe(gulpif(function (file) {
            return _.contains(exts, Path.extname(file.path));
        }, babel(options)));

        /** coffee support */
        _.each(config.coffee.extensions, function (ext) {
            src.pipe(gulpif('*.' + ext, coffee({
                "bare": true
            })).on('error', gutil.log));
        });

        return src
            .pipe(mark.if('bower', multipipe(
                normalizer(),
                rename(function (path) {
                    path.dirname = config.path + '/' + path.dirname;
                })
            )))
            .pipe(mark.if('bundles', rename(bundleRename)));
    };

    gulp.task('clean', function () {
        return gulp.src(config.root + config.path, {read: false})
            .pipe(clean());
    });

    gulp.task('dump', ['clean', 'bower'], function () {
        return getPipe()
            .pipe(minify())
            .pipe(gulp.dest(config.root));
    });

    gulp.task('dump-merged-bundles', ['dump'], function () {
        var modules = [],
            bundles = symfonyMapper();
        _.each(bundles, function (bundle) {
            var files = globule.find(bundle.path);
            var prefix = bundle.name ? bundle.name + '/' : '';
            _.each(files, function (file) {
                var module = prefix + Path.relative(bundle.prefix, file);
                module = module.substr(0, module.lastIndexOf('.'));

                modules.push(module);
            });
        });

        var rjsBuild = require('./rjs-build');

        modules = _.difference(modules, rjsBuild.excludeShallow);
        //console.log(modules, rjsBuild.excludeShallow);
        //return;

        var data = 'requirejs(["' + modules.join('", "') + '"], function(){})';

        var bundlesFile = [
            process.cwd(),
            config.root,
            config.path,
            'js/bundles.js',
        ].join('/');
        require('fs').writeFileSync(Path.normalize(bundlesFile), data);

        requirejs.optimize(rjsBuild, null, function (err) {
            console.log(err);
        });

        return;

        return streams.bundles()
            .pipe(change(function (content, done) {
                var r = this.file.relative,
                    moduleName = this.file.resource.name + '/' + r.substr(0, r.lastIndexOf('.'));

                //content = content.replace(/(define\()/, '$1"' + moduleName + '", ');

                //modules = modules.concat(parseRjs(moduleName, content));
                modules.push(moduleName);

                done(null, content);
            }))
            .pipe(concat('bundles.js'))
            .pipe(gulp.dest(config.root + '/' + config.path + '/js'))
            .on('finish', function () {
                modules = _.unique(modules).sort();

                var rjs = 'requirejs(["' + modules.join('", "') + '"], function(){})';
                console.log(rjs);
            });
    });

    gulp.task('default', ['dump', 'dump-merged-bundles'], function () {
        return getPipe()
            .pipe(minify())
            .pipe(gulp.dest(config.root));
    });

    gulp.task('watch', function () {
        var list = symfonyMapper(),
            files = _.pluck(list, 'path');

        return watch(files, function (event) {
            var path = event.path,
                dest = _.find(list, function (row) {
                    return globule.isMatch(row.path, path);
                });

            var prefix = path.substr(dest.prefix.length);
            prefix = prefix.replace(/(\/[^\/]+)$/, '');

            dest = config.root + '/' + config.path + dest.dest + prefix;
            dest = dest.replace(/\/\//, '/');

            dest = process.cwd() + '/' + dest;

            var src = gulp.src(path);

            /** es6 support */
            var exts = _.map(config.es6.extensions, function (ext) {
                return '.' + ext;
            }), options = _.pick(config.es6, ['modules']);
            src.pipe(gulpif(function (file) {
                return _.contains(exts, require('path').extname(file.path));
            }, babel(options)));

            /** coffee support */
            _.each(config.coffee.extensions, function (ext) {
                src.pipe(gulpif('*.' + ext, coffee({
                    "bare": true
                })).on('error', gutil.log));
            });


            src.pipe(minify())
                .pipe(plumber({errorHandler: notify.onError("Error: <%= error.message %>")}))
                .pipe(notify({
                    message:  'File changed: <%= file.relative %>',
                    notifier: function (options, callback) {
                        callback();
                    },
                }))
                .pipe(gulp.dest(dest));
        });
    });
};


