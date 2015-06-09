gulp task example:

var gulp       = require('gulp'),
    _          = require('underscore'),
    execSync   = require('exec-sync'),
    //watch = require('gulp-watch'),

    exportPath = './web';

gulp.task('default', ['dump-bundles'], function () {
    var bower           = require('main-bower-files'),
        bowerNormalizer = require('gulp-bower-normalize'),
        bowerData       = bower();
    return gulp.src(bowerData, {base: './bower_components'})
        .pipe(bowerNormalizer({bowerJson: './bower.json', flatten: true}))
        .pipe(gulp.dest(exportPath));
});

gulp.task('dump-bundles', function () {
    var myData = execSync('app/console tommy:js:dump --screen');
    var colors = require('colors/safe');
    myData = JSON.parse(myData);
    _.each(myData, function (data, type) {
        var was = {};
        _.each(data, function (path, dest) {
            if (!was.hasOwnProperty(path)) {
                was[path] = [];
            }
            was[path].push(dest);
        });
        _.each(was, function (dests, path) {
            if (dests.length > 1) {
                _.each(dests, function (dest) {
                    console.log(colors.yellow(dest) + " (" + exportPath + "/" + type + dest.substr(1) + ")\n");
                });
                console.log(
                    colors.green("  file:\n")
                    + path
                    + colors.green("\n  was already placed by:\n")
                );
            }
        });
    });
    var src = myData.js;
    _.each(src, function (path, dest) {
        dest = dest.substr(1);
        dest = dest.replace(/\/[^\/]+$/, '');
        //process one by one
        gulp.src(path/*, {base: './bower_components'}*/)
            //.pipe(normalize, compress, ...)
            .pipe(gulp.dest(exportPath + '/js' + dest));
    });
});