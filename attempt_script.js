var flag = document.querySelector('div.questionflag').childNodes[1];
flag.addEventListener("change", flipFlag);

function flipFlag() {
	var flag = document.querySelector('input.questionflagvalue');
	console.log(flag);
	var newstate = flag.value=='1'?true:false;
	var qid = document.getElementById('questionid').value;
	var toggleurl = 'toggleflag.php?qid='+qid+'&newstate='+newstate
	$ajax({url: toggleurl});
}