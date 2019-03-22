
module.exports.showerr = function(msg){
    if(msg != ""){
        Vue.$notify.error({
            title: '提醒',
            message: msg
        });
    }
}