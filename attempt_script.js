M.core_question_flags.add_listener(flipFlag);

function flipFlag() {
	var flag = document.querySelector('input.questionflagvalue');
	var questionflagpostdata = document.querySelector('input.questionflagpostdata').value;
	var toggleurl = 'toggleflag.php?'+questionflagpostdata;
	$.ajax({url: toggleurl, success: function(result){alert(result);}});
}