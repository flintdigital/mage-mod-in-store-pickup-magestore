// load color for input text in custom style

function loadColor_storepickup(click_id, map){
   new colorPicker('pin_color',{
        previewElement:'pin_color',
        inputElement:'pin_color',
        eventName:click_id ,
        color:'#'+$('pin_color').value,
        onChange:function(){
            if(map == '1')
                geocodeMap();
        }
    });

      
    
}

function toggleCustomValueElements(checkbox, container, excludedElements, checked){
    if(container && checkbox){
        var ignoredElements = [checkbox];
        if (typeof excludedElements != 'undefined') {
            if (Object.prototype.toString.call(excludedElements) != '[object Array]') {
                excludedElements = [excludedElements];
            }
            for (var i = 0; i < excludedElements.length; i++) {
                ignoredElements.push(excludedElements[i]);
            }
        }
        //var elems = container.select('select', 'input');
        var elems = Element.select(container, ['select', 'input', 'textarea', 'button', 'img']);
        var isDisabled = (checked != undefined ? checked : checkbox.checked);
        elems.each(function (elem) {
            if (checkByProductPriceType(elem)) {
                var isIgnored = false;
                for (var i = 0; i < ignoredElements.length; i++) {
                    if (elem == ignoredElements[i]) {
                        isIgnored = true;
                        break;
                    }
                }
                if (isIgnored) {
                    return;
                }
                elem.disabled=isDisabled;
                if (isDisabled) {
                    elem.addClassName('disabled');
                } else {
                    elem.removeClassName('disabled');
                }
                if(elem.tagName == 'IMG') {
                    isDisabled ? elem.hide() : elem.show();
                }
            }
        })
    }
} 


