/**
 * wplog gulpfile.js
 */

/* globals require */

/**==================================================================================
 * REQUIRE
 *=================================================================================*/

var gulp = require('gulp');
var gulpUtil = require('gulp-util');
var plumber = require('gulp-plumber');
var runSeq = require('run-sequence');
var sort = require('gulp-sort');
var fs = require('fs');
var git = require('gulp-git');
var sourcemaps = require('gulp-sourcemaps');

var uglify = require('gulp-uglify');
var stripDebug = require('gulp-strip-debug');

var sass = require('gulp-sass');
var minCss = require('gulp-minify-css');

var pot = require('gulp-wp-pot');

var zip = require('gulp-zip');

/**==================================================================================
 * BOWER
 *=================================================================================*/

/**
 * Move src/lib packages to assets/lib.
 */
gulp.task('bower', function () {

    'use strict';

    return gulp.src('./src/lib/**/*')
        .pipe(gulp.dest('./assets/lib'));

});

/**==================================================================================
 * JS
 *=================================================================================*/

/**
 * Minify JavaScripts from src to assets.
 */
gulp.task('js', function () {

    'use strict';

    return gulp.src('./src/js/**/*.js')
        .pipe(plumber(function (error) {
            gulpUtil.log(gulpUtil.colors.red(
                'Error (' + error.plugin + '): ' + error.message)
            );

            this.emit('end');
        }))
        .pipe(sourcemaps.init({
            includeSource: false
        }))
        //.pipe(stripDebug())
        .pipe(uglify())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('./assets/js'));

});

/**==================================================================================
 * CSS
 *=================================================================================*/

/**
 * Compile and minify Sass to assets.
 */
gulp.task('sass', function () {

    'use strict';

    return gulp.src('./src/sass/**/*.scss')
        .pipe(plumber(function (error) {
            gulpUtil.log(gulpUtil.colors.red(
                'Error (' + error.plugin + '): ' + error.message)
            );

            this.emit('end');
        }))
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest('./src/css'));

});

gulp.task('css', function() {

    'use strict';

    return gulp.src('./src/css/**/*.css')
        .pipe(plumber(function (error) {
            gulpUtil.log(gulpUtil.colors.red(
                'Error (' + error.plugin + '): ' + error.message)
            );

            this.emit('end');
        }))
        .pipe(minCss())
        .pipe(gulp.dest('./assets/css'));

});

/**==================================================================================
 * GENERAL
 *=================================================================================*/

/**
 * POT file generator.
 */
gulp.task('pot', function () {

    'use strict';

    var src = [
        './**/*.php',
        '!./vendor/**/*'
    ];

    return gulp.src(src)
        .pipe(sort())
        .pipe(pot({
            domain: 'wplog',
            destFile:'wplog.pot',
            lastTranslator: 'Otto Rask <ojrask@gmail.com>'
        }))
        .pipe(gulp.dest('./languages'));

});

/**
 * Compiler.
 */
gulp.task('compile', function (cb) {

    'use strict';

    gulpUtil.log(gulpUtil.colors.yellow('Compiling all assets...'));

    runSeq(['bower', 'js', 'pot'], ['sass', 'css'], cb);

});

/**
 * Watcher.
 */
gulp.task('watch', ['compile'], function () {

    'use strict';

    var potSrc = ['./**/*.php', '!./vendor/**/*'];

    gulp.watch('./src/js/**/*.js', ['js']);
    gulp.watch('./src/sass/**/*.scss', ['sass']);
    gulp.watch('./src/css/**/*.css', ['css']);
    gulp.watch('./src/lib/**/*', ['bower']);
    gulp.watch(potSrc, ['pot']);

    gulpUtil.log(gulpUtil.colors.yellow('Watching for changes, Ctrl-C to quit...'));

});

/**==================================================================================
 * BUILDS
 *=================================================================================*/

/**
 * Build a release.
 */
gulp.task('build', function () {

    'use strict';

    var stream = null;

    var pluginSrc = [
        'index.php',
        'wplog.php',
        'functions.php',
        'README.md',
        'LICENSE.md',
        'uninstall.php',
        'assets/**',
        'languages/**',
        'config/**',
        'classes/**',
        'vendor/**',
        'includes/**'
    ];

    var getPluginVersion = function (file) {
        var contents = fs.readFileSync(file, 'utf8');

        var matched = contents.match(/Version: *([0-9]+?\.[0-9]+?\.[0-9]+?)/gi);

        if (matched === undefined || !matched[0] || !matched[0].length) {
            return false;
        }

        var versLine = matched[0].trim();
        var version = versLine.replace(/[^0-9\.]/gi, '').trim();

        return version;
    };

    git.exec({args: 'rev-parse --abbrev-ref HEAD', quiet: true}, function (err, stdout) {
        if (err) {
            throw err;
        }

        var branch = stdout.trim();

        if (branch !== 'master') {
            gulpUtil.log(gulpUtil.colors.red('Error: ' + 'Cannot build plugin package, must be built on master branch.'));
            return false;
        }

        var versionNumber = getPluginVersion('./wplog.php');

        if (!versionNumber) {
            gulpUtil.log(gulpUtil.colors.red('Build failed, could not read version number from `wplog.php`!'));
            return false;
        }

        var zipName = 'wplog-' + versionNumber + '.zip';

        var str = gulp.src(pluginSrc, {base: '.'})
            .pipe(plumber(function (error) {
                gulpUtil.log(gulpUtil.colors.red(
                    'Error (' + error.plugin + '): ' + error.message)
                );

                this.emit('end');
            }))
            .pipe(zip(zipName))
            .pipe(gulp.dest('./builds'));

        gulpUtil.log(gulpUtil.colors.green('Built distributable plugin archive `./builds/wplog-' + versionNumber + '.zip`'));

        stream = str;

    });

    return stream;

});

/**
 * Default to watching.
 */
gulp.task('default', ['watch']);
