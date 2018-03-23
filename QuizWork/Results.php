<?php

//Start session in order to access previously assigned session data
session_start();

//DB Connection
include 'dbconnect.php';
?>
<table>
  <th>
    <tr>You answered <?php echo($_SESSION["Correct_Result"]); ?> questions correctly out of <?php echo($_SESSION["Cap"]); ?></tr>
  </br>
    <tr>Which means you got a total of <?php echo($_SESSION["PercentageCorrect"]);?>%</tr>
  </th>
</table>
</br>
<?php 
  
  //Slice the Array text from the array
  $array = $_SESSION["Questions_Asked"];
  $array = ltrim($array, 'Array');
  $SplitArray = str_split($array);

  //Get date information
  $Date = date('l jS \of F Y h:i:s A');
  //Get % 
  $Percentage = $_SESSION["PercentageCorrect"];
  $Percentage .= "%";
  //Get username / user ID 
  $UserID = "Connor";

  print_r("User: " . $UserID . " got " . $Percentage . " on " . $Date . ".");

  $WrongArray = $_SESSION["Incorrect_Array"];
  $array2 = ltrim($WrongArray, '0');
  $SplitArray2 = str_split($array2);

  $count=0;
  $count2 = 0;
  //While the count is less than the cap in the session
  while($count<$_SESSION["Cap"])
  {
    $ID = $SplitArray[$count];
    //Add count up so it knows which ID to get from the array next
    $count++;
  }
  $stmt= "SELECT * FROM Numbers WHERE ID IN(";
  print_r("These are what you got wrong: </br> </br>");
  $total = count($SplitArray2);
  while($count2<$total)
  {
    $stmt .= $SplitArray2[$count2];
    $count2 += 1;
    if($count2 == $total)
    {
      $stmt .= ")";
    }
    else
    {
      $stmt .= ",";
    }
  }

  $sql = $conn->prepare($stmt);
  $sql->execute();
  $result = $sql->get_result();

  while ($row = $result->fetch_assoc())
  {
    print_r("Question: " . $row["Question"] . "</br>");
    print_r("Answer: " . $row["Answer"] . "</br>");
  }

?>
<!--Form to redirect the user back and destroy all session data-->
<form method="POST" action="<?php echo($_SERVER["PHP_SELF"]); ?>">
  <input type="submit" value="Go Back!" name="submit">
</form>
<?php

if(isset($_REQUEST["submit"]))
{
  // Unset all of the session variables.
  $_SESSION = array();

  // If it's desired to kill the session, also delete the session cookie.
  // Note: This will destroy the session, and not just the session data!
  if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
          $params["path"], $params["domain"],
          $params["secure"], $params["httponly"]
      );
  }

  // Finally, destroy the session.
  session_destroy();
  header("Location: QuizSelector.php");
}

?>