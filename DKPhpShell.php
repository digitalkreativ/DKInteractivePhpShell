<?php
/************************************************************************************************
 * PROJECT: Digitalkreativ Interactive PHP Shell
 * DESCRIPTION: class to have PHP scripts interact with user to display messages,
 *              confirm settings, ask input etc.
 * CREATOR: @digitalkreativ Tom Sacré
 ************************************************************************************************/

class DKPhpShell {

    const   LF_PHP = PHP_EOL;
    const   LF_UNIX = "\n";
    const   LF_WINDOWS = "\r\n";

    private $lineFeed = self::LF_PHP;

    function __construct($defaultLineFeed=self::LF_PHP){
        if($defaultLineFeed != $this->lineFeed){
            $this->lineFeed = $defaultLineFeed;
        }
    }

    /**
     * set the line feed to Windows style line feed (with carriage return)
     */
    public function setLineFeedToWindows(){
        $this->lineFeed = self::LF_WINDOWS;
    }

    /**
     * set the line feed to unix style line feed (no carriage return)
     */
    public function setLineFeedToUnix(){
        $this->lineFeed = self::LF_UNIX;
    }

    /**
     * set line feed to default (PHP_EOL)
     */
    public function setLineFeedToDefault(){
        $this->lineFeed = self::LF_PHP;
    }

    /**
     * write a message to the screen
     * @param $message
     */
    public function writeMessage($message){
        echo $message . $this->lineFeed;
    }


    /**
     * write a line surrounded by a "block"
     * +---------+
     * | message |
     * +---------+
     * @param string $message
     * @return void
     */
    public function writeMessageBlock($message){

        $i = 0;
        $line = '';
        while($i < (strlen($message)+2)){
            $line.= '-';
            ++$i;
        }

        $this->writeMessage('');
        $this->writeMessage("+".$line."+");
        $this->writeMessage("| ".$message." |");
        $this->writeMessage("+".$line."+");
        $this->writeMessage('');

    }

    /**
     * write a styled error message with possible solution if provided
     * @param string $message
     * @param string $solution
     * @return void
     */
    public function writeErrorMessage($message,$solution=''){
        $this->writeMessageBlock('ERROR');
        $this->writeMessage($message);

        if($solution != ''){
            $this->writeMessageBlock('SOLUTION');
            $this->writeMessage($solution);
        }

        $this->writeMessage('');
    }

    /**
     * write multiple lines at once from a provided array
     * @param array $lines
     * @param bool $border
     * @return void
     */
    public function writeMultilineMessage($lines=array(),$border=true){

        if(!is_array($lines)){
            $lines = array( (string) $lines );
        }

        $countLines = count($lines);
        $maxLineLength = 0;

        for($l = 0; $l < $countLines; ++$l){
            if(strlen($lines[$l]) > $maxLineLength){
                $maxLineLength = strlen($lines[$l]);
            }
        }

        $i = 0;
        $line = '';

        if($border){
            for($l = 0; $l < $countLines; ++$l){
                while(strlen($lines[$l]) <> $maxLineLength){
                    $lines[$l] = $lines[$l].' ';
                }
            }
            $maxLineLength = $maxLineLength+2;
            while($i < $maxLineLength){
                $line.= '-';
                ++$i;
            }

            $this->writeMessage('');
            $this->writeMessage("+".$line."+");
            for($l = 0; $l < $countLines; ++$l){
                $this->writeMessage("| ".$lines[$l]." |");
            }
            $this->writeMessage("+".$line."+");
            $this->writeMessage('');
        } else {
            for($l = 0; $l < $countLines; ++$l){
                $this->writeMessage($lines[$l]);
            }
        }
    }

    /**
     * ask the user for input on a mandatory question
     * @param string $question
     * @param string $defaultAnswer [optional]
     * @return bool|string
     */
    public function askMandatoryInput($question,$defaultAnswer=''){

        if($question == ''){
            return false;
        }

        $answer = '';
        $error = false;
        $countTimes = 0;

        while($answer == ''){
            $answer = $this->askInput($question,array(),$defaultAnswer,false);
            $countTimes++;
            if($countTimes >= 5){
                $error = true;
                break;
            }
        }

        if($error){
            return false;
        }

        return $answer;
    }



    /**
     * ask the user for input
     * @param string $question
     * @param array $validAnswers [optional]
     * @param string $defaultAnswer [optional]
     * @param bool $addDefaultAnswerToValidAnswers [optional, default=true]
     * @return string
     */
    public function askInput($question='',$validAnswers=array(),$defaultAnswer='',$addDefaultAnswerToValidAnswers=true){

        if($question != ''){
            if($defaultAnswer != ''){
                $question.= ' ['.$defaultAnswer.']';
            }
            $this->writeMessage($question);
        }

        $handle = fopen ("php://stdin","r");
        $line = trim(fgets($handle));

        if($defaultAnswer != '' && $addDefaultAnswerToValidAnswers){
            if(!in_array($defaultAnswer,$validAnswers)){
                $validAnswers[] = $defaultAnswer;
            }
        }

        if($line == '' && $defaultAnswer != ''){
            $line = $defaultAnswer;
        }

        if(count($validAnswers) > 0){
            if(in_array($line,$validAnswers) or in_array(strtoupper($line),$validAnswers) or in_array(strtolower($line),$validAnswers)){
                fclose($handle);
                return $line;
            } else {
                fclose($handle);
                return false;
            }
        } else {
            fclose($handle);
            //no valid answers provided so any answer is correct
            return $line;
        }

        fclose($handle);
        return false;
    }
    
}