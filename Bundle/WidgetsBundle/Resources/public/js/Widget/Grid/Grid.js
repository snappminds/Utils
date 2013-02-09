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

jQuery.namespace('Snappminds.Utils.Widget.Grid');
		

Snappminds.Utils.Widget.Grid.Grid = function(id, actionUrl) 
{
    this.id = id;
    this.grid = $('#snappminds_utils_widget_grid_' + this.id)
    this.actionUrl = actionUrl;
    this.currentPage = 1;
    this.pageCount = 1;
    this.totalRowCount = '-';
    this.firstRowNumber = '-';
    this.lastRowNumber = '-';

    var self = this;												

    //Inicializar eventos
    $('li.snappminds_utils_widget_grid_paginator_first a', this.grid).click(function() {
        self.refresh(1);
    });

    $('li.snappminds_utils_widget_grid_paginator_previous a', this.grid).click(function() {
        self.refresh(self.currentPage - 1); 
    });

    $('li.snappminds_utils_widget_grid_paginator_next a', this.grid).click(function() {
        self.refresh(self.currentPage + 1); 
    });

    $('li.snappminds_utils_widget_grid_paginator_last a', this.grid).click(function() {
        self.refresh(self.pageCount); 
    });
				
    this.updatePaginator();
    this.updateStateInfo();
    this.refresh(1); 
};

Snappminds.Utils.Widget.Grid.Grid.prototype.updatePaginator = function()
{
    var buttons = $('ul.snappminds_utils_widget_grid_paginator li', this.grid);

    buttons.show();

    if (this.pageCount == 1)
        buttons.hide();

    if (this.currentPage == 1)
        $('.snappminds_utils_widget_grid_paginator_previous').hide();

    if (this.currentPage == this.pageCount)
        $('.snappminds_utils_widget_grid_paginator_next').hide();

}

Snappminds.Utils.Widget.Grid.Grid.prototype.updateStateInfo = function()
{
    $('dd.snappminds_utils_widget_grid_stateinfo_rowcount', this.grid).text(this.firstRowNumber + ' al ' + this.lastRowNumber + ' de ' + this.totalRowCount);
    $('dd.snappminds_utils_widget_grid_stateinfo_pagecount', this.grid).text(this.currentPage + ' de ' + this.pageCount);
}

Snappminds.Utils.Widget.Grid.Grid.prototype.refresh = function(page)
{
    $('tbody', this.grid).html('');
		
    var self = this;
        
    var url = this.actionUrl;
    if (url.indexOf('?') >= 0) {
        url = url + '&' + this.id + '[page]=' + page;
    } else {
        url = url + '?' + this.id + '[page]=' + page;
    }
        
        
        
    $.ajax({
        url: url,
        success: function( data ) {
            data = eval('(' + data + ')');
            $('tbody', self.grid).html(data.data);
            self.currentPage = parseInt(data.page);
            self.pageCount = parseInt(data.pageCount);
            self.totalRowCount = parseInt(data.totalRowCount);
            self.firstRowNumber = parseInt(data.firstRowNumber);
            self.lastRowNumber = parseInt(data.lastRowNumber);

            self.updatePaginator();
            self.updateStateInfo();
        }
    });
}