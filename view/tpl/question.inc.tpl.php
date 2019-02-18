<div class="row">
    <div class="col-100">
        <h5 class="subtitle"><span title="Quiz Name">{$QUIZ_NAME}</span> - <span title="Quiz Category">{$QUIZ_CATEGORY}</span> - <span title="Quiz Difficulty">{$QUIZ_DIFFICULTY}</span></h5>
        <h3>QUESTION {$QUESTION_NUMBER}/{$TOTAL_QUESTIONS}</h3>
        <div id="progressbar">
            <div style="width: {$PERCENT_COMPLETE}%;">{$PERCENT_COMPLETE}%</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-100">
        <label class="question_text">{$QUESTION_TEXT}</label>
    </div>
</div>
{$ANSWERS}
<!--div class="row"><div class="col-100"></div></div>
<div class="row">
    <div class="col-100">
        <input type="button" value="Back" onclick="lastQuestion()">
        &nbsp;
        <input type="button" value="Skip" onclick="nextQuestion()">
    </div>
</div-->