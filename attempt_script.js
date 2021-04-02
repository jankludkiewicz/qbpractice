M.util.js_pending('qbpractice_flags');
Y.use('core_question_flags', function(Y) {
	M.core_question_flags.add_listener(ajaxDBFlagQuery);
	M.util.js_complete('qbpractice_flags'); 
});

function ajaxDBFlagQuery() {
	var flag = document.querySelector('input.questionflagvalue');
	var questionid = document.querySelector('input[id="questionid"]').value;
	var newstate = flag.value;
	var toggleurl = 'toggleflag.php?qid='+questionid+'&newstate='+newstate;
	$.ajax({url: toggleurl});
}