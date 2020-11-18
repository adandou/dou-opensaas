function checkresult(resstr) {
    var res = $.parseJSON(resstr);
    if(res.errcode !=0){
        alert(res.errmsg);
        return false
    }
    return res.result;
}
