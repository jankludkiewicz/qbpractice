var flag = document.querySelectorAll('input[type="hidden"]');
console.log(flag);

function flipFlag() {
	var flag = document.querySelector('input.questionflagvalue');
	var newstate = flag.value=='1'?true:false;
	var qid = document.getElementById('questionid').value;
	var toggleurl = 'toggleflag.php?qid='+qid+'&newstate='+newstate
	$ajax({url: toggleurl});
}