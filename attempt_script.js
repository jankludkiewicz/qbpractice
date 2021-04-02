/*var flag = document.querySelector('div.questionflag').childNodes[1];
console.log(flag);
flag.addEventListener("change", flipFlag);*/

document.querySelector('input[id="changer"]').addEventListener("click", consoleShow);

function flipFlag() {
	var flag = document.querySelector('input.questionflagvalue');
	console.log(flag);
	var newstate = flag.value=='1'?true:false;
	var qid = document.getElementById('questionid').value;
	var toggleurl = 'toggleflag.php?qid='+qid+'&newstate='+newstate
	$ajax({url: toggleurl});
}

function consoleShow() {
	var flag = document.querySelector('input.questionflagvalue');
	flag.addEventListener("change", flipFlag);
	console.log(flag);
}