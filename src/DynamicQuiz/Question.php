<?php

namespace DynamicQuiz;

/**
 * Model + ORM for table 'question'
 */
class Question extends \Orm
{
	/**
	 * table name in database
	 * @var string
	 */
	static $tableName = 'question';

	/**
	 * primary key field name
	 * @var string
	 */
	static $pkFieldName = 'id';

	/**
	 * list of table fields and datatypes (for ORM purposes)
	 * @var array
	 */
	protected $attributes = [
		'id' => 'int',
		'quiz_id' => 'int',
		'text' => 'string',
		'question_number' => 'int',
		'correct_answer' => 'string',
		'category_id' => 'int',
		'difficulty' => 'string',
		'type' => 'string',
		'created_at' => 'timestamp'
	];

	/**
	 * Question constructor
	 * @param null $quizId
	 * @param null $text
	 * @param null $questionNumber
	 * @param null $correctAnswer
	 * @param null $categoryId
	 * @param null $difficulty
	 */
	public function __construct($quizId=null, $text=null, $questionNumber=null, $correctAnswer=null, $categoryId=null, $difficulty=null) {

		// create attributes based on $this->attributes
		parent::init($this);

		if($quizId)
			$this->setQuizId($quizId);

		if($text)
			$this->setText($text);

		if($questionNumber)
			$this->setQuestionNumber($questionNumber);

		if($correctAnswer)
			$this->setCorrectAnswer($correctAnswer);

		if($categoryId)
			$this->setCategoryId($categoryId);

		if($difficulty)
			$this->setDifficulty($difficulty);

	}

	/**
	 * method required for ORM to work
	 */
	function save() {

		// calls generic ORM save (insert or update)
		parent::save();

	}

	/**
	 * retrieve every answer for this question
	 * @return mixed
	 * @throws \Exception\DatabaseException
	 */
	public function getAnswers()
	{
		return Answer::retrieveAll($this->getId(), 'question_id');
	}

	/**
	 * retrieve the first Quiz Question unanswered by the User
	 * @param $userId
	 * @param $quizId
	 * @return mixed
	 * @throws \Exception\DatabaseException
	 */
	public static function firstUnansweredQuestion($userId, $quizId)
	{
		$sql = 'select q.*
from question q
left join user_answer ua on ua.question_id = q.id and ua.user_id = :userId
where q.quiz_id = :quizId and ua.user_id is null
order by q.question_number asc
limit 1';

		$binds = array(
			'userId' => $userId,
			'quizId' => $quizId,
		);

		$sql .= ';';

		$row = \Database::getConnection()->fetchOne($sql, $binds);

		if(empty($row)) {
			return null;
		}

		// instantiate and return a new Question object
		$question = self::hydrate($row, self::class);

		return $question;
	}

	/**
	 * return <div>answer</answer> for each of the question's possible answers
	 * @return string
	 */
	public function getHtmlAnswerButtons()
	{
		$answers = $this->getAnswers();

		$html = '';

		if(! empty($answers)) {

			foreach($answers as $answer) {

				$html .= "<div class='row'>
    <div class='col-100'>
        <input type='submit' class='answer_button' value='{$answer->getValue()}' onclick='submitAnswer({$this->getId()},{$answer->getId()})'  />
    </div>
</div>";
			}
		}
		else {
			$html = 'No Answers Found (Error Reported)';
		}

		return $html;
	}

}