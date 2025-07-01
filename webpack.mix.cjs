const mix = require('laravel-mix');

mix.js('resources/js/script.js', 'public/js')
   .js('resources/js/bootstrap.min.js', 'public/js')
   .css('resources/css/style.css', 'public/css')
   .css('resources/css/bootstrap/bootstrap.min.css', 'public/css') 
   .css('resources/css/pertails/navbar/navbar.css', 'public/css') 
   .css('resources/css/pertails/post/post.css', 'public/css') 
   .css('resources/css/pertails/navbar/nav-auth.css', 'public/css') 
   .css('resources/css/pertails/profile/profile.css', 'public/css') 
   .css('resources/css/pertails/indexPage/indexPage.css', 'public/css') 
   .version(); 