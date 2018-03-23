<?php

//Session Start
session_start();

$_SESSION["ID"] = 0;
if(isset($_SESSION["Cap"]))
{
  
}
else
{
  //Create the cap for amount of questions to ask
  $_SESSION["Cap"] = 25;
}

if(isset($Question_ID))
{
  
}
else
{
  //Create the ID for the questions to ask
  $Question_ID = 0;
}

//Initiliase the session for correct answers
if(isset($_SESSION["Correct_Answers"]))
{
  
}
else
{
  $_SESSION["Correct_Answers"]=0;
}

//Initiliase the session for incorrect answers
if(isset($_SESSION["Incorrect_Answers"]))
{
  
}
else
{
  $_SESSION["Incorrect_Answers"]=0;
}

//Initiliase the session for incorrect answers
if(isset($_SESSION["Incorrect_Array"]))
{
  
}
else
{
  $_SESSION["Incorrect_Array"]=0;
}

//Initialise the array
if(isset($_SESSION["Questions_Asked"]))
{
  //Don't do anything
}
else
{
  //Create the session array
  //Stored 0 in for testing and needs to have a value
  $UnusedValue = 0;
  $_SESSION["Questions_Asked"] = array($UnusedValue);
}

//Display results function
function Display_Results()
{
  //Store all variables in new sessions 
  $_SESSION["Correct_Result"] = $_SESSION["Correct_Answers"];
  $_SESSION["Incorrect_Result"] = $_SESSION["Incorrect_Answers"];
  
  //Percentage Calculator
  $_SESSION["PercentageCorrect"] = $_SESSION["Cap"] - $_SESSION["Correct_Result"];
  $_SESSION["PercentageCorrect"] = $_SESSION["PercentageCorrect"] / $_SESSION["Cap"];
  $_SESSION["PercentageCorrect"] = $_SESSION["PercentageCorrect"] * 100;
  $_SESSION["PercentageCorrect"] = 100 - $_SESSION["PercentageCorrect"];
  
  //Redirect to results page
  header("Location: Results.php");
}


//[========================================]
//                                         |
//         Form Submitted Section          |
//                                         |
//[========================================]

//If form is submitted
if(isset($_REQUEST["submit"]))
{
  //Get answer from form
  $Answer = $_REQUEST["Answer"];
  
  //If answer given is the same as the correct answer stored for that question
  if($Answer == $_SESSION["Answer"])
  {
    //if correct answers session does already exist add 1 and rerun question get 
    if(isset($_SESSION["Correct_Answers"]))
    {
      $_SESSION["Correct_Answers"] += 1;
    
      if($_SESSION["Count"] == $_SESSION["Cap"])
      {
        //Don't ask anymore questions and display results
        Display_Results();
      }
      else
      {
        //Run function to get a new question
        Get_Question();
      }
    }
    else  //Create it and set 0
    {
      $_SESSION["Correct_Answers"] = 0;
    
      if($_SESSION["Count"] == $_SESSION["Cap"])
      {
        //Don't ask anymore questions and display results
        Display_Results();
      }
      else
      {
        //Run function to get a new question
        Get_Question();
      }
    }
  }
  else
  {
    if(isset($_SESSION["Incorrect_Answers"]))
    {
      $_SESSION["Incorrect_Answers"] += 1;
    
      $_SESSION["Incorrect_Array"] .= $_SESSION["ID"];
      if($_SESSION["Count"] == $_SESSION["Cap"])
      {
        //Don't ask anymore questions and display results
        Display_Results();
      }
      else
      {
        //Run function to get a new question
        Get_Question();
      }
    }
    else  //Create it and set 0
    {
      $_SESSION["Incorrect_Answers"] = 0;
    
      if($_SESSION["Count"] == $_SESSION["Cap"])
      {
        //Don't ask anymore questions and display results
        Display_Results();
      }
      else
      {
        //Run function to get a new question
        Get_Question();
      }
    }
  }
}
else
{
  //Get the first question
  Get_Question();
}

//Get the question function
function Get_Question()
{
  //If there is already a session for count created (would be set if it isn't already there)
  if(isset($_SESSION["Count"]))
  {
    //Don't add one as its already done later on when the SQL statement runs
  }
  else
  {
    //Create the session
    $_SESSION["Count"] = 0;
  }
  
  //If the session array already exists
  if(isset($_SESSION["Questions_Asked"]))
  {
    //Don't do anything
  }
  else
  {
    //Create the session array
    //Stored 0 in for testing and needs to have a value
    $_SESSION["Questions_Asked"] = array(0);
  }
  
  //Random Number Generator for Selecting from database the question
  //6 is the max as I only created 6 in the test table
  $Question_ID = rand(1,25); //(Minimum,Maximum) 
  $_SESSION["ID"] = $Question_ID;
  $_SESSION["Genre"] = "League";
  $Genre = $_SESSION["Genre"]; //Session was set when the user selected what Genre the questions they wanted to answer

  //DB Connect
  include 'dbconnect.php';
  
  //Prepare an SQL statement to SELECT * from the Table name / Genre the user selected and prepare the paramater using '?'
  $stmt = $conn->prepare("SELECT * FROM Numbers WHERE ID=?");

  $Questsss = array($_SESSION["Questions_Asked"]);
  $ID = $_SESSION["ID"];
  //While the random generated number is the same number as one previously asked and stored
  //$pos = strpos($ID, $_SESSION['Questions_Asked']);
  if(in_array($ID, $Questsss))
  {
    $pos = true;
  }
  else
  {
    $pos = false;
  }
  if($pos !== false) 
  {
      //Then the number was already asked and stored in array - Random Number Generator for Selecting from database the question
      $Question_ID = rand(1,25); //(Minimum,Maximum) 
      $_SESSION["ID"] = $Question_ID;
  }
  else //It hasn't already been asked
  {
    //Bind paramater as an integer/number (i) and pass the value using random generated number and string (s) using Genre variable
    $stmt->bind_param("i", $Question_ID);

    //Store in array random number generated
    $Questsss[$_SESSION["Count"]] = $_SESSION["ID"];
    print_r($Questsss[$_SESSION["Count"]]);
    print_r($Questsss);
    if($_SESSION["Count"] == 25)
    {
      Display_Results();
    }
    //Execute the statement - example looks like ("SELECT * FROM $Genre WHERE ID=55");
    $stmt->execute();

    //get the result and store it
    $result = $stmt->get_result();
    //[========================================]
    //     Install the get_result function     |
    //                                         |
    //           Using SSH Terminal            |
    //                                         |
    //[========================================]
    //sudo apt-get install php5-mysqlnd

    //restart code

    //sudo /etc/init.d/apache2 restart
    
    //Add to count which determines how many questions have been answered
    $_SESSION["Count"] += 1;
    
    //While there is a result (SHOULD ONLY BE 1) then do within the { }
    while ($row = $result->fetch_assoc())
    { 
      $_SESSION["Answer"] = $row["Answer"];
      //Table to display question and a form
      ?>
        <!--Table to display question and form-->
        <table>
         <!--Question asked-->
          <th>
            <tr>
              <b>Question: </b><?php echo($row["Question"]); ?>
            </tr>
          </th>
          <!--Form-->
          </br>
          <tr>
            <!--Submit to self-->
            <form method="POST" action="<?php echo($_SERVER["PHP_SELF"]); ?>">
              <!--Name is Answer, it's required-->
              <b>Answer: </b><input type="text" name="Answer" required>
              </br>
              </br>
              <!--Have a gap between answer field and button-->
              <input type="submit" name="submit" value="Submit Answer!">
            </form>
          </tr>
        </table>
      </br>
      <?php
      print_r("Correct Answers given: " . $_SESSION["Correct_Answers"] . "</br>");
      print_r("Inorrect Answers given: " . $_SESSION["Incorrect_Answers"]);
    }
  }
}
?>