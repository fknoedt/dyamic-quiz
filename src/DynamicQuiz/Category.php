<?php

namespace DynamicQuiz;
use Common\Template;

/**
 * Model + ORM for table 'category'
 */
class Category extends \Orm
{
	/**
	 * table name in database
	 * @var string
	 */
	static $tableName = 'category';

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
		'name' => 'string',
		'created_at' => 'timestamp'
	];

	/**
	 * Category constructor
	 * @param string $name
	 * @param string $id
	 */
	public function __construct($name=null, $id=null)
	{
		// create attributes based on $this->attributes
		parent::init($this);

		if($name) {
			$this->setName($name);
		}

		if($id) {
			$this->setId($id);
		}

	}

	/**
	 * method required for ORM to work
	 */
	function save()
	{
		// calls generic ORM save (insert or update)
		parent::save();
	}

	/**
	 * load all categories from API and save in the database
	 * @param QuizApiInterface|null $quizApi
	 */
	public function loadFromApi(QuizApiInterface $quizApi = null)
	{
		if(! $quizApi) {
			$quizApi = Quiz::$quizApi;
		}

		$categories = $quizApi->getAllCategories();

		foreach($categories as $row) {
			$category = new Category($row['name'],$row['id']);
			$category->save();
		}
	}

	/**
	 * if the category exists, retrieves it; if not, create and retrieve
	 * @param $name
	 * @return mixed
	 * @throws \Exception\DatabaseException
	 */
	public static function createOrRetrieve($name)
	{
		$category = self::retrieve($name, 'name');

		// user doesn't exist: create a new one
		if(! $category) {

			$category = new Category($name);

			$category->save();

		}

		return $category;
	}


	/**
	 * return <select> for this table's data
	 * @param \Common\Template $template
	 * @return string
	 * @throws \Exception
	 * @throws \Exception\DatabaseException
	 * @throws \Exception
	 */
	public static function getHtmlSelect(Template $template)
	{
		// Database Connection
		$db = \Database::getConnection();

		// existing categories for select
		$categories = $db->retrieveAll(self::class);

		if(empty($categories)) {
			Category::loadFromApi(self::$quizApi);
		}

		// mount html select
		$categorySelect = $template->getSelect($categories, 'category_id', ['id', 'name']);

		return $categorySelect;
	}


}