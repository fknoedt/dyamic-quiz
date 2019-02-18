<?php

namespace DynamicQuiz;

/**
 * Model + ORM for table 'user_answer'
 */
class UserAnswer extends \Orm
{
	/**
	 * table name in database
	 * @var string
	 */
	static $tableName = 'user_answer';

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
		'user_id' => 'int',
		'answer_id' => 'int',
		'question_id' => 'int',
		'correct_answer' => 'bool',
		'created_at' => 'timestamp'
	];

	/**
	 * Question constructor
	 * @param null $userId
	 * @param null $answerId
	 * @param null $questionId
	 * @param null $correctAnswer
	 */
	public function __construct($userId=null, $answerId=null, $questionId=null, $correctAnswer=null) {

		// create attributes based on $this->attributes
		parent::init($this);

		if($userId)
			$this->setUserId($userId);

		if($answerId)
			$this->setAnswerId($answerId);

		if($questionId)
			$this->setQuestionId($questionId);

		if($correctAnswer)
			$this->setCorrectAnswer($correctAnswer);

	}

	/**
	 * method required for ORM to work
	 */
	function save() {
		// calls generic ORM save (insert or update)
		parent::save();
	}

	/**
	 * count how many questions the user has answered [for a quiz]
	 * @param $userId
	 * @param $quizId
	 * @param $correct
	 * @return mixed
	 * @throws \Exception\DatabaseException
	 */
	public static function countAnswers($userId=null, $quizId=null, $correct=null)
	{
		$db = \Database::getConnection();

		$sql = "select count(*) from " . self::$tableName . " ua
		inner join " . Answer::$tableName . " a on a.id = ua.answer_id
		inner join " . Question::$tableName . " q on a.question_id = q.id
		where ";

		$binds = [];
		$where = [];

		if($userId) {
			$where[] = 'ua.user_id = :userId';
			$binds['userId'] = $userId;
		}
		if($quizId) {
			$where[] = 'q.quiz_id = :quizId';
			$binds['quizId'] = $quizId;
		}
		if($correct !== null) {
			$where[] = 'ua.correct_answer = :correct';
			$binds['correct'] = $correct;
		}

		// no filters
		if(empty($where)) {
			$where[] = '1';
		}

		$sql .= implode(' and ', $where);

		return $db->count($sql, $binds);
	}

	/**
	 * delete every user answer for the given quiz
	 * TODO: optional quiz_id
	 * @param $quizId
	 * @param $userId
	 * @return mixed
	 * @throws \Exception\DatabaseException
	 */
	public static function deleteByUser($quizId, $userId)
	{
		$db = \Database::getConnection();

		$sql = "delete ua from " . self::$tableName . " ua
		inner join " . Answer::$tableName . " a on a.id = ua.answer_id
		inner join " . Question::$tableName . " q on a.question_id = q.id
		where q.quiz_id = :quizId and ua.user_id = :userId;";

		$binds = ['quizId' => $quizId, 'userId' => $userId];

		$db->query($sql, $binds);

		return true;
	}

}