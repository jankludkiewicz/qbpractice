M.util.js_pending('qbpractice_flags');
Y.use('core_question_flags', function(Y) {
	M.core_question_flags.add_listener(manageFlags);
	M.util.js_complete('qbpractice_flags'); 
});

YUI().use('node-base', function(Y) {
    function init() {
        Y.all("input[name='finish']").on('click', function(e) {
            var args = {'message':'Are you sure you want to finish current session? All the finished and unfinished progress is going to be saved and you will not be able to attempt this session any more. Results can be accessed through summary table for future reference.'};
            M.util.show_confirm_dialog(e, args);
            return false;
        });
    }
 
 Y.on("domready", init);
});

function manageFlags() {
	// Send AJAX query
	var questionid = document.querySelector('input[id="questionid"]').value;
	var newstate = document.querySelector('input.questionflagvalue').value;
	$.ajax({url: 'toggleflag.php?qid='+questionid+'&newstate='+newstate});
	
	// Change flag in navigation menu
	var qnbutton = document.querySelector('a.qnbutton.thispage');
	qnbutton.classList.remove('flagged');
	if (newstate == 1) qnbutton.classList.add('flagged');
}