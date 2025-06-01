<!DOCTYPE html>
<html lang="vi">
<head>

    <link rel="stylesheet" href="style.css">
    <link rel="script" href="script.js">
</head>
<body>
    
    <?php include('Header.php'); ?>
    
    <div class="contact-container flexx" >
        <div class="left-col col-55" >
            <img class="logo" src="images/totalFood.jpg" style="width: 100%; height:100%; object-fit:cover;"/>
        </div>
            
        <div class="col-55 pad-2">
            <h2>Liên hệ với  chúng tôi</h2>
            <p><strong>Hương Từ Bi</strong> là nhà hàng chuyên cung cấp các món ăn chay thanh đạm, ngon miệng và tốt cho sức khỏe.</p>
            <p><strong>Địa chỉ:</strong> 123 Đường Thanh Tịnh, Quận Tây Hồ, TP. Hà Nội</p>
            <p><strong>Giờ mở cửa:</strong> 9:00 - 21:00 hàng ngày</p>
            
            <form id="contact-form" method="post">
            <label for="name">Full name</label>
            <input type="text" id="name" name="name" placeholder="Your Full Name" required>
                <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Your Email Address" required>
                <label for="message">Message</label>
            <textarea rows="6" placeholder="Your Message" id="message" name="message" required></textarea>
                <!--<a href="javascript:void(0)">--><button type="submit" id="submit" name="submit">Send</button><!--</a>-->
            
            </form>
            <div id="error"></div>
            <div id="success-msg"></div>
        </div>
        
        </div>
    </div>
    <?php include('Footer.php'); ?>

</body>
</html>