/**
 * script required by main Dynamic-Quiz page
 */

/**
 * global quiz id
 */
var quizId;

/**
 * onload
 */
$( document ).ready(function() {

    // checks if the console length has been exceeded every 20s (and trims it)
    setTimeout('consoleLengthMonitor',20000);

    loadPage(function() {successMsg('Hello') });

});

/**
 * display central notification
 * @param msg
 * @param classname
 */
function centeredNotification(msg, classname) {

    $('#by_fk').notify(msg, { position:"bottom center", className:classname});

}

/**
 * central success notification
 * @param txt
 */
function successMsg(txt) {
    centeredNotification(txt, 'success');
}

/**
 * central error notification
 * @param txt
 */
function errorMsg(txt) {
    centeredNotification(txt, 'error');
}

/**
 * display txt warning notification below given inputId
 * @param txt
 * @param inputId
 */
function warningBelowInput(txt, inputId) {
    $('#' + inputId).notify(txt, { position:"bottom center", className:"warning"});
}

/**
 * input has any value?
 * @param inputId
 * @param msg
 * @returns {boolean}
 */
function validateInput(inputId, msg) {

    if(! $('#' + inputId).val()) {

        warningBelowInput(msg, inputId);
        $('#' + inputId).focus();

        return false;

    }

    return true;
}

/**
 * helper/wrapper to read input value
 * @param inputId
 * @returns {*|jQuery}
 */
function getInputValue(inputId) {
    return $('#' + inputId).val();
}

/**
 * write html to built-in console log
 * @param log
 */
function consoleLog(log) {
    $('#console_log').prepend(log + '<br/>');
}

/**
 * load current page within the white main div
 */
function loadPage(callback) {

    // $('#main_div').innerHeight(300);

    // get current page to be requested through index.ajax
    return $.ajax({
        url: "/api/html",
        type: 'GET',
        success: function (data) {

            // display file.inc.tpl on the main div
            $('#main_div').html(
                data.data
            );

            consoleLog('page loaded');

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {

            errorMsg('Internal Error');

            consoleLog('API Error (' + errorThrown + ') ' + XMLHttpRequest.responseText);

        }
    }).done(callback);

}

/**
 * create or retrieve an user
 * send POST to /api/user
 */
function submitUser() {

    var formData = new Object();

    if(! $('#username').val()) {

        warningBelowInput('Enter your user name', 'welcomeTxt');
        $('#username').focus();

        return false;

    }

    formData.username = getInputValue('username');

    // json payload
    var payload = JSON.stringify(formData);

    $.ajax({
        url: "/api/user",
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json;charset=UTF-8;',
        processData: false,
        data: payload,
        createOrRetrieve: 'retrieve', // default action for page control
        statusCode: {
            200: function() { this.createOrRetrieve = 'retrieve' },
            201: function() { this.createOrRetrieve = 'create' },
        },
        success: function (data) {

            userId = data.data;

            consoleLog('user ' + userId + ' retrieved');

            loadPage();

        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {

            errorMsg('Internal Error');

            consoleLog('API Error (' + errorThrown + ') ' + XMLHttpRequest.responseText);
        }
    });
}

/**
 * create or retrieve a quiz
 * send POST to /api/quiz
 */
function submitQuiz() {

    var createOrRetrieve = '';
    var endpoint = '';
    var method = '';

    var payload = null;

    var formData = new Object();

    formData.quiz_id = $('#quiz_id').val();

    // existing quiz seleected
    if(formData.quiz_id > 0) {
        createOrRetrieve = 'retrieve';
        endpoint = '/api/quiz/' + formData.quiz_id;
        method = 'GET';
    }
    else {

        // validate and read form
        if(! validateInput('quiz_name', 'Choose a quiz or enter a new one')) return;
        formData.quiz_name = getInputValue('quiz_name');

        if(! validateInput('number_of_questions', 'Enter the number of questions')) return;
        formData.number_of_questions = getInputValue('number_of_questions');

        if(! validateInput('category_id', 'Select a Category')) return;
        formData.category_id = getInputValue('category_id');

        if(! validateInput('difficulty', 'Select a Difficulty')) return;
        formData.difficulty = getInputValue('difficulty');

        createOrRetrieve = 'create';
        endpoint = '/api/quiz';
        method = 'POST';

        // json payload
        payload = JSON.stringify(formData);

    }

    $.ajax({
            url: endpoint,
            type: method,
            dataType: 'json',
            contentType: 'application/json;charset=UTF-8;',
            processData: false,
            data: payload,
            success: function (data) {

                if(createOrRetrieve == 'create') {
                    successMsg('Quiz ' + data.name + ' created');
                }
                else {
                    successMsg('Quiz ' + data.name + ' retrieved');
                }

                quizId = data.id;

                consoleLog('quiz ' + createOrRetrieve + ' (id: ' + quizId + ')');

                loadPage();

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {

                errorMsg('Internal Error');

                consoleLog('API Error (' + errorThrown + ') ' + XMLHttpRequest.responseText);
            }
        })
        .done(

        );

}

/**
 * save user's response
 * send POST to /api/user
 * @param questionId
 * @param answerId
 * @returns {boolean}
 */
function submitAnswer(questionId, answerId) {

    consoleLog('submitting answer ' + answerId + ' (question ' + questionId + ')');

    $.ajax({
        url: "/api/question/" + questionId + "/answer/" + answerId,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json;charset=UTF-8;',
        processData: false,
        success: function (data) {

            // TODO: handle html chars
            if(data.correctOrWrong == 'CORRECT') {
                successMsg('Correct! \n' + data.answer);
            }
            else {
                warningBelowInput('Wrong! \nCorrect answer: ' + data.correctAnswer, 'by_fk')
            }

            loadPage();

        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {

            errorMsg('Internal Error');

            consoleLog('API Error (' + errorThrown + ') ' + XMLHttpRequest.responseText);
        }
    });
}

/**
 * destroy session and go back to the initial page
 */
function newQuiz() {

    if(! quizId) {
        warningBelowInput("You didn't start your quiz", 'by_fk');
        return;
    }

    if(! window.confirm('Are you sure you want to start a new quiz for this session?'))
        return;

    $.ajax({
        url: "/api/quiz/" + quizId + '/session',
        type: 'DELETE',
        dataType: 'json',
        contentType: 'application/json;charset=UTF-8;',
        success: function (data) {

            successMsg("Let's start again");

            consoleLog('quiz ' + quizId + ' deleted');

            loadPage();

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {

            errorMsg('Internal Error');

            consoleLog('API Error (' + errorThrown + ') ' + XMLHttpRequest.responseText);
        }
    });

}

/**
 * destroy session and go back to the initial page
 */
function newSession() {

    if(! window.confirm('Are you sure you want to start a new session?'))
        return;

    $.ajax({
        url: "/api/user",
        type: 'DELETE',
        dataType: 'json',
        contentType: 'application/json;charset=UTF-8;',
        success: function (data) {

            quizId = null;

            consoleLog('session destroyed');

            successMsg("Ok, I forgot who you are");

            loadPage();

        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {

            errorMsg('Internal Error');

            consoleLog('API Error (' + errorThrown + ') ' + XMLHttpRequest.responseText);
        }
    });

}

/**
 * ensures console will be rotated (reseted) when it exceeds 1000 characters
 */
function consoleLengthMonitor() {

    if(document.getElementById('console_log').innerHTML.length > 1000)
        document.getElementById('console_log').innerHTML = 'log rotated';

}

/**
 * shows/hides the javascript console
 */
function toggleConsole() {

    $('.console').slideToggle('slow');

}

/**
 * tests if a variable is empty
 * @param obj
 * @returns {boolean}
 */
function isEmpty(obj) {

    for(var key in obj) {
        if(obj.hasOwnProperty(key))
            return false;
    }

    return true;

}

/**
 * common function for testing if string is a json (yes, that's - as some other javascript solutions - the recommended way of doing it)
 * @param str
 * @returns {boolean}
 */
function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

/**
 * closes any action modal
 * @param action
 */
function formModalClose() {

    $.modal.close();

}