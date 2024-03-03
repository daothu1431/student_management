<?php

if(!defined('_INCODE'))
die('Access denied...');


$data = [
    'pageTitle' => 'Thêm học sinh'
];

layout('header', 'admin', $data);
layout('sidebar', 'admin', $data);
layout('breadcrumb', 'admin', $data);


// Kiểm tra phân quyền
$groupId = getGroupId();

$permissionData = getPermissionData($groupId);

// $check = checkPermission($permissionData, 'student', 'add');
// if(!$check) {
//     setFlashData('msg', '<i class="nav-icon fas fa-solid fa-star"></i> Bạn không có quyền thêm học sinh !');
//     setFlashData('msg_type', 'warning');
//     redirect('admin/?module=');
// }

// Truy vấn lấy ra danh sách nhóm
$allClass = getRaw("SELECT id, name FROM class ORDER BY name");

// Xử lý thêm người dùng
if(isPost()) {
    // Validate form
    $body = getBody(); // lấy tất cả dữ liệu trong form
    $errors = [];  // mảng lưu trữ các lỗi
    
    // Valide họ tên: Bắt buộc phải nhập, >=5 ký tự
    if(empty(trim($body['fullname']))) {
        $errors['fullname']['required'] = '** Bạn chưa nhập họ tên!';
    }else {
        if(strlen(trim($body['fullname'])) <= 5) {
        $errors['fullname']['min'] = '** Họ tên phải lớn hơn 5 ký tự!';
        }
    }


   // Validate chọn nhóm
   if(empty(trim($body['class_id']))) {
    $errors['class_id']['required'] = '** Vui lòng chọn lớp học !';
   }

   
   // Kiểm tra mảng error
  if(empty($errors)) {
    // không có lỗi nào
    $dataInsert = [
        'fullname' => $body['fullname'],
        'thumb' => $body['thumb'],
        'birthday' => $body['birthday'],
        'sex' => $body['sex'],
        'address' => $body['address'],
        'class_id' => $body['class_id'],
    ];

    $insertStatus = insert('student', $dataInsert);
    if ($insertStatus) {
        setFlashData('msg', 'Thêm học sinh '.'<strong>'.$body['fullname'].'</strong>'.' thành công');
        setFlashData('msg_type', 'success');
        redirect('?module=student&action=lists');
    }else {
    setFlashData('msg', 'Hệ thống đang gặp sự cố, vui lòng thử lại sau');
    setFlashData('msg_type', 'danger');
    redirect('?module=student&action=add'); 
    }

  }else {
    // Có lỗi xảy ra
    setFlashData('msg', 'Vui lòng kiểm tra chính xác thông tin nhập vào');
    setFlashData('msg_type', 'danger');
    setFlashData('errors', $errors);
    setFlashData('old', $body);  // giữ lại các trường dữ liệu hợp lê khi nhập vào
    redirect('?module=student&action=add'); 
  }

}
$msg =getFlashData('msg');
$msgType = getFlashData('msg_type');
$errors = getFlashData('errors');
$old = getFlashData('old');
   

?>
    <div class="container">
        <hr/>
        <?php
            getMsg($msg, $msgType);
        ?>

        <form action="" method="post">
            <div class="row">
                <div class="col-6">

                    <div class="form-group">
                        <label for="name">Ảnh</label>
                        <div class="row ckfinder-group">
                            <div class="col-8">
                                <input type="text" name="thumb" id="name" class="form-control image-render" value="<?php echo old('thumb', $old); ?>">   
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-success choose-image"><i class="nav-icon fas fa-solid fa-upload"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="">Họ tên</label>
                        <input type="text" name="fullname" id="" class="form-control" value="<?php echo old('fullname', $old); ?>">
                        <?php echo form_error('fullname', $errors, '<span class="error">', '</span>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="">Địa chỉ</label>
                        <input type="text" name="address" id="" class="form-control" value="<?php echo old('address', $old); ?>">
                        <?php echo form_error('address', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                    <div class="form-group">
                        <label for="">Lớp học</label>
                        <select name="class_id" id="" class="form-select">
                            <option value="">Chọn lớp học</option>
                            <?php

                                if(!empty($allClass)) {
                                    foreach($allClass as $item) {
                                ?>
                                    <option value="<?php echo $item['id'] ?>" <?php  echo (!empty($classId) && $classId == $item['id'])?'selected':false; ?>>Lớp: <?php echo $item['name'] ?></option> 
                                
                                <?php
                                    }
                                }
                                ?>
                        </select>
                        <?php echo form_error('group_id', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-5">
                    <div class="form-group">
                        <label for="">Ngày sinh</label>
                        <input type="date" name="birthday" id="" class="form-control" value="<?php echo old('birthday', $old); ?>">
                        <?php echo form_error('birthday', $errors, '<span class="error">', '</span>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="">Giới tính</label>
                        <input type="text" name="sex" id="" class="form-control" value="<?php echo old('sex', $old); ?>">
                        <?php echo form_error('sex', $errors, '<span class="error">', '</span>'); ?>
                    </div>
                   
                </div>              
            </div>
            <div class="col">
                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">Thêm mới</button>
                    <a style="margin-left: 20px " href="<?php echo getLinkAdmin('student', 'lists') ?>" class="btn btn-success"><i class="fa fa-forward"></i></a>
                </div>
            </div>
        </form>
    </div>


<?php
layout('footer', 'admin');





