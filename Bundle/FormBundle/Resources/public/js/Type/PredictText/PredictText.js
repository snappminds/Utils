jQuery.namespace('Snappminds.Utils.Form.Type');

Snappminds.Utils.Form.Type.PredictText = function() 
{

}


$.extend(Snappminds.Utils.Form.Type.PredictText.prototype, Snappminds.Utils.Form.Type.Choice.prototype);


Snappminds.Utils.Form.Type.PredictText.prototype.initialize = function(id, ajaxUrl, ajaxDataParamName) 
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

    var entry = $('#' + this.getId() + '_value', this);
    $(entry).bind('keyup', $.proxy(this.onKeyUp, this));
    $(entry).bind('keydown', $.proxy(this.onKeyDown, this));
    $(entry).bind('keypress', $.proxy(this.onKeyPress, this));
    $(entry).bind('focusin', focusin_callback);
    $(entry).bind('focusout', focusout_callback);
        
    this.setCssPrefix('snappminds_utils_form_type_predicttext');
    
}

Snappminds.Utils.Form.Type.PredictText.prototype.applySelection = function() {    
    this.setValue(this.getSelectedChoice().attr('choice'));
    this.getChoiceListContainer().fadeOut();
}

Snappminds.Utils.Form.Type.PredictText.prototype.refresh = function() {

    var url = this.getAjaxUrl();


    if (url.indexOf('?') >= 0) {
        url += '&';
    } else {
        url += '?';
    }

    url += this.getAjaxDataParamName() + '=' + this.getValue();


    //Realizar llamada ajax
    $.ajax({
        url: url,
        context: this,
        success: function( data ) {
            var html = data;
            var self = this;

            var choices = this.getChoiceListContainer();

            $('*', choices).remove();

            if (this.focus) {
                choices.fadeIn();
                choices.html(html);


                $('.' + this.getCssPrefix() + '_choice', choices).click(function(e) {
                    self.setValue($(this).attr('choice'));
                    choices.fadeOut();
                });

                this.setSelectedIndex(0);
            }
        }
    });

}