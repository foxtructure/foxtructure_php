var gulp = require('gulp'),
    sass = require('gulp-sass'),
    csso = require('gulp-csso'),
    autoprefixer = require('gulp-autoprefixer'),
    plumber = require('gulp-plumber'),
    uglify = require('gulp-uglify'),
    pump = require('pump'),
    rename = require('gulp-rename');

gulp.task('sass', function(done) {
    return gulp.src('dev/scss/**/*.scss')
    .pipe(plumber())
    .pipe(autoprefixer('last 10 versions'))
    .pipe(sass.sync())
    .pipe(csso())
    .pipe(gulp.dest('app/css'));
    done();
});

gulp.task('compress', function(done) {
    pump([
        gulp.src('dev/js/*.js'),
        uglify(),
        rename({ suffix: '.min'}),
        gulp.dest('app/js')
    ], done );
});

gulp.task('watch', function(done) {
    gulp.watch('dev/scss/**/*.scss', gulp.series('sass'));
    gulp.watch('dev/js/*.js', gulp.series('compress'));
    done();
});

gulp.task('default', gulp.series('sass', 'compress', 'watch'));