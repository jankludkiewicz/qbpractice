YUI().use('node-base', function(Y) {
    function init() {
        Y.all("#clear_practice_history").on('click', function(e) {
            var args = {'url':e.currentTarget.get('href'), 'message':'Are you sure you want to clear all <b>practice history</b>? All question attempts including flags, sessions and its results will be lost.'};
            M.util.show_confirm_dialog(e, args);
            return false;
        });
    }
 
 Y.on("domready", init);
});