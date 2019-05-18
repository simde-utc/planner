export default function chartistScroll(scrollbarOptions) {

    return _chartistScoll;

    function _writeStyles(styleName, cssRules) {
        var styleElement = document.getElementById(styleName);
        var pastCssRules = (styleElement && styleElement.textContent) ? styleElement.textContent : null;

        if (styleElement) {
            document.getElementsByTagName('head')[0].removeChild(
                styleElement);
        }

        styleElement = document.createElement('style');
        styleElement.type = 'text/css';
        styleElement.id = styleName;


        if (cssRules.length) {
            for (var css of cssRules) {
                styleElement.appendChild(document.createTextNode(css));
            }
        }
        else {
            styleElement.innerHTML = cssText;
        }

        document.getElementsByTagName('head')[0].appendChild(styleElement);
    }

    function _chartistScoll(chart) {

        var styleTag = document.createElement('style');

        console.log(chart.container.id);
        var cssRules = [
            '#' + chart.container.id + ' { overflow-x: scroll; overflow-y: hidden; }',
            '#' + chart.container.id + '::-webkit-scrollbar {  width: ' + ((scrollbarOptions && scrollbarOptions.width) ? scrollbarOptions.width : '10px' ) +  '; height: ' + ((scrollbarOptions && scrollbarOptions.height) ? scrollbarOptions.height : '10px' ) +  '; background-color: #F5F5F5; }',
            '#' + chart.container.id + '::-webkit-scrollbar * { background: transparent; }',
            '#' + chart.container.id + '::-webkit-scrollbar-thumb { border-radius: 14px; -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3); background: ' + ((scrollbarOptions && scrollbarOptions.scollbarColor) ? scrollbarOptions.scollbarColor + '!important' : 'rgba(0, 0, 0, 0.4) !important' ) +  '; }',
            '#' + chart.container.id + '::-webkit-scrollbar-track {  -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3); border-radius: 10px; background-color: ' + ((scrollbarOptions && scrollbarOptions.backgroundColor) ? scrollbarOptions.backgroundColor : '#F5F5F5' ) +  '; }',
        ];

        _writeStyles("_chartistStyles", cssRules);

    }

};
