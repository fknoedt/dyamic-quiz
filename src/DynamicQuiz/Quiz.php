<?php

namespace DynamicQuiz;

use Common\Template;

class Quiz extends \Orm
{
    static $tableName = 'quiz';

    /**
     * primary key field name
     * @var string
     */
    static $pkFieldName = 'id';

    /**
     * QuizApi object which implements QuizApiInterface
     * @TODO: DI Container
     * @var
     */
    static $quizApi;

    /**
     * list of table fields and datatypes (for ORM purposes)
     * @var array
     */
    protected $attributes = [
        'id' => 'int',
        'name' => 'string',
        'api_url' => 'string',
        'category_id' => 'int',
        'number_of_questions' => 'int',
        'difficulty' => 'string',
        'created_at' => 'timestamp'
    ];

    /**
     * list of related (0.* 1) tables
     * tableName => [class, fk]
     * @var array
     */
    protected $belongsTo = [
        'category' => [
            'class' => Category::class,
            'fk' => 'category_id'
        ]
    ];

    /**
     * list of related (1 0.*) tables
     * tableName => [class, external fk]
     * @var array
     */
    protected $hasMany = [
        'question' => [
            'class' => Question::class,
            'fk' => 'quiz_id'
        ]
    ];

    /**
     * Quiz constructor.
     * @param null $name
     * @param null $apiUrl
     * @param null $numberOfQuestions
     */
    public function __construct($name = null, $apiUrl = null, $numberOfQuestions = null)
    {
        // create attributes based on $this->attributes
        parent::init($this);

        if ($name) {
            $this->setName($name);
        }

        if ($apiUrl) {
            $this->setApiUrl($apiUrl);
        }

        if ($numberOfQuestions) {
            $this->setNumberOfQuestions($numberOfQuestions);
        }
    }

    /**
     * count query to determine how many answers we have [for this quiz]
     * @param null $userId
     * @return mixed
     */
    public function getNumberOfAnsweredQuestions($userId = null)
    {
        /*if(! isset($this->number_of_answered_questions)) {
            $this->number_of_answered_questions = UserAnswer::countAnswers($userId, $this->getId());
        }*/

        return UserAnswer::countAnswers($userId, $this->getId());
    }

    /**
     * count query to determine how many answers we have [for this quiz]
     * @param null $userId
     * @return mixed
     */
    public function getNumberOfCorrectAnsweredQuestions($userId = null)
    {
        /*if(! isset($this->number_of_correct_answered_questions)) {
            $this->number_of_correct_answered_questions = UserAnswer::countAnswers($userId, $this->getId(), true);
        }*/

        return UserAnswer::countAnswers($userId, $this->getId(), true);
    }

    /**
     * return the user's
     * @param null $userId
     * @return float
     */
    public function getPercentComplete($userId)
    {
        return number_format($this->getNumberOfAnsweredQuestions($userId) / $this->getNumberOfQuestions() * 100, 0);
    }

    /**
     * @param $userId
     * @return string
     */
    public function getPercentCorrect($userId)
    {
        return number_format($this->getNumberOfCorrectAnsweredQuestions($userId) / $this->getNumberOfQuestions() * 100, 0);
    }

    /**
     * return a human message depending on the user's result for this quiz
     * @param $userId
     * @return string
     */
    public function getResultMessage($userId)
    {
        $percentCorrect = $this->getPercentCorrect($userId);

        if($percentCorrect == 100) {
            $msg = "WOW! That was impressive! You didn't use Google, did you? =S";
        }
        elseif($percentCorrect > 90) {
            $msg = "Wow! You're really good ad it!";
        }
        elseif($percentCorrect > 70) {
            $msg = "You did good! Keep going!";
        }
        elseif($percentCorrect > 50) {
            $msg = "Hmmmm...I think you can do better than that";
        }
        elseif($percentCorrect > 20) {
            $msg = "Come on...focus";
        }
        elseif($percentCorrect > 0) {
            $msg = "I know you were just pressing random buttons";
        }
        // zero
        else {
            $msg = "ZERO?! Are you a bot? =)";
        }

        return $msg;
    }

    /**
     * return <select> for this table data
     * @param Template $template
     * @return string
     * @throws \Exception
     * @throws \Exception\DatabaseException
     * @throws \Exception
     */
    public static function getHtmlSelect(Template $template)
    {
        // Database Connection
        $db = \Database::getConnection();

        // existing Quizes for select
        $quizes = $db->retrieveAll(self::class);

        // mount html select
        $quizSelect = $template->getSelect($quizes, 'quiz_id', ['id', 'name']);

        return $quizSelect;
    }

    /**
     * check if a quiz with that name already exists
     * @param $name
     * @param null $quizId
     * @return mixed
     * @throws \Exception\DatabaseException
     */
    public static function nameExists($name, $quizId=null) {


        $sql = 'select count(*) from quiz where `name` = :name';

        $aParam = array(
            'name' => $name
        );

        // factory_id received: make sure it won't be taken into account
        if($quizId) {

            $sql .= ' and `id` != :id';
            $aParam['id'] = $quizId;

        }

        $sql .= ';';

        return \Database::getConnection()->count($sql,$aParam);

    }

    /**
     * check if name exists, retrieve data from API and create a quiz and N questions entries
     * @param $attributes
     * @return mixed
     * @throws \Exception
     */
    public static function createFromApi($attributes)
    {
        if(self::nameExists($attributes['quiz_name'])) {
            throw new \Exception("Name already exists", 409);
        }

        $quiz = new self();

        $quiz->setName($attributes['quiz_name']);

        $quiz->setCategoryId($attributes['category_id']);

        $quiz->setNumberOfQuestions($attributes['number_of_questions']);

        $quiz->setDifficulty($attributes['difficulty']);

        $questions = $quiz->getQuestionsFromApi();

        $quiz->save();

        // counter
        $questionNumber = 1;

        foreach($questions as $apiQuestion) {
            $question = new Question(
                $quiz->getId(),
                $apiQuestion['question'],
                $questionNumber,
                $apiQuestion['correct_answer'],
                $quiz->getCategoryId(),
                $quiz->getDifficulty()
            );
            $question->save();

            // add correct answer to the list
            $apiQuestion['incorrect_answers'][] = $apiQuestion['correct_answer'];

            // each incorrect answer
            foreach($apiQuestion['incorrect_answers'] as $answerText) {
                $answer = new Answer(
                    $question->getId(),
                    $answerText
                );
                $answer->save();
            }

            $questionNumber++;
        }

        return $quiz;

    }

    /**
     * load all quiz questions from the API
     * @param QuizApiInterface|null $quizApi
     * @return array
     */
    public function getQuestionsFromApi(QuizApiInterface $quizApi = null)
    {
        // singleton while we don't have a DIC
        if(! $quizApi) {
            $quizApi = Quiz::$quizApi;
        }

        $questions = $quizApi->getQuizQuestions($this);

        return $questions;
    }

}