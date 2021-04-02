/*var flag = document.querySelector('div.questionflag').childNodes[1];
console.log(flag);
flag.addEventListener("change", flipFlag);*/

document.querySelector('input[id="changer"]').addEventListener("click", consoleShow);

function flipFlag() {
	var flag = document.querySelector('input.questionflagvalue');
	var questionflagpostdata = document.querySelector('input.questionflagpostdata').value;
	var toggleurl = 'toggleflag.php?'+questionflagpostdata;
	$.ajax({url: toggleurl, success: function(result){alert(result);}});
}

function consoleShow() {
	var flag = document.querySelector('input.questionflagvalue');
	flipFlag();
	console.log(flag);
}