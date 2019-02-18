<?php

namespace DynamicQuiz;

/**
 * Model + ORM for table 'user'
 */
class User extends \Orm
{

	/**
	 * table name in database
	 * @var string
	 */
	static $tableName = 'user';

	/**
	 * primary key field name
	 * @var string
	 */
	static $pkFieldName = 'id';

	/**
	 * list of table fields and datatypes (for ORM purposes)
	 * @var array
	 */
	protected $attributes = array(
		'id' => 'int',
		'name' => 'string',
		'remote_ip' => 'string',
		'created_at' => 'timestamp'
	);

	/**
	 * User constructor
	 * @param string $name
	 * @param string $remoteIp
	 */
	public function __construct($name=null, $remoteIp=null) {

		// create attributes based on $this->attributes
		parent::init($this);

		if($name)
			$this->setName($name);

		if($remoteIp)
			$this->setRemoteIp($remoteIp);

	}

	/**
	 * method required for ORM to work
	 */
	function save() {

		// calls generic ORM save (insert or update)
		parent::save();

	}

	/**
	 * if the user exists, retrieves it; if not, create and retrieve
	 * @param $username
	 * @return mixed
	 * @throws \Exception\DatabaseException
	 */
	public static function createOrRetrieve($username)
	{
		$user = self::retrieve($username, 'name');

		// user doesn't exist: create a new one
		if(! $user) {

			$user = new User($username, $_SERVER['REMOTE_ADDR']);
			$user->save();

		}

		return $user;
	}
}