const gulp = require('gulp');
const sass = require('gulp-sass');
const minifyCss = require('gulp-minify-css');
const postcss = require('gulp-postcss');
const autoprefixer = require('autoprefixer');
const rename = require('gulp-rename');
const clean = require('gulp-clean');
const browserify = require('browserify');
const path = require('path');
const uglify = require('gulp-uglify');
const es2015 = require('babel-preset-es2015');
const source = require('vinyl-source-stream');
const streamify = require('gulp-streamify');
const gutil = require('gulp-util');

const paths = {
    sass: [
        './src/sass/**/*.scss'
    ],
    js: [
        './src/js/**/*.js',
    ],
    includes: [
        path.join(__dirname, 'node_modules'),
    ],
};

gulp.task('sass', () => {
    return gulp.src(paths.sass)
        .pipe(sass({ includePaths: paths.sass, outputStyle: 'expanded' }))
        .on('error', sass.logError)
        .pipe(postcss([
            autoprefixer({
                browsers: [
                    'last 2 versions',
                    'iOS 8',
                ],
            }),
        ]))
        .pipe(gulp.dest('./dist/css/'))
        .pipe(minifyCss({
            keepSpecialComments: 0,
        }))
        .pipe(rename({ extname: '.min.css' }))
        .pipe(gulp.dest('./dist/css/'));
});

gulp.task('js', () => {
    return browserify('./src/js/primer.js', { debug: true, paths: [path.join(__dirname, 'js/src')].concat(paths.includes) })
        .transform('babelify', {
            presets: [es2015],
        })
        .bundle()
        .on('error', function (err) {
            gutil.log(gutil.colors.red(`JavaScript ERROR:\n${err.stack}`));
            this.emit('end');
        })
        // Pass desired output filename to vinyl-source-stream
        .pipe(source('primer.js'))
        .pipe(gulp.dest('./dist/js/'))
        .pipe(streamify(uglify()))
        .pipe(rename('primer.min.js'))
        .pipe(gulp.dest('./dist/js/'));
});

gulp.task('copy-images', function () {
    return gulp.src(['src/img/**/*'], {
        base: 'src'
    }).pipe(gulp.dest('dist'));
});

gulp.task('watch', () => {
    gulp.watch(paths.sass, ['sass']);
    gulp.watch(paths.js, ['js']);
});

gulp.task('clean', function () {
    return gulp.src('dist', {read: false}).pipe(clean());
});

gulp.task('build', ['clean', 'sass', 'js', 'copy-images']);
