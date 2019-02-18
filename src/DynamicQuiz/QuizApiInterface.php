<?php

namespace DynamicQuiz;

/**
 * Interface QuizApiInterface
 * The class which implements this interface has to talk to a Quiz API like https://opentdb.com
 * @package DynamicQuiz
 */
interface QuizApiInterface
{
    /**
     * return all questions from API
     * @param Quiz $quiz
     * @return array
     */
    public function getQuizQuestions(Quiz $quiz);

    /**
     * return all categories from API
     * @return array -- example: array(2) { [0]=> array(2) { ["id"]=> int(9) ["name"]=> string(17) "General Knowledge" } [1]=> array(2) { ["id"]=> int(10) ["name"]=> string(20) "Entertainment: Books" } }
     */
    public function getAllCategories();

}