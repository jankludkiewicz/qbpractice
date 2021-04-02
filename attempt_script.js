M.util.js_pending('random606772a4ef9cb1');
Y.use('core_question_flags', function(Y) {
	M.core_question_flags.add_listener(flipFlag);
	M.util.js_complete('random606772a4ef9cb1'); 
}

M.core_question_flags.add_listener(flipFlag);

function flipFlag() {
	var flag = document.querySelector('input.questionflagvalue');
	var questionflagpostdata = document.querySelector('input.questionflagpostdata').value;
	var toggleurl = 'toggleflag.php?'+questionflagpostdata;
	$.ajax({url: toggleurl, success: function(result){alert(result);}});
}