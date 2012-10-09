
jQuery.namespace('Snappminds.Utils.Form.Type');		

Snappminds.Utils.Form.Type.Choice = function() 
{

};

Snappminds.Utils.Form.Type.Choice.prototype.initialize = function(id, ajaxUrl, ajaxDataParamName) 
{
    this.setId(id);
    this.setAjaxUrl(ajaxUrl);
    this.setAjaxDataParamName(ajaxDataParamName);
    
    this.timer = null;

    var self = this;                                       

    var focusout_callback = function(e) {
        self.focus = false;
        self.getChoiceListContainer().fadeOut();
    };

    var focusin_callback = function(e) {
        self.focus = true;
    };

    var entry = $('#' + this.getId() + '_entry', this);    
    $(entry).bind('keyup', $.proxy(this.onKeyUp, this));
    $(entry).bind('keydown', $.proxy(this.onKeyDown, this));
    $(entry).unbind('keypress');
    $(entry).bind('keypress', $.proxy(this.onKeyPress, this));
    $(entry).bind('focusin', focusin_callback);
    $(entry).bind('focusout', focusout_callback);
        
    this.setCssPrefix('snappminds_utils_form_type_choice');
    
}

Snappminds.Utils.Form.Type.Choice.prototype.getId = function() {
    return this.id;
}

Snappminds.Utils.Form.Type.Choice.prototype.setId = function(value) {
    this.id = value;
}

Snappminds.Utils.Form.Type.Choice.prototype.setAjaxUrl = function(value) {
    this.ajaxUrl = value;    
}

Snappminds.Utils.Form.Type.Choice.prototype.getAjaxUrl = function() {
    return this.ajaxUrl;
}

Snappminds.Utils.Form.Type.Choice.prototype.setAjaxDataParamName = function(value) {
    this.ajaxDataParamName = value;    
}

Snappminds.Utils.Form.Type.Choice.prototype.getAjaxDataParamName = function() {
    return this.ajaxDataParamName;
}

Snappminds.Utils.Form.Type.Choice.prototype.setCssPrefix = function(value) {
    this.cssPrefix = value;
}

Snappminds.Utils.Form.Type.Choice.prototype.getCssPrefix = function() {
    return this.cssPrefix;
}

Snappminds.Utils.Form.Type.Choice.prototype.onKeyUp = function(e) {                    
    switch (e.keyCode) {
        case 27:
            if (this.getChoiceListContainer().is(':visible')) {
                e.preventDefault();
                e.stopPropagation();
            }

        case 13:
            if (this.getChoiceListContainer().is(':visible')) {                            
                //Prevenir submit al presionar ENTER
                e.preventDefault();
            }
            break;
    }                    
}

Snappminds.Utils.Form.Type.Choice.prototype.onKeyDown = function(e) {
    switch (e.keyCode) {
        case 27:
            if (this.getChoiceListContainer().is(':visible')) {
                e.preventDefault();
                e.stopPropagation();
            }
            break;
        case 13:
            if (this.getChoiceListContainer().is(':visible')) {
                //Prevenir submit al presionar ENTER
                e.preventDefault();
            }
            break;
    }

}

Snappminds.Utils.Form.Type.Choice.prototype.onKeyPress = function(e) {

    switch (e.keyCode) {
        case 27:
            if (this.getChoiceListContainer().is(':visible')) {
                this.getChoiceListContainer().fadeOut();
                e.preventDefault();
                e.stopPropagation();
            }            
            break;
        case 13:
            if (this.getChoiceListContainer().is(':visible')) {
                //Aplicar la selección del usuario
                this.applySelection();
                //Prevenir submit al presionar ENTER
                e.preventDefault();
            }
            break;
        case 38: //UP
            //Cambiar selección hacia arriba
            this.setSelectedIndex(this.getSelectedIndex() - 1);
            break;
        case 40: //DOWN
            //Cambiar selección hacia abajo
            this.setSelectedIndex(this.getSelectedIndex() + 1);
            break;
        default:
            /* 
                Para evitar demasiado overloading al server,
                la lista se refresca solo cada 500ms.
            */
            if (e.charCode || e.keyCode == 8) {
                if (!this.timer) {                        
                    this.timer = setTimeout($.proxy(function() {                            
                        this.refresh();
                        clearTimeout(this.timer);
                        this.timer = null;                    
                    }, this), 500);
                }
            }
            break;

    }                    
}

Snappminds.Utils.Form.Type.Choice.prototype.getValueInput = function() {
    return $('#' + this.getId() + '_value', $(this));
}

Snappminds.Utils.Form.Type.Choice.prototype.setValue = function(value) {
    this.getValueInput().val(value);
    this.trigger("change");
}

Snappminds.Utils.Form.Type.Choice.prototype.getValue = function() {
    return this.getValueInput().val();
}

Snappminds.Utils.Form.Type.Choice.prototype.setTextValue = function(value) {
    $('#' + this.getId() + '_entry', $(this)).val(value);
}

Snappminds.Utils.Form.Type.Choice.prototype.getTextValue = function() {
    return $('#' + this.getId() + '_entry', $(this)).val();
}

Snappminds.Utils.Form.Type.Choice.prototype.getChoiceListContainer = function() {
    return $('.' + this.getCssPrefix() + '_choicelist-container', $(this));
}

Snappminds.Utils.Form.Type.Choice.prototype.getChoices = function() {
    return $('.' + this.getCssPrefix() + '_choice', this.getChoiceListContainer());
}

Snappminds.Utils.Form.Type.Choice.prototype.setSelectedIndex = function(value) {
    var items = this.getChoices();
    
    if (value < 0) value = 0;
    if (value > items.length - 1) value = items.length - 1;


    this.selectedIndex = value;

    items.removeClass(this.getCssPrefix() + '_choice_selected');
    $(items[value]).addClass(this.getCssPrefix() + '_choice_selected');

    var suggestionHeight = $(this.getChoices()[0]).height();
    var boxHeight = this.getChoiceListContainer().height();
    this.getChoiceListContainer().scrollTop(value * suggestionHeight - boxHeight / 2);    
}

Snappminds.Utils.Form.Type.Choice.prototype.getSelectedIndex = function() {
    return this.selectedIndex;
}

Snappminds.Utils.Form.Type.Choice.prototype.getSelectedChoice = function() {
    return $(this.getChoices()[this.getSelectedIndex()]);
}

Snappminds.Utils.Form.Type.Choice.prototype.applySelection = function() {    
    this.setValue(this.getSelectedChoice().attr('keyvalue'));
    this.setTextValue(this.getSelectedChoice().text());
    this.getChoiceListContainer().fadeOut();
}

Snappminds.Utils.Form.Type.Choice.prototype.refresh = function() {

    if (this.getTextValue() == '')
        return;
    
    var url = this.getAjaxUrl();

    /*
     * Determinar si hay que agregar a la url un ? o un & según si tiene
     * parámetros o no.
     */
    if (url.indexOf('?') >= 0) {
        url += '&';
    } else {
        url += '?';
    }

    url += this.getAjaxDataParamName() + '=' + this.getTextValue();
    
    
    //Realizar llamada ajax
    $.ajax({
        url: url,
        context: this,
        success: function( data ) {
            options = eval('('+ data + ')');

            var ul = $('ul', this.getChoiceListContainer());

            $('li', ul).remove();

            //Llenar el listado
            for (var i=0; i < options.length; i++) {
                var self = this;
                var li = $('<li class="' + this.getCssPrefix() + '_choice"></li>');

                li.attr('keyvalue', options[i].id);
                li.text(options[i].data);

                li.click({
                    elem: li
                }, function(e) {
                    self.setTextValue(e.data.elem.text());
                    self.setValue(e.data.elem.attr('keyvalue'));
                    self.getChoiceListContainer().fadeOut();
                });

                ul.append(li);                
            }
            
            this.setSelectedIndex(0);
            this.getChoiceListContainer().fadeIn();
        }
    });

}
