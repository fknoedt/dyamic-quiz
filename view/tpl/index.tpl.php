<!DOCTYPE html>
<html>
<head>
    <title>Dynamic Quiz by Filipe Knoedt</title>

    <!-- adjustment for IOS -->
    <meta name="viewport" content="initial-scale=1.0">

	<link rel="icon" href="img/favicon.ico" />

    <!-- main style sheet -->
    <link rel="stylesheet" href="css/style.css" />

    <!-- jQuery -->
    <script src="/js/jquery/jquery-3.2.1.min.js"></script>

    <script src="/js/notify.js"></script>

    <!-- font awesome (icons) -->
    <script src="https://use.fontawesome.com/2789e763d5.js"></script>

    <!-- jQuery Modal -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

    <!-- main project's javascript -->
	<script src="js/main.js"></script>

    <script language="JavaScript">

    </script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-109716981-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-109716981-1');
    </script>

    </head>
<body>

    <h1 id="dynamic_quiz_logo" class="logo"><i class="fa fa-lightbulb-o fa-lg"></i> Dynamic Quiz<br/></h1>
    <div class="signature_label">by <a id="by_fk" href="https://filipe.knoedt.net" target="_blank">Filipe Knoedt</a></div>
    <!-- white rounded div -->
    <section>
        <div class="container">
            <div class="inner">
                <!-- action blue buttons -->
                <div>
                    <!--
                    <input type="button" value="Create"  onclick="factoryAction('create')" />&nbsp;
                    <input type="button" value="Update"  onclick="factoryAction('update')" />&nbsp;
                    <input type="button" value="Delete"  onclick="factoryAction('delete')" />&nbsp;
                    <input type="button" value="Generate"  onclick="factoryAction('generate')" />&nbsp;-->
                </div>

                <div id="ajax-panel"></div>

                <!-- dynamic content goes in here -->
                <div id="main_div" class="main_div">

                </div>

                <!-- ajax loader and responses -->


                <br/>
                <!--hr/-->
                <div id="console_log" class="console">
                    javascript output goes here<br/>
                </div>
                <br/>
                <!-- action blue buttons -->
                <div>
                    <input type="button" class="grey" value="Refresh"  onclick="loadPage()" />&nbsp;
                    <!--input type="button" class="grey" value="JSON"  onclick="getJson()" />&nbsp;-->
                    <input type="button" class="grey" value="New Quiz"  onclick="newQuiz()" />&nbsp;
                    <input type="button" class="grey" value="New Session"  onclick="newSession()" />&nbsp;
                    <input type="button" class="grey" value="Console"  onclick="toggleConsole()" />&nbsp;
                </div>

            </div>
        </div>
    </section>
    <footer>
        <div>
            <small><a href="#modal_help" rel="modal:open">How it works</a></small>
        </div>
        <div style="margin-bottom: 10px;">
            <small>see source and presentation on <a href="https://github.com/fknoedt/dynamic-quiz" target="_blank"><strong>github</strong>&nbsp;<i class="fa fa-github fa-2x"></i></a></small>
        </div>
    </footer>

    <!-- Modal HTML embedded directly into document -->
    <div id="modal_help" class="modal">
        <p>1) Enter your name and press [ Next ]. If the name was previously used, that session will be retrieved.</p>
        <p>2) Choose a previously saved Quiz or create a new one by choosing it's options and press [ Start ]. Any previously answered question will be retrieved.</p>
        <p>3) Answer each question by clicking one of the answers.</p>
        <p>4) When every question is answered, you'll see the Quiz report.</p>
        <p>This Quiz is Fed by <a href="https://opentdb.com" target="_blank">OpenTDB</a>.</p>
        <input type="button" value="Got it" onclick="formModalClose('help')"></p>
    </div>
</body>
</html>