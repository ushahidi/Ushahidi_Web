<div class="content">
<div id="fb-root"><fb:comments numposts="10" width="800"></fb:comments></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: '<?php echo $facebook_app_id; ?>', status: true, cookie: true,
             xfbml: true});
  };
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());
</script>
</div>