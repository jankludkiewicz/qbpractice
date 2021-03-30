var noofquestions = 0;

var formElements = document.querySelectorAll("input[type='checkbox'][name^='subcategories']");

for (var i=0; i<formElements.length; i++) {
	formElements[i].addEventListener("change", function() {eventListener(this)}, false);
	questionElement = document.querySelector("input[name='"+formElements[i].name+"_noofquestions']");
	formElements[i].checked = true;
	noofquestions += parseInt(questionElement.value);
}

initRange();

function eventListener(a) {
	console.log(a.checked);
	questionElement = document.querySelector("input[name='"+a.name+"_noofquestions']");
	if (a.checked) noofquestions += parseInt(questionElement.value);
	else noofquestions -= parseInt(questionElement.value);
	updateRange();
}

function updateRange() {
	var rangeElement = document.getElementById('questionsno');
	if (noofquestions == 0) {
		rangeElement.min = 0;
		document.getElementById('id_submitbutton').disabled = true;
	}
	else {
		rangeElement.min = 1;
		document.getElementById('id_submitbutton').disabled = false;
	}
	rangeElement.max = noofquestions;
	if (rangeElement.value > noofquestions) rangeElement.value = noofquestions;
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value+" / "+noofquestions;
}

function initRange() {
	var rangeElement = document.getElementById('questionsno');
	if (noofquestions == 0) {
		rangeElement.min = 0;
		document.getElementById('id_submitbutton').disabled = true;
	}
	else {
		rangeElement.min = 1;
		document.getElementById('id_submitbutton').disabled = false;
	}
	rangeElement.max = noofquestions;
	rangeElement.value = Math.round(noofquestions/2);
	console.log(rangeElement.value);
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value+" / "+noofquestions;
}