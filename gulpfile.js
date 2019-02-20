const gulp = require('gulp');
const sass = require('gulp-sass');
const {exec} = require('child_process');

var phpServ;

function dev(){
  exec('docker-compose up -d', () => {
    gulp.watch(['src/scss/*.scss'], {ignoreInitial: false}, buildSass);
    phpServ = exec('php -S localhost:8080 -t public');
    return phpServ;
  });
}

function buildSass() {
  return gulp.src('src/scss/*.scss')
    .pipe(sass())
    .pipe(gulp.dest('public/css'));
}

exports.dev = dev;
exports.sass = buildSass;

process.on('SIGINT', () => {
  console.log('\nReceived SIGINT, cleaning up...');
  phpServ.kill();
  exec('docker-compose down', process.exit);
});
