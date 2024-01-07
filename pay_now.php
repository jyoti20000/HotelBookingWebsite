<?php
require('admin/include_1/db_config.php');
require('admin/include_1/essentials.php');

// require('include/razorpay-php/Razorpay.php');


date_default_timezone_set("Asia/Kolkata");

session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) 
{
    redirect('index.php');
}

if (isset($_POST['submit']))
{
   $frm_data = filteration($_POST);


   $query1 = "INSERT INTO `booking_order`( `user_id`, `room_id`, `check_in`, `check_out`) VALUES (?,?,?,?)";

    insert($query1,[$_SESSION['uId'],$_SESSION['room']['id'],$frm_data['checkin'],$frm_data['checkout']],'iiss');

    $booking_id = mysqli_insert_id(($conn));

    $query2 = "INSERT INTO `booking_details1`(`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `email`, `address`, `child`, `adult`) VALUES (?,?,?,?,?,?,?,?,?,?)";

    insert($query2,[$booking_id, $_SESSION['room']['name'],$_SESSION['room']['price'],$_SESSION['room']['payment'],$frm_data['name'],$frm_data['phonenum'],$frm_data['email'],$frm_data['address'],$frm_data['child'],$frm_data['adult']],'isssssssss');

    echo<<<data
        <div class="col-12-px-4">
            <p class="fw-bold alert alert-success">
               <i class="bi bi-check-circle-fill"></i>
               <h1 style='color:red'>Your Payment is Done! You have successfully booked this room .......</h1>
                 <br><br>
                <h2><a href = 'bookings.php'>Go to Bookings</a></h2>
            </p>
        </div>
    data;

    
    $upd_query = "UPDATE `booking_order` SET `booking_status` = 'booked' WHERE `booking_id`='$booking_id'";

    mysqli_query($conn,$upd_query);

    
}
?>






<!-- // // Check if the form is submitted

// if (isset($_POST['submit'])) {

    // use Razorpay\Api\Api;

// $keyId = "rzp_test_lXctM3sLPh0P0a";
// $keySecret = "EJnC7XOzSmZ986lhEKu9WnVM";


   
//     $name = $_POST['name'];
//     $phonenum = $_POST['phonenum'];
//     $address = $_POST['address'];
//     $checkin = $_POST['checkin'];
//     $checkout = $_POST['checkout'];


// $key ="test_97e3420843aa35dbaa4accc07b7";
// $token = "test_8c76bd02de3a4c55e5a223d3ac6";
// $mojoURL = "test.instamojo.com ";



// $ch = curl_init();

// curl_setopt($ch, CURLOPT_URL, "https://$mojoURL/v2/payment_requests/");
// curl_setopt($ch, CURLOPT_HEADER, FALSE);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
// curl_setopt($ch, CURLOPT_HTTPHEADER,
//             array("X-Api-key:$key" ,
//             "X-Auth-Token:$token" ));

// $payload = Array(
//   'purpose' => 'WEB DEVELOPMENT',
//   'amount' => '2500',
//   'buyer_name' => "$name",
// //   'email' => "$email",
//   'phone' => "$phonenum",
//   'redirect_url' => 'http://www.example.com/redirect/',
//   'send_email' => true,
//   //'webhook' => 'http://www.example.com/webhook/',
//   'allow_repeated_payments' => false
// );

// curl_setopt($ch, CURLOPT_POST, true);
// curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
// $response = curl_exec($ch);
// curl_close($ch); 

// $decode = json_decode($response) ;
// $paymentURL = $decode->payment_request->longurl;

// // sql portion
// header("Location:$paymentURL");
// exit;
// 
// } -->



    <!-- // $_SESSION['name'] = $_POST['name'];
    // $_SESSION['phonenum'] = $_POST['phonenum'];
    // $_SESSION['address'] = $_POST['address'];
    // $_SESSION['checkin'] = $_POST['checkin'];
    // $_SESSION['checkout'] = $_POST['checkout'];
    // //if($_POST['email']!='');


   // $price = $row['price'];
//     $_SESSION['price']=$price;
//     $displayCurrency= 'INR';

//     $orderData = [
//         'receipt'         => 3456,
//         'amount'          => $price * 100, // 2000 rupees in paise
//         'currency'        => 'INR',
//         'payment_capture' => 1 // auto capture
//     ];

//     $razorpayOrder = $api->order->create($orderData);

// $razorpayOrderId = $razorpayOrder['id'];

// $_SESSION['razorpay_order_id'] = $razorpayOrderId;

// $displayAmount = $amount = $orderData['amount'];

// if ($displayCurrency !== 'INR')
// {
//     $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
//     $exchange = json_decode(file_get_contents($url), true);

//     $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
// }

// $data = [
//     "key"               => $keyId,
//     "amount"            => $amount,
//     "name"              => "StayEasy",
//     //"description"       => "Tron Legacy",
//     //"image"             => "https://s29.postimg.org/r6dj1g85z/daft_punk.jpg",
//     "prefill"           => [
//     "name"              => $name,
//     "email"             => $email,
//     "contact"           => $phonenum,
//     ],
//     "notes"             => [
//     "address"           => $address,
//     "merchant_order_id" => "12312321",
//     ],
//     "theme"             => [
//     "color"             => "#F37254"
//     ],
//     "order_id"          => $razorpayOrderId,
// ];

// if ($displayCurrency !== 'INR')
// {
//     $data['display_currency']  = $displayCurrency;
//     $data['display_amount']    = $displayAmount;
// }

// $json = json_encode($data);




//     // Generate a payment request
//     try {
//         $response = $api->paymentRequestCreate(
//             array(
//                 "purpose" => "Hotel Booking",
//                 "amount" => $_SESSION['room']['payment'],
                
//                 "buyer_name" => $name,
                
//                 "phone" => $phonenum,
//                 "send_email" => true,
//                 "send_sms" => true,
//                 "redirect_url" => "http://localhost/gj/pay_response.php",
//                 //"webhook" => "http://your-website.com/webhook"
//             )
//         );
        
//         $pay_url = $response['longurl'];
//         header("location: $pay_url");
//     } catch (Exception $e) {
//         echo 'Error: ' . $e->getMessage();
//     }
//

// Inser data into database

// $frm_data = filteration($_POST);

// $query1 = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`, `order_id`) VALUES (?,?,?,?,?)";

// insert($query1, [$_SESSION['uId'],$_SESSION['room']['id'],$frm_data['checkin'],$frm_data['checkout']],'isss');

// $booking_id = mysqli_insert_id($conn);

// $query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?) ";

// insert($query2,[$booking_id,$_SESSION['room']['name'],$_SESSION['room']['price'],$_SESSION['room']['payment'],$frm_data['name'],$frm_data['phonenum'],$frm_data['address']],'issssss');

// }



// if (isset($_POST['pay_now'])) {
//     // Retrieve form data
//     $name = $_POST['name'];
//     $phonenum = $_POST['phonenum'];
//     $address = $_POST['address'];
//     $checkin = $_POST['checkin'];
//     $checkout = $_POST['checkout'];

//     // Validate and sanitize form data as needed

//     // Save the booking details in the database
//     $insert_query = "INSERT INTO bookings (user_id, room_id, check_in, check_out, booking_status) VALUES (1, 1, '$checkin', '$checkout', 'pending')";
//     // Execute the insert query and handle any errors

//     // Get the booking ID of the inserted record
//     $booking_id = mysqli_insert_id($connection);

//     // Calculate the total payment amount based on your logic
//     $total_pay = 1000; // Replace with your calculation logic

//     // Perform dummy payment processing
//     $payment_success = performDummyPayment($booking_id, $total_pay);

//     if ($payment_success) {
//         // Payment successful
//         $message = "Payment successful!";
//         // You can perform any additional actions here, such as sending confirmation emails, generating invoices, etc.
//     } else {
//         // Payment failed
//         $message = "Payment failed!";
//         // You can handle the failure scenario as per your requirements, such as displaying an error message, redirecting to a different page, etc.
//     }

//     // Display the payment status message
//     echo "<h1>$message</h1>";
// }

// function performDummyPayment($booking_id, $total_pay)
// {
//     // Simulate payment processing
//     $payment_success = false; // Variable to track payment status

//     // Generate a random number to simulate payment success or failure
//     $random_number = rand(1, 10);

//     // Simulate a successful payment if the random number is even
//     if ($random_number % 2 === 0) {
//         // Payment successful
//         $payment_success = true;

//         // Update the database with payment details
//         $sql = "UPDATE bookings SET trans_id = '123456', trans_amt = $total_pay, trans_status = 'paid', trans_res_msg = 'Payment successful' WHERE booking_id = '$booking_id'";
//         // Execute the SQL query and handle any errors
//     } else {
//         // Payment failed
//         $payment_success = false;

//         // Update the database with payment failure details
//         $sql = "UPDATE bookings SET trans_id = NULL, trans_amt = 0, trans_status = 'failed', trans_res_msg = 'Payment failed' WHERE booking_id = '$booking_id'";
//         // Execute the SQL query and handle any errors
//     }

//     // Return the payment status
//     return $payment_success;
//}

// if (isset($_POST['submit_booking'])) {

//     $frm_data = filteration($_POST);
//     $name = $_POST['name'];
//     $phoneNumber = $_POST['phonenum'];
//     $address = $_POST['address'];
//     $checkin = $_POST['checkin'];
//     $checkout = $_POST['checkout'];
//     $email = $_POST['email'];
//     $child = $_POST['child'];
//     $adult = $_POST['adult'];



//     $query = "INSERT INTO `booking`(`name`, `email`, `phonenum`, `address`, `check_in`, `check_out`, `child`, `adult`) VALUES (?,?,?,?,?,?,?,?)";

//     $values = [$frm_data['name'],$frm_data['email'],$frm_data['phonenum'],$frm_data['address'],$frm_data['check_in'],$frm_data['check_out'],$frm_data['child'],$frm_data['adult']];
//     $res = insert($query, $values, 'ssisiiii');
//     echo $res;


//     if($stmt->execute())
//     {
//         echo "booking succeesful";
//     }
//     else
//     {
//         echo "error";
//     }
    
// //     if($verify_booking->rowCount() > 0){
// //         $delete_booking = $conn->prepare("DELETE FROM `bookings` WHERE booking_id = ?");
// //         $delete_booking->execute([$booking_id]);
// //         $success_msg[] = 'booking cancelled successfully!';
// //      }else{
// //         $warning_msg[] = 'booking cancelled already!';
// //      }



// // Check if form is submitted
// // if ($_SERVER["REQUEST_METHOD"] === "POST") {
// //     // Get form data
// //     $name = $_POST["name"];
// //     $email = $_POST["email"];
// //     $room_type = $_POST["room_type"];
// //     $check_in = $_POST["check_in"];
// //     $check_out = $_POST["check_out"];

//     // Perform validation (you can add more validation here)

//     // Database configuration
//     // $servername = "your_database_server";
//     // $username = "your_database_username";
//     // $password = "your_database_password";
//     // $dbname = "your_database_name";

//     // // Create a connection
//     // $conn = new mysqli($servername, $username, $password, $dbname);

//     // // Check connection
//     // if ($conn->connect_error) {
//     //     die("Connection failed: " . $conn->connect_error);
//     // }

//     // Prepare and bind the statement to prevent SQL injection
// //     $stmt = $conn->prepare("INSERT INTO bookings (name, email, room_type, check_in, check_out) VALUES (?, ?, ?, ?, ?)");
// //     $stmt->bind_param("sssss", $name, $email, $room_type, $check_in, $check_out);

// //     // Execute the statement
// //     if ($stmt->execute()) {
// //         // Display success message
// //         echo "Booking successful! Thank you, $name, for booking a $room_type room from $check_in to $check_out.";
// //     } else {
// //         // Display error message
// //         echo "Error: " . $stmt->error;
// //     }

// //     // Close the statement and the connection
// //     $stmt->close();
// //     $conn->close();
// // }








//     // $query1 ="INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`,  `order_id`) VALUES (?,?,?,?,?)";

//     // insert($query1,[['room']['id'],$frm_data['checkin'],$frm_data['checkout']],'sss');

//     // $booking_id = mysqli_insert_id($con);

//     // $query2 =  "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?) ";

//     // insert($query2,[$booking_id,['room']['name'],$_SESSION['room']['price'],$_SESSION['room']['payment'],$frm_data['name'],$frm_data['phonenum'],$frm_data['address']],'issssss');
    
  

// }















 
