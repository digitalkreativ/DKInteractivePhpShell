DKPhpShell
==========

The DKPhpShell class is a small class to help integrate user input in php command line scripts.

This could be used to verify things with the user when running i.e. maintenance scripts or to just to confirm with the
user that the script should be run.

Usage
-----

You start by including the file `DKPhpShell.php`

    require_once('<path-to-file>/DKPhpShell.php');

After that you can call the class

    $dkShell = new DKPhpShell();

Then you can start using the class, i.e. confirm message

    $defaultAnswers = array('yes','no');
    $answer = $dkShell->askInput('Are you human?',$defaultAnswers,'yes');

    if(!$answer){
        $dkShell->writeErrorMessage('failed to answer one of the answers, can not continue');
        exit();
    }elseif($answer == 'yes'){
        $dkShell->writeMessage('Phew!');
    }else{
        $dkShell->writeMessage('I am only allowed to interact with humans ...');
    }

