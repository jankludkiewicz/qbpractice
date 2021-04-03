YUI().use('node-base', function(Y) {
    function init() {
        Y.all(".delete_item").on('click', function(e) {
            var args = {'url':e.currentTarget.get('href'),
                'message':'Are you sure you want to delete <b>' + 
                  e.currentTarget.get('title') + 
                     '</b> ? All fields and data associated with this form will be lost'};
            M.util.show_confirm_dialog(e, args);
            return false;
        });
    }
 
 Y.on("domready", init);
 
 
});