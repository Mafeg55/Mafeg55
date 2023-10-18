<?php

session_start();

$email = "";
$name = "";
$errors = array();

include('config.php') ;


//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../assets/vendor/autoload.php';
//require '../config/Database.php';
$edit_state = false;


        // $result = mysqli_query($conn, $query);

        //if user signup button
        if (isset($_POST['signup'])) {
            $name = mysqli_real_escape_string($connection, $_POST['name']);
            $email = mysqli_real_escape_string($connection, $_POST['email']);
            $password = mysqli_real_escape_string($connection, $_POST['password']);
            $cpassword = mysqli_real_escape_string($connection, $_POST['cpassword']);

            if ($password !== $cpassword) {
                echo "Confirm password not matched!";
            } else {

                $email_check = "SELECT * FROM user WHERE email = '$email'";
                $res = mysqli_query($connection, $email_check);
                $check = mysqli_num_rows($res) > 0;
                if ($check == true) {
                    //to be re write
                    echo "Email that you have entered is already exist!";
                } else {
                    $encpass = md5($password);
                    $code = rand(999999, 111111);
                    $status = "notverified";

                    //function
                    $query = "INSERT INTO user (name, email, password, code, type)
                        values('$name', '$email', '$encpass', '$code', '$status')";
                    // $data_check = mysqli_query($con, $query);
                    $result = mysqli_query($connection, $query);

                    if ($result) {

                        echo "<div style='display: none;'>";
                        //Create an instance; passing `true` enables exceptions
                        $mail = new PHPMailer(true);

                        try {
                            //Server settings
                            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                            $mail->isSMTP();                                            //Send using SMTP
                            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                            $mail->Username   = 'mesfing594@gmail.com';                     //SMTP username
                            $mail->Password   = 'pjkljjwhxjourjtd';                               //SMTP password
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                            //Recipients
                            $mail->setFrom('mesfing594@gmail.com');
                            $mail->addAddress($email);

                            //Content
                            $mail->isHTML(true);                                  //Set email format to HTML
                            $mail->Subject = 'OTP - Verification';
                            $mail->Body = 'Your verification code is : ' . $code;

                            $mail->send();
                            echo 'We send a verification link to your email address';

                            //goto otp page
                            //  $_SESSION['info'] = $info;
                            $_SESSION['email'] = $email;
                            $_SESSION['password'] = $password;
                            header('location:../user-otp.php');
                            exit();
                        } catch (Exception $e) {
                            //to be re write
                            echo "Failed while sending code! {$mail->ErrorInfo}";
                        }
                    }
                    return $result;
                }
            }
        }
   

        //if user click verification code submit button
        if (isset($_POST['check'])) {
            $_SESSION['info'] = "";
            $otp_code = mysqli_real_escape_string($connection, $_POST['otp']);
            $check_code = "SELECT * FROM user WHERE code = $otp_code";
            $code_res = mysqli_query($connection, $check_code);
            if (mysqli_num_rows($code_res) > 0) {
                $fetch_data = mysqli_fetch_assoc($code_res);
                $fetch_code = $fetch_data['code'];
                $email = $fetch_data['email'];
                $code = 0;
                $status = 'verified';
                $query = "UPDATE user SET code = $code, status = '$status' WHERE code = $fetch_code";
                $otp_result = mysqli_query($connection, $query);
                //  $otp_result = mysqli_query($con, $result);


                if ($otp_result) {
                    $_SESSION['name'] = $name;
                    $_SESSION['email'] = $email;
                    header('location:../index.php');

                    exit();
                } else {

                    echo "Failed while updating code!";
                }
                return $otp_result;
            } else {
                echo "You've entered incorrect code!";
                header('location:../user-otp.php');
            }
        }


        //if user click login button
        if (isset($_POST['login'])) {
            $email = mysqli_real_escape_string($connection, $_POST['email']);
            $password = mysqli_real_escape_string($connection, $_POST['password']);
           


            $check_email = "SELECT * FROM user WHERE email = '$email'";
            $res = mysqli_query($connection, $check_email);
            if (mysqli_num_rows($res) > 0) {
                $fetch = mysqli_fetch_assoc($res);
                $fetch_pass = $fetch['password'];
                $encpass = md5($password);
                if ($encpass == $fetch_pass) {
                    //only for session i use  $_SESSION['role'] = $fetch['user_type'];

                    $_SESSION['role'] = $fetch['user_type'];
                    $_SESSION['email'] = $email;
                    $status = $fetch['status'];
                    if ($status == 'verified') {
                        $profile = $fetch['profile'];
                        $date = $fetch['date'];
                        $verify_user = $fetch['user_type'];

                        if ($verify_user == "Adminstrator" && $user == $verify_user ) {

                            $_SESSION['name'] = $fetch['name'];
                            $_SESSION['email'] = $email;
                            $_SESSION['password'] = $password;
                       //     $invoice_number="RS-".invoice_number();
                            header("location: ../ph_Adminstrator/index.php");

                        } 
                        elseif ($verify_user == "Doctor"  && $user == $verify_user ) {
                            $_SESSION['name'] = $fetch['name'];
                            $_SESSION['email'] = $email;
                            $_SESSION['password'] = $password;
                       //     $invoice_number="RS-".invoice_number();
                            header("location: ../ph_Doctor/index.php");

                        } elseif ($verify_user == "Manager"  && $user == $verify_user) {
                            $_SESSION['name'] = $fetch['name'];
                            $_SESSION['email'] = $email;
                            $_SESSION['password'] = $password;
                        //    $invoice_number="RS-".invoice_number();
                            header("location: ../ph_Manager/index.php");

                        } elseif ($verify_user == "Pharmacist"  && $user == $verify_user) {
                            $_SESSION['name'] = $fetch['name'];
                            $_SESSION['email'] = $email;
                            $_SESSION['password'] = $password;
                      //      $invoice_number="RS-".invoice_number();
                            header("location: ../ph_Pharmacist/index.php");

                        } elseif ($verify_user == "Store_coordinator"  && $user == $verify_user) {
                            $_SESSION['name'] = $fetch['name'];
                            $_SESSION['email'] = $email;
                            $_SESSION['password'] = $password;
               //            $invoice_number="RS-".invoice_number();
                            header("location: ../ph_store_coordinator/index.php");

                        } elseif ($verify_user == "Cashier"  && $user == $verify_user) {
                            $_SESSION['name'] = $fetch['name'];
                            $_SESSION['email'] = $email;
                            $_SESSION['password'] = $password;
              //            $invoice_number="RS-".invoice_number();
                            header("location: ../ph_Cashier/index.php");

                        } elseif ($verify_user == "Guest"  && $user == $verify_user) {
                            $_SESSION['name'] = $fetch['name'];
                            $_SESSION['email'] = $email;
                            $_SESSION['password'] = $password;
              //            $invoice_number="RS-".invoice_number();
                            header("location: ../ph_Guest/index.php");

                        }
                        else{
                             //to be rewrite
                    $info = " Incorrect Previllaged !!.";
                    $_SESSION['info'] = $info;
                    header('location:../login.php');

                    }
                        
                    } else {
                        $info = "It's look like you haven't still verify your email - $email";
                        $_SESSION['info'] = $info;
                        header('location:../user-otp.php');
                    }
                } else {
                    //to be rewrite
                    $info = " Incorrect email or password!.";
                    $_SESSION['info'] = $info;
                    header('location:../login.php');

                }
            } else {
                $info = "It's look like you're not yet a member! Click on the 'Create Account' link to signup. - $email";
                $_SESSION['info'] = $info;
                header('location:../login.php');
                //to be rewrite
            }
        }
 

        //when user click continue button in forgot password form
        if (isset($_POST['check-email'])) {

            $email = mysqli_real_escape_string($connection, $_POST['email']);
            $check_email = "SELECT * FROM user WHERE email='$email'";
            $run_sql = mysqli_query($connection, $check_email);

            if (mysqli_num_rows($run_sql) > 0) {
                $code = rand(999999, 111111);


                $insert_code = "UPDATE user SET code = $code WHERE email = '$email'";
                $run_query =  mysqli_query($connection, $insert_code);


                if ($run_query) {
                    $subject = "Password Reset Code";
                    $message = "Your password reset code is $code";
                    $sender = "From: shahiprem7890@gmail.com";
                    if (mail($email, $subject, $message, $sender)) {
                        $info = "We've sent a passwrod reset otp to your email - $email";
                        $_SESSION['info'] = $info;
                        $_SESSION['email'] = $email;
                        header('location: ../reset-code.php');
                        exit();
                    } else {
                        echo "Failed while sending code!";
                    }
                } else {
                    echo "Something went wrong!";
                }
            } else {
                echo "This email address does not exist!";
            }
        }
  


        //if user click check reset otp button
        if (isset($_POST['check-reset-otp'])) {
            $_SESSION['info'] = "";
            $otp_code = mysqli_real_escape_string($connection, $_POST['otp']);

            //app class
            $query = "SELECT * FROM user WHERE code = $otp_code";
            $code_res = mysqli_query($connection, $query);


            if (mysqli_num_rows($code_res) > 0) {
                $fetch_data = mysqli_fetch_assoc($code_res);
                $email = $fetch_data['email'];
                $_SESSION['email'] = $email;
                $info = "Please create a new password that you don't use on any other site.";
                $_SESSION['info'] = $info;
                header('location: ../new-password.php');
                exit();
            } else {
                echo "You've entered incorrect code!";
            }
        }

        
  
        //if user click change password button
        if (isset($_POST['change-password'])) {
            $_SESSION['info'] = "";
            $password = mysqli_real_escape_string($connection, $_POST['password']);
            $cpassword = mysqli_real_escape_string($connection, $_POST['cpassword']);
            if ($password !== $cpassword) {
                echo "Confirm password not matched!";
            } else {
                $code = 0;
                $email = $_SESSION['email']; //getting this email using session
                $encpass = md5($password);
                $update_pass = "UPDATE user SET code = $code, password = '$encpass' WHERE email = '$email'";
                $run_query = mysqli_query($connection, $update_pass);
                if ($run_query) {
                    echo "Your password changed. Now you can login with your new password.";
                    header('Location: ../index.php');
                } else {
                    echo "Failed to change your password!";
                }
            }
        }
  
     


 ?>