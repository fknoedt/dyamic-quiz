<div class="row">
    <div class="col-100">
        <label class="welcome_text">Welcome, <label id="username_step_2">{$USERNAME}</label></label>
        <br/>
        <label class="subtext">Pick an existing quiz</label>
    </div>
</div>

<div class="row">
    <div class="col-25">
        <label for="quiz_id">Saved Quiz</label>
    </div>
    <div class="col-75">
        {$QUIZ_SELECT}
    </div>
</div>
<div class="row">
    <div class="col-100">
        or create a new one
    </div>
</div>
<div class="row">
    <div class="col-25">
        <label for="quiz_name">Name</label>
    </div>
    <div class="col-75">
        <input type="text" id="quiz_name" name="quiz_name" placeholder="Quiz Name..">
    </div>
</div>
<div class="row">
    <div class="col-25">
        <label for="number_of_questions"># of Questions</label>
    </div>
    <div class="col-75">
        <input type="number" max="50" min="1" id="number_of_questions" name="number_of_questions" placeholder="Enter a number from 1 to 50">
    </div>
</div>
<div class="row">
    <div class="col-25">
        <label for="category_id">Category</label>
    </div>
    <div class="col-75">
        {$CATEGORY_SELECT}
    </div>
</div>
<div class="row">
    <div class="col-25">
        <label for="difficulty">Difficulty</label>
    </div>
    <div class="col-75">
        {$DIFFICULTY_SELECT}
    </div>
</div>
<div class="row">
    <input type="submit" value="Start" onclick="submitQuiz()">
</div>
<script language="JavaScript">
    $('#quiz_id').change(function() {
        $('#quiz_name').val('');
    });
    $('#quiz_name').change(function() {
        $("#quiz_id option[value='']").attr('selected', true)
    });
</script>