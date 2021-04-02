M.util.js_pending('qbpractice_flags');
Y.use('core_question_flags', function(Y) {
	M.core_question_flags.add_listener(ajaxDBFlagQuery);
	M.util.js_complete('qbpractice_flags'); 
});

function ajaxDBFlagQuery() {
	var flag = document.querySelector('input.questionflagvalue');
	var questionflagpostdata = document.querySelector('input.questionflagpostdata').value;
	var toggleurl = 'toggleflag.php?'+questionflagpostdata;
	$.ajax({url: toggleurl, success: function(result){alert(result);}});
}