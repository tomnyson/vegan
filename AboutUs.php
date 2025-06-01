<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Về Chúng Tôi - CHAY 365</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
    /* Main content styles for about page */
    .about-hero {
      background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('../../images/totalFood.jpg');
      background-size: cover;
      background-position: center;
      height: 350px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      text-align: center;
    }
    
    .about-hero h1 {
      font-size: 3rem;
      text-shadow: 2px 2px 5px rgba(0,0,0,0.5);
      margin-bottom: 15px;
      position: relative;
    }
    
    .about-hero h1:after {
      content: "Since 1981";
      display: block;
      font-size: 1.2rem;
      margin-top: 10px;
      color: var(--accent-color);
    }
    
    .about-hero .divider {
      height: 3px;
      width: 100px;
      background: var(--accent-color);
      margin: 0 auto 20px;
    }
    
    /* Main content section */
    .section {
      max-width: 1200px;
      margin: 60px auto;
      padding: 0 20px;
    }
    
    .section-title {
      text-align: center;
      color: var(--primary-color);
      margin-bottom: 40px;
      font-size: 2.2rem;
      position: relative;
      font-weight: 700;
    }
    
    .section-title:after {
      content: "";
      position: absolute;
      width: 80px;
      height: 4px;
      background: var(--accent-color);
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 2px;
    }
    
    /* About content styles */
    .about-content {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 40px;
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    
    .about-image {
      flex: 1;
      min-width: 300px;
    }
    
    .about-image img {
      width: 100%;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .about-text {
      flex: 1;
      min-width: 300px;
    }
    
    .about-text h2 {
      color: var(--primary-color);
      font-size: 1.8rem;
      margin-bottom: 20px;
      position: relative;
      padding-bottom: 15px;
    }
    
    .about-text h2:after {
      content: "";
      position: absolute;
      width: 60px;
      height: 3px;
      background: var(--accent-color);
      bottom: 0;
      left: 0;
      border-radius: 2px;
    }
    
    .about-text p {
      margin-bottom: 15px;
      line-height: 1.8;
      font-size: 1.1rem;
    }

    /* Founder section */
    .founder-section {
      background: var(--bg-accent);
      padding: 60px 0;
      margin: 70px 0;
    }
    
    .founder-content {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 40px;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }
    
    .founder-image {
      flex: 1;
      min-width: 300px;
      text-align: center;
    }
    
    .founder-image img {
      width: 300px;
      height: 300px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid #fff;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .founder-text {
      flex: 2;
      min-width: 300px;
    }
    
    .founder-text h2 {
      color: var(--primary-color);
      font-size: 1.8rem;
      margin-bottom: 10px;
    }
    
    .founder-text h3 {
      color: var(--accent-color);
      font-size: 1.2rem;
      margin-bottom: 20px;
    }
    
    .founder-text blockquote {
      font-style: italic;
      font-size: 1.2rem;
      padding: 20px;
      background: rgba(255,255,255,0.7);
      border-left: 4px solid var(--accent-color);
      margin-bottom: 20px;
      border-radius: 0 10px 10px 0;
    }
    
    /* Mission and Values */
    .mission-values {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 30px;
      margin-top: 40px;
    }
    
    .value-item {
      flex: 1;
      min-width: 250px;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
      text-align: center;
      transition: transform 0.3s ease;
    }
    
    .value-item:hover {
      transform: translateY(-10px);
    }
    
    .value-item i {
      font-size: 2.5rem;
      color: var(--primary-color);
      margin-bottom: 20px;
    }
    
    .value-item h3 {
      margin-bottom: 15px;
      color: var(--accent-color);
    }
    
    /* Branch locations */
    .locations {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
      margin-top: 40px;
    }
    
    .location-item {
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .location-image {
      height: 200px;
      overflow: hidden;
    }
    
    .location-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    
    .location-item:hover .location-image img {
      transform: scale(1.1);
    }
    
    .location-info {
      padding: 20px;
    }
    
    .location-info h3 {
      color: var(--primary-color);
      margin-bottom: 10px;
    }
    
    .location-info p {
      margin-bottom: 5px;
      display: flex;
      align-items: center;
    }
    
    .location-info i {
      margin-right: 10px;
      color: var(--accent-color);
    }
    
    /* CTA section */
    .cta-section {
      background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../../images/totalFood.jpg');
      background-size: cover;
      background-position: center;
      color: white;
      text-align: center;
      padding: 80px 20px;
      margin: 70px 0;
      position: relative;
    }
    
    .cta-section h2 {
      font-size: 2.2rem;
      margin-bottom: 20px;
      color: var(--text-light);
    }
    
    .cta-section .cta-buttons {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 30px;
    }
    
    .cta-section .btn {
      background: var(--accent-color);
      color: white;
      border: none;
      padding: 15px 30px;
      border-radius: 30px;
      font-size: 1.1rem;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }
    
    .cta-section .btn-outline {
      background: transparent;
      border: 2px solid var(--accent-color);
    }
    
    .cta-section .btn:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    
    /* Footer */
    footer {
      background: var(--primary-color);
      color: var(--text-light);
      padding: 50px 0 20px;
      text-align: center;
    }
    
    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      padding: 0 20px;
    }
    
    .footer-section {
      flex: 1;
      min-width: 250px;
      margin-bottom: 30px;
      text-align: left;
    }
    
    .footer-section h3 {
      color: var(--accent-color);
      margin-bottom: 20px;
      font-size: 1.3rem;
    }
    
    .footer-section p {
      margin-bottom: 10px;
    }
    
    .social-icons {
      display: flex;
      gap: 15px;
      margin-top: 15px;
    }
    
    .social-icons a {
      color: var(--text-light);
      font-size: 1.5rem;
      transition: color 0.3s ease;
    }
    
    .social-icons a:hover {
      color: var(--accent-color);
    }
    
    .footer-bottom {
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid rgba(255,255,255,0.1);
      font-size: 0.9rem;
    }
    
    /* Responsive styles */
    @media (max-width: 900px) {
      .about-hero {
        height: 300px;
      }
      
      .about-hero h1 {
        font-size: 2.5rem;
      }
      
      .section {
        margin: 40px auto;
      }
      
      .founder-image img {
        width: 250px;
        height: 250px;
      }
    }
    
    @media (max-width: 768px) {
      .mobile-menu-btn {
        display: block;
      }
      
      nav {
        position: absolute;
        top: 60px;
        left: 0;
        background: var(--primary-color);
        width: 100%;
        flex-direction: column;
        padding: 20px 0;
        transform: translateY(-200%);
        transition: transform 0.3s ease;
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
      }
      
      nav.active {
        transform: translateY(0);
      }
      
      nav a {
        margin: 10px 0;
      }
      
      .footer-section {
        flex: 100%;
      }
    }
    
    @media (max-width: 600px) {
      .about-hero {
        height: 250px;
      }
      
      .about-hero h1 {
        font-size: 2rem;
      }
      
      .section-title {
        font-size: 1.8rem;
      }
      
      .about-content {
        padding: 30px 20px;
      }
      
      .founder-image img {
        width: 200px;
        height: 200px;
      }
    }
  </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<!-- Hero banner -->
<section class="about-hero">
  <div>
    <h1>Hương Từ Bi</h1>
    <div class="divider"></div>
  </div>
</section>

<!-- About us content -->
<section class="section">
  <div class="section-title">Giới thiệu về Hương Từ Bi</div>
  <div class="about-content">
    <div class="about-image">
      <img src="../../images/totalFood.jpg" alt="Nhà hàng Hương Từ Bi">
    </div>
    <div class="about-text">
      <h2>Ẩm thực chay tinh tế, sống khỏe mỗi ngày</h2>
      <p>Ăn chay đang trở thành xu hướng mới của lối sống hiện đại. Theo ước tính, thế giới đang có khoảng 900 triệu người ăn chay. Riêng Việt Nam, con số này vào khoảng 10 triệu. Người ta chọn ăn chay vì có lợi cho sức khỏe, hoặc do bất nguồn từ lòng từ bi với động vật, hoặc mong muốn thực hành tu tâm để có một cuộc sống thanh tịnh và an yên.</p>
      <p>Dù lý do nào, trong quá trình ăn chay của mỗi người đều hướng đến mục đích sống tốt đẹp... Đó là khởi thủy để chuỗi nhà hàng Hương Từ Bi ra đời và phát triển đến ngày hôm nay!!!</p>
      <p>Được thành lập từ năm 1981, Hương Từ Bi đã có hơn 30 năm kinh nghiệm trong việc phục vụ món chay tinh tế, kết hợp giữa truyền thống và hiện đại, mang đến cho thực khách những trải nghiệm ẩm thực thanh tịnh và đầy cảm hứng.</p>
    </div>
  </div>
</section>

<!-- Founder section -->
<section class="founder-section">
  <div class="founder-content">
    <div class="founder-image">
      <img src="../../images/manhinh.jpg" alt="Cleo Hồ Trần Phương Nga - Founder">
    </div>
    <div class="founder-text">
      <h2>Lê Hồng Sơn</h2>
      <h3>Founder & Owner</h3>
      <blockquote>
        "Với tôi, những gì càng đơn giản, xuất phát từ trái tim thì sẽ dễ dàng chạm đến trái tim."
      </blockquote>
      <p>Thương hiệu nhà hàng Chay 365 đang rất được cộng đồng ăn chay tại TP.HCM và khách nước ngoài yêu thích. Chị Cleo Hồ với niềm đam mê ẩm thực chay và tâm huyết với việc mang đến những món ăn lành mạnh, đã không ngừng nghiên cứu và phát triển các công thức chế biến món chay ngon miệng mà vẫn đảm bảo đầy đủ dinh dưỡng.</p>
      <p>Chị luôn tâm niệm rằng: "Ăn chay không chỉ là một chế độ ăn uống mà còn là một lối sống, một cách để gần gũi hơn với thiên nhiên và nuôi dưỡng lòng từ bi trong mỗi con người."</p>
      <p><a href="#" style="color: var(--accent-color); text-decoration: none;">Xem thêm bài viết về CHAY 365 và người sáng lập tại Tạp chí Du lịch TPHCM tại đây</a></p>
    </div>
  </div>
</section>

<!-- Our Mission and Values -->
<section class="section">
  <div class="section-title">Sứ mệnh & Giá trị của chúng tôi</div>
  <div class="mission-values">
    <div class="value-item">
      <i class="fas fa-heart"></i>
      <h3>Tâm từ bi</h3>
      <p>Mỗi món ăn đều được chế biến với tâm từ bi, thương yêu và tôn trọng mọi sự sống, góp phần giảm bạo lực và tổn thương cho động vật.</p>
    </div>
    <div class="value-item">
      <i class="fas fa-leaf"></i>
      <h3>Thiên nhiên</h3>
      <p>Sử dụng nguyên liệu hữu cơ, thân thiện với môi trường, góp phần bảo vệ hành tinh của chúng ta và sức khỏe của cộng đồng.</p>
    </div>
    <div class="value-item">
      <i class="fas fa-utensils"></i>
      <h3>Ẩm thực tinh tế</h3>
      <p>Cam kết mang đến những món ăn chay ngon miệng, hấp dẫn, phá vỡ định kiến về ẩm thực chay nhạt nhẽo, thiếu hấp dẫn.</p>
    </div>
  </div>
</section>

<!-- Our Locations -->
<section class="section">
  <div class="section-title">Hệ thống cửa hàng</div>
  <div class="locations">
    <div class="location-item">
      <div class="location-image">
        <img src="../../images/totalFood.jpg" alt="CHAY 365 - Chi nhánh 1">
      </div>
      <div class="location-info">
        <h3>CHAY 365 - Chi nhánh 1</h3>
        <p><i class="fas fa-map-marker-alt"></i> 365 Lê Văn Sỹ, P.13, Q.3, TP.HCM</p>
        <p><i class="fas fa-phone-alt"></i> (028) 3995 3365</p>
        <p><i class="fas fa-clock"></i> 9:00 - 21:00 hàng ngày</p>
      </div>
    </div>
    <div class="location-item">
      <div class="location-image">
        <img src="../../images/totalFood.jpg" alt="CHAY 365 - Chi nhánh 2">
      </div>
      <div class="location-info">
        <h3>CHAY 365 - Chi nhánh 2</h3>
        <p><i class="fas fa-map-marker-alt"></i> 98 Võ Thị Sáu, P.Tân Định, Q.1, TP.HCM</p>
        <p><i class="fas fa-phone-alt"></i> (028) 3526 3656</p>
        <p><i class="fas fa-clock"></i> 9:00 - 21:00 hàng ngày</p>
      </div>
    </div>
    <div class="location-item">
      <div class="location-image">
        <img src="../../images/totalFood.jpg" alt="CHAY 365 - Chi nhánh 3">
      </div>
      <div class="location-info">
        <h3>CHAY 365 - Chi nhánh 3</h3>
        <p><i class="fas fa-map-marker-alt"></i> 156 Nguyễn Văn Đậu, P.7, Q.Bình Thạnh, TP.HCM</p>
        <p><i class="fas fa-phone-alt"></i> (028) 3510 6565</p>
        <p><i class="fas fa-clock"></i> 9:00 - 21:00 hàng ngày</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA đặt bàn -->
<section class="cta-section">
  <div class="section">
    <h2>Trải nghiệm ẩm thực chay cùng CHAY 365</h2>
    <p style="margin-bottom: 10px">Hãy liên hệ với chúng tôi để có những trải nghiệm ẩm thực chay tuyệt vời</p>
    <div class="cta-buttons">
      <a href="tel:02839953365" class="btn">
        <i class="fas fa-phone-alt"></i> Gọi: (028) 3995 3365
      </a>
      <a href="reservation.html" class="btn btn-outline">
        <i class="fas fa-calendar-alt"></i> Đặt bàn online
      </a>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
</body>
</html>