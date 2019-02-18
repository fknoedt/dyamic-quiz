<?php

namespace DynamicQuiz;

/**
 * Open Trivia Database (TDB) Connector
 * https://opentdb.com/api_config.php
 * Class OpenTdbQuizApi
 * @package DynamicQuiz
 */
class OpenTdbQuizApi implements QuizApiInterface
{
    /**
     * read all categories from the API
     * @return string
     */
    public function getAllCategories()
    {
        $url = 'https://opentdb.com/api_category.php';

        $categories = json_decode(file_get_contents($url), true);

        return $categories['trivia_categories'];
    }

    /**
     * read all questions for the given quiz parameters
     * @param Quiz $quiz
     * @return mixed
     */
    public function getQuizQuestions(Quiz $quiz)
    {
        // mount url
        $url = "https://opentdb.com/api.php?amount={$quiz->getNumberOfQuestions()}&category={$quiz->getCategoryId()}&difficulty={$quiz->getDifficulty()}";

        $quiz->setApiUrl($url);

        $questions = json_decode(file_get_contents($url), true);

        return $questions['results'];
    }

}