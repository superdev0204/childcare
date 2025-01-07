<?php
	session_start();
if(isset($_POST['submit'])) {
 
      $errors = array();
   
      if($_POST['name'] == "") {
         $errors[] = 'The name field is empty';
      }
      if($_POST['email'] == "") {
         $errors[] = "The email field is empty";
      }
     if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
          $errors[] = "The email address was not valid";
      }
    if($_POST['subject'] == "") {
         $errors[] = "Please enter your subject";
      }
      if($_POST['comment'] == "") {
         $errors[] = "The comment field is empty";
      }
    if ($_REQUEST['captcha_entered']!=$_SESSION['security_number']) {
     $errors[] = "The math is incorrectly";
      }
      if(count($errors) == 0) {
         $sendto = "youremail@email.com";//Your email goes here
         $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
     $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
     $subject = $_POST['subject'];//You can change your subject here
     $comment = filter_var($_POST['comment'], FILTER_SANITIZE_STRING);
     
     $message = "<strong>$name</strong> has sent you a message by using the contact form:
   
    <p><strong>Name:</strong> <em>$name</em></p>
   
        <p><strong>Email:</strong> <em>$email</em></p>
       
        <p><strong>The subject:</strong> <em>$subject</em></p>
   
    <p><strong>Message:</strong> <em>$comment</em></p>";
   
    $headers = "From: $name <$email> \r\n";
    $headers .= "X-Mailer:PHP/\r\n";
    $headers .= "MIME-Version:1.0\r\n";
    $headers .= "Content-type:text/html; charset=iso-8859-1\r\n";
 
         if(mail($sendto, $subject, $message, $headers)) {
             $success = true;
         } else {
             $success = false;
         }
    } else {
       $success = false;
 
    }
  }
 
  if(isset($_POST['submit'])) {
     if($success == true & count($errors) == 0) {
        echo "<script>alert('Thank you for your email $name, we will get back to you asap.');</script>";
     }
     if(count($errors) == 0 & $success == false & isset($_POST['submit'])) {
        echo "<h2>There was a problem with our form. Please email us at youremail@email.com.</h2>";
     }
 
     if($success == false & count($errors) > 0 & isset($_POST['submit'])) {
        echo '<ul>';
        foreach($errors as $show_all) {
           echo '<li><span style="color:#ff0000;">'.$show_all.'</span></li>';
        }
        echo '</ul>';
     }
 }

?>

 
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
   <p><label for="name">Name </label><input type="text" name="name" placeholder="Name" id="name"></p>
   
   <p><label for="email">Email </label><input type="text" name="email" placeholder="youremail@email.com" id="email"></p>
   
   <p><label for="subject">Subject </label><input type="text" name="subject" placeholder="Subject" id="subject"></p>
   
   <p><label for="comment">Comment </label><textarea name="comment" placeholder="Drop a line"></textarea></p>
     
<?php echo '<img src="captcha.php" />'; ?><input name="captcha_entered" type="text" id="captcha_entered" size="5" maxlength="2" />
   
   <p><input type="submit" name="submit" value="Submit"></p>
 </form>

