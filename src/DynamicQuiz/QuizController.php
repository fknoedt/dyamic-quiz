<?php

namespace DynamicQuiz;

use Api\ApiController;
use Common\Template;
use\Exception;

class QuizController
{
    /**
     * Template engine
     * @TODO: clean non-used placeholders,
     * @var
     */
    var $template;

    /**
     * QuizController constructor.
     * Template engine is not passed as Dependency Injection as this object is instantiated and the methods are called generically (callback function) on ApiRouter
     */
    public function __construct()
    {
        // TODO: create interface for template systems
        $this->template = new Template(VIEWS_DIR);
    }

    /**
     * return HTML template parsed with {$VARIABLES} set
     * @param $page
     * @param $vars
     * @return string
     * @throws \Exception
     */
    public function getHtml($page, $vars = null)
    {
        $tplFile = $page . '.inc.tpl.php';

        // default variables -- dynamic controllers would be the approach if the due date was not short =(
        switch ($page) {

            case 'new-quiz':

                // get select from DB query
                $quizSelect = Quiz::getHtmlSelect($this->template);

                $this->template->addVar('QUIZ_SELECT', $quizSelect);

                $categorySelect = Category::getHtmlSelect($this->template);

                $this->template->addVar('CATEGORY_SELECT', $categorySelect);

                $difficultySelect = Difficulty::getHtmlSelect($this->template);

                $this->template->addVar('DIFFICULTY_SELECT', $difficultySelect);

                break;

            case 'question':
            case 'report':

                // retrieve quiz
                $quizId = @$_SESSION['quizId'];

                if (!$quizId) {
                    throw new Exception(__METHOD__ . ': quizId not in session');
                }

                $quiz = Quiz::retrieve($quizId);

                if (! is_object($quiz)) {
                    throw new Exception(__METHOD__ . ': invalid session quizId');
                }

                // get quiz info
                $this->template->addVar('QUIZ_NAME', $quiz->getName());
                $this->template->addVar('QUIZ_CATEGORY', $quiz->getCategory()->getName());
                $this->template->addVar('QUIZ_DIFFICULTY', $quiz->getDifficulty());
                $this->template->addVar('PERCENT_COMPLETE', $quiz->getPercentComplete($_SESSION['userId']));

                $this->template->addVar('TOTAL_QUESTIONS', $_SESSION['numberOfQuestions']);

                $question = null;

                if($page == 'question') {

                    // question received: retrieve
                    if (isset($vars['questionId'])) {
                        $question = Question::retrieve($vars['questionId']);
                    }
                    // question not defined: get first not responded
                    else {
                        $question = Question::firstUnansweredQuestion($_SESSION['userId'], $_SESSION['quizId']);
                    }

                }

                // if all questions were answered, the above method will return null
                if($question) {

                    // bind vars to the template
                    $this->template->addVar('QUESTION_NUMBER', $question->getQuestionNumber());
                    $this->template->addVar('QUESTION_TEXT', $question->getText());
                    $this->template->addVar('ANSWERS', $question->getAnswers());

                    // create html elements for each possible answer for the question
                    $answerButtons = $question->getHtmlAnswerButtons();

                    $this->template->addVar('ANSWERS', $answerButtons);

                    break;

                }
                // all questions answered and no question defined
                else {
                    $this->template->addVar('USERNAME', $_SESSION['username']);
                    $this->template->addVar('CORRECT_ANSWERS', $quiz->getNumberOfCorrectAnsweredQuestions($_SESSION['userId']));

                    $this->template->addVar('RESULT_MSG', $quiz->getResultMessage($_SESSION['userId']));

                    $tplFile = 'report.inc.tpl.php';
                }

        }

        if (!empty($vars)) {
            $this->template->addVars($vars);
        }

        $tpl = $this->template->fetchTpl($tplFile);

        // always add quizId (the client side's global var might not have been instantiated yet) when available
        if (isset($_SESSION['quizId'])) {
            $tpl .= $this->template->javascript("window.quizId = {$_SESSION['quizId']};");
        }

        return $tpl;
    }

    /**
     * detect which page the user should land
     * @return string
     */
    public function getCurrentPage()
    {
        $page = '';

        // user not created:
        if (!isset($_SESSION['userId'])) {
            $page = 'new-user';
        } // no quiz created or selected
        elseif (!isset($_SESSION['quizId'])) {
            $page = 'new-quiz';
        } // quiz defined: any answer?
        elseif (!isset($_SESSION['lastAnswer'])) {
            $page = 'question';
        }

        return $page;

    }

    /**
     * detect current page and return it's html
     * @return html
     */
    public function getCurrentHtmlPage()
    {
        $page = $this->getCurrentPage();
        return $this->getHtml($page, $_SESSION);
    }

    /**
     * return html page for the given question
     * @param $questionId
     * @return string
     */
    public function getQuestionHtml($questionId)
    {
        $page = 'question';
        $vars = ['questionId' => $questionId];

        return $this->getHtml($page, $vars);
    }

    /**
     * create or retrieve an existing user
     * @return array|mixed
     */
    public function createOrRetrieveUser()
    {
        $post = ApiController::getPostData();

        $username = $post['username'];

        $user = User::createOrRetrieve($username);

        // set in the session (this is needed to the page navigation)
        $_SESSION['userId'] = $user->getId();
        $_SESSION['username'] = $user->getName();

        return $user->getId();
    }

    /**
     * create a new quiz
     * @return array|mixed
     */
    public function createQuiz()
    {
        $post = ApiController::getPostData();

        \Database::getConnection()->beginTransaction();

        $quiz = Quiz::createFromApi($post);

        \Database::getConnection()->commit();

        // zero questions answered
        $this->writeQuizToSession($quiz, 0);

        return $quiz;
    }

    /**
     * retrieve quiz from DB, set session and return object
     * @param $quizId
     * @return string
     */
    public function retrieveQuiz($quizId)
    {
        $quiz = Quiz::retrieve($quizId);

        $this->writeQuizToSession($quiz);

        return $quiz;

    }

    /**
     * set quiz info in the session (this is needed for page navigation)
     * user is read from $_SESSION['userId']
     * @param Quiz $quiz
     * @param int $numberOfAnsweredQuestions
     * @throws Exception
     */
    public function writeQuizToSession(Quiz $quiz, $numberOfAnsweredQuestions = -1)
    {
        $_SESSION['quizId'] = $quiz->getId();
        $_SESSION['quizName'] = $quiz->getName();
        $_SESSION['numberOfQuestions'] = $quiz->getNumberOfQuestions();

        // if not set, retrieve the number of users from the database
        if ($numberOfAnsweredQuestions === -1) {

            // read user from session
            if (!isset($_SESSION['userId'])) {
                throw new Exception(__METHOD__ . '$_SESSION["userId"] not set');
            }
            $_SESSION['numberOfAnsweredQuestions'] = $quiz->getNumberOfAnsweredQuestions($_SESSION['userId']);

        }

        $_SESSION['numberOfAnsweredQuestions'] = $numberOfAnsweredQuestions;
    }

    /**
     * deletes quiz_id from the session for the user to start a new one
     * @return string
     */
    public function startNewQuiz()
    {
        unset($_SESSION['quizId']);

        return 'OK';
    }

    /**
     * delete current user's answers and start a new quiz
     * @param $quizId
     * @return mixed
     * @throws Exception
     */
    public function deleteUserQuiz($quizId)
    {
        throw new Exception(__METHOD__ . " IS CURRENTLY BLOCKED in favor of startNewQuiz()");

        $userId = $_SESSION['userId'];

        if(! $userId) {
            throw new Exception(__METHOD__ . ": userId not stored in session");
        }

        // do you really wanna erase the user's answers? why if he can go back to the quiz and start from where he left with no extra-effors?

        $response = UserAnswer::deleteByUser($quizId, $userId);

        unset($_SESSION['quizId']);

        return $response;
    }

    /**
     * destroy the user's session so he can start all over
     * @return bool
     */
    public function destroySession()
    {
        session_destroy();

        return true;
    }

    /**
     * save UserAnswer
     * @param $questionId
     * @param $answerId
     * @return string
     */
    public function postAnswer($questionId, $answerId)
    {
        // get question and answer
        $question = Question::retrieve($questionId);

        $answer = Answer::retrieve($answerId);

        // correct answer is a text column in the question table
        $correctAnswer = ($answer->getValue() == $question->getCorrectAnswer());

        $userAnswer = new UserAnswer($_SESSION['userId'], $answerId, $question->getId(), $correctAnswer);

        $userAnswer->save();

        // TODO: handle OpenTdb html entities like &#039;

        $response = [
            'answer' => self::htmlDecode($answer->getValue())
        ];

        if($correctAnswer) {
            $response['correctOrWrong'] = 'CORRECT';
        }
        else {
            $response['correctOrWrong'] = 'WRONG';
            $response['correctAnswer'] = self::htmlDecode($question->getCorrectAnswer());

        }

        return $response;
    }

    /**
     * apply html_entity_decode() and convert some particular html entities
     * @param $htmlText
     * @return mixed
     */
    public static function htmlDecode($htmlText)
    {
        return str_replace(['&#039;'], ["'"], html_entity_decode($htmlText));
    }

}