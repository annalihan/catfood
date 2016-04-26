;!function($) {

    if (!$.os.tablet && !$.os.phone && !$.os.ipod) {
        return;
    }

    if (window.devicePixelRatio && devicePixelRatio > 1) {
        var testEle = document.createElement('div');
        testEle.style.border = '.5px solid transparent';
        document.body.appendChild(testEle);
        if (testEle.offsetHeight == 1) {
            document.querySelector('html').classList.add('hairline');
        }
        document.body.removeChild(testEle);
    }
}(Zepto);
