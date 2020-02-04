let gulp = require('gulp');
let sass = require('gulp-sass');
let watch = require('gulp-watch');
var newer = require('gulp-newer');
var notify = require('gulp-notify');

gulp.task('sass', function(){
    return gulp.src('src/style.scss')
        .pipe(sass())
        .pipe(gulp.dest('src/css/'))
});

gulp.task('watch-sass', function(){
    gulp.watch('src/style.scss', gulp.series('sass'));
});

var projectWWW = 'dist';
var copySRC = ['src/**/*', '!src/**/*.{scss}'];

// Copy files task
gulp.task( 'copyFiles', function() {
    gulp.src( copySRC )
        .pipe( newer( projectWWW ) )
        .pipe( gulp.dest( projectWWW ) )
        .pipe( notify( { message: 'TASK: "copyFiles" Completed!', onLast: true } ) );
});

// Watch tasks
gulp.task( 'default', function () {
    gulp.watch( copySRC, gulp.series('copyFiles')  ); // Copy on file changes.
});