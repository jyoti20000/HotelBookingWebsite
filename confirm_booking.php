<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('include/links.php'); ?>
  <title>
    <?php echo $settings_r['site_title'] ?> - CONFIRM BOOKING
  </title>
</head>

<body class="bg-light">
  <?php require('include/header.php'); ?>
  <?php

  if (!isset($_GET['id']) || $settings_r['shutdown'] == true) {
    redirect('rooms.php');
  } else if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('rooms.php');
  }
  // filter and get room and user data
  
  $data = filteration($_GET);

  $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? AND `removed`=?", [$data['id'], 1, 0], 'iii');

  if (mysqli_num_rows($room_res) == 0) {
    redirect('rooms.php');
  }
  $room_data = mysqli_fetch_assoc($room_res);

  $_SESSION['room'] =
    [
      "id" => $room_data['id'],
      "name" => $room_data['name'],
      "price" => $room_data['price'],
      "payment" => null,
      "available" => false,
    ];

  $user_res = select("SELECT * FROM `user_cred1` WHERE `id`=? LIMIT 1", [$_SESSION['uId']], "i");
  $user_data = mysqli_fetch_assoc($user_res);
  ?>

  <div class="container">
    <div class="row">
      <div class="col-12 my-5 mb-4 px-4">
        <h2 class="fw-bold">
          CONFIRM BOOKING
        </h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
          <span class="text-secondary"> > </span>
          <a href="rooms.php" class="text-secondary text-decoration-none">ROOMS</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">CONFIRM</a>
        </div>
      </div>

      <div class="col-lg-7 col-md-12 px-4 ">
        <?php
        $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
        $thumb_q = mysqli_query($conn, "SELECT * FROM `room_images` 
        WHERE `room_id`='$room_data[id]' AND `thumb`='1'");

        if (mysqli_num_rows($thumb_q) > 0) {
          $thumb_res = mysqli_fetch_assoc($thumb_q);
          $room_thumb = ROOMS_IMG_PATH . $thumb_res['image'];
        }

        echo <<<data
          <div class="card p-3 shadow-sm rounded">
            <img src="$room_thumb" class="img-fluid rounded mb-3">
            <h5>$room_data[name]</h5>
            <h6>₹$room_data[price] per night</h6>
          </div>
         data;
        ?>
      </div>

      <div class="col-lg-5 col-md-12 px-4">
        <div class="card mb-4 border-0 shadow-sm rounded-3">
          <div class="card-body">

            <form action="pay_now.php" method="POST" id="booking_form">

              <h2 class="mb-3 text-center h-font bg-light">[BOOKING DETAILS]</h2>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Name:</label>
                  <input name="name" type="text" value="<?php echo $user_data['name'] ?>"
                    class="form-control shadow-none" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Phone Number:</label>
                  <input name="phonenum" type="number" value="<?php echo $user_data['phonenum'] ?>"
                    class="form-control shadow-none" required>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Address:</label>
                  <textarea name="address" class="form-control shadow-none"
                    required><?php echo $user_data['address'] ?></textarea>
                </div>
                <div class="col-md-12 mb-3">
                  <label class="form-label">Email:</label>
                  <input name="email" class="form-control shadow-none" value="<?php echo $user_data['email'] ?>"
                    required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Child:</label>
                  <input name="child" class="form-control shadow-none">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Adult:</label>
                  <input name="adult" class="form-control shadow-none">
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">Check-in:</label>
                  <input name="checkin" onchange="check_availability()" type="date" class="form-control shadow-none"
                    required>
                </div>
                <div class="col-md-6 mb-4">
                  <label class="form-label">Check-out:</label>
                  <input name="checkout" onchange="check_availability()" type="date" class="form-control shadow-none"
                    required>
                </div>
                <div class="col-12">
                  <div class="spinner-border text-info mb-3 d-none" id="info_loader" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <h6 class="mb-3 text-danger" id="submit_info">Provide check-in & check-out date... </h6>

                  <button type="submit" value="submit" name="submit"
                    class="btn w-100 text-white custom-bg shadow-none mb-1" disabled>submit</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('include/footer.php'); ?>

  <script>
    let booking_form = document.getElementById('booking_form');
    let info_loader = document.getElementById('info_loader');
    let submit_info = document.getElementById('submit_info');

    function check_availability() {
      let checkin_val = booking_form.elements['checkin'].value;
      let checkout_val = booking_form.elements['checkout'].value;

      booking_form.elements['submit'].setAttribute('disabled', true);

      if (checkin_val != '' && checkout_val != '') {
        submit_info.classList.add('d-none');
        submit_info.classList.replace('text-dark', 'text-danger');
        info_loader.classList.remove('d-none');

        let data = new FormData();

        data.append('check_availability', '');
        data.append('check_in', checkin_val);
        data.append('check_out', checkout_val);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax1/confirm_booking1.php", true);

        xhr.onload = function () {
          let data = JSON.parse(this.responseText);
          if (data.status == 'check_in_out_equal') {
            submit_info.innerText = "You cannot check-out on the same day!";
          }
          else if (data.status == 'check_out_earlier') {
            submit_info.innerText = "Check-out date is earlier than check-in date!";
          }
          else if (data.status == 'check_in_earlier') {
            submit_info.innerText = "Check-in date is earlier than today's date!";
          }
          else if (data.status == 'unavailable') {
            submit_info.innerText = "Room is not available for this check-in date!";
          }
          else {
            submit_info.innerHTML = "No. of Days: " + data.days + "<br>Total Amount to Pay: ₹" + data.payment;
            submit_info.classList.replace('text-danger', 'text-dark');

            booking_form.elements['submit'].removeAttribute('disabled');
          }
          submit_info.classList.remove('d-none')
          info_loader.classList.add('d-none');
        }

        xhr.send(data);

      }
      else {

        submitInfo.classList.remove('d-none');
        submitInfo.classList.replace('text-dark', 'text-danger');
        submitInfo.textContent = 'Provide check-in & check-out date...';
        submitButton.disabled = true;

      }

    }
  </script>
</body>

</html>