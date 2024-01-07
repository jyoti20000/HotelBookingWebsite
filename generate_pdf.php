<?php
   require('admin/include_1/essentials.php');
   require('admin/include_1/db_config.php');
   require('admin/include_1/mpdf/vendor/autoload.php');
   
   session_start();

   if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) 
   {
       redirect('index.php');
   }
   if(isset($_GET['gen_pdf']) && isset($_GET['id']))
   {
      $frm_data = filteration($_GET);

      $query = "SELECT bo.*, bd.*,uc.email FROM `booking_order` bo 
      INNER JOIN `user_cred1` uc ON bo.user_id = uc.id
      INNER JOIN `booking_details1` bd ON bo.booking_id = bd.booking_id
      WHERE ((bo.booking_status='booked' AND bo.arrival=1) 
      OR (bo.booking_status='cancelled' AND bo.refund=1))
      AND bo.booking_id ='$frm_data[id]'";

      $res = mysqli_query($conn,$query);

      $total_rows = mysqli_num_rows($res);
  
      if($total_rows==0)
      {
         header('location: index.php');
         exit;
      }

      $data = mysqli_fetch_assoc($res);
      $date = date("d-m-Y | h:i:sa", strtotime($data['datentime']));
      $checkin = date("d-m-Y", strtotime($data['check_in']));
      $checkout = date("d-m-Y", strtotime($data['check_out']));

    
      $table_data = "
         <h2>BOOKING RECIEPT</h2>
         <table border='1'>
            <tr>
               <td>User_ID : $data[user_id]</td>
               <td>Booking Date : $date</td>
            </tr>
            <tr>
               <td colspan='2'>Status : $data[booking_status]</td>
            </tr>
            <tr>
               <td>Name : $data[user_name]</td>
               <td>Email : $data[email]</td>
            </tr>
            <tr>
               <td>Phone Number: $data[phonenum]</td>
               <td>Address : $data[address]</td>
            </tr>
            <tr>
               <td>Room Name : $data[room_name]</td>
               <td>Cost : ₹$data[price] per night</td>
            </tr>
            <tr>
               <td>Check in : $checkin</td>
               <td>Check -out : $checkout</td>
            </tr>
      ";

      if($data['booking_status']=='cancelled')
      {
         $refund = ($data['refund']) ? "Amount Refunded" : "Not yet Refunded";

         $table_data.="<tr>
           <td>Amount Paid : ₹$data[total_pay]</td>
           <td>Refund : $refund</td>
         </tr>";
      }
      else 
      {
         $table_data.="<tr>
           <td>Room Number : $data[room_no]</td>
           <td>Amount Paid : ₹$data[total_pay]</td>
         </tr>";
      }

      $table_data.="</table>";
      
      // Generate pdf
      
      // Create an instance of the class:
      $mpdf = new \Mpdf\Mpdf();

      // Write some HTML code:
      $mpdf->WriteHTML($table_data);

      // Output a PDF file directly to the browser
      $mpdf->Output($data['user_id'].'.pdf','D');      // D for download
      
   
   }

   else
   {
      header('location: index.php');
   }
?>
