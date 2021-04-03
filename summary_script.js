YUI().use('node-base', function(Y) {
    function init() {
        Y.all(".clear_practice_history").on('click', function(e) {
            var args = {'url':e.currentTarget.get('href'),
                'message':'Are you sure you want to clear all <b>' + 
                  e.currentTarget.get('title') + 
                     '</b>? All question attempts including <b>flags</b>, sessions and its results are going to be lost.'};
            M.util.show_confirm_dialog(e, args);
            return false;
        });
    }
 
 Y.on("domready", init);
 
 
});