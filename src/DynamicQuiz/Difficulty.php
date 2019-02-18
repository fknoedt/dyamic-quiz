<?php

namespace DynamicQuiz;

use Common\Template;

class Difficulty
{
    static $enum = [
        'easy' => 'Easy',
        'medium' => 'Medium',
        'hard' => 'Hard'
    ];

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
        // mount html select
        $difficultySelect = $template->getSelect(self::$enum, 'difficulty', []);

        return $difficultySelect;
    }
}