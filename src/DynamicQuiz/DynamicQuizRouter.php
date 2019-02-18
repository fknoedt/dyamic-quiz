<?php

namespace DynamicQuiz;
use Api\ApiRouter;

/**
 * Class DynamicQuizRouter
 */
class DynamicQuizRouter extends ApiRouter
{
    /**
     * each route is an array with 'method' (GET, POST, PUT, etc) + 'url' (you can use :variableName) + 'callback' (function to be called)
     * @var array
     */
    var $aRoute = [

        /** QUIZ CRUD **/
        [
            'method' => 'GET',
            'url' => 'quiz/:quizId',
            'callback' => 'DynamicQuiz\QuizController::retrieveQuiz'
        ],
        [
            'method' => 'POST',
            'url' => 'quiz',
            'callback' => 'DynamicQuiz\QuizController::createQuiz'
        ],
        [
            'method' => 'DELETE',
            'url' => 'quiz/:quizId/session',
            'callback' => 'DynamicQuiz\QuizController::startNewQuiz'
        ],

        /** QUIZ PAGES **/
        [
            'method' => 'GET',
            'url' => 'page',
            'callback' => 'DynamicQuiz\QuizController::getCurrentPage'
        ],
        [
            'method' => 'GET',
            'url' => 'html',
            'callback' => 'DynamicQuiz\QuizController::getCurrentHtmlPage'
        ],
        [
            'method' => 'GET',
            'url' => 'html/question/:questionId',
            'callback' => 'DynamicQuiz\QuizController::getQuestionHtml'
        ],
        [
            'method' => 'GET',
            'url' => 'html/:page',
            'callback' => 'DynamicQuiz\QuizController::getHtmlPage'
        ],
        [
            'method' => 'POST',
            'url' => 'user',
            'callback' => 'DynamicQuiz\QuizController::createOrRetrieveUser'
        ],
        [
            'method' => 'DELETE',
            'url' => 'user',
            'callback' => 'DynamicQuiz\QuizController::destroySession'
        ],

        /** QUESTIONS & ANSWERS **/
        [
            'method' => 'POST',
            'url' => 'question/:questionId/answer/:answerId',
            'callback' => 'DynamicQuiz\QuizController::postAnswer'
        ]
    ];

    /**
     * @TODO: implement json format in an array
     * // array to be converted to JSON and returned as response
    $aResponse = array(
    'status'	=> 'success', // success || error || warning
    'action'	=> '', // possible livetree button actions ($_POST['action'])
    'msg'		=> '', // main success message to the user
    'errorMsg'	=> '', // main error message
    'aNotify'	=> array() // js notifies = array('input_id' => 'message')
    );
     */

    /**
     * call (generic method on parent class) ApiRouter->handleRoute with the routes set on this class
     * @param $path
     * @return mixed
     * @throws \Api\ApiException
     */
    public function route($path)
    {
        return parent::handleRoute($path, $this->aRoute);
    }

}