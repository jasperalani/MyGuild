let gulp = require('gulp');
let sass = require('gulp-sass');

gulp.task('sass', function(){
    return gulp.src('src/style.scss')
        .pipe(sass())
        .pipe(gulp.dest('src/css/'))
});

gulp.task('watch-sass', function(){
    gulp.watch('src/style.scss', gulp.series('sass'));
});