<?php session_set_cookie_params('36000');ini_set('session.gc_maxlifetime', 36000); session_start();
require '../vendor/autoload.php';
include('../connection.php');
  include('check-login.php');  
// if(!isset($_SESSION['user_id'])){

//     echo '<script>window.location.href="login.php"</script>';
// }

 ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <title>SEO-YACSS</title>

     
   
        <!-- Icons -->
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
        <link rel="shortcut icon" href="<?php  echo SITE_URL ?>/dist/dashboard_assets/media/photos/fav.png">
        <link rel="icon" type="image/png" sizes="192x192" href="<?php  echo SITE_URL ?>/dist/dashboard_assets/media/photos/fav.png">
        <link rel="apple-touch-icon" sizes="180x180" href="<?php  echo SITE_URL ?>/dist/dashboard_assets/media/photos/fav.png">
        <!-- END Icons -->
       <link rel="stylesheet" href="<?php  echo SITE_URL ?>/dist/dashboard_assets/js/plugins/highlightjs/styles/atom-one-light.css">
        <!-- Stylesheets -->
           <link rel="stylesheet" href="<?php  echo SITE_URL ?>/dist/dashboard_assets/js/plugins/select2/css/select2.min.css">
        <link rel="stylesheet" href="<?php  echo SITE_URL ?>/dist/dashboard_assets/js/plugins/datatables/dataTables.bootstrap4.css">
        <link rel="stylesheet" href="<?php  echo SITE_URL ?>/dist/dashboard_assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css">

        <link rel="stylesheet" href="<?php  echo SITE_URL ?>/dist/dashboard_assets/js/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css">

        <link rel="stylesheet" href="<?php  echo SITE_URL ?>/dist/dashboard_assets/js/plugins/flatpickr/flatpickr.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Arimo:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Barlow+Condensed:wght@100;200;300;400;500&display=swap" rel="stylesheet">
        <!-- Fonts and Dashmix framework -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
        <link rel="stylesheet" id="css-main" href="<?php echo SITE_URL ?>dist/dashboard_assets/css/dashmix.min.css">
 <style type="text/css">
     .thead-dark th{
        background: black!important;
     }
     .form-control {
  
    outline: 0;
 
    border-radius: 33px;
    
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #d4dcec;
    border-radius: 9px;
    transition: border-color 0.30s ease-in-out,box-shadow .30s ease-in-out;
}
   .form-control:focus {
    /* color: #495057; */
    background-color: #f0f3f8;
    border-color:orange;
    box-shadow: 0px 0px 5px #ff8b26;
    outline: 1;
    box-shadow: 0 0 0 .2remrgba(6,101,208,.25);
}
     .text-warning{
        color: #FF8B26!important;
     }
     .text-info{
        color: #48c2fb!important;
     }
     .select2-container{
        width: 100%!important;
     }
       .bg-violet{
        background: #f1ebe8;
     }
     .bg-info{
        background: #48c2fb;
     }
      .btn-info{
        background: #48c2fb!important;
        border-color:#48c2fb!important ;
     }
      .btn-warning{
        background: #FF8B26!important;
     }
        .bg-warning{
        background: #FF8B26;
     }
     .btn-hero-info{
        background: #48c2fb!important;
     }
      .btn-hero-warning{
        background: #FF8B26!important;
     }
     
.tooltip1 {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black;
}

 
#page-header-loader{
    position: fixed;
        height: 68px;
        width: 100%;
        z-index: 1000000;
    top: 0;
    left: 250px;
background: #FF8B26!important;
    right: 0;
    bottom: 0;
}

.tooltip1 .tooltiptext {
  visibility: hidden;
     width: 230px;

  background-color: #555;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;
  position: absolute;
  z-index: 400000000000000000000;
  bottom: 0%;
  left: 50%;
  margin-left: -60px;
  opacity: 0;
  transition: opacity 0.3s;
}
  td,th{
        font-size: 10pt;
    }
 

 
 ::-webkit-scrollbar-track
{
    -webkit-box-shadow:none;
    background-color: transparent;
    border-radius:0px;
}

 ::-webkit-scrollbar
{
    width: 8px;
    height: 10px;
    background-color: #F5F5F5;
}

 ::-webkit-scrollbar-thumb
{
    border-radius: 2px;
    background-image:-webkit-gradient(linear, left bottom, left top, color-stop(0.44, #9e9e9e), color-stop(0.72, #9e9e9e), color-stop(0.86,#9e9e9e));

}
.tooltip1:hover .tooltiptext {
  visibility: visible;
  opacity: 1;
}
 </style>
 <body>

      <?php include('sidebar.php') ?>

           <!-- Header -->
            <header id="page-header">
                <!-- Header Content -->
                <div class="content-header">
                    <!-- Left Section -->
                    <div>
                        <!-- Toggle Sidebar -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout()-->
                        <button type="button" class="btn btn-dual" data-toggle="layout" data-action="sidebar_toggle">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>
                        <!-- END Toggle Sidebar -->

        <?php
               $date=time(); 
$user_id=$_SESSION['user_id'];
$limit=0;
$usage=0;
$user=mysqli_query($con,"select *,FROM_UNIXTIME(currentStartDate) as start_date,FROM_UNIXTIME(currentEndDate) as end_date  from users as u left join yo_prices as p on u.price_id=p.priceId left join yo_subscriptions as s on s.stripeSubscriptionId=u.subscription_id where u.id='$user_id'");
$user_row=mysqli_fetch_array($user);
   $month=date('m');
   $current_date=date('Y-m-d');
   $start_date=date('Y-m-d H:i:s',strtotime($user_row['start_date']));
        //start date + current month spent -1   
         // 2022-01-05     2022-05-01 2022-05-05  2022-05-24    2023-01-05
$end_date=date('Y-m-d H:i:s',strtotime($user_row['end_date']));

    if($user_row['duration']=='Yearly'){
$first_date = new DateTime($start_date);
$second_date = new DateTime($current_date);
$difference = $first_date->diff($second_date);

$dif=(int)$difference->m;
 
$start_date=date('Y-m-d H:i:s',strtotime("$start_date + $dif month"));
$end_date=date('Y-m-d H:i:s',strtotime("$start_date + 1 month"));

 
    $current_month_array=explode(' | ',$user_row['current_month']);
    
    $m_date=@$current_month_array[0];
    $e_date=@$current_month_array[1];    


if(($current_date>=$e_date || $user_row['current_month']=='' ) && $user_row['current_month_website']!=''){
       
                $current_month=$start_date.' | '.$end_date;
                mysqli_query($con,"update users set current_month_website='".$user_row['feature_1']."',current_month='$current_month' where id='$user_id'");
        
          $user=mysqli_query($con,"select *,FROM_UNIXTIME(currentStartDate) as start_date,FROM_UNIXTIME(currentEndDate) as end_date  from users as u left join yo_prices as p on u.price_id=p.priceId left join yo_subscriptions as s on s.stripeSubscriptionId=u.subscription_id where u.id='$user_id'");
            $user_row=mysqli_fetch_array($user);
 


}



   }

   



            $check=mysqli_query($con,"select count(*) as count from pages where user_id='$user_id' and created_at>='$start_date' and created_at<='$end_date'    and website_status='generated'");
            $check_row=mysqli_fetch_array($check);



               $usage=$check_row['count'];
               
if($_SESSION['role']=='user'){
 
  
            if($user_row['sortOrder']=='0' ){
                            $limit=20;
               }

                    if($user_row['sortOrder']==1 ){
                            $limit=50;
             }

            if($user_row['sortOrder']==2   ){
                 $limit=100; 
            }

           if($user_row['sortOrder']==3 ){
                     $limit=200; 
             }

       if($user_row['sortOrder']==4  ){
                    $limit=300;
               
                     
        }
     if($user_row['sortOrder']==5  ){
                    $limit=300;
               
                     
        }

       if($user_row['price_id']==''  ){
                    $limit=5;
               
        }
           
                
}
if($_SESSION['role']=='lifetime'){
      
         
            if($user_row['subscription']==1  ){
                      $limit=20;
 
             }

                    if($user_row['subscription']==2  ){
                      $limit=50; 
                     
             }

            if($user_row['subscription']==3  ){
                       $limit=100; 
                            
             }

           if($user_row['subscription']==4 ){
                      $limit=200;    
                     
             }

       if($user_row['subscription']==5 ){

                     $limit=300;
                            
        }

       if($user_row['subscription']==6 ){

                     $limit=300;
                            
        }
}
 
if($limit!=0){

if($user_row['current_month_website']!=''){
    $usage=$limit-$user_row['current_month_website'];
  
  
}
}
$progress=0;
if($_SESSION['role']!='admin'){
    if($limit!=0){
$progress=($usage/$limit*100).'%';
}  
}


                ?>         
                        <!-- END Open Search Section -->
                    </div>
                     
                    <!-- Right Section -->
                    <div>

                        <?php if(isset($_COOKIE['admin_access'])){ ?>
                        <a class="btn btn-warning" href="access-users-back.php">Go Back To Admin</a>
                        <?php } ?>
                        <!-- User Dropdown -->
                         <a href="https://www.facebook.com/groups/yacss" target="_blank" class="btn btn-hero-primary mr-1 btn-sm" style="font-size: 12px;">YACSS Facebook Group <i class="fab fa-facebook-square"></i></a>
                                 
                                  <a  href="help.php" class="btn btn-dual" >
                            <i class="fa fa-fw fa-question"></i>
                        </a>    
                                <?php
                                if($_SESSION['role']=='admin'){
     $notifications=mysqli_query($con,"select * from notifications where to_user_id=0 order by id desc limit 6");
                                    $notification_count=mysqli_query($con,"select id from notifications where to_user_id=0 and is_read=0 ");
                                
                                }
                                else{
                                 $notifications=mysqli_query($con,"select * from notifications where to_user_id='$user_id' order by id desc limit 6");
                                    $notification_count=mysqli_query($con,"select id from notifications where to_user_id='$user_id' and is_read=0 ");
                                  }
                                  ?>

                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn btn-dual" id="page-header-notifications-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-fw fa-bell"></i>
                                <span class="badge badge-danger badge-pill"><?php echo mysqli_num_rows($notification_count)  ?></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0" aria-labelledby="page-header-notifications-dropdown" style="">
                                <div class="bg-primary rounded-top font-w600 text-white text-center p-3">
                                    Notifications
                                </div>
                                <ul class="nav-items my-2">
                             
                                    <?php 
                                        if(mysqli_num_rows($notifications)>0){
                                    while($noti=mysqli_fetch_array($notifications)){ ?>
                                    <li>

                                        <a class="text-dark media  <?php echo $noti['is_read']==1?'':'bg-light' ?> py-2" href="https://<?php echo $noti['link'] ?>">
                                            <div class="mx-3">
                                                <i class="fa fa-fw fa-comment-alt text-primary"></i>
                                            </div>
                                            <div class="media-body font-size-sm pr-2">
                                                <div class="font-w600"><?php echo $noti['description'] ?></div>
                                                <div class="text-muted font-italic"><?php echo date('d M Y h:i A',strtotime($noti['created_at'])) ?></div>
                                            </div>
                                        </a>
                                    </li>
                                <?php } }
                                else{?>
                                    <li>

                                        <a class="text-dark media py-2" >
                                      
                                            <div class="media-body text-center font-size-sm pr-2">
                                                <div class="font-w600">No New Notifications</div>
                                                 
                                            </div>
                                        </a>
                                    </li>
                                <?php } ?>
                                </ul>
                                <div class="p-2 border-top">
                                    <a class="btn btn-light btn-block text-center" href="all-notifications.php">
                                        <i class="fa fa-fw fa-eye mr-1"></i> View All
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown d-inline-block" >
                            <button type="button" class="btn btn-dual" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-fw fa-user d-sm-none"></i>
                                <span class="d-none d-sm-inline-block"><?php echo  $_SESSION['name']?></span>
                                <i class="fa fa-fw fa-angle-down ml-1 d-none d-sm-inline-block"></i>
                            </button>
                        

                            <div class="dropdown-menu dropdown-menu-right p-0" aria-labelledby="page-header-user-dropdown" style="left: -100px;width: 400px;"> 
                                <div class="bg-primary rounded-top font-w600 text-white text-center p-3">
                                    User Options
                                </div>
                                 <div class="p-3">
                                    <?php if($_SESSION['role']!='admin'){ ?>
                                     <p class="font-size-h3 font-w700 mb-0">
                                         <?php echo $usage ?>/<?php echo $limit ?>
                                        </p>
                                        <p class="text-muted mb-0">
                                            Monthly Usage
                                        </p>

                                        <div class="mb-0">
                                       
                                        <div class="progress mb-1" style="height: 6px;">
                                            <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: <?php echo $progress ?>;" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                     
                                        <p class="font-size-sm font-w600 mb-3">
                                            <span class="font-w700">  <?php echo $usage ?> Items</span> of <span class="font-w700"><?php echo $limit ?></span> Websites
                                        </p>
                                                                         
                                    </div>
                                    <?php }?>
                                  
                                 <a class="dropdown-item" href="profile.php">
                                        <i class="far fa-fw fa-user mr-1"></i> Profile
                                    </a>
                                                     
                                    <div role="separator" class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="logout.php">
                                        <i class="far fa-fw fa-arrow-alt-circle-left mr-1"></i> Sign Out
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- END User Dropdown -->

                      
 
                    </div>
                    <!-- END Right Section -->
                </div>
                <!-- END Header Content -->

            
                <!-- Header Loader -->
                <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
                <div id="page-header-loader" class="overlay-header bg-header-dark">
                    <div class="bg-white-10">
                        <div class="content-header">
                            <div class="w-100 text-center">
                                <i class="fa fa-fw fa-sun fa-spin text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Header Loader -->
            </header>
            <!-- END Header -->


   
    