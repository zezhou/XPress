/**
 * @author axu
 */
(function($){
var Table=function(target,data,options){
    this.options=options;
    this.target=target;
    this.data=data;
};

Table.prototype.show=function(){
    $(this.target).html(this.render());
};

Table.prototype.render=function(){
    var tableHTML="<table>";
    if(this.options.th) tableHTML+=this.getThead();   
    if(this.data)tableHTML+=this.getTbody();
    tableHTML+="</table>";
    return tableHTML;
};

Table.prototype.getThead=function(){
    var tableHTML=""
    var thead=this.options.th;
    tableHTML+="<thead>";
    for(var i=0 ; i<thead.length;i++){
        var th=thead[i];
        tableHTML+="<th>"+this.getContent(th,thead)+"</th>";
    }
    tableHTML+="</thead>";
    return tableHTML;
}

Table.prototype.getTbody=function(){
    var data=this.data;
    var tds=this.options.td;
    var tableHTML="";
    for(var i=0 ; i<data.length;i++){
        var row=data[i];
        tableHTML+="<tr>";
        for(var j =0;j<tds.length;j++){
            if (tds[j]){
                var tr=tds;
            }else if (tds[j]===null){
                var tr=false;
            }else{
                var tr=row;
            }
            if(tr){
                var td=tr[j];
                tableHTML+="<td>"+this.getContent(td,row)+"</td>";
            }
        }
        tableHTML+="</tr>";
    }
    return tableHTML;
}

Table.prototype.getContent=function(item,data){
    if (typeof item == 'function'){
        return item.call(this,data);
    }else{
        return item;
    }
}


$.extend({
    "table":function(target,data,options){
        var oTable=new Table(target,data,options);
        oTable.show();
    }
});

})(jQuery);

