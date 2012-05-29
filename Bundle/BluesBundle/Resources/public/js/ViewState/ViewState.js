/* TODO: Esto no va aca */
jQuery.namespace = function() {
	var a=arguments, o=null, i, j, d;
	for (i=0; i<a.length; i=i+1) {
		d=a[i].split(".");
		o=window;
		for (j=0; j<d.length; j=j+1) {
			o[d[j]]=o[d[j]] || {};
			o=o[d[j]];
		}
	}
	return o;
};

jQuery.namespace('Snappminds.Utils.Blues.ViewState');
		

Snappminds.Utils.Blues.ViewState.ViewState = function(requestParamName, data) 
{
	this.requestParamName = requestParamName;
	this.data = data
};

Snappminds.Utils.Blues.ViewState.ViewState.prototype.getData = function()
{
    return this.data;
};

Snappminds.Utils.Blues.ViewState.ViewState.prototype.getRequestParamName = function()
{
    return this.requestParamName;
};

Snappminds.Utils.Blues.ViewState.redirect = function( url, postData )
{
    var form = $("<form></form>");

    form.attr('action', url);
    form.attr('method', 'POST');
    
    var hidden = $('<input type="hidden"></input>');
    
    hidden.val(snappminds_utils_blues_viewstate_viewstate.getData());
    hidden.attr('name', snappminds_utils_blues_viewstate_viewstate.getRequestParamName());
    
    form.append(hidden);
    
    $('body').append(form);
    
    form.submit();
};

Snappminds.Utils.Blues.ViewState.renderHiddenViewState = function( formId, snappminds_utils_blues_viewstate_viewstate )
{
    var form = $("#" + formId);
    
    var hidden = $('<input type="hidden"></input>');
    
    hidden.val(snappminds_utils_blues_viewstate_viewstate.getData());
    hidden.attr('name', snappminds_utils_blues_viewstate_viewstate.getRequestParamName());
    hidden.appendTo(form);
    
};