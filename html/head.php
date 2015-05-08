<head> 
    <meta charset="utf-8">
    <link href="favicon.ico" rel="shortcut icon"/>
    <title>Dækcenter.nu</title> 
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1; user-scalable=1;" /> 
    <link href='http://fonts.googleapis.com/css?family=Lora:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
 
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"> 
    <!-- <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"> -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">

    <link rel="stylesheet" type="text/css" href="fonts/css/stylesheet.css"> 
    <link rel="stylesheet" href="fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" /> 
    <link rel="stylesheet" type="text/css" href="css/style.css"> 
    <link rel="stylesheet" type="text/css" href="css/style-mobile.css">

    <!-- JS <script src="http://code.jquery.com/jquery.min.js"></script> --> 
    <script src="js/jquery-1.10.2.min.js"></script> 
    <script src="js/bootstrap.min.js"></script>  
    <!-- Add fancyBox --> 
    <script type="text/javascript" src="fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>
    <script type="text/javascript" src="fancybox/source/helpers/jquery.fancybox-media.js"></script>
    <script type="text/javascript">
        $(document).ready(function() { 
            $(".fancybox").fancybox();
            $(".fancybox_login").fancybox({  
                 beforeShow: function(){
                  $(".fancybox-skin").addClass('wrap_login'); 
                 // $(".fancybox-overlay").addClass('aaa'); 
                 }
            }); 
            $(".fancybox_send_gift").fancybox({  
                 beforeShow: function(){
                  $(".fancybox-skin").addClass('wrap_send_gift');  
                 }
            }); 
        });
    </script> 
     
</head> 