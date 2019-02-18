<?php

namespace DynamicQuiz;

/**
 * Model + ORM for table 'answer'
 */
class Answer extends \Orm
{
	/**
	 * table name in database
	 * @var string
	 */
	static $tableName = 'answer';

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
		'question_id' => 'int',
		'value' => 'string',
		'created_at' => 'timestamp'
	];

	/**
	 * Question constructor
	 * @param null $questionId
	 * @param null $value
	 */
	public function __construct($questionId=null, $value=null)
	{
		// create attributes based on $this->attributes
		parent::init($this);

		if($questionId)
			$this->setQuestionId($questionId);

		if($value)
			$this->setValue($value);
	}

	/**
	 * method required for ORM to work
	 */
	public function save() {
		// calls generic ORM save (insert or update)
		parent::save();
	}

}