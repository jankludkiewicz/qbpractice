// Define global variables
var studyPreference;
var studyPreferenceQuestionNumbers = []; // Indexes: 0 - allquestions, 1 - flagged, 2 - unseen, 3 - incorrect

// Initialize form
initQuestionCategorySelectors();
initTabs();

// Function definitions

/*
 * Updates numbers of questions after checking category selector
 */
function updateQuestionNumbers(input) {
	var specifier;
	switch(studyPreference) {
		case 0:
			specifier = "allquestions";
			break;
		case 1:
			specifier = "flagged";
			break;
		case 2:
			specifier = "unseen";
			break;
		case 3:
			specifier = "incorrect";
			break;
	}
	
	var categoryQuestionNumber = parseInt(document.querySelector("input[name='"+input.name+"_"+specifier+"']").value)
	
	if (input.checked) studyPreferenceQuestionNumbers[studyPreference] += categoryQuestionNumber;
	else studyPreferenceQuestionNumbers[studyPreference] -= categoryQuestionNumber;
	
	updateRange();
}

/*
 * Updates tabs after user selection
 */
function updateTabs(input) {
	studyPreference = parseInt(input.value)
	
	var selectedLabel;
	switch(studyPreference) {
		case 0:
		selectedLabel = document.getElementById('allquestions');
		break;
		case 1:
		selectedLabel = document.getElementById('flagged');
		break;
		case 2:
		selectedLabel = document.getElementById('unseen');
		break;
		case 3:
		selectedLabel = document.getElementById('incorrect');
		break;
		case 4:
		selectedLabel = document.getElementById('exam');
		break;
	}
	
	//Clear all labels
	labels = document.querySelectorAll("label[class='studypreference-label']");
	for (var i=0; i<labels.length; i++) labels[i].style = "box-shadow: none";
	
	//Select label
	selectedLabel.style = "box-shadow: 0 3px 0 -1px #fff, inset 0 5px 0 -1px #13cd4a;";
	
	initQuestionNumbers();
	updateQuestionCategorySelectors();
	initRange();
}

/*
 * Updates states of question category selectors based on question numbers in selected study preference
 */
function updateQuestionCategorySelectors() {
	var specifier;
	switch(studyPreference) {
		case 0:
			specifier = "allquestions";
			break;
		case 1:
			specifier = "flagged";
			break;
		case 2:
			specifier = "unseen";
			break;
		case 3:
			specifier = "incorrect";
			break;
	}
	
	var formElements = document.querySelectorAll("input[type='checkbox'][name^='subcategories']");
	for (var i = 0; i < formElements.length; i++) {
		var categoryQuestionNumber = parseInt(document.querySelector("input[name='"+formElements[i].name+"_"+specifier+"']").value)
		if (categoryQuestionNumber > 0) {
			formElements[i].disabled = false;
			formElements[i].checked = true;
		}
		else {
			formElements[i].disabled = true;
			formElements[i].checked = false;
		}
	}
}

/*
 * Updates question number range selector
 */
function updateRange() {
	var rangeElement = document.getElementById('questionsno');
	
	if (studyPreferenceQuestionNumbers[studyPreference] == 0) {
		rangeElement.min = 0;
		document.getElementById('id_submitbutton').disabled = true;
	}
	else {
		rangeElement.min = 1;
		document.getElementById('id_submitbutton').disabled = false;
	}
	
	rangeElement.max = studyPreferenceQuestionNumbers[studyPreference];
	if (rangeElement.value > studyPreferenceQuestionNumbers[studyPreference]) rangeElement.value = studyPreferenceQuestionNumbers[studyPreference];
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value+" / "+studyPreferenceQuestionNumbers[studyPreference];
}

/*
 * Pulls data out of form to get question numbers in specified studyPreference
 */
function initQuestionNumbers() {
	studyPreferenceQuestionNumbers[studyPreference] = 0;
	var formElements = document.querySelectorAll("input[type='checkbox'][name^='subcategories']");
	for (var i = 0; i < formElements.length; i++) {
		// Adjust specifier based on studyPreference
		var specifier;
		switch(studyPreference) {
			case 0:
				specifier = "allquestions";
				break;
			case 1:
				specifier = "flagged";
				break;
			case 2:
				specifier = "unseen";
				break;
			case 3:
				specifier = "incorrect";
				break;
		}
		
		// Find category question number by studypreference
		var categoryQuestionNumber = parseInt(document.querySelector("input[name='"+formElements[i].name+"_"+specifier+"']").value)
		studyPreferenceQuestionNumbers[studyPreference] += categoryQuestionNumber;
		
		// Change displayed question number in category
		var questionNoLabel = document.querySelector("label[id='"+formElements[i].name+"_questionnolabel']");
		questionNoLabel.innerHTML = "("+categoryQuestionNumber+")";
	}
}

/*
 * Initializes tabs: 
 * 1) adds eventListeners (change)
 * 2) marks first element
 */
function initTabs() {
	var radioTabInputs = document.querySelectorAll("input[name='studypreference']");
	
	for (var i = 0; i < radioTabInputs.length; i++) radioTabInputs[i].addEventListener("change", function() {updateTabs(this)}, false);
	
	// Check first element
	radioTabInputs[0].checked = true;
	updateTabs(radioTabInputs[0]);
}

/*
 * Initializes question category selectors:
 * adds eventListeners (change)
 */
function initQuestionCategorySelectors() {
	var formElements = document.querySelectorAll("input[type='checkbox'][name^='subcategories']");
	for (var i = 0; i < formElements.length; i++) formElements[i].addEventListener("change", function() {updateQuestionNumbers(this)}, false);
}

/*
 * Initializes question number range selector
 * 1) adjusts min, max and value of a range according to logic
 * 2) diables submit button when questionnumber = 0
 */
function initRange() {
	var rangeElement = document.getElementById('questionsno');
	
	if (studyPreferenceQuestionNumbers[studyPreference] == 0) {
		rangeElement.min = 0;
		document.getElementById('id_submitbutton').disabled = true;
	}
	else {
		rangeElement.min = 1;
		document.getElementById('id_submitbutton').disabled = false;
	}
	
	rangeElement.max = studyPreferenceQuestionNumbers[studyPreference];
	rangeElement.value = Math.round(studyPreferenceQuestionNumbers[studyPreference]/2);
	document.getElementById('questionsnodisplay').innerHTML = rangeElement.value+" / "+studyPreferenceQuestionNumbers[studyPreference];
}