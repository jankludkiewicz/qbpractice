var flag = document.querySelector('input[class="questionflagvalue"]');

initFlagListener();

function initFlagListener() {
		document.querySelector('span[class="questionflagimage"').addEventListener("click", flipFlag);
}

function flipFlag() {
	var newstate = flag.value=="1"?true:false;
	var qid = document.getElementById('questionid').value;
	var toggleurl = "toggleflag.php?qid="+qid+"&newstate="+newstate
	$ajax({url: toggleurl});
}