var noofquestions = 0;

var formElements = document.querySelectorAll("input[type='checkbox'][name^='subcategories']");

console.log(formElements);
for (var i=0; i<formElements.length; i++) {
	formElements[i].addEventListener("change", function() {eventListener(this)}, false);
	questionElement = document.querySelector("input[name='"+formElements[i].name+"_noofquestions']");
	noofquestions += parseInt(questionElement.value);
}

function eventListener(a) {
	console.log(a.checked);
	questionElement = document.querySelector("input[name='"+a.name+"_noofquestions']");
	if (a.checked) noofquestions += parseInt(questionElement.value);
	else noofquestions -= parseInt(questionElement.value);
	updateRange();
}

function updateRange() {
	var rangeElement = document.getElementById('questionsno');
	if (rangeElement.value > noofquestions) rangeElement.value = noofquestions;
	if (noofquestions == 0) rangeElement.min = 0;
	rangeElement.max = noofquestions;
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value+" / "+noofquestions;
}