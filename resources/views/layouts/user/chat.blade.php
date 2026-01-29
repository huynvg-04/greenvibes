<button id="ai-widget-trigger" class="shadow-lg d-flex justify-content-center align-items-center" title="Trợ lý AI của riêng bạn">
     <i class='bx bx-bot fs-5'></i>
     <span class="notification-badge">1</span>
</button>

<div id="ai-chat-window" class="shadow-lg">
     <div class="chat-header d-flex justify-content-between align-items-center p-3 text-white">
          <div class="d-flex align-items-center gap-2">
               <div class="bg-white color-accent rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class='bx bx-plant-pot fs-5'></i>
               </div>
               <div>
                    <h6 class="mb-0 fw-bold">Trợ lý GreenVibes</h6>
                    <small class="text-white-50" style="font-size: 11px;">Luôn sẵn sàng hỗ trợ</small>
               </div>
          </div>
          <button id="close-chat" class="btn-close-chat text-white"><i class='bx bx-x fs-4'></i></button>
     </div>

     <div id="chat-messages" class="p-3">
          <div class="message ai d-flex gap-2 mb-3">
               <div class="avatar flex-shrink-0">
                    <img src="{{ asset('images/ai-avatar.png') }}" onerror="this.src='https://cdn-icons-png.flaticon.com/512/4712/4712139.png'" width="30" height="30" class="rounded-circle bg-light p-1">
               </div>
               <div class="content bg-light p-2 shadow-sm">
                    Xin chào! 🌱 Mình rất vui khi được hỗ trợ bạn 😊.
               </div>
          </div>
     </div>

     <div class="chat-footer p-3 bg-white">
          <div class="chat-input-group">

               <input type="text"
                    id="user-input"
                    class="form-control"
                    placeholder="Nhập câu hỏi..."
                    autocomplete="off">

               <button id="send-btn" type="button">
                    <i class='bx bx-send fs-5'></i>
               </button>

          </div>
     </div>
</div>

<button id="myNewScrollBtn" class="scroll-to-top" title="Lên đầu trang">
     <i class='bx bx-chevron-up'></i>
</button>

<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v5.0&appId=2474475439538110&autoLogAppEvents=1"></script>
<div class="general_social_icons">
     <nav class="social">
          <ul>
               <li class="w3_facebook">
                    <div data-layout="box_count" data-href="http://greenvibes.onlinewebshop.net" data-size="large"><a target="_blank" title="Share GreenVibes đến với bạn bè trên Facebook!" href="https://www.facebook.com/sharer/sharer.php?u=http://greenvibes.onlinewebshop.net/index.php">Facebook<i class="fa fa-facebook"></i></a></div>
               </li>
          </ul>
     </nav>
</div>