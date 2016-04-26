#gulp-steel-css-fix

Use to fix css rules

###Usage

use glob pattern to filter your file path;

the filter could also be Array or String, like: 

```JavaScript
{filter:["pages/*.*"]} 
{filter:"lib/*.*"}
```
In your steel-gulpfile, you can use in this way:

```JavaScript
var gscf = require('gulp-steel-css-fix');

gscf({ 
    prefix: true,//bool
    filter:["lib/*.*","pages/*.*"]//Array or String 
})
```

###Example
If your file path is "src/css/pages/pageA.css", the filter(sub-path) pattern should be "pages/*.*", like:

```JavaScript
function testCss(){
 	gulp.src(['src/css/**/*.*'])
        .pipe($.steelCssfix({
             prefix: true, 
             filter:["pages/pointsmall/*.*"] 
        }))
	.pipe(gulp.dest(front_base + '/css/'));
}
```
in the function testCss, the whole file path is "src/css/pages/pointsmall/*.*"
